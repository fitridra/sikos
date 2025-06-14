@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!--  Row 1 -->
        @if (Auth::check() && Auth::user()->name === 'superadmin')
        <div class="row">
            <div class="col-lg">
                <div class="row">
                    <div class="col-lg-4">
                        <!-- Annual Income -->
                        <div class="card">
                            <div class="card-body">
                                <div class="row alig n-items-start">
                                    <div class="col-8">
                                        <h5 class="card-title mb-9 fw-semibold"> Annual Income </h5>
                                        <h5 class="fw-semibold mb-3">Rp {{ number_format($annualIncome, 0, ',', '.') }}</h5>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex justify-content-end">
                                            <div
                                                class="text-white bg-primary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-currency-dollar fs-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <!-- Monthly Earnings -->
                        <div class="card">
                            <div class="card-body">
                                <div class="row alig n-items-start">
                                    <div class="col-8">
                                        <h5 class="card-title mb-9 fw-semibold"> Monthly Earnings </h5>
                                        <h5 class="fw-semibold mb-3">Rp {{ number_format($monthlyEarnings, 0, ',', '.') }}</h5>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex justify-content-end">
                                            <div
                                                class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-currency-dollar fs-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <!-- Unpaid Rent -->
                        <div class="card">
                            <div class="card-body">
                                <div class="row alig n-items-start">
                                    <div class="col-8">
                                        <h5 class="card-title mb-9 fw-semibold"> Unpaid Rent </h5>
                                        <h5 class="fw-semibold mb-3">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</h5>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex justify-content-end">
                                            <div
                                                class="text-white bg-danger rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-currency-dollar fs-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h5 class="card-title fw-semibold">Recent Transactions</h5>
                        </div>
                        <ul class="timeline-widget mb-0 position-relative mb-n5">
                            @forelse ($lastPayments as $payment)
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-time text-dark flex-shrink-0 text-end">
                                        {{ \Carbon\Carbon::parse($payment->updated_at ?? $payment->created_at)->format('H:i') }}
                                    </div>
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-success flex-shrink-0 my-8"></span>
                                        @unless ($loop->last)
                                            <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                        @endunless
                                    </div>
                                    <div class="timeline-desc fs-3 text-dark mt-n1">
                                        <b>{{ $payment->full_name }}</b>
                                        {{ number_format($payment->amount, 0, ',', '.') }}
                                    </div>
                                </li>
                            @empty
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-desc fs-4 text-muted">
                                        No transactions available.
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold mb-4">List of Unpaid</h5>
                        <div class="table-responsive">
                            <table class="table text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Name</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Kost Name</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Status</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Amount</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($unpaidTop5 as $member)
                                        <tr>
                                            <td class="border-bottom-0">
                                                <h6 class="fw-semibold mb-1">{{ $member->full_name }}</h6>
                                            </td>
                                            <td class="border-bottom-0">
                                                <p class="mb-0 fw-normal">{{ $member->kost_name }} -
                                                    {{ $member->room_number }}</p>
                                            </td>
                                            <td class="border-bottom-0">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-danger rounded-3 fw-semibold">
                                                        {{ number_format($member->months_unpaid, 0) }}
                                                        Month
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0 fs-4">
                                                    Rp{{ number_format($member->total_due, 0, ',', '.') }}
                                                </h6>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No unpaid data available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="py-6 px-6 text-center">
            <p class="mb-0 fs-4" style="color:white">Design and Developed by AdminMart.com Distributed by ThemeWagon</p>
        </div>
    </div>
@endsection
