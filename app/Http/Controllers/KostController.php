<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;

class KostController extends Controller
{
    public function index()
    {
        $data_kost = Kost::with('rooms')->get();

        $data_kost = $data_kost->map(function ($kost) {
            $kost->total_rooms = $kost->rooms->count();
            $kost->total_available = $kost->rooms->where('status', 0)->count(); // status 0 = available
            $kost->total_filled = $kost->rooms->where('status', 1)->count();  // status 1 = filled
            return $kost;
        });

        return view('kost.index', compact('data_kost'));
    }

    public function show($id)
    {
        $kost = Kost::with('rooms')->findOrFail($id);

        return view('kost.show', compact('kost'));
    }

    public function create(Request $request)
    {

        $validatedData = $request->validate([
            'kost_name' => 'required',
            'address'   => 'required',
            'amount'    => 'required'
        ]);

        Kost::create($validatedData);

        return redirect()->route('kost')->with('success', 'Data has been added successfully');
    }

    public function edit($id)
    {
        $kost = Kost::where('kost_id', $id)->first();
        return view('kost/edit', compact('kost'));
    }

    public function update(Request $request, $id)
    {

        $kost = Kost::where('kost_id', $id)->first();
        $kost->where('kost_id', $kost->kost_id)
            ->update([
                'kost_name' => $request->input('kost_name'),
                'address'   => $request->input('address'),
                'amount'    => $request->input('amount'),
            ]);
        return redirect()->route('kost')->with('success', 'Data has been updated successfully');
    }
}
