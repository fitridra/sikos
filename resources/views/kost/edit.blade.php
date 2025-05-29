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
                        <form action="{{ route('kost.update', $kost->kost_id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="exampleInputname1" class="form-label">Name</label>
                                <input type="text" name="kost_name" value="{{ $kost->kost_name }}" class="form-control"
                                    id="exampleInputName1" aria-describedby="nameHelp" placeholder="Kost Harapan Indah"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputaddress1" class="form-label">Address</label>
                                <input type="text" name="address" value="{{ $kost->address }}" class="form-control"
                                    id="exampleInputAddress1" aria-describedby="addressHelp"
                                    placeholder="Jl. Indonesia Raya No. 1" required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputamount1" class="form-label">Amount</label>
                                <input type="number" name="amount" value="{{ $kost->amount }}" class="form-control"
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
