<?php

namespace App\Http\Controllers;

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
}
