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
                    @if (Auth::check() && Auth::user()->name === 'superadmin')
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="ti ti-plus"></i>&nbsp; Add Payment</a>
                    @endif
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
                                @if (Auth::check() && Auth::user()->name === 'superadmin')
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0 text-center">Action</h6>
                                </th>
                                @endif
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
                                            @switch($payment->duration)
                                                @case('monthly')
                                                    Monthly
                                                @break

                                                @case('6months')
                                                    6 Months
                                                @break

                                                @case('yearly')
                                                    Yearly
                                                @break

                                                @default
                                                    {{ ucfirst($payment->duration) }}
                                            @endswitch
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
                                    @if (Auth::check() && Auth::user()->name === 'superadmin')
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
                                    @endif
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
                                    <label for="payment_date" class="form-label">Payment Date</label>
                                    <input type="date" name="payment_date" id="payment_date" class="form-control"
                                        value="{{ old('payment_date') }}" required>
                                    @error('payment_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="duration" class="form-label">Period</label>
                                    <select name="duration" id="duration" class="form-select" required>
                                        <option value="monthly" {{ old('duration') == 'monthly' ? 'selected' : '' }}>
                                            Monthly</option>
                                        <option value="6months" {{ old('duration') == '6months' ? 'selected' : '' }}>6
                                            Months</option>
                                        <option value="yearly" {{ old('duration') == 'yearly' ? 'selected' : '' }}>Yearly
                                        </option>
                                    </select>
                                    @error('duration')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="discount" class="form-label">Discount (Rp)</label>
                                    <input type="number" name="discount" id="discount" class="form-control"
                                        value="0" min="0">
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
            const durationSelect = document.getElementById('duration');
            const discountInput = document.getElementById('discount');
            const amountInput = document.getElementById('amount');
            const amountDisplay = document.getElementById('amount_display');

            function updateAmount() {
                const memberId = memberSelect.value;
                const duration = durationSelect.value;
                const discount = discountInput.value || 0;

                if (memberId) {
                    fetch(`/get-amount/${memberId}?duration=${duration}&discount=${discount}`)
                        .then(response => response.json())
                        .then(data => {
                            const amount = data.amount || 0;
                            amountInput.value = amount;
                            amountDisplay.value = new Intl.NumberFormat('id-ID').format(amount);
                        })
                        .catch(error => {
                            console.error('Error fetching amount:', error);
                        });
                }
            }

            memberSelect.addEventListener('change', updateAmount);
            durationSelect.addEventListener('change', updateAmount);
            discountInput.addEventListener('input', updateAmount);
        });
    </script>
@endsection
