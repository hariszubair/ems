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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg mb-5 bg-white rounded ">
                    <div class="card-body bg-warning rounded text-dark">
                        <b> Casual Leave : {{ Auth::user()->casual_leave }}</b>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg mb-5 bg-white rounded">
                    <div class="card-body bg-primary rounded text-white">
                        <b> Annual Leaves : {{ Auth::user()->annual_leave }}</b>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg  mb-5 bg-white rounded">
                    <a href="{{ route('leaves.apply') }}" class="card-body  btn btn-success">Apply for a Leave</a>
                </div>
            </div>
        </div>
    </div>
    <div class="container ">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-lg mb-5 bg-white rounded">
                    <div class="card-header text-center">
                        <h1 class="h3 mb-0 text-gray-800">Upcommig Leaves</h1>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <table id="leaves" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Detail</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Comments</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leaves as $key => $leave)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><b> From:</b>{{ $leave->from }} <br><b> To:</b>{{ $leave->to }}
                                            <br><b>Reason:</b> {{ $leave->reason }}
                                        </td>
                                        <td>{{ $leave->type }}</td>
                                        <td>
                                            @if ($leave->is_approved === null)
                                                Pending
                                            @elseif($leave->is_approved === 1)
                                                Approved
                                            @else
                                                Declined
                                            @endif
                                        </td>

                                        <td>{{ $leave->comments }}</td>
                                        <td>
                                            <div class="row">
                                                @if ($leave->is_approved == null)
                                                    <a class="btn btn-info mr-2"
                                                        href="{{ route('leaves.edit', $leave->id) }}"><i
                                                            class="far fa-edit"></i></a>
                                                    <form method="POST"
                                                        action="{{ route('leaves.destroy', $leave->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $(document).ready(function() {
            $('#leaves').DataTable();
        });
    </script>
@endsection
