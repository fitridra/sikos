<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Member;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $bulanIni = $today->copy()->startOfMonth();

        // 1. Jumlah Pendapatan Bulan Ini (berdasarkan payment_date)
        $monthlyEarnings = Payment::whereMonth('payment_date', $today->month)
            ->whereYear('payment_date', $today->year)
            ->sum('amount');

        // 2. Jumlah Pendapatan Tahun Ini
        $annualIncome = Payment::whereYear('payment_date', $today->year)
            ->sum('amount');

        // Ambil semua member aktif
        $members = Member::whereDate('move_out_date', '<', $today)
            ->with(['room.kost'])
            ->get();

        $totalUnpaid = 0;
        $unpaidMembers = collect();

        foreach ($members as $member) {
            $kost = $member->room->kost ?? null;
            if (!$kost || !$member->move_out_date) continue;

            $moveOut = Carbon::parse($member->move_out_date);
            if ($today->lte($moveOut)) continue;

            $moveOutMonth = $moveOut->copy()->startOfMonth();
            $nowMonth = $today->copy()->startOfMonth();

            $monthsUnpaid = $moveOutMonth->diffInMonths($nowMonth);
            if ($today->gt($moveOut)) {
                $monthsUnpaid += 1;
            }

            // Buat daftar bulan unpaid
            $unpaidMonths = [];
            $loop = $moveOutMonth->copy()->addMonth();
            for ($i = 0; $i < $monthsUnpaid; $i++) {
                $unpaidMonths[] = $loop->copy()->addMonths($i)->format('Y-m');
            }

            $totalUnpaid = $monthsUnpaid * ($kost->amount ?? 0);

            $unpaidMembers->push((object)[
                'full_name'     => $member->full_name,
                'room_number'   => $member->room->room_number ?? '-',
                'kost_name'     => $kost->kost_name ?? '-',
                'months_unpaid' => $monthsUnpaid,
                'total_due'     => $totalUnpaid,
                'unpaid_months' => $unpaidMonths,
            ]);
        }

        // 3. Jumlah Unpaid
        $UnpaidDasboard = collect($unpaidMembers)->sum('total_due');

        // 4. 5 Teratas unpaid berdasarkan total_due terbesar
        $unpaidTop5 = collect($unpaidMembers)->sortByDesc('total_due')->take(5);

        // 5. 5 Terakhir melakukan payment
        $lastPayments = Payment::with('member.room.kost')
            ->orderByDesc('payment_date')
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return (object)[
                    'full_name'   => $payment->member->full_name,
                    'room_number' => $payment->member->room->room_number ?? '-',
                    'kost_name'   => $payment->member->room->kost->kost_name ?? '-',
                    'payment_date'=> $payment->payment_date,
                    'amount'      => $payment->amount,
                    'created_at'  => $payment->created_at,
                    'updated_at'  => $payment->updated_at,
                ];
            });

        return view('dashboard.index', compact('UnpaidDasboard','monthlyEarnings', 'annualIncome', 'totalUnpaid', 'unpaidTop5', 'lastPayments'));
    }
}
