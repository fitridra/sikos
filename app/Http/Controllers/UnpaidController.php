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

        $membersRaw = Member::whereDate('move_in_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('move_out_date')
                    ->orWhereDate('move_out_date', '>=', $today);
            })
            ->when($kostId, function ($q) use ($kostId) {
                $q->whereHas('room.kost', function ($sub) use ($kostId) {
                    $sub->where('kost_id', $kostId);
                });
            })
            ->with(['room.kost', 'payments'])
            ->get();

        $unpaidMembers = collect();

        foreach ($membersRaw as $member) {
            $kost = $member->room->kost ?? null;
            if (!$kost) continue;

            $amountPerMonth = $kost->amount ?? 0;
            $moveIn = Carbon::parse($member->move_in_date);
            $moveInDay = $moveIn->day;
            $moveInMonth = $moveIn->copy()->startOfMonth();

            // Hitung bulan terakhir yang sudah jatuh tempo untuk member ini
            $currentMonth = $today->copy()->startOfMonth();
            if ($today->day < $moveInDay) {
                $lastDueMonth = $currentMonth->subMonth(); // belum jatuh tempo bulan ini
            } else {
                $lastDueMonth = $currentMonth;
            }

            // Buat daftar bulan yang seharusnya dibayar
            $expectedMonths = collect();
            $loop = $moveInMonth->copy();
            while ($loop <= $lastDueMonth) {
                $expectedMonths->push($loop->format('Y-m'));
                $loop->addMonth();
            }

            // Buat daftar bulan yang sudah dibayar
            $paidMonths = collect();
            foreach ($member->payments as $payment) {
                $start = Carbon::parse($payment->payment_date)->startOfMonth();

                if ($payment->duration === 'monthly') {
                    $months = 1;
                } elseif ($payment->duration === '6months') {
                    $months = 6;
                } elseif ($payment->duration === 'yearly') {
                    $months = 12;
                } else {
                    $months = 0;
                }

                for ($i = 0; $i < $months; $i++) {
                    $paidMonths->push($start->copy()->addMonths($i)->format('Y-m'));
                }
            }

            // Bandingkan expected dengan paid
            $unpaidMonths = $expectedMonths->diff($paidMonths);
            $totalUnpaid = $unpaidMonths->count() * $amountPerMonth;

            if ($totalUnpaid > 0) {
                $unpaidMembers->push((object)[
                    'full_name'     => $member->full_name,
                    'room_number'   => $member->room->room_number ?? '-',
                    'kost_name'     => $kost->kost_name ?? '-',
                    'months_unpaid' => $unpaidMonths->count(),
                    'total_due'     => $totalUnpaid,
                    'unpaid_months' => $unpaidMonths->toArray(),
                ]);
            }
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

        $membersRaw = Member::whereDate('move_in_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('move_out_date')
                    ->orWhereDate('move_out_date', '>=', $today);
            })
            ->when($kostId, function ($q) use ($kostId) {
                $q->whereHas('room.kost', function ($sub) use ($kostId) {
                    $sub->where('kost_id', $kostId);
                });
            })
            ->with(['room.kost', 'payments'])
            ->get();

        $unpaidMembers = collect();

        foreach ($membersRaw as $member) {
            $kost = $member->room->kost ?? null;
            if (!$kost) continue;

            $amountPerMonth = $kost->amount ?? 0;
            $moveIn = Carbon::parse($member->move_in_date);
            $moveInDay = $moveIn->day;
            $moveInMonth = $moveIn->copy()->startOfMonth();
            $currentMonth = $today->copy()->startOfMonth();

            $lastDueMonth = ($today->day < $moveInDay) ? $currentMonth->subMonth() : $currentMonth;

            $expectedMonths = collect();
            $loop = $moveInMonth->copy();
            while ($loop <= $lastDueMonth) {
                $expectedMonths->push($loop->format('Y-m'));
                $loop->addMonth();
            }

            $paidMonths = collect();
            foreach ($member->payments as $payment) {
                $start = Carbon::parse($payment->payment_date)->startOfMonth();
                $months = match ($payment->duration) {
                    'monthly' => 1,
                    '6months' => 6,
                    'yearly'  => 12,
                    default   => 0,
                };
                for ($i = 0; $i < $months; $i++) {
                    $paidMonths->push($start->copy()->addMonths($i)->format('Y-m'));
                }
            }

            $unpaidMonths = $expectedMonths->diff($paidMonths);
            $totalUnpaid = $unpaidMonths->count() * $amountPerMonth;

            if ($totalUnpaid > 0) {
                $unpaidMembers->push([
                    'full_name'   => $member->full_name,
                    'kost_name'   => $kost->kost_name ?? '-',
                    'room_number' => $member->room->room_number ?? '-',
                    'months'      => $unpaidMonths->count(),
                    'amount'      => $totalUnpaid,
                ]);
            }
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
            $sheet->setCellValue('B' . $row, $item['full_name']);
            $sheet->setCellValue('C' . $row, $item['kost_name']);
            $sheet->setCellValue('D' . $row, $item['room_number']);
            $sheet->setCellValue('E' . $row, $item['months']);
            $sheet->setCellValue('F' . $row, number_format($item['amount'], 0, ',', '.'));
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
