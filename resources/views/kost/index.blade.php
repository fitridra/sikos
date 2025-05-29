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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Data Kost</h5>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="ti ti-plus"></i>&nbsp; Add Kost</a>
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
                                            <a href="{{ route('kost.detail', $kost->kost_id) }}" type="button"
                                                class="btn btn-secondary">
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

    <!-- Modal -->
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
                            <form action="{{ route('kost.create') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="exampleInputname1" class="form-label">Name</label>
                                    <input type="text" name="kost_name" class="form-control" id="exampleInputName1"
                                        aria-describedby="nameHelp" placeholder="Kost Harapan Indah" required>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputaddress1" class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" id="exampleInputAddress1"
                                        aria-describedby="addressHelp" placeholder="Jl. Indonesia Raya No. 1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputamount1" class="form-label">Amount</label>
                                    <input type="number" name="amount" class="form-control" id="exampleInputAmount1"
                                        aria-describedby="amountHelp" placeholder="1500000" required>
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
