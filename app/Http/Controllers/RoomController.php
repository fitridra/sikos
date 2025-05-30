<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Kost;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $data_room = Room::with('kost')
            ->when($request->kost_id, function ($query) use ($request) {
                $query->where('kost_id', $request->kost_id);
            })
            ->when($request->cari, function ($query) use ($request) {
                $query->where('room_number', 'LIKE', "%{$request->cari}%");
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->paginate(10);

        $data_room->appends($request->only(['kost_id', 'cari', 'status']));

        $all_kosts = Kost::select('kost_id', 'kost_name')->get();

        return view('room.index', compact('data_room', 'all_kosts'));
    }

    public function create(Request $request)
    {

        $validatedData = $request->validate([
            'kost_id'       => 'required',
            'room_number'   => 'required',
            'status'        => 'required'
        ]);

        Room::create($validatedData);

        return redirect()->route('room')->with('success', 'Data has been added successfully');
    }

    public function edit($id)
    {
        $room = Room::where('room_id', $id)->first();
        $all_kosts = Kost::select('kost_id', 'kost_name')->get();
        return view('room/edit', compact('room', 'all_kosts'));
    }

    public function update(Request $request, $id)
    {

        $room = Room::where('room_id', $id)->first();
        $room->where('room_id', $room->room_id)
            ->update([
                'kost_id'       => $request->input('kost_id'),
                'room_number'   => $request->input('room_number'),
                'status'        => $request->input('status'),
            ]);
        return redirect()->route('room')->with('success', 'Data has been updated successfully');
    }

    public function delete($id)
    {
        Room::where('room_id', $id)->delete();
        return redirect()->route('room')->with('success', 'Data has been deleted successfully.');
    }
}
