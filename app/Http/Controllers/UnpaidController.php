<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;
use App\Models\Member;

class UnpaidController extends Controller
{
    public function index(Request $request)
    {
        $today = \Carbon\Carbon::today();
        $kostId = $request->input('kost_id');

        // Query members yang unpaid (belum bayar bulan ini) dan masih aktif
        $membersQuery = Member::whereDate('move_in_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('move_out_date')
                    ->orWhereDate('move_out_date', '>=', $today);
            })
            ->whereDoesntHave('payments', function ($q) use ($today) {
                $q->where('payment_month', $today->month)
                  ->where('payment_year', $today->year);
            })
            ->when($kostId, function ($q) use ($kostId) {
                $q->whereHas('room.kost', function ($sub) use ($kostId) {
                    $sub->where('kost_id', $kostId);
                });
            })
            ->with('room.kost');

        // Pagination 10 data per halaman
        $perPage = 10;
        $paginator = $membersQuery->paginate($perPage);

        // Map data untuk menghitung months_unpaid & total_due
        $items = $paginator->getCollection()->map(function ($member) use ($today) {
            $kost = $member->room->kost;

            $tanggalAwal = \Carbon\Carbon::create($member->move_in_date)->startOfMonth();
            $targetDate = $today->copy()->startOfMonth();
            $diffInMonths = $tanggalAwal->diffInMonths($targetDate);

            return (object)[
                'full_name'     => $member->full_name,
                'room_number'   => $member->room->room_number ?? '-',
                'kost_name'     => $kost->kost_name ?? '-',
                'months_unpaid' => $diffInMonths > 0 ? $diffInMonths : 1,
                'total_due'     => ($kost->amount ?? 0) * ($diffInMonths > 0 ? $diffInMonths : 1),
            ];
        });

        // Update collection di paginator agar blade dapat data yang sudah dimapping
        $paginator->setCollection($items);

        // Hitung total unpaid dari data pada halaman ini
        $totalUnpaid = $items->sum('total_due');

        // Ambil daftar kost untuk filter dropdown
        $allkosts = Kost::select('kost_id', 'kost_name')->get();

        // Pastikan filter kost ikut ke link pagination
        $paginator->appends($request->only('kost_id'));

        return view('unpaid.index', [
            'members'    => $paginator,
            'allkosts'   => $allkosts,
            'kostId'     => $kostId,
            'totalUnpaid' => $totalUnpaid,
        ]);
    }
}
