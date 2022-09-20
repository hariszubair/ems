@extends('layouts.main')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Users</h1>
    </div>
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-success">
                    <div class="card-header">
                        {{ __('Update User') }}
                        <a href="{{ route('users.index') }}" class="float-right">Back</a>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('users.update', $user->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="username"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>

                                <div class="col-md-6">
                                    <input id="username" type="text"
                                        class="form-control @error('username') is-invalid @enderror" name="username"
                                        value="{{ old('username', $user->username) }}" required autocomplete="username"
                                        autofocus>

                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="first_name"
                                    class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>

                                <div class="col-md-6">
                                    <input id="first_name" type="text"
                                        class="form-control @error('first_name') is-invalid @enderror" name="first_name"
                                        value="{{ old('first_name', $user->first_name) }}" required
                                        autocomplete="first_name" autofocus>

                                    @error('first_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="last_name"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>

                                <div class="col-md-6">
                                    <input id="last_name" type="text"
                                        class="form-control @error('last_name') is-invalid @enderror" name="last_name"
                                        value="{{ old('last_name', $user->last_name) }}" required autocomplete="last_name"
                                        autofocus>

                                    @error('last_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email', $user->email) }}">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="role"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Role') }}</label>

                                <div class="col-md-6">
                                    <select name="role" id="role"
                                        class="form-control @error('role') is-invalid @enderror">
                                        @foreach ($roles as $role)
                                            <option {{ $user->role == $role->id ? 'selected' : '' }}
                                                value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Update User') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <div class="col-md-8 mt-2">
                <div class="card border-success">
                    <div class="card-header">{{ __('Change Password') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('users.change.password', $user->id) }}">
                            @csrf
                            <div class="form-group row">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Update Password') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @if ($user->role != 1)
                <div class="col-md-8 mt-2">
                    <div class="card border-success">
                        <div class="card-header">{{ __('Change Leave Balance') }}</div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('users.change.leaves', $user->id) }}">
                                @csrf
                                <div class="form-group row">
                                    <label for="casual_leave"
                                        class="col-md-4 col-form-label text-md-right">{{ __('Casual Leaves') }}</label>

                                    <div class="col-md-6">
                                        <input id="casual_leave" type="number"
                                            class="form-control @error('casual_leave') is-invalid @enderror"
                                            name="casual_leave" autocomplete="casual_leave"
                                            value="{{ $user->casual_leave }}">

                                        @error('casual_leave')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="annual_leave"
                                        class="col-md-4 col-form-label text-md-right">{{ __('Annual Leaves') }}</label>

                                    <div class="col-md-6">
                                        <input id="annual_leave" type="number" class="form-control" name="annual_leave"
                                            autocomplete="annual_leave" value="{{ $user->annual_leave }}">
                                    </div>
                                    @error('casual_leave')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Update Leaves') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

        </div>
        @if ($user->id != Auth::user()->id)
            <div class="row justify-content-center row m-5">
                <form method="POST" action="{{ route('users.destroy', $user->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger mr-2">Delete Account</button>
                </form>


            </div>
        @endif
    </div>
@endsection
