@extends('layouts.main')

@section('content')
    <!-- Page Heading -->

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h1 class="h3 mb-0 text-gray-800">Reports</h1>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('reports.generate') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="year"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Year') }}</label>

                                <div class="col-md-6">
                                    <select id="year" type="text"
                                        class="form-control @error('year') is-invalid @enderror" name="year"
                                        value="{{ old('year') }}" required autocomplete="year" autofocus>
                                        <option value="">Please select a year</option>
                                    </select>

                                    @error('year')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="month"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Month') }}</label>

                                <div class="col-md-6">
                                    <select id="month" type="text"
                                        class="form-control @error('month') is-invalid @enderror" name="month"
                                        value="{{ old('month') }}" autocomplete="month" autofocus>
                                        <option value="">Please select a month</option>
                                        <option value="01">January</option>
                                        <option value="02">Feburary</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">Augugst</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>

                                    </select>
                                    @error('month')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Generate') }}
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

@section('footer')
    <script>
        $(document).ready(function() {
            var max = new Date().getFullYear()
            var min = 2022
            for (var i = max; i >= min; i--) {
                if (i == max) {
                    var selected = 'selected'
                }
                $('#year').append("<option " + selected + " value=" + i + ">" + i + "</option>")
            }
        });
    </script>
@endsection
