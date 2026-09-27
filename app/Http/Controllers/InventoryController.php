<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\InventoryHistory;
use App\Models\InventoryLoan;
use App\Models\Resident;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function dashboard()
    {
        $totalJenis = Inventory::count();
        $totalBarang = Inventory::sum('jumlah');

        $barangTersedia = 0;
        foreach (Inventory::all() as $inv) {
            $barangTersedia += $inv->tersedia_count;
        }

        $sedangDipinjam = InventoryLoan::whereIn('status', ['Disetujui', 'Dipinjam'])->sum('jumlah');
        $barangRusak = Inventory::whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat'])->sum('jumlah');
        $barangHilang = Inventory::where('kondisi', 'Hilang')->sum('jumlah');

        $latestLoans = InventoryLoan::with(['inventory', 'user'])->latest()->take(5)->get();

        $categories = InventoryCategory::withCount('inventories')->get();

        return view('inventories.dashboard', compact(
            'totalJenis',
            'totalBarang',
            'barangTersedia',
            'sedangDipinjam',
            'barangRusak',
            'barangHilang',
            'latestLoans',
            'categories'
        ));
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->hasRole('resident') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin') && ! $user->hasRole('rt-admin')) {
            abort(403, 'Akses dibatasi.');
        }

        $query = Inventory::with('category');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('merek', 'like', "%{$search}%");
            });
        }

        if ($kategori = $request->get('kategori')) {
            $query->where('kategori_id', $kategori);
        }

        if ($kondisi = $request->get('kondisi')) {
            $query->where('kondisi', $kondisi);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($lokasi = $request->get('lokasi')) {
            $query->where('lokasi', 'like', "%{$lokasi}%");
        }

        $inventories = $query->latest()->paginate(10)->withQueryString();
        $categories = InventoryCategory::all();

        return view('inventories.index', compact('inventories', 'categories'));
    }

    public function create()
    {
        $categories = InventoryCategory::all();
        $lastId = Inventory::max('id') ?? 0;
        $nextKode = 'INV-'.str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        return view('inventories.create', compact('categories', 'nextKode'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:inventory_categories,nama',
            'deskripsi' => 'nullable|string',
        ]);

        InventoryCategory::create($validated);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|unique:inventories,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:inventory_categories,id',
            'merek' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'kondisi' => 'required|in:Baik,Cukup Baik,Rusak Ringan,Rusak Berat,Hilang',
            'tahun_perolehan' => 'nullable|digits:4|integer',
            'sumber_dana' => 'nullable|string|max:255',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'lokasi' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:Tersedia,Dipinjam,Rusak,Hilang,Tidak Aktif',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('inventories', 'public');
        }

        $validated['harga_perolehan'] = $validated['harga_perolehan'] ?? 0;

        $inventory = Inventory::create($validated);

        InventoryHistory::create([
            'barang_id' => $inventory->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Barang ditambahkan',
            'keterangan' => 'Pengadaan atau pencatatan awal barang inventaris.',
        ]);

        return redirect()->route('inventories.index')->with('success', 'Data barang inventaris berhasil ditambahkan.');
    }

    public function show(Inventory $inventory)
    {
        $inventory->load(['category', 'histories.user', 'loans.user']);

        return view('inventories.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $categories = InventoryCategory::all();

        return view('inventories.edit', compact('inventory', 'categories'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:inventory_categories,id',
            'merek' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'kondisi' => 'required|in:Baik,Cukup Baik,Rusak Ringan,Rusak Berat,Hilang',
            'tahun_perolehan' => 'nullable|digits:4|integer',
            'sumber_dana' => 'nullable|string|max:255',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'lokasi' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:Tersedia,Dipinjam,Rusak,Hilang,Tidak Aktif',
        ]);

        if ($request->hasFile('foto')) {
            if ($inventory->foto) {
                Storage::disk('public')->delete($inventory->foto);
            }
            $validated['foto'] = $request->file('foto')->store('inventories', 'public');
        }

        $validated['harga_perolehan'] = $validated['harga_perolehan'] ?? 0;

        $inventory->update($validated);

        InventoryHistory::create([
            'barang_id' => $inventory->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Barang diedit',
            'keterangan' => 'Pembaruan data spesifikasi atau status barang.',
        ]);

        return redirect()->route('inventories.index')->with('success', 'Data barang inventaris berhasil diperbarui.');
    }

    public function destroy(Inventory $inventory)
    {
        if ($inventory->foto) {
            Storage::disk('public')->delete($inventory->foto);
        }

        $inventory->delete();

        return redirect()->route('inventories.index')->with('success', 'Data barang inventaris berhasil dihapus.');
    }

    public function loansIndex()
    {
        $user = auth()->user();
        $isStaff = $user && ($user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('rt-admin'));

        $loans = InventoryLoan::with(['inventory', 'user'])->latest()->paginate(15);
        $inventories = Inventory::where('status', 'Tersedia')->where('jumlah', '>', 0)->get();

        $residents = collect();
        if ($isStaff) {
            $residentsQuery = Resident::with(['family.rt', 'user']);
            if ($user->hasRole('rt-admin') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin')) {
                $rtId = $user->rt_id;
                if (! $rtId && $user->roles) {
                    foreach ($user->roles as $role) {
                        if ($role->slug === 'rt-admin' && $role->pivot->scope_id) {
                            $rtId = $role->pivot->scope_id;
                            break;
                        }
                    }
                }
                if ($rtId) {
                    $residentsQuery->whereHas('family', fn ($q) => $q->where('rt_id', $rtId));
                }
            } elseif ($user->hasRole('rw-admin') && ! $user->hasRole('super-admin')) {
                $rwId = $user->rw_id;
                if (! $rwId && $user->roles) {
                    foreach ($user->roles as $role) {
                        if ($role->slug === 'rw-admin' && $role->pivot->scope_id) {
                            $rwId = $role->pivot->scope_id;
                            break;
                        }
                    }
                }
                if ($rwId) {
                    $residentsQuery->whereHas('family.rt', fn ($q) => $q->where('rw_id', $rwId));
                }
            }
            $residents = $residentsQuery->orderBy('full_name')->get();
        }

        return view('inventories.loans', compact('loans', 'inventories', 'isStaff', 'residents'));
    }

    public function loansStore(Request $request)
    {
        $user = auth()->user();
        $isStaff = $user && ($user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('rt-admin'));

        if ($isStaff) {
            $validated = $request->validate([
                'resident_id' => 'required|exists:residents,id',
                'barang_id' => 'required|exists:inventories,id',
                'jumlah' => 'required|integer|min:1',
                'tanggal_pinjam' => 'required|date',
                'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
                'keperluan' => 'nullable|string',
                'catatan' => 'nullable|string',
            ]);

            $resident = Resident::with('user')->findOrFail($validated['resident_id']);

            if ($user->hasRole('rt-admin') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin')) {
                $rtId = $user->rt_id;
                if (! $rtId && $user->roles) {
                    foreach ($user->roles as $role) {
                        if ($role->slug === 'rt-admin' && $role->pivot->scope_id) {
                            $rtId = $role->pivot->scope_id;
                            break;
                        }
                    }
                }
                $residentRtId = $resident->family?->rt_id;
                if ($residentRtId !== $rtId) {
                    abort(403, 'Akses ditolak. Warga tidak berada dalam cakupan RT Anda.');
                }
            }

            $namaPeminjam = $resident->full_name;
            $nomorHp = $resident->phone ?? '-';
            $peminjamUserId = $resident->user?->id ?? $user->id;
        } else {
            $resident = $user->resident;
            if (! $resident || empty($resident->phone)) {
                return redirect()->back()->withErrors(['nomor_hp' => 'Nomor HP profil Anda belum lengkap. Mohon lengkapi profil terlebih dahulu.'])->withInput();
            }

            $validated = $request->validate([
                'barang_id' => 'required|exists:inventories,id',
                'jumlah' => 'required|integer|min:1',
                'tanggal_pinjam' => 'required|date',
                'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
                'keperluan' => 'nullable|string',
                'catatan' => 'nullable|string',
            ]);

            $namaPeminjam = $resident->full_name;
            $nomorHp = $resident->phone;
            $peminjamUserId = $user->id;
        }

        $inventory = Inventory::findOrFail($validated['barang_id']);

        if (in_array($inventory->kondisi, ['Rusak Berat', 'Hilang'])) {
            return redirect()->back()->withErrors(['barang_id' => 'Barang dengan kondisi rusak berat atau hilang tidak dapat dipinjam.'])->withInput();
        }

        if ($validated['jumlah'] > $inventory->tersedia_count) {
            return redirect()->back()->withErrors(['jumlah' => 'Jumlah pinjaman melebihi stok yang tersedia ('.$inventory->tersedia_count.').'])->withInput();
        }

        $lastLoanId = InventoryLoan::max('id') ?? 0;
        $kodePeminjaman = 'PIN-'.str_pad($lastLoanId + 1, 4, '0', STR_PAD_LEFT);

        $loan = InventoryLoan::create([
            'kode_peminjaman' => $kodePeminjaman,
            'user_id' => $peminjamUserId,
            'nama_peminjam' => $namaPeminjam,
            'nomor_hp' => $nomorHp,
            'barang_id' => $inventory->id,
            'jumlah' => $validated['jumlah'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
            'keperluan' => $validated['keperluan'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'kondisi_sebelum' => $inventory->kondisi,
            'status' => 'Dipinjam',
        ]);

        InventoryHistory::create([
            'barang_id' => $inventory->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Barang dipinjam',
            'keterangan' => 'Dipinjam oleh '.$namaPeminjam.' sejumlah '.$validated['jumlah'].' '.$inventory->satuan,
        ]);

        return redirect()->route('inventories.loans')->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function loanUpdateStatus(Request $request, InventoryLoan $loan)
    {
        $user = auth()->user();
        $isStaff = $user && ($user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('rt-admin'));

        if (! $isStaff) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak untuk menyetujui peminjaman.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        $loan->update(['status' => $validated['status']]);

        InventoryHistory::create([
            'barang_id' => $loan->barang_id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Peminjaman '.strtolower($validated['status']),
            'keterangan' => 'Kode Peminjaman: '.$loan->kode_peminjaman,
        ]);

        if ($loan->user) {
            $loan->user->notify(new GeneralNotification(
                'Status Peminjaman Inventaris',
                'Pengajuan peminjaman barang ('.optional($loan->inventory)->nama_barang.') telah '.strtolower($validated['status']).'.',
                'inventory_loan',
                route('inventories.loans')
            ));
        }

        return redirect()->route('inventories.loans')->with('success', 'Status peminjaman diperbarui.');
    }

    public function returnsIndex()
    {
        $activeLoans = InventoryLoan::with(['inventory', 'user'])->whereIn('status', ['Disetujui', 'Dipinjam', 'Terlambat'])->latest()->get();
        $returnedLoans = InventoryLoan::with(['inventory', 'user'])->where('status', 'Dikembalikan')->latest()->paginate(10);

        return view('inventories.returns', compact('activeLoans', 'returnedLoans'));
    }

    public function returnsStore(Request $request, InventoryLoan $loan)
    {
        $user = auth()->user();
        if (! $user || $loan->user_id !== $user->id) {
            abort(403, 'Akses ditolak. Anda bukan peminjam transaksi ini.');
        }

        if ($loan->status === 'Dikembalikan') {
            return redirect()->back()->withErrors(['error' => 'Peminjaman ini sudah dikembalikan sebelumnya.']);
        }

        $validated = $request->validate([
            'kondisi_sesudah' => 'required|in:Baik,Cukup Baik,Rusak Ringan,Rusak Berat,Hilang',
            'catatan' => 'nullable|string',
        ]);

        $loan->update([
            'tanggal_kembali_actual' => now()->toDateString(),
            'kondisi_sesudah' => $validated['kondisi_sesudah'],
            'status' => 'Dikembalikan',
            'catatan' => $validated['catatan'] ?? $loan->catatan,
        ]);

        $inventory = $loan->inventory;
        $inventory->update([
            'kondisi' => $validated['kondisi_sesudah'],
            'status' => in_array($validated['kondisi_sesudah'], ['Rusak Berat', 'Hilang']) ? $validated['kondisi_sesudah'] : 'Tersedia',
        ]);

        InventoryHistory::create([
            'barang_id' => $inventory->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Barang dikembalikan',
            'keterangan' => 'Dikembalikan oleh '.$loan->nama_peminjam.'. Kondisi setelah kembali: '.$validated['kondisi_sesudah'],
        ]);

        if ($loan->user) {
            $loan->user->notify(new GeneralNotification(
                'Pengembalian Inventaris Berhasil',
                'Pengembalian barang ('.optional($inventory)->nama_barang.') telah berhasil diproses.',
                'inventory_return',
                route('inventories.returns')
            ));
        }

        return redirect()->route('inventories.returns')->with('success', 'Pengembalian barang berhasil diproses.');
    }

    public function historiesIndex()
    {
        $histories = InventoryHistory::with(['inventory', 'user'])->latest()->paginate(15);

        return view('inventories.histories', compact('histories'));
    }

    public function reportsIndex(Request $request)
    {
        $query = Inventory::with('category');

        if ($kategori = $request->get('kategori')) {
            $query->where('kategori_id', $kategori);
        }
        if ($kondisi = $request->get('kondisi')) {
            $query->where('kondisi', $kondisi);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($tahun = $request->get('tahun')) {
            $query->where('tahun_perolehan', $tahun);
        }

        $inventories = $query->get();
        $categories = InventoryCategory::all();

        return view('inventories.reports', compact('inventories', 'categories'));
    }

    public function exportExcel(Request $request)
    {
        $query = Inventory::with('category');
        if ($kategori = $request->get('kategori')) {
            $query->where('kategori_id', $kategori);
        }
        if ($kondisi = $request->get('kondisi')) {
            $query->where('kondisi', $kondisi);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        $inventories = $query->get();

        $filename = 'laporan-inventaris-rw29-'.date('Y-m-d').'.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $html = '<table border="1">
            <thead>
                <tr>
                    <th colspan="9" style="background-color: #10b981; color: #ffffff; font-size: 14px; text-align: center;">LAPORAN INVENTARIS BARANG RW 29 ALAMANDA REGENCY</th>
                </tr>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>Tahun</th>
                    <th>Harga (Rp)</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($inventories as $item) {
            $html .= '<tr>
                <td>'.$item->kode_barang.'</td>
                <td>'.htmlspecialchars($item->nama_barang).'</td>
                <td>'.optional($item->category)->nama.'</td>
                <td>'.$item->jumlah.' '.$item->satuan.'</td>
                <td>'.$item->kondisi.'</td>
                <td>'.$item->tahun_perolehan.'</td>
                <td>'.$item->harga_perolehan.'</td>
                <td>'.htmlspecialchars($item->lokasi).'</td>
                <td>'.$item->status.'</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        return response($html, 200, $headers);
    }
}
