<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $peminjamanRaw = Peminjaman::with(['user', 'alat', 'petugas'])
            ->whereDate('tanggal_peminjaman', $tanggal)
            ->get();

        $pengembalianRaw = Pengembalian::with(['peminjaman.user', 'peminjaman.alat'])
            ->whereDate('tanggal_kembali_aktual', $tanggal)
            ->get();

        // Map ke array supaya view bisa akses dengan $item['key']
        $peminjamanHariIni = $peminjamanRaw->map(function ($p) {
            $tglJatuhTempo = $p->tanggal_jatuh_tempo ?? $p->jatuh_tempo ?? null;
            return [
                'tgl_pinjam'  => $p->tanggal_peminjaman,
                'peminjam'    => optional($p->user)->nama_lengkap ?? optional($p->user)->username ?? '-',
                'alat'        => optional($p->alat)->nama_alat ?? '-',
                'jumlah'      => $p->jumlah ?? 1,
                'jatuh_tempo' => $tglJatuhTempo,
                'petugas'     => optional($p->petugas)->username ?? '-',
            ];
        });

        $pengembalianHariIni = $pengembalianRaw->map(function ($k) {
            $terlambat = 0;
            if ($k->peminjaman) {
                $jatuhTempo = $k->peminjaman->tanggal_jatuh_tempo ?? $k->peminjaman->jatuh_tempo ?? null;
                if ($jatuhTempo) {
                    $diff = Carbon::parse($k->tanggal_kembali_aktual)->diffInDays(Carbon::parse($jatuhTempo), false);
                    $terlambat = $diff < 0 ? abs($diff) : 0;
                }
            }
            return [
                'tgl_kembali' => $k->tanggal_kembali_aktual,
                'peminjam'    => optional(optional($k->peminjaman)->user)->nama_lengkap
                                 ?? optional(optional($k->peminjaman)->user)->username
                                 ?? '-',
                'alat'        => optional(optional($k->peminjaman)->alat)->nama_alat ?? '-',
                'kondisi'     => $k->kondisi_alat ?? $k->kondisi ?? '-',
                'terlambat'   => $terlambat,
                'denda'       => $k->total_denda ?? $k->denda ?? 0,
            ];
        });

        $totalPeminjamanHariIni  = $peminjamanHariIni->count();
        $totalPengembalianHariIni = $pengembalianHariIni->count();
        $totalDendaHariIni       = $pengembalianHariIni->sum('denda');

        return view('pages.laporan.index', compact(
            'tanggal',
            'peminjamanHariIni',
            'pengembalianHariIni',
            'totalPeminjamanHariIni',
            'totalPengembalianHariIni',
            'totalDendaHariIni'
        ));
    }

    public function cetak(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $peminjamanRaw = Peminjaman::with(['user', 'alat', 'petugas'])
            ->whereDate('tanggal_peminjaman', $tanggal)
            ->get();

        $pengembalianRaw = Pengembalian::with(['peminjaman.user', 'peminjaman.alat'])
            ->whereDate('tanggal_kembali_aktual', $tanggal)
            ->get();

        $peminjamanHariIni = $peminjamanRaw->map(function ($p) {
            $tglJatuhTempo = $p->tanggal_jatuh_tempo ?? $p->jatuh_tempo ?? null;
            return [
                'tgl_pinjam'  => $p->tanggal_peminjaman,
                'peminjam'    => optional($p->user)->nama_lengkap ?? optional($p->user)->username ?? '-',
                'alat'        => optional($p->alat)->nama_alat ?? '-',
                'jumlah'      => $p->jumlah ?? 1,
                'jatuh_tempo' => $tglJatuhTempo,
                'petugas'     => optional($p->petugas)->username ?? '-',
            ];
        });

        $pengembalianHariIni = $pengembalianRaw->map(function ($k) {
            $terlambat = 0;
            if ($k->peminjaman) {
                $jatuhTempo = $k->peminjaman->tanggal_jatuh_tempo ?? $k->peminjaman->jatuh_tempo ?? null;
                if ($jatuhTempo) {
                    $diff = Carbon::parse($k->tanggal_kembali_aktual)->diffInDays(Carbon::parse($jatuhTempo), false);
                    $terlambat = $diff < 0 ? abs($diff) : 0;
                }
            }
            return [
                'tgl_kembali' => $k->tanggal_kembali_aktual,
                'peminjam'    => optional(optional($k->peminjaman)->user)->nama_lengkap
                                 ?? optional(optional($k->peminjaman)->user)->username
                                 ?? '-',
                'alat'        => optional(optional($k->peminjaman)->alat)->nama_alat ?? '-',
                'kondisi'     => $k->kondisi_alat ?? $k->kondisi ?? '-',
                'terlambat'   => $terlambat,
                'denda'       => $k->total_denda ?? $k->denda ?? 0,
            ];
        });

        $totalPeminjamanHariIni  = $peminjamanHariIni->count();
        $totalPengembalianHariIni = $pengembalianHariIni->count();
        $totalDendaHariIni       = $pengembalianHariIni->sum('denda');

        return view('pages.laporan.cetak', compact(
            'tanggal',
            'peminjamanHariIni',
            'pengembalianHariIni',
            'totalPeminjamanHariIni',
            'totalPengembalianHariIni',
            'totalDendaHariIni'
        ));
    }
}