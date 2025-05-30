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
                        <form action="{{ route('room.update', $room->room_id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="exampleInputkostname1" class="form-label">Kost Name</label>
                                <select name="kost_id" class="form-select" required>
                                    <option value="" disabled>-- Kost Name --</option>
                                    @foreach ($all_kosts as $kost)
                                        <option value="{{ $kost->kost_id }}"
                                            {{ $room->kost_id == $kost->kost_id ? 'selected' : '' }}>
                                            {{ $kost->kost_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputroomnumber1" class="form-label">Room Number</label>
                                <input type="text" name="room_number" class="form-control" id="exampleInputroomnumber1"
                                    aria-describedby="roomnumberHelp" value="{{ $room->room_number }}" placeholder="A101"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputstatus1" class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="" disabled>-- Status --</option>
                                    <option value="0" {{ $room->status == 0 ? 'selected' : '' }}>Available</option>
                                    <option value="1" {{ $room->status == 1 ? 'selected' : '' }}>Filled</option>
                                </select>
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
