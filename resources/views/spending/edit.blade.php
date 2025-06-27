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
                        <form action="{{ route('spending.update', $spending->spending_id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="exampleInputkostname1" class="form-label">Kost Name</label>
                                <select name="kost_id" id="kost" class="form-select" required>
                                    <option value="" disabled selected>-- Kost Name --</option>
                                    @foreach ($allkosts as $kost)
                                        <option value="{{ $kost->kost_id }}"
                                            {{ $spending->kost->kost_id == $kost->kost_id ? 'selected' : '' }}>
                                            {{ $kost->kost_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputname1" class="form-label">Spending</label>
                                <input type="text" name="spending_name" value="{{ $spending->spending_name }}"
                                    class="form-control" id="exampleInputName1" aria-describedby="nameHelp"
                                    placeholder="Listrik" required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputdate1" class="form-label">Date</label>
                                <input type="date" name="spending_date" value="{{ $spending->spending_date }}"
                                    class="form-control" id="exampleInputDate1" aria-describedby="dateHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputamount1" class="form-label">Amount</label>
                                <input type="number" name="amount" value="{{ $spending->amount }}" class="form-control"
                                    id="exampleInputAmount1" aria-describedby="amountHelp" placeholder="1500000" required>
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
