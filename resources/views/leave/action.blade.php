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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h1 class="h3 mb-0 text-gray-800">Update Leave Status</h1>
                    </div>

                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-4">
                                <b> From: </b> {{ $leave->from }}
                            </div>
                            <div class="col-4">
                                <b> To: </b>{{ $leave->to }}
                            </div>
                            <div class="col-4">
                                <b> Type: </b>{{ $leave->type }}
                            </div>
                            <div class="col-4">
                                <b> Days off: </b>{{ $leave->days }}
                            </div>
                            <div class="col-4">
                                <b> Casual Balance: </b>{{ $user->casual_leave }}
                            </div>
                            <div class="col-4">
                                <b> Annual Balance: </b>{{ $user->annual_leave }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('leaves.approve', $leave->id) }}">
                            @csrf
                            @method('Patch')
                            <div class="form-group row">
                                <label for="is_approved"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Is Approved?') }}</label>

                                <div class="col-md-6">
                                    <select id="is_approved" type="text"
                                        class="form-control @error('is_approved') is-invalid @enderror" name="is_approved"
                                        required autocomplete="is_approved" autofocus>
                                        <option value="">Please select a following</option>
                                        <option {{ $leave->is_approved == '1' ? 'selected' : '' }} value="1">Yes
                                        </option>
                                        <option {{ $leave->is_approved == '0' ? 'selected' : '' }} value="0">No
                                        </option>
                                    </select>
                                    @error('is_approved')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="comments"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Comments') }}</label>

                                <div class="col-md-6">
                                    <textarea id="comments" name="comments" rows="3" class="form-control" style="resize: none">{{ $leave->comments }}</textarea>

                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
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
