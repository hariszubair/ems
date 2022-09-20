<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarkOldRequest;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function mark_old(MarkOldRequest $request)
    {
        $user = Auth::user();
        if ($user->role != 1) {
            abort(401);
        }
        $users = User::where('role', '!=', 1)->where('users.id', '!=', Auth::user()->id)->leftJoin('attendances', function ($join) use ($request) {
            $join->on('attendances.user_id', '=', 'users.id')
                ->on('attendances.id', '=', DB::raw("(SELECT max(id) from attendances WHERE attendances.user_id = users.id)"))->where('date', $request->date);
        })->get(['users.id as id', 'first_name', 'last_name', 'attendances.status', 'attendances.date']);
        $date = $request->date;
        return view('attendance.mark', compact('users', 'date'));
    }

    public function mark()
    {
        // return $user = Attendance::where('date', Carbon::now()->format('Y-m-d'))->with('user')->get();
        $users = User::where('role', '!=', 1)->where('users.id', '!=', Auth::user()->id)->leftJoin('attendances', function ($join) {
            $join->on('attendances.user_id', '=', 'users.id')
                ->on('attendances.id', '=', DB::raw("(SELECT max(id) from attendances WHERE attendances.user_id = users.id)"))->where('date', Carbon::now()->format('Y-m-d'));
        })->get(['users.id as id', 'first_name', 'last_name', 'attendances.status', 'attendances.date']);
        $date = Carbon::now()->format('Y-m-d');
        return view('attendance.mark', compact('users', 'date'));
    }
    public function store(StoreAttendanceRequest $request, $id)
    {
        $user = Auth::user();
        if ($user->role == 1) {
            $date = $request->date;
        } else {
            $date = Carbon::now()->format('Y-m-d');
        }
        if ($id == $user->id) {
            abort(401);
        }

        if ($request->status == 'On Time' || $request->status == 'Present') {
            $attendance = Attendance::where('user_id', $id)->where('date', $date)->first();
            $type = $attendance->leave_type;
            $attendance->delete();
            $this->recalculateLeaves(
                $type,
                1,
                $id,
                false
            );
        } else {
            $employee = User::findOrFail($id);
            $leave_type = 'Unpaid';
            if ($request->status == 'On Leave' && $employee->casual_leave > 0) {
                $leave_type = 'Casual';
            } elseif ($request->status == 'On Leave' && $employee->annual_leave > 0) {
                $leave_type = 'Annual';
            } elseif ($request->status == 'Late') {
                $leave_type = null;
            }
            $record = Attendance::updateOrCreate(
                ['date' => $date, 'user_id' => $id],
                ['status' => $request->status, 'leave_type' => $leave_type]
            );
            if ($request->status == 'On Leave' && $record->wasRecentlyCreated) {
                $this->recalculateLeaves(
                    $leave_type,
                    1,
                    $id,
                    true
                );
            }
        }
        return redirect()->back()->with('success', 'Attendance marked successfully!!!');
    }


    public function alreadyAppliedLeaves($start, $end, $user_id): int
    {
        $period = CarbonPeriod::create($start, $end)->toArray();
        $period = array_map(function ($array_item) {
            return Carbon::parse($array_item)->format('Y-m-d');
        }, $period);
        return $already_applied_leaves = Attendance::where('user_id', $user_id)->whereIn('date', $period)->where('status', 'On Leave')->count();
    }
    public function removeAppliedLeaves($start, $end, $user_id): void
    {
        $period = CarbonPeriod::create($start, $end)->toArray();
        $period = array_map(function ($array_item) {
            return Carbon::parse($array_item)->format('Y-m-d');
        }, $period);
        $already_applied_leaves = Attendance::where('user_id', $user_id)->whereIn('date', $period)->where('status', 'On Leave')->delete();
    }
    public function recalculateLeaves($type, $days, $user_id, $approve)
    {
        if ($approve) {
            $operator = '-';
        } else {
            $operator = '+';
        }
        if ($type == 'Casual') {
            User::where('id', $user_id)->update([
                'casual_leave' => DB::raw('casual_leave ' . $operator . ' ' . $days),
            ]);
        } elseif ($type == 'Annual') {
            User::where('id', $user_id)->update([
                'annual_leave' => DB::raw('annual_leave ' . $operator . ' ' . $days),
            ]);
        }
    }
}
