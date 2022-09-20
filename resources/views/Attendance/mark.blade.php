@extends('layouts.main')

@section('content')
    <!-- Page Heading -->

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
    @error('status')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
    @enderror
    @error('date')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
    @enderror
    @if (\Auth::user()->role == 1)
        <form action="{{ route('attendance.mark_old') }}" method="get">
            <div class="form-group row">
                <label for="date" class="col-md-3 col-form-label text-md-right">{{ __('To') }}</label>
                <div class="col-md-6">
                    <input id="date" type="date" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                        class="form-control @error('date') is-invalid @enderror" name="date" value="{{ $date }}"
                        required autocomplete="date" autofocus>
                </div>
                <div class="col-md-3">
                    <Button class="btn btn-success">Submit</Button>
                </div>
            </div>
        </form>
    @endif
    <div class="row">
        <div class="card w-100">
            <div class="card-header text-center">
                <h1 class="h3 mb-0 text-gray-800">Attendance</h1>

            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $key => $user)
                            <form action="{{ route('attendance.store', $user->id) }}" method="POST">
                                @csrf
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                    <td><input type="date" value="{{ $date }}" name="date" readonly required>
                                    </td>
                                    <td><select name="status" id="status" class="form-control">
                                            <option value="">Selet a status</option>
                                            @if ($user->date)
                                                @if ($user->status == 'Late')
                                                    <option {{ $user->status == 'Late' ? 'selected' : '' }} value="Late">
                                                        Late</option>
                                                    <option value="On Time">On Time</option>
                                                @elseif($user->status == 'On Leave')
                                                    <option {{ $user->status == 'On Leave' ? 'selected' : '' }}
                                                        value="On Leave">On Leave</option>
                                                    <option value="Present">Present</option>
                                                @endif
                                            @else
                                                <option value="Late">Late</option>
                                                <option value="On Leave">On Leave</option>
                                            @endif

                                        </select></td>
                                    <td>
                                        <button class="btn btn-primary" type="submit">update</button>

                                    </td>

                                </tr>
                            </form>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
