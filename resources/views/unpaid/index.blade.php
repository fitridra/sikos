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
                    <h5 class="card-title fw-semibold mb-0">Data Unpaid</h5>
                </div>
                <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end mb-4">
                    <div class="row g-3 mb-3">
                        <div class="col-auto col-sm-2 col-md-1 d-flex align-items-center">
                            <a href="{{ url()->current() }}" class="btn btn-outline-dark w-100">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                        <div class="col-12 col-sm-5 col-md-3">
                            <select name="kost_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- All Kosts --</option>
                                @foreach ($allkosts as $kost)
                                    <option value="{{ $kost->kost_id }}"
                                        {{ $kost->kost_id == request('kost_id') ? 'selected' : '' }}>
                                        {{ $kost->kost_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if (Auth::check() && Auth::user()->name === 'superadmin')
                    <div class="col-12 col-sm-4 col-md-3">
                        <div><strong>Total Unpaid:</strong> Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
                    </div>
                    @endif
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
                                    <h6 class="fw-semibold mb-0">Room</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Kost Name</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Months Unpaid</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Amount</h6>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @forelse ($members as $item)
                                <tr>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">&nbsp;{{ $no++ }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-semibold mb-0">{{ $item->full_name }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ $item->room_number }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ $item->kost_name }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-danger rounded-3 fw-semibold">
                                                {{ $item->months_unpaid }} Months
                                            </span>
                                        </div>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ number_format($item->total_due, 0, ',', '.') }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">All members are paid.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pagging text-center">
                    <nav>
                        <ul class="pagination justify-content-center">
                            {{ $members->links() }}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    @endsection
