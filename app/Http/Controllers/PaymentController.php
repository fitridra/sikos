<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;
use App\Models\Member;
use App\Models\Payment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

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

    public function getAmount(Request $request, $member_id)
    {
        $member = Member::with('room.kost')->find($member_id);

        if (!$member || !$member->room || !$member->room->kost) {
            return response()->json(['amount' => 0]);
        }

        $baseAmount = $member->room->kost->amount;

        $duration = $request->input('duration', 'monthly');
        $discount = floatval($request->input('discount', 0));

        switch (strtolower($duration)) {
            case 'yearly':
                $multiplier = 12;
                break;
            case '6months':
                $multiplier = 6;
                break;
            case 'monthly':
            default:
                $multiplier = 1;
                break;
        }

        // Hitung total
        $total = ($baseAmount * $multiplier) - $discount;

        $finalAmount = max(0, round($total));

        return response()->json(['amount' => $finalAmount]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:tb_members,member_id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric',
            'duration' => 'required|in:monthly,6months,yearly',
            'discount' => 'nullable|numeric|min:0',
        ]);

        Payment::create([
            'member_id' => $request->member_id,
            'payment_date' => $request->payment_date,
            'duration' => $request->duration,
            'amount' => $request->amount,
        ]);

        return redirect()->route('payment')->with('success', 'Data has been added successfully');
    }

    public function delete($id)
    {
        Payment::where('payment_id', $id)->delete();
        return redirect()->route('payment')->with('success', 'Data has been deleted successfully.');
    }

    public function exportPaymentExcel(Request $request)
    {
        $query = Payment::with('member.room.kost');

        // Filter Kost
        if ($request->filled('kost_id')) {
            $query->whereHas('member.room.kost', function ($q) use ($request) {
                $q->where('kost_id', $request->kost_id);
            });
        }

        // Filter Bulan & Tahun
        if ($request->filled('filter_month')) {
            $query->whereMonth('payment_date', $request->filter_month);
        }

        if ($request->filled('filter_year')) {
            $query->whereYear('payment_date', $request->filter_year);
        }

        // Filter Nama
        if ($request->filled('cari')) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->cari . '%');
            });
        }

        $data = $query->orderBy('payment_date', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Full Name');
        $sheet->setCellValue('C1', 'Kost Name');
        $sheet->setCellValue('D1', 'Room');
        $sheet->setCellValue('E1', 'Period');
        $sheet->setCellValue('F1', 'Payment Date');
        $sheet->setCellValue('G1', 'Amount');

        // Bold header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Fill data
        $row = 2;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->member->full_name);
            $sheet->setCellValue('C' . $row, $item->member->room->kost->kost_name);
            $sheet->setCellValue('D' . $row, $item->member->room->room_number);

            // Period
            switch ($item->duration) {
                case 'monthly':
                    $period = 'Monthly';
                    break;
                case '6months':
                    $period = '6 Months';
                    break;
                case 'yearly':
                    $period = 'Yearly';
                    break;
                default:
                    $period = ucfirst($item->duration);
            }

            $sheet->setCellValue('E' . $row, $period);
            $sheet->setCellValue('F' . $row, Carbon::parse($item->payment_date)->format('d M Y'));
            $sheet->setCellValue('G' . $row, number_format($item->amount, 0, ',', '.'));

            $row++;
        }

        // Auto width
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $filename = 'data_payment_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
