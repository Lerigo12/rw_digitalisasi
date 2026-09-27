<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\CashTransaction;
use App\Models\LetterRequest;
use App\Models\Payment;
use App\Models\Resident;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $payments = Payment::with(['payerResident', 'verification.verifiedBy'])
            ->where('status', 'verified')
            ->whereHas('verification', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('verified_at', [$startDate, $endDate]);
            })
            ->get();

        $totalIncome = $payments->sum('total_amount');

        $residentsCount = Resident::count();
        $assetsCount = Asset::count();
        $lettersCount = LetterRequest::whereBetween('created_at', [$startDate, $endDate])->count();

        return view('reports.index', compact('payments', 'totalIncome', 'residentsCount', 'assetsCount', 'lettersCount', 'startDate', 'endDate'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $transactions = CashTransaction::with(['cashAccount', 'category'])
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->latest('transaction_date')
            ->get();

        $filename = 'rekap-kas-iuran-rw29-'.date('Y-m-d').'.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $html = '<table border="1">
            <thead>
                <tr>
                    <th colspan="5" style="background-color: #10b981; color: #ffffff; font-size: 14px; text-align: center;">REKAP DATA KAS IURAN RW 29 ALAMANDA REGENCY (PERIODE: '.$startDate.' s/d '.$endDate.')</th>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori / Akun</th>
                    <th>Keterangan</th>
                    <th>Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($transactions as $trx) {
            $html .= '<tr>
                <td>'.$trx->transaction_date.'</td>
                <td>'.strtoupper($trx->transaction_type).'</td>
                <td>'.optional($trx->category)->name.' / '.optional($trx->cashAccount)->name.'</td>
                <td>'.htmlspecialchars($trx->description).'</td>
                <td>'.$trx->amount.'</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        return response($html, 200, $headers);
    }
}
