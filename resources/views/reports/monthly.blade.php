@extends('layouts.main')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <!-- Page Heading -->

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h1 class="h3 mb-0 text-gray-800">Monthly Report</h1>
                    </div>

                    <div class="card-body" style="overflow:scroll">
                        <table id="attendance" class="table-bordered" style="width:100%;">
                            <thead>
                                <tr>
                                    <th style="width:200px">Name</th>
                                    @for ($i = 01; $i <= $days; $i++)
                                        <th>{{ $i }}</th>
                                    @endfor
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                        @for ($i = 01; $i <= $days; $i++)
                                            <td>
                                                @if (isset($new_records[$user->id]) && isset($new_records[$user->id][$i < 10 ? '0' . $i : $i]))
                                                    C={{ $new_records[$user->id][$i < 10 ? '0' . $i : $i]['On Leave-Casual'] ?? 0 }}<br>A={{ $new_records[$user->id][$i < 10 ? '0' . $i : $i]['On Leave-Annual'] ?? 0 }}<br>U={{ $new_records[$user->id][$i < 10 ? '0' . $i : $i]['On Leave-Unpaid'] ?? 0 }}<br>L={{ $new_records[$user->id][$i < 10 ? '0' . $i : $i]['Late-'] ?? 0 }}
                                                @else
                                                    C=0<br>A=0<br>U=0<br>L=0
                                                @endif
                                            </td>
                                        @endfor
                                        <td>
                                            @if (isset($total[$user->id]))
                                                C={{ $total[$user->id]['On Leave-Casual'] ?? 0 }}<br>A={{ $total[$user->id]['On Leave-Annual'] ?? 0 }}<br>U={{ $total[$user->id]['On Leave-Unpaid'] ?? 0 }}<br>L={{ $total[$user->id]['Late-'] ?? 0 }}
                                            @else
                                                C=0<br>A=0<br>U=0<br>L=0
                                            @endif
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
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#attendance').DataTable({
                dom: 'Bfrtip',
                'buttons': [{
                        extend: 'print',
                        text: 'Print Report',
                        exportOptions: {
                            stripHtml: false
                        },
                        title: '{{ $month . ' ' . $year }}' + ' Attendance Report',
                        messageTop: 'C: Casual Leave, A: Annual Leave, U:Unpaid Leave, L:Late',

                    },
                    {
                        extend: 'excel',
                        text: 'Download Excel',
                        title: '{{ $month . ' ' . $year }}' + ' Attendance Report',
                        exportOptions: {
                            modifier: {
                                page: 'current'
                            },
                            format: {
                                body: function(data, row, column, node) {
                                    data = data.replace(/<br\s*\/?>/gi, "\r\n")
                                    return data;
                                }
                            }
                        }
                    }
                ],
            });
        });
    </script>
@endsection
