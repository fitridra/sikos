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
                    <h5 class="card-title fw-semibold mb-0">Data Spending</h5>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="ti ti-plus"></i>&nbsp; Add Spending</a>
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
                                    <h6 class="fw-semibold mb-0">Kost Name</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Spending</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Date</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Amount</h6>
                                </th>
                                @if (Auth::check() && Auth::user()->username === 'superadmin')
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
                            @foreach ($data_spending as $spending)
                                <tr>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ $no++ }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-1">{{ $spending->kost->kost_name }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-1">{{ $spending->spending_name }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="mb-0 fw-normal">
                                            {{ \Carbon\Carbon::parse($spending->spending_date)->format('d M Y') }}
                                        </p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0 fs-4">{{ number_format($spending->amount, 0, ',', '.') }}
                                        </p>
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        <small>
                                            @if (Auth::check() && Auth::user()->username === 'superadmin')
                                                <a href="{{ route('spending.edit', $spending->spending_id) }}"
                                                    type="button" class="btn btn-warning">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#confirmDeleteModal{{ $spending->spending_id }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            @endif
                                            <!-- Modal Delete-->
                                            <div class="modal fade" id="confirmDeleteModal{{ $spending->spending_id }}"
                                                tabindex="-1" aria-labelledby="deleteLabel{{ $spending->spending_id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="deleteLabel{{ $spending->spending_id }}">
                                                                Delete Confirmation</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete the
                                                            <strong>{{ $spending->spending_name }}</strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            @method('delete')
                                                            @csrf
                                                            <a href="{{ route('spending.delete', $spending->spending_id) }}"
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
                        {{ $data_spending->links() }}
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
                            <form action="{{ route('spending.create') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="exampleInputkostname1" class="form-label">Kost Name</label>
                                    <select name="kost_id" id="kost" class="form-select" required>
                                        <option value="" disabled selected>-- Kost Name --</option>
                                        @foreach ($allkosts as $kost)
                                            <option value="{{ $kost->kost_id }}">{{ $kost->kost_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputname1" class="form-label">Spending</label>
                                    <input type="text" name="spending_name" class="form-control"
                                        id="exampleInputName1" aria-describedby="nameHelp" placeholder="Listrik"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputdate1" class="form-label">Date</label>
                                    <input type="date" name="spending_date" class="form-control"
                                        id="exampleInputDate1" aria-describedby="dateHelp" required>
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
