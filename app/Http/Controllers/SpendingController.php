<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spending;
use App\Models\Kost;

class SpendingController extends Controller
{
    public function index(Request $request)
    {
        $data_spending = Spending::with('kost')
            ->when($request->kost_id, function ($query) use ($request) {
                $query->where('kost_id', $request->kost_id);
            })
            ->when($request->cari, function ($query) use ($request) {
                $query->where('spending_name', 'LIKE', "%{$request->cari}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data_spending->appends($request->only('cari'));

        $allkosts = Kost::select('kost_id', 'kost_name')->get();

        return view('spending.index', compact('data_spending', 'allkosts'));
    }

    public function create(Request $request)
    {

        $validatedData = $request->validate([
            'kost_id'       => 'required',
            'spending_name' => 'required',
            'spending_date' => 'required',
            'amount'        => 'required'
        ]);

        Spending::create($validatedData);

        return redirect()->route('spending')->with('success', 'Data has been added successfully');
    }

    public function edit($id)
    {
        $allkosts = Kost::select('kost_id', 'kost_name')->get();

        $spending = Spending::where('spending_id', $id)->first();
        return view('spending/edit', compact('spending', 'allkosts'));
    }

    public function update(Request $request, $id)
    {

        $spending = Spending::where('spending_id', $id)->first();
        $spending->where('spending_id', $spending->spending_id)
            ->update([
                'kost_id'       => $request->input('kost_id'),
                'spending_name' => $request->input('spending_name'),
                'spending_date' => $request->input('spending_date'),
                'amount'        => $request->input('amount'),
            ]);
        return redirect()->route('spending')->with('success', 'Data has been updated successfully');
    }

    public function delete($id)
    {
        Spending::where('spending_id', $id)->delete();
        return redirect()->route('spending')->with('success', 'Data has been deleted successfully.');
    }
}
