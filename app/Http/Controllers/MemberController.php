<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;
use App\Models\Room;
use App\Models\Member;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $data_member = Member::whereHas('room.kost', function ($query) use ($request) {
            if ($request->kost_id) {
                $query->where('kost_id', $request->kost_id);
            }
        })
            ->when($request->cari, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('full_name', 'like', "%{$request->cari}%")
                        ->orWhereHas('room', function ($q2) use ($request) {
                            $q2->where('room_number', 'like', "%{$request->cari}%");
                        });
                });
            })
            ->with('room.kost')
            ->orderBy('member_id', 'desc')->paginate(10);

        $data_member->appends($request->only(['kost_id', 'cari']));

        $all_kosts = Kost::whereHas('rooms', function ($query) {
            $query->where('status', 0);
        })->get();

        $allkosts = Kost::select('kost_id', 'kost_name')->get();

        return view('member.index', compact('data_member', 'all_kosts', 'allkosts'));
    }

    public function getRoomsByKost(Request $request, $kostId)
    {
        $selectedRoomId = $request->query('selected'); // ambil dari query param ?selected=...

        $rooms = Room::where('kost_id', $kostId)
            ->where(function ($query) use ($selectedRoomId) {
                $query->where('status', 0);

                if ($selectedRoomId) {
                    // Pastikan selectedRoomId adalah integer agar query aman
                    $query->orWhere('room_id', intval($selectedRoomId));
                }
            })
            ->get(['room_id', 'room_number', 'status']);

        return response()->json($rooms);
    }

    public function create(Request $request)
    {

        $request->validate([
            'full_name'     => 'required',
            'address'       => 'nullable',
            'phone'         => 'nullable',
            'kost_id'       => 'required',
            'room_id'       => 'required',
            'move_in_date'  => 'required|date',
        ]);

        $member = new Member();
        $member->room_id = $request->room_id;
        $member->full_name = $request->full_name;
        $member->address = $request->address;
        $member->phone = $request->phone;
        $member->move_in_date = $request->move_in_date;
        $member->save();

        $room = Room::find($request->room_id);
        if ($room) {
            $room->status = 1;
            $room->save();
        }

        return redirect()->route('member')->with('success', 'Data has been added successfully');
    }

    public function edit($id)
    {
        $member = Member::where('member_id', $id)->first();

        $all_kosts = Kost::whereHas('rooms', function ($query) use ($id) {
            $query->where('status', 0)
                ->orWhereHas('members', function ($q) use ($id) {
                    $q->where('member_id', $id);
                });
        })->with(['rooms' => function ($query) use ($id) {
            $query->where('status', 0)
                ->orWhereHas('members', function ($q) use ($id) {
                    $q->where('member_id', $id);
                });
        }])->get();

        $selectedRoomId = $member->room_id;

        return view('member.edit', compact('member', 'all_kosts', 'selectedRoomId'));
    }


    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        $old_room_id = $member->room_id;

        $request->validate([
            'full_name' => 'required',
            'address' => 'nullable',
            'phone' => 'nullable',
            'room_id' => 'required',
            'move_in_date' => 'required|date',
            'move_out_date' => 'nullable|date|after_or_equal:move_in_date'
        ]);

        // Update data member
        $member->full_name = $request->full_name;
        $member->address = $request->address;
        $member->phone = $request->phone;
        $member->room_id = $request->room_id;
        $member->move_in_date = $request->move_in_date;
        $member->move_out_date = $request->move_out_date;
        $member->save();

        // Handle update status room
        if ($old_room_id != $request->room_id) {
            // Kosongkan room lama
            $old_room = Room::find($old_room_id);
            if ($old_room) {
                $old_room->status = 0;
                $old_room->save();
            }

            // Tandai room baru sebagai terisi
            $new_room = Room::find($request->room_id);
            if ($new_room) {
                $new_room->status = 1;
                $new_room->save();
            }
        }

        // Cek apakah member sudah keluar
        if ($member->move_out_date && $member->move_out_date <= today()) {
            $current_room = Room::find($member->room_id);
            if ($current_room) {
                $current_room->status = 0;
                $current_room->save();
            }
        }

        return redirect()->route('member')->with('success', 'Data has been updated successfully');
    }

    public function delete($id)
    {
        $member = Member::findOrFail($id);
        $room = $member->room;

        $member->delete();

        if ($room) {
            $room->status = 0;
            $room->save();
        }
        return redirect()->route('member')->with('success', 'Data has been deleted successfully.');
    }
}
