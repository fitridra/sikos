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
            ->with(['member.room.kost'])
            ->orderBy('payment_id', 'desc')
            ->paginate(10);

        $data_payment->appends($request->only(['kost_id', 'cari']));

        $all_members = Member::select('member_id', 'full_name')->get();

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

        $validatedData = $request->validate([
            'member_id'     => 'required',
            'payment_date'  => 'required|date',
            'amount'        => 'required'
        ]);

        Payment::create($validatedData);

        return redirect()->route('payment')->with('success', 'Data has been added successfully');
    }

    public function delete($id)
    {
        Payment::where('payment_id', $id)->delete();
        return redirect()->route('payment')->with('success', 'Data has been deleted successfully.');
    }
}
