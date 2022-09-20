<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function create()
    {
        return view('reports.create');
    }
    public function generate(ReportRequest $request)
    {
        if ($request->month) {
            $records = Attendance::where('date', 'like', $request->year . '-' . $request->month . '-%')->get();
            $new_records = [];
            $total = [];
            foreach ($records as $record) {
                if ($record['status'] == 'Late') {
                    $record['leave_type'] = '';
                }
                if (!array_key_exists($record['user_id'], $new_records)) {
                    $new_records[$record['user_id']] = [];
                }
                if (!array_key_exists(Carbon::parse($records[0]->date)->format('d'), $new_records[$record['user_id']])) {
                    $new_records[$record['user_id']][Carbon::parse($records[0]->date)->format('d')] = [];
                }
                if (!array_key_exists($record['status'] . '-' . $record['leave_type'], $new_records[$record['user_id']][Carbon::parse($records[0]->date)->format('d')])) {

                    $new_records[$record['user_id']][Carbon::parse($records[0]->date)->format('d')][$record['status'] . '-' . $record['leave_type']] = 0;
                }
                //total
                if (!array_key_exists($record['user_id'], $total)) {
                    $total[$record['user_id']] = [];
                }
                if (!array_key_exists($record['status'] . '-' . $record['leave_type'], $total[$record['user_id']])) {
                    $total[$record['user_id']][$record['status'] . '-' . $record['leave_type']] = 0;
                }

                $new_records[$record['user_id']][Carbon::parse($records[0]->date)->format('d')][$record['status'] . '-' . $record['leave_type']]++;
                $total[$record['user_id']][$record['status'] . '-' . $record['leave_type']]++;
            }
            // return $total;
            $users = User::where('role', '!=', 1)->get();
            $year = $request->year;
            $dateObj   = DateTime::createFromFormat('!m', $request->month);
            $days = Carbon::parse($request->year . '-' . $request->month . '-01')->daysInMonth;
            $month = $dateObj->format('F'); // March
            $request->month;

            return view('reports.monthly', compact('users', 'total', 'year', 'month', 'new_records', 'days'));
        } else {
            $records = Attendance::where('date', 'like', $request->year . '-%')->get();
            $new_records = [];
            $total = [];

            foreach ($records as $record) {
                if ($record['status'] == 'Late') {
                    $record['leave_type'] = '';
                }
                if (!array_key_exists($record['user_id'], $new_records)) {
                    $new_records[$record['user_id']] = [];
                }
                if (!array_key_exists(Carbon::parse($records[0]->date)->format('m'), $new_records[$record['user_id']])) {
                    $new_records[$record['user_id']][Carbon::parse($records[0]->date)->format('m')] = [];
                }
                if (!array_key_exists($record['status'] . '-' . $record['leave_type'], $new_records[$record['user_id']][Carbon::parse($records[0]->date)->format('m')])) {
                    $new_records[$record['user_id']][Carbon::parse($records[0]->date)->format('m')][$record['status'] . '-' . $record['leave_type']] = 0;
                }
                //total
                if (!array_key_exists($record['user_id'], $total)) {
                    $total[$record['user_id']] = [];
                }
                if (!array_key_exists($record['status'] . '-' . $record['leave_type'], $total[$record['user_id']])) {
                    $total[$record['user_id']][$record['status'] . '-' . $record['leave_type']] = 0;
                }

                $new_records[$record['user_id']][Carbon::parse($records[0]->date)->format('m')][$record['status'] . '-' . $record['leave_type']]++;
                $total[$record['user_id']][$record['status'] . '-' . $record['leave_type']]++;
            }
            $users = User::where('role', '!=', 1)->get();
            $year = $request->year;
            return view('reports.yearly', compact('new_records', 'users', 'total', 'year'));
        }
    }
}
