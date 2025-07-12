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
                        <form action="{{ route('payment.update', $payment->payment_id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="member_id" class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="{{ $payment->member->full_name }}" readonly>
                                @error('member_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" id="payment_date" class="form-control"
                                    value="{{ $payment->payment_date }}" required>
                                @error('payment_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputamount" class="form-label">Amount</label>
                                <input type="text" class="form-control" value="{{ number_format($payment->amount, 0, ',', '.') }}" readonly>
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
