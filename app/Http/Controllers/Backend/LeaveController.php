<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveLeaveRequest;
use App\Http\Requests\StoreLeaveRequest;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;

class LeaveController extends Controller
{
    public function apply()
    {
        return view('leave.apply');
    }
    public function store(StoreLeaveRequest $request)
    {
        $input = $request->all();
        $start =  Carbon::parse($request->from);
        $end =  Carbon::parse($request->to)->addDay(1);
        $input['days'] = $end->diffInWeekdays($start);


        if ($input['days'] == 0) {
            return redirect()->back()->with('error', 'You are applying zero day leave!!!')->withInput($request->input());
        }


        if ($request->type == 'Casual' && $input['days'] > Auth::user()->casual_leave) {
            return redirect()->back()->with('error', 'Leave quota exceeded!!!')->withInput($request->input());
        } elseif ($request->type == 'Annual' && $input['days'] > Auth::user()->annual_leave) {
            return redirect()->back()->with('error', 'Leave quota exceeded!!!')->withInput($request->input());
        }


        if (Auth::user()->casual_leave + Auth::user()->annual_leave == 0 && $request->type != 'Unpaid') {
            return redirect()->back()->with('error', 'Leave must be unpaid!!!')->withInput($request->input());
        }
        if ($request->type == 'Casual' && Carbon::parse($request->to)->format('Y') != Carbon::now()->format('Y')) {
            return redirect()->back()->with('error', 'Casual Leave cannot be applied for next year!!!')->withInput($request->input());
        }
        if ($this->alreadyAppliedLeaves($start, $end, Auth::user()->id) > 0) {
            return redirect()->back()->with('error', 'Leaves are overlapping with already approved leaves!!!')->withInput($request->input());
        }
        $input['user_id'] = Auth::user()->id;
        Leave::create($input);
        return redirect('')->with('success', 'Leave applied successfully!!!');
    }
    public function edit(Leave $leave)
    {
        if ($leave->is_approved !== null || $leave->user_id != Auth::user()->id) {
            abort(401);
        } else {
            return view('leave.edit', compact('leave'));
        }
    }
    public function update(StoreLeaveRequest $request, Leave $leave)
    {
        $start =  Carbon::parse($request->from);
        $end =  Carbon::parse($request->to)->addDay(1);
        $input = $request->all();
        $input['days'] = $end->diffInWeekdays($start);

        if ($request->type == 'Casual' && $input['days'] > Auth::user()->casual_leave) {
            return redirect()->back()->with('error', 'Leave quota exceeded!!!');
        } elseif ($request->type == 'Annual' && $input['days'] > Auth::user()->annual_leave) {
            return redirect()->back()->with('error', 'Leave quota exceeded!!!');
        }
        $leave->update($input);
        return redirect('')->with('success', 'Leave edited successfully!!!');
    }
    public function destroy(Leave $leave)
    {
        if ($leave->is_approved !== null || $leave->user_id != Auth::user()->id) {
            abort(401);
        } else {
            $leave->delete();
            return redirect('')->with('success', 'Leave deleted successfully!!!');
        }
    }
    public function index(Leave $leave)
    {
        $leaves = Leave::where('from', '>=', Carbon::now()->format('Y-m-d'))->where('user_id', '!=', Auth::user()->id)->whereRaw('(is_approved is null or is_approved =1)')->with('user:id,first_name,last_name,casual_leave,annual_leave')->orderBy('id', 'desc')->get(['id', 'user_id', 'from', 'to', 'days', 'type', 'reason', 'is_approved', 'comments']);
        return view('leave.index', compact('leaves'));
    }

    public function action(Leave $leave)
    {
        if ($leave->from < Carbon::now()->format('Y-m-d')) {
            abort(401);
        } else {
            $user = User::where('id', $leave->user_id)->first(['casual_leave', 'annual_leave']);
            return view('leave.action', compact('leave', 'user'));
        }
    }
    public function approve(ApproveLeaveRequest $request, Leave $leave)
    {
        if ($request->is_approved == 1 && $this->alreadyAppliedLeaves($leave->from, $leave->to, $leave->user_id) > 0) {
            return redirect()->back()->with('error', 'Leaves are overlapping with already approved leaves!!!');
        }
        $period = CarbonPeriod::create($leave->from, $leave->to);
        // Iterate over the period
        foreach ($period as $date) {
            if ($request->is_approved == 1) {
                $day = Carbon::parse($date)->format('N');
                if ($day < 6) {
                    Attendance::updateOrCreate(
                        ['date' => $date, 'user_id' => $leave->user_id],
                        ['status' => 'On Leave', 'leave_type' => $leave->type]
                    );
                }
            }
        }
        $leave->update($request->all());
        if ($request->is_approved == 1) {
            $this->recalculateLeaves($leave, true);
        } else {
            //mean the leave was first approved
            if ($this->alreadyAppliedLeaves($leave->from, $leave->to, $leave->user_id) > 0) {
                $this->removeAppliedLeaves($leave->from, $leave->to, $leave->user_id);
                $this->recalculateLeaves($leave, false);
            }
        }
        return redirect()->route('leaves.index')->with('success', 'Leave edited successfully!!!');
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
    public function recalculateLeaves($record, $approve)
    {
        if ($approve) {
            $operator = '-';
        } else {
            $operator = '+';
        }
        if ($record->type == 'Casual') {
            User::where('id', $record->user_id)->update([
                'casual_leave' => DB::raw('casual_leave ' . $operator . ' ' . $record->days),
            ]);
        } elseif ($record->type == 'Annual') {
            User::where('id', $record->user_id)->update([
                'annual_leave' => DB::raw('annual_leave ' . $operator . ' ' . $record->days),
            ]);
        }
    }
    public function own()
    {
        $date = Carbon::now()->format('Y') . '-01-01';
        $user = Auth::user();
        $leaves = Leave::where('user_id', $user->id)->whereDate('from', '>=', $date)->orderBy('id', 'desc')->get(['id', 'from', 'to', 'type',  'is_approved', 'comments', 'reason']);
        return view('leave.own', compact('leaves'));
    }
    public function carry_forward(Request $request)
    {
        // if (Carbon::now()->format('m') != 12) {
        //     abort(401);
        // };
        $threshold = Carbon::now()->subDays(60)->format('Y-m-d');
        User::where('carry_forward_date', '<', $threshold)->where('role', '!=', 1)->update(['casual_leave' => 10, 'annual_leave' => DB::raw('annual_leave+10'), 'carry_forward_date' => Carbon::now()]);
        return redirect()->back()->with('success', 'Carry forwarded all the leaves!!!');
    }
}
