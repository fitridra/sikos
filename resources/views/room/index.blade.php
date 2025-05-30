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
                    <h5 class="card-title fw-semibold mb-0">Data Room</h5>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="ti ti-plus"></i>&nbsp; Add Room</a>
                </div>
                <form method="GET" action="{{ url()->current() }}">
                    <div class="row mb-3">
                        <div class="col-md-1">
                            <a href="{{ url()->current() }}" class="btn btn-outline-dark me-2">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <select name="kost_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Filter by Kost --</option>
                                @foreach ($all_kosts as $kost)
                                    <option value="{{ $kost->kost_id }}"
                                        {{ request('kost_id') == $kost->kost_id ? 'selected' : '' }}>
                                        {{ $kost->kost_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Filter by Status --</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Available</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Filled</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="input-group">
                                <input name="cari" type="text" class="form-control" placeholder="Search Room..."
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
                                    <h6 class="fw-semibold mb-0">Room</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Status</h6>
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
                            @foreach ($data_room as $room)
                                <tr>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">&nbsp;{{ $no++ }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ $room->kost->kost_name }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-semibold mb-0">{{ $room->room_number }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <span
                                                class="badge {{ $room->status == 1 ? 'bg-danger' : 'bg-primary' }} rounded-3 fw-semibold">
                                                {{ $room->status_text }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        <small>
                                            <a href="{{ route('room.edit', $room->room_id) }}" type="button"
                                                class="btn btn-warning">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#confirmDeleteModal{{ $room->room_id }}">
                                                <i class="ti ti-trash"></i>
                                            </button>

                                            <!-- Modal Delete-->
                                            <div class="modal fade" id="confirmDeleteModal{{ $room->room_id }}"
                                                tabindex="-1" aria-labelledby="deleteLabel{{ $room->room_id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteLabel{{ $room->room_id }}">
                                                                Delete Confirmation</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete the room
                                                            <strong>{{ $room->room_number }}</strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            @method('delete')
                                                            @csrf
                                                            <a href="{{ route('room.delete', $room->room_id) }}"
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
                        {{ $data_room->links() }}
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
                            <form action="{{ route('room.create') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="exampleInputkostname1" class="form-label">Kost Name</label>
                                    <select name="kost_id" class="form-select" required>
                                        <option value="" disabled>-- Kost Name --</option>
                                        @foreach ($all_kosts as $kost)
                                            <option value="{{ $kost->kost_id }}"
                                                {{ request('kost_id') == $kost->kost_id ? 'selected' : '' }}>
                                                {{ $kost->kost_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputroomnumber1" class="form-label">Room Number</label>
                                    <input type="text" name="room_number" class="form-control"
                                        id="exampleInputroomnumber1" aria-describedby="roomnumberHelp" placeholder="A101"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputstatus1" class="form-label">Status</label>
                                    <input type="text" class="form-control" value="Available" readonly>
                                    <input type="hidden" name="status" value="0">
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
