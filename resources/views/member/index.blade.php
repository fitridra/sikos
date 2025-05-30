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
                    <h5 class="card-title fw-semibold mb-0">Data Member</h5>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="ti ti-plus"></i>&nbsp; Add Member</a>
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
                                    <h6 class="fw-semibold mb-0">Move-in Date</h6>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 class="fw-semibold mb-0">Move-out Date</h6>
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
                            @foreach ($data_member as $member)
                                <tr>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">&nbsp;{{ $no++ }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-semibold mb-0">{{ $member->full_name }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ $member->room->kost->kost_name }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ $member->room->room_number }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ $member->move_in_date }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="fw-normal mb-0">{{ $member->move_out_date }}</p>
                                    </td>
                                    <td class="border-bottom-0 text-center">
                                        <small>
                                            <a href="{{ route('member.edit', $member->member_id) }}" type="button"
                                                class="btn btn-warning">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#confirmDeleteModal{{ $member->member_id }}">
                                                <i class="ti ti-trash"></i>
                                            </button>

                                            <!-- Modal Delete-->
                                            <div class="modal fade" id="confirmDeleteModal{{ $member->member_id }}"
                                                tabindex="-1" aria-labelledby="deleteLabel{{ $member->member_id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="deleteLabel{{ $member->member_id }}">
                                                                Delete Confirmation</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete
                                                            <strong>{{ $member->full_name }}</strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            @method('delete')
                                                            @csrf
                                                            <a href="{{ route('member.delete', $member->member_id) }}"
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
                        {{ $data_member->links() }}
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
                            <form action="{{ route('member.create') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="exampleInputname1" class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" id="exampleInputName1"
                                        aria-describedby="nameHelp" placeholder="Bunga Cinta Lestari" required>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputkostname1" class="form-label">Kost Name</label>
                                    <select name="kost_id" id="kost" class="form-select" required>
                                        <option value="" disabled selected>-- Kost Name --</option>
                                        @foreach ($all_kosts as $kost)
                                            <option value="{{ $kost->kost_id }}">{{ $kost->kost_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputkostname1" class="form-label">Room Number</label>
                                    <select name="room_id" id="room" class="form-select" required>
                                        <option value="" disabled selected>-- Pilih Room --</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputmovein1" class="form-label">Move-in Date</label>
                                    <input type="date" name="move_in_date" class="form-control"
                                        id="exampleInputmovein1" aria-describedby="moveinHelp" required>
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
        document.getElementById('kost').addEventListener('change', function() {
            let kostId = this.value;

            fetch('/get-rooms/' + kostId)
                .then(response => response.json())
                .then(data => {
                    let roomSelect = document.getElementById('room');
                    roomSelect.innerHTML = '<option value="" disabled selected>-- Pilih Room --</option>';
                    data.forEach(function(room) {
                        roomSelect.innerHTML +=
                            `<option value="${room.room_id}">${room.room_number}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error fetching rooms:', error);
                });
        });
    </script>
@endsection
