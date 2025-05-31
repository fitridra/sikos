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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title fw-semibold mb-0">Data Payment</h5>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="ti ti-plus"></i>&nbsp; Add Payment</a>
                </div>
                <form method="GET" action="{{ url()->current() }}">
                    <div class="row g-3 mb-3 align-items-center">
                        <div class="col-auto">
                            <a href="{{ url()->current() }}" class="btn btn-outline-dark">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <select name="kost_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Filter by Kost --</option>
                                @foreach ($allkosts as $kost)
                                    <option value="{{ $kost->kost_id }}"
                                        {{ request('kost_id') == $kost->kost_id ? 'selected' : '' }}>
                                        {{ $kost->kost_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-5">
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
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
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
                                    <label for="exampleInputname1" class="form-label">Full Name</label>
                                    <select name="member_id" class="form-select" required>
                                        <option value="" disabled selected>-- Full Name --</option>
                                        @foreach ($all_members as $member)
                                            <option value="{{ $member->member_id }}">{{ $member->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputpaymentdate1" class="form-label">Payment Date</label>
                                    <input type="date" name="payment_date" class="form-control"
                                        id="exampleInputpaymentdate1" aria-describedby="paymentdateHelp" required>
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
