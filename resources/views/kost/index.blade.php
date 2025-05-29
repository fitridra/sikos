@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Data Kosts</h5>
                    <a href="#" class="btn btn-primary"><i class="ti ti-plus"></i>&nbsp; Add Kost</a>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 align-middle">
                        <thead class="text-dark fs-4">
                            <tr>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">No</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Name</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Address</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Available</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Filled</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Total Rooms</h6>
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
                            @foreach ($data_kost as $kost)
                                <tr>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0 text-center">{{ $no++ }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-1">{{ $kost->kost_name }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="mb-0 fw-normal">{{ $kost->address }}</p>
                                    </td>
                                    {{-- <td class="border-bottom-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary rounded-3 fw-semibold">Low</span>
                                        </div>
                                    </td> --}}
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0 ml-5 fs-4 text-center">{{ $kost->total_available }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0 ml-5 fs-4 text-center">{{ $kost->total_filled }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0 ml-5 fs-4 text-center">{{ $kost->total_rooms }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0 fs-4">{{ $kost->amount }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <small>
                                            <a href="#" type="button" class="btn btn-secondary">
                                                <i class="ti ti-alert-circle"></i>
                                            </a>
                                            <a href="#" type="button" class="btn btn-warning">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a href="#" type="button" class="btn btn-danger">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
