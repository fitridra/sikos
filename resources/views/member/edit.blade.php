@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <a href="{{ url()->previous() }}" class="btn btn-outline-primary mb-3">
                    <i class="ti ti-arrow-left"></i> Back
                </a>
                <h5 class="card-title fw-semibold mb-4">Edit Data</h5>
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('member.update', $member->member_id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="exampleInputname1" class="form-label">Full Name</label>
                                <input type="text" name="full_name" value="{{ $member->full_name }}" class="form-control"
                                    id="exampleInputName1" aria-describedby="nameHelp" placeholder="Bunga Cinta Lestari"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputaddress1" class="form-label">Address</label>
                                <input type="text" name="address" value="{{ $member->address }}" class="form-control" id="exampleInputAddress1"
                                    aria-describedby="addressHelp" placeholder="DKI Jakarta">
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputphone1" class="form-label">Phone</label>
                                <input type="text" name="phone" value="{{ $member->phone }}" class="form-control" id="exampleInputPhone1"
                                    aria-describedby="phoneHelp" placeholder="081234567890">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kost Name</label>
                                <select name="kost_id" id="kost" class="form-select" required
                                    data-selected-room="{{ $member->room_id }}">
                                    <option value="" disabled>-- Kost Name --</option>
                                    @foreach ($all_kosts as $kost)
                                        <option value="{{ $kost->kost_id }}"
                                            {{ $member->room->kost_id == $kost->kost_id ? 'selected' : '' }}>
                                            {{ $kost->kost_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Room Number</label>
                                <select name="room_id" id="room" class="form-select" required>
                                    <option value="" disabled>-- Pilih Room --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputmovein1" class="form-label">Move-in Date</label>
                                <input type="date" name="move_in_date" value="{{ $member->move_in_date }}"
                                    class="form-control" id="exampleInputmovein1" aria-describedby="moveinHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputmoveout1" class="form-label">Move-out Date</label>
                                <input type="date" name="move_out_date" value="{{ $member->move_out_date }}"
                                    class="form-control" id="exampleInputmoveout1" aria-describedby="moveoutHelp">
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
@endsection
@section('scripts_content')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kostSelect = document.getElementById('kost');
            const roomSelect = document.getElementById('room');
            const selectedRoomId = kostSelect.getAttribute('data-selected-room');

            function loadRooms(kostId) {
                fetch(`/get-rooms/${kostId}?selected=${selectedRoomId || ''}`)
                    .then(res => res.json())
                    .then(data => {
                        roomSelect.innerHTML = '<option value="" disabled>-- Pilih Room --</option>';
                        data.forEach(room => {
                            const selected = room.room_id == selectedRoomId ? 'selected' : '';
                            roomSelect.innerHTML +=
                                `<option value="${room.room_id}" ${selected}>${room.room_number}</option>`;
                        });
                    });
            }

            kostSelect.addEventListener('change', function() {
                loadRooms(this.value);
            });

            // load room saat halaman pertama kali terbuka jika kost sudah terpilih
            if (kostSelect.value) {
                loadRooms(kostSelect.value);
            }
        });
    </script>
@endsection
