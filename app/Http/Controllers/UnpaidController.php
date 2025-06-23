<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UnpaidController extends Controller
{
    public function index(Request $request)
    {
        $today = now();
        $kostId = $request->input('kost_id');

        $membersRaw = Member::whereDate('move_out_date', '<', $today)
            ->when($kostId, function ($q) use ($kostId) {
                $q->whereHas('room.kost', function ($sub) use ($kostId) {
                    $sub->where('kost_id', $kostId);
                });
            })
            ->with(['room.kost'])
            ->get();

        $unpaidMembers = collect();

        foreach ($membersRaw as $member) {
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

        // Pagination
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $unpaidMembers->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginator = new LengthAwarePaginator(
            $currentItems,
            $unpaidMembers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $totalUnpaid = $currentItems->sum('total_due');
        $allkosts = Kost::select('kost_id', 'kost_name')->get();

        return view('unpaid.index', [
            'members'     => $paginator,
            'allkosts'    => $allkosts,
            'kostId'      => $kostId,
            'totalUnpaid' => $totalUnpaid
        ]);
    }

    public function exportExcel(Request $request)
    {
        $today = now();
        $kostId = $request->input('kost_id');

        $membersRaw = Member::whereDate('move_out_date', '<', $today)
            ->when($kostId, function ($q) use ($kostId) {
                $q->whereHas('room.kost', function ($sub) use ($kostId) {
                    $sub->where('kost_id', $kostId);
                });
            })
            ->with(['room.kost'])
            ->get();

        $unpaidMembers = collect();

        foreach ($membersRaw as $member) {
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

        // === Export to Excel ===
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Full Name');
        $sheet->setCellValue('C1', 'Kost Name');
        $sheet->setCellValue('D1', 'Room');
        $sheet->setCellValue('E1', 'Months Unpaid');
        $sheet->setCellValue('F1', 'Amount');

        // Bold header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        // Isi data
        $row = 2;
        $no = 1;
        foreach ($unpaidMembers as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->full_name);
            $sheet->setCellValue('C' . $row, $item->kost_name);
            $sheet->setCellValue('D' . $row, $item->room_number);
            $sheet->setCellValue('E' . $row, $item->months_unpaid);
            $sheet->setCellValue('F' . $row, number_format($item->total_due, 0, ',', '.'));
            $row++;
        }

        // Auto width
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output file
        $filename = 'data_unpaid_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
