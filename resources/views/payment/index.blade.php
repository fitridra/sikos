@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title fw-semibold mb-0">Data Payment</h5>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="ti ti-plus"></i>&nbsp; Add Payment</a>
                </div>
                <form method="GET" action="{{ url()->current() }}">
                    <div class="row g-3 mb-3 align-items-center">
                        <div class="col-auto">
                            <a href="{{ url()->current() }}" class="btn btn-outline-dark" title="Reset Filter">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <select name="kost_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Kost</option>
                                @foreach ($allkosts as $kost)
                                    <option value="{{ $kost->kost_id }}"
                                        {{ request('kost_id') == $kost->kost_id ? 'selected' : '' }}>
                                        {{ $kost->kost_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Bulan -->
                        <div class="col-6 col-md-2">
                            <select name="filter_month" class="form-select" onchange="this.form.submit()">
                                <option value="">Bulan</option>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}"
                                        {{ request('filter_month') == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Tahun -->
                        <div class="col-6 col-md-2">
                            <select name="filter_year" class="form-select" onchange="this.form.submit()">
                                <option value="">Tahun</option>
                                @foreach (range(date('Y'), date('Y') - 5) as $year)
                                    <option value="{{ $year }}"
                                        {{ request('filter_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="input-group">
                                <input name="cari" type="text" class="form-control" placeholder="Search..."
                                    value="{{ request('cari') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="ti ti-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 align-middle">
                        <thead class="text-dark fs-4">
                            <tr>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">No</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Full Name</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Kost Name</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Room</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Period</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Payment Date</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Amount</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0 text-center">Action</h6>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($data_payment as $payment)
                                <tr>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">&nbsp;{{ $no++ }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-semibold mb-0">{{ $payment->member->full_name }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ $payment->member->room->kost->kost_name }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ $payment->member->room->room_number }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">
                                            {{ \Carbon\Carbon::create($payment->payment_year, $payment->payment_month, 1)->format('M Y') }}
                                        </p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                        </p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        <small>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#confirmDeleteModal{{ $payment->payment_id }}">
                                                <i class="ti ti-trash"></i>
                                            </button>

                                            <!-- Modal Delete-->
                                            <div class="modal fade" id="confirmDeleteModal{{ $payment->payment_id }}"
                                                tabindex="-1" aria-labelledby="deleteLabel{{ $payment->payment_id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="deleteLabel{{ $payment->payment_id }}">
                                                                Delete Confirmation</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete the payment
                                                            <strong>{{ $payment->member->full_name }} -
                                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            @method('delete')
                                                            @csrf
                                                            <a href="{{ route('payment.delete', $payment->payment_id) }}"
                                                                class="btn btn-danger">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pagging text-center">
                <nav>
                    <ul class="pagination justify-content-center">
                        {{ $data_payment->links() }}
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Add-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('payment.create') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="member_id" class="form-label">Full Name</label>
                                    <select name="member_id" id="member_id" class="form-select" required>
                                        <option value="" disabled selected>-- Select Member --</option>
                                        @foreach ($all_members as $member)
                                            <option value="{{ $member->member_id }}"
                                                {{ old('member_id') == $member->member_id ? 'selected' : '' }}>
                                                {{ $member->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('member_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="payment_month" class="form-label">Payment Month</label>
                                    <select name="payment_month" id="payment_month" class="form-select" required>
                                        @foreach (range(1, 12) as $month)
                                            <option value="{{ $month }}"
                                                {{ old('payment_month') == $month ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_month')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="payment_year" class="form-label">Payment Year</label>
                                    <select name="payment_year" id="payment_year" class="form-select" required>
                                        @foreach (range(date('Y'), date('Y') + 2) as $year)
                                            <option value="{{ $year }}"
                                                {{ old('payment_year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_year')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Payment Date</label>
                                    <input type="date" name="payment_date" id="payment_date" class="form-control"
                                        value="{{ old('payment_date') }}" required>
                                    @error('payment_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputamount" class="form-label">Amount</label>
                                    <input type="text" class="form-control" id="amount_display" readonly>
                                    <input type="hidden" name="amount" id="amount">
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts_content')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const memberSelect = document.querySelector('select[name="member_id"]');
            const amountInput = document.getElementById('amount');
            const amountDisplay = document.getElementById('amount_display');

            memberSelect.addEventListener('change', function() {
                const memberId = this.value;
                if (memberId) {
                    fetch(`/get-amount/${memberId}`)
                        .then(response => response.json())
                        .then(data => {
                            const amount = data.amount || 0;
                            amountInput.value = amount;
                            amountDisplay.value = new Intl.NumberFormat('id-ID').format(amount);
                        });
                }
            });
        });
    </script>
@endsection
