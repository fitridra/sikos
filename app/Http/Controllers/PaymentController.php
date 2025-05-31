<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;
use App\Models\Member;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $data_payment = Payment::whereHas('member.room.kost', function ($query) use ($request) {
            if ($request->kost_id) {
                $query->where('kost_id', $request->kost_id);
            }
        })
            ->when($request->cari, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->whereHas('member', function ($q1) use ($request) {
                        $q1->where('full_name', 'like', "%{$request->cari}%")
                            ->orWhereHas('room', function ($q2) use ($request) {
                                $q2->where('room_number', 'like', "%{$request->cari}%");
                            });
                    });
                });
            })
            ->when($request->filter_year, function ($query) use ($request) {
                $query->whereYear('payment_date', $request->filter_year);
            })
            ->when($request->filter_month, function ($query) use ($request) {
                $query->whereMonth('payment_date', $request->filter_month);
            })
            ->with(['member.room.kost'])
            ->orderBy('payment_date', 'desc')
            ->paginate(10);

        $data_payment->appends($request->only(['kost_id', 'cari', 'filter_year', 'filter_month']));

        $all_members = Member::select('member_id', 'full_name')
            ->where(function ($query) {
                $query->whereNull('move_out_date')
                    ->orWhereDate('move_out_date', '>', now());
            })->get();

        $allkosts = Kost::select('kost_id', 'kost_name')->get();

        return view('payment.index', compact('data_payment', 'all_members', 'allkosts'));
    }

    public function getAmount($member_id)
    {
        $member = Member::with('room.kost')->find($member_id);

        if (!$member || !$member->room || !$member->room->kost) {
            return response()->json(['amount' => 0]);
        }

        return response()->json(['amount' => $member->room->kost->amount]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:tb_members,member_id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric',
            'payment_month' => 'required|integer|between:1,12',
            'payment_year' => 'required|integer',
        ]);

        // Cek apakah pembayaran bulan & tahun ini sudah ada
        $exists = Payment::where('member_id', $request->member_id)
            ->where('payment_month', $request->payment_month)
            ->where('payment_year', $request->payment_year)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Payment for this month and year has already been made.');
        }

        Payment::create([
            'member_id' => $request->member_id,
            'payment_date' => $request->payment_date,
            'payment_month' => $request->payment_month,
            'payment_year' => $request->payment_year,
            'amount' => $request->amount,
        ]);

        return redirect()->route('payment')->with('success', 'Data has been added successfully');
    }

    public function delete($id)
    {
        Payment::where('payment_id', $id)->delete();
        return redirect()->route('payment')->with('success', 'Data has been deleted successfully.');
    }
}
