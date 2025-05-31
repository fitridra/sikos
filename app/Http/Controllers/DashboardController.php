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

        // 1. Jumlah Pendapatan Bulan Ini
        $monthlyEarnings = Payment::where('payment_year', $today->year)
            ->where('payment_month', $today->month)
            ->sum('amount');

        // 2. Jumlah Pendapatan Tahun Ini
        $annualIncome = Payment::where('payment_year', $today->year)
            ->sum('amount');

        // Ambil semua member aktif (move_in <= today && (move_out null or >= today))
        $members = Member::whereDate('move_in_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('move_out_date')
                    ->orWhereDate('move_out_date', '>=', $today);
            })
            ->with('room.kost')
            ->get();

        // Hitung total unpaid seluruh periode (bulan masuk sampai bulan ini)
        $totalUnpaid = 0;

        $unpaidMembers = [];

        foreach ($members as $member) {
            $kost = $member->room->kost;
            $amountPerMonth = $kost->amount ?? 0;

            // Hitung jumlah bulan dari move_in_date sampai bulan ini (termasuk bulan ini)
            $bulanMasuk = Carbon::parse($member->move_in_date)->startOfMonth();
            $bulanSekarang = $today->copy()->startOfMonth();
            $totalBulan = $bulanMasuk->diffInMonths($bulanSekarang) + 1;

            // Hitung total pembayaran member sampai sekarang
            $totalPaid = $member->payments()
                ->where(function ($q) use ($today) {
                    // Pembayaran hanya sampai bulan dan tahun sekarang (biar nggak ngitung ke depan)
                    $q->where(function ($sub) use ($today) {
                        $sub->where('payment_year', '<', $today->year)
                            ->orWhere(function ($sub2) use ($today) {
                                $sub2->where('payment_year', $today->year)
                                    ->where('payment_month', '<=', $today->month);
                            });
                    });
                })
                ->sum('amount');

            // Total biaya yang harus dibayar sampai bulan ini
            $totalDue = $amountPerMonth * $totalBulan;

            // Hitung tunggakan
            $totalUnpaidMember = $totalDue - $totalPaid;

            if ($totalUnpaidMember > 0) {
                $totalUnpaid += $totalUnpaidMember;

                $unpaidMembers[] = (object) [
                    'full_name' => $member->full_name,
                    'room_number' => $member->room->room_number ?? '-',
                    'kost_name' => $kost->kost_name ?? '-',
                    'months_unpaid' => $totalUnpaidMember > 0 ? $totalUnpaidMember / $amountPerMonth : 0,
                    'total_due' => $totalUnpaidMember,
                ];
            }
        }

        // 4. 5 Teratas unpaid berdasarkan total_due terbesar
        $unpaidTop5 = collect($unpaidMembers)->sortByDesc('total_due')->take(5);

        // 5. 5 Teratas yang terakhir melakukan payment
        $lastPayments = Payment::with('member.room.kost')
            ->orderByDesc('payment_date')
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return (object)[
                    'full_name' => $payment->member->full_name,
                    'room_number' => $payment->member->room->room_number ?? '-',
                    'kost_name' => $payment->member->room->kost->kost_name ?? '-',
                    'payment_date' => $payment->payment_date,
                    'amount' => $payment->amount,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                ];
            });

        return view('dashboard.index', compact('monthlyEarnings','annualIncome','totalUnpaid','unpaidTop5','lastPayments'));
    }
}
