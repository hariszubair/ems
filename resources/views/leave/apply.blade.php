@extends('layouts.main')

@section('content')
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    @endif
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
    </div>
    <div class="row pb-2">
        <div class="col-6">
            <b>Casual Leave:</b> {{ Auth::user()->casual_leave }}
        </div>
        <div class="col-6">
            <b>Annual Leave:</b> {{ Auth::user()->annual_leave }}
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h1 class="h3 mb-0 text-gray-800">Apply for a leave</h1>

                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('leaves.store') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="from"
                                    class="col-md-4 col-form-label text-md-right">{{ __('From') }}</label>

                                <div class="col-md-6">
                                    <input id="from" type="date" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                        class="form-control @error('from') is-invalid @enderror" name="from"
                                        value="{{ old('from') }}" required autocomplete="from" autofocus>

                                    @error('from')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="to"
                                    class="col-md-4 col-form-label text-md-right">{{ __('To') }}</label>

                                <div class="col-md-6">
                                    <input id="to" type="date" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                        class="form-control @error('to') is-invalid @enderror" name="to"
                                        value="{{ old('to') }}" required autocomplete="to" autofocus>

                                    @error('to')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="type"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Type') }}</label>

                                <div class="col-md-6">
                                    <select id="type" type="text"
                                        class="form-control @error('type') is-invalid @enderror" name="type" required
                                        autocomplete="type" autofocus>
                                        <option value="">Please select leave type</option>
                                        @if (Auth::user()->casual_leave + Auth::user()->annual_leave == 0)
                                            <option {{ old('type') == 'Unpaid' ? 'selected' : '' }} value="Unpaid">Unpaid
                                            </option>
                                        @else
                                            @if (Auth::user()->casual_leave != 0)
                                                <option {{ old('type') == 'Casual' ? 'selected' : '' }} value="Casual">
                                                    Casual
                                                </option>
                                            @endif
                                            @if (Auth::user()->annual_leave != 0)
                                                <option {{ old('type') == 'Annual' ? 'selected' : '' }} value="Annual">
                                                    Annual
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="reason"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Reason') }}</label>

                                <div class="col-md-6">
                                    <textarea id="reason" name="reason" rows="2" class="form-control" style="resize: none">{{ old('reason') }}</textarea>

                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Apply') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
