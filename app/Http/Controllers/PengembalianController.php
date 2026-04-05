<?php

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function index()
    {
        $pengembalian = Pengembalian::with('peminjaman.user', 'peminjaman.alat')->latest()->get();
        return view('pages.pengembalian.index', compact('pengembalian'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,peminjaman_id',
            'tanggal_kembali_aktual' => 'required|date',
            'kondisi_alat' => 'required|in:baik,rusak,hilang',
            'keterangan' => 'nullable|string',
        ]);

        $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);

        // Hitung keterlambatan
        $tanggalKembali = Carbon::parse($request->tanggal_kembali_aktual);
        $jatuhTempo = Carbon::parse($peminjaman->tanggal_kembali_rencana);
        $keterlambatan = max(0, $tanggalKembali->diffInDays($jatuhTempo, false) * -1);

        // Hitung denda (Rp 50.000 per hari)
        $tarifDenda = 50000;
        $totalDenda = $keterlambatan * $tarifDenda;

        DB::transaction(function () use ($validated, $peminjaman, $keterlambatan, $tarifDenda, $totalDenda) {
            // Buat pengembalian
            Pengembalian::create([
                'peminjaman_id' => $validated['peminjaman_id'],
                'tanggal_kembali_aktual' => $validated['tanggal_kembali_aktual'],
                'kondisi_alat' => $validated['kondisi_alat'],
                'keterlambatan_hari' => $keterlambatan,
                'tarif_denda_per_hari' => $tarifDenda,
                'total_denda' => $totalDenda,
                'status_denda' => $totalDenda > 0 ? 'belum_lunas' : 'lunas', // pakai belum_lunas (dengan underscore)
                'keterangan' => $validated['keterangan'],
            ]);

            // Update status peminjaman
            $peminjaman->update(['status' => 'dikembalikan']);

            // Kembalikan stok
            $peminjaman->alat->increment('stok_tersedia', $peminjaman->jumlah);

            // Update kondisi alat jika rusak/hilang
            if ($validated['kondisi_alat'] != 'baik') {
                $peminjaman->alat->update(['kondisi' => $validated['kondisi_alat']]);
            }
        });

        // Log aktivitas
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Proses Pengembalian',
            'modul' => 'Pengembalian',
            'timestamp' => now(),
        ]);

        return redirect()->route('pengembalian.index')->with('success', 'Pengembalian berhasil diproses!');
    }

    public function destroy(Pengembalian $pengembalian)
    {
        $pengembalian->delete();

        // Log aktivitas
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Hapus Pengembalian',
            'modul' => 'Pengembalian',
            'timestamp' => now(),
        ]);

        return redirect()->route('pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus!');
    }
}