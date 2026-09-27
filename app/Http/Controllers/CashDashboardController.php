<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\FeeBill;
use App\Models\Payment;
use App\Models\Rt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashDashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'current_month'); // current_month, 3_months, 6_months, year

        $now = Carbon::now();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();
        $prevStartDate = $now->copy()->subMonth()->startOfMonth();
        $prevEndDate = $now->copy()->subMonth()->endOfMonth();

        if ($period === '3_months') {
            $startDate = $now->copy()->subMonths(2)->startOfMonth();
            $prevStartDate = $now->copy()->subMonths(5)->startOfMonth();
            $prevEndDate = $now->copy()->subMonths(3)->endOfMonth();
        } elseif ($period === '6_months') {
            $startDate = $now->copy()->subMonths(5)->startOfMonth();
            $prevStartDate = $now->copy()->subMonths(11)->startOfMonth();
            $prevEndDate = $now->copy()->subMonths(6)->endOfMonth();
        } elseif ($period === 'year') {
            $startDate = $now->copy()->startOfYear();
            $prevStartDate = $now->copy()->subYear()->startOfYear();
            $prevEndDate = $now->copy()->subYear()->endOfYear();
        }

        // 1. SALDO KAS RW = Opening Balance + Total Approved Income - Total Approved Expense
        $openingBalance = CashAccount::sum('opening_balance');
        $allApprovedIncome = CashTransaction::where('status', 'approved')->where('transaction_type', 'income')->sum('amount');
        $allApprovedExpense = CashTransaction::where('status', 'approved')->where('transaction_type', 'expense')->sum('amount');
        $totalBalance = $openingBalance + $allApprovedIncome - $allApprovedExpense;

        // 2. PEMASUKAN & PENGELUARAN Periode Terpilih
        $periodIncome = CashTransaction::where('status', 'approved')
            ->where('transaction_type', 'income')
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');

        $prevIncome = CashTransaction::where('status', 'approved')
            ->where('transaction_type', 'income')
            ->whereBetween('transaction_date', [$prevStartDate->format('Y-m-d'), $prevEndDate->format('Y-m-d')])
            ->sum('amount');

        $incomeChange = $prevIncome > 0 ? (($periodIncome - $prevIncome) / $prevIncome) * 100 : null;

        $periodExpense = CashTransaction::where('status', 'approved')
            ->where('transaction_type', 'expense')
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');

        $prevExpense = CashTransaction::where('status', 'approved')
            ->where('transaction_type', 'expense')
            ->whereBetween('transaction_date', [$prevStartDate->format('Y-m-d'), $prevEndDate->format('Y-m-d')])
            ->sum('amount');

        $expenseChange = $prevExpense > 0 ? (($periodExpense - $prevExpense) / $prevExpense) * 100 : null;

        // 4. IURAN / PIUTANG BELUM LUNAS = Total Tagihan - Total Pembayaran Approved
        $totalBillAmount = FeeBill::sum('amount_due');
        $approvedPaymentsTotal = Payment::where('status', 'verified')->sum('total_amount');
        $paidBillAmount = FeeBill::where('status', 'paid')->sum('amount_due');
        $unpaidBillAmount = max(0, $totalBillAmount - $approvedPaymentsTotal);
        $collectionPercentage = $totalBillAmount > 0 ? round((min($totalBillAmount, $approvedPaymentsTotal) / $totalBillAmount) * 100, 1) : 0;

        // D. GRAFIK ARUS KAS (Bulanan)
        $cashFlowMonths = 6;
        if ($period === '3_months') {
            $cashFlowMonths = 3;
        }
        if ($period === 'year') {
            $cashFlowMonths = 12;
        }

        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = $cashFlowMonths - 1; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $chartLabels[] = $m->translatedFormat('M Y');
            $mStart = $m->copy()->startOfMonth()->format('Y-m-d');
            $mEnd = $m->copy()->endOfMonth()->format('Y-m-d');

            $chartIncome[] = (float) CashTransaction::where('status', 'approved')
                ->where('transaction_type', 'income')
                ->whereBetween('transaction_date', [$mStart, $mEnd])
                ->sum('amount');

            $chartExpense[] = (float) CashTransaction::where('status', 'approved')
                ->where('transaction_type', 'expense')
                ->whereBetween('transaction_date', [$mStart, $mEnd])
                ->sum('amount');
        }

        // F. STATUS IURAN PER RT
        $rtSummary = [];
        $rts = Rt::with('families.residents.feeBills')->get();
        foreach ($rts as $rt) {
            $rtTotal = 0;
            $rtPaid = 0;
            foreach ($rt->families as $family) {
                foreach ($family->residents as $resident) {
                    foreach ($resident->feeBills as $bill) {
                        $rtTotal += $bill->amount_due;
                        if ($bill->status === 'paid') {
                            $rtPaid += $bill->amount_due;
                        }
                    }
                }
            }
            $rtUnpaid = $rtTotal - $rtPaid;
            $rtPercentage = $rtTotal > 0 ? round(($rtPaid / $rtTotal) * 100, 1) : 0;

            $rtSummary[] = [
                'rt_name' => $rt->name ?? 'RT 00'.$rt->id,
                'total' => $rtTotal,
                'paid' => $rtPaid,
                'unpaid' => $rtUnpaid,
                'percentage' => $rtPercentage,
            ];
        }

        // G. TRANSAKSI KAS TERBARU
        $recentTransactions = CashTransaction::with(['cashAccount', 'category'])
            ->where('status', 'approved')
            ->latest('transaction_date')
            ->take(8)
            ->get();

        // H. PENGELUARAN BERDASARKAN KATEGORI
        $expenseByCategory = CashTransaction::with('category')
            ->where('status', 'approved')
            ->where('transaction_type', 'expense')
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select('category_id', DB::raw('sum(amount) as total'))
            ->groupBy('category_id')
            ->get();

        return view('cash_dashboard', compact(
            'period',
            'totalBalance',
            'periodIncome',
            'incomeChange',
            'periodExpense',
            'expenseChange',
            'unpaidBillAmount',
            'totalBillAmount',
            'paidBillAmount',
            'collectionPercentage',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'rtSummary',
            'recentTransactions',
            'expenseByCategory'
        ));
    }
}
