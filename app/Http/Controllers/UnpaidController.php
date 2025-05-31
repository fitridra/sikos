<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class UnpaidController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $kostId = $request->input('kost_id');

        // Ambil semua member aktif (move_in <= today && (move_out null or >= today))
        $membersRaw = Member::whereDate('move_in_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('move_out_date')
                    ->orWhereDate('move_out_date', '>=', $today);
            })
            ->when($kostId, function ($q) use ($kostId) {
                $q->whereHas('room.kost', function ($sub) use ($kostId) {
                    $sub->where('kost_id', $kostId);
                });
            })
            ->with('room.kost', 'payments')
            ->get();

        // Hitung unpaid secara manual
        $unpaidMembers = collect();

        foreach ($membersRaw as $member) {
            $kost = $member->room->kost;
            $amountPerMonth = $kost->amount ?? 0;

            // Hitung total bulan dari move_in sampai bulan ini
            $bulanMasuk = Carbon::parse($member->move_in_date)->startOfMonth();
            $bulanSekarang = $today->copy()->startOfMonth();
            $totalBulan = $bulanMasuk->diffInMonths($bulanSekarang) + 1;

            // Hitung total pembayaran valid hingga bulan ini
            $totalPaid = $member->payments()
                ->where(function ($q) use ($today) {
                    $q->where('payment_year', '<', $today->year)
                        ->orWhere(function ($sub) use ($today) {
                            $sub->where('payment_year', $today->year)
                                ->where('payment_month', '<=', $today->month);
                        });
                })
                ->sum('amount');

            $totalDue = $amountPerMonth * $totalBulan;
            $totalUnpaid = $totalDue - $totalPaid;

            if ($totalUnpaid > 0) {
                $unpaidMembers->push((object)[
                    'full_name'     => $member->full_name,
                    'room_number'   => $member->room->room_number ?? '-',
                    'kost_name'     => $kost->kost_name ?? '-',
                    'months_unpaid' => $amountPerMonth > 0 ? round($totalUnpaid / $amountPerMonth) : 0,
                    'total_due'     => $totalUnpaid,
                ]);
            }
        }

        // pagination
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $unpaidMembers->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginator = new LengthAwarePaginator(
            $currentItems,
            $unpaidMembers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Total unpaid untuk halaman ini
        $totalUnpaid = $currentItems->sum('total_due');

        // Daftar kost untuk filter
        $allkosts = Kost::select('kost_id', 'kost_name')->get();

        return view('unpaid.index', [
            'members'     => $paginator,
            'allkosts'    => $allkosts,
            'kostId'      => $kostId,
            'totalUnpaid' => $totalUnpaid
        ]);
    }
}
