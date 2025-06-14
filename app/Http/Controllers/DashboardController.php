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
        $members = Member::whereDate('move_in_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('move_out_date')
                    ->orWhereDate('move_out_date', '>=', $today);
            })
            ->with('room.kost', 'payments')
            ->get();

        $totalUnpaid = 0;
        $unpaidMembers = [];

        foreach ($members as $member) {
            $kost = $member->room->kost;
            if (!$kost) continue;

            $amountPerMonth = $kost->amount ?? 0;
            $moveIn = Carbon::parse($member->move_in_date);
            $moveInDay = $moveIn->day;
            $bulanMasuk = $moveIn->copy()->startOfMonth();

            // Tentukan bulan terakhir yang jatuh tempo
            if ($today->day < $moveInDay) {
                $bulanTerakhir = $bulanIni->copy()->subMonth();
            } else {
                $bulanTerakhir = $bulanIni->copy();
            }

            // Buat list bulan yang seharusnya dibayar
            $expectedMonths = collect();
            $loop = $bulanMasuk->copy();
            while ($loop <= $bulanTerakhir) {
                $expectedMonths->push($loop->format('Y-m'));
                $loop->addMonth();
            }

            // Ambil bulan-bulan yang sudah dibayar berdasarkan payment_date dan durasi
            $paidMonths = collect();
            foreach ($member->payments as $payment) {
                $start = Carbon::parse($payment->payment_date)->startOfMonth();

                if ($start > $bulanTerakhir) continue;

                $months = 0;
                if ($payment->duration === 'monthly') $months = 1;
                elseif ($payment->duration === '6months') $months = 6;
                elseif ($payment->duration === 'yearly') $months = 12;

                for ($i = 0; $i < $months; $i++) {
                    $month = $start->copy()->addMonths($i)->format('Y-m');
                    if ($month <= $bulanTerakhir->format('Y-m')) {
                        $paidMonths->push($month);
                    }
                }
            }

            $unpaidMonths = $expectedMonths->diff($paidMonths);
            $totalUnpaidMember = $unpaidMonths->count() * $amountPerMonth;

            if ($totalUnpaidMember > 0) {
                $totalUnpaid += $totalUnpaidMember;

                $unpaidMembers[] = (object)[
                    'full_name'     => $member->full_name,
                    'room_number'   => $member->room->room_number ?? '-',
                    'kost_name'     => $kost->kost_name ?? '-',
                    'months_unpaid' => $unpaidMonths->count(),
                    'total_due'     => $totalUnpaidMember,
                ];
            }
        }

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

        return view('dashboard.index', compact('monthlyEarnings', 'annualIncome', 'totalUnpaid', 'unpaidTop5', 'lastPayments'));
    }
}
