<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $date = Carbon::now()->format('Y') . '-01-01';
        $user = Auth::user();

        if ($user->role == 3) {
            $leaves = Leave::where('user_id', $user->id)->whereDate('from', '>=', Carbon::now()->format('Y-m-d'))->orderBy('id', 'desc')->get(['id', 'from', 'to', 'type',  'is_approved', 'comments', 'reason']);
            return view('employee_dashboard', compact('leaves'));
        } else {
            $users = User::where('role', '!=', 1)->count();
            $leave = Attendance::where('date', '=', Carbon::now()->format('Y-m-d'))->where('status', 'On Leave')->count();
            $late = Attendance::where('date', '=', Carbon::now()->format('Y-m-d'))->where('status', 'Late')->count();
            $leaves = Leave::where('from', '>=', Carbon::now()->format('Y-m-d'))->where('user_id', '!=', Auth::user()->id)->whereRaw('(is_approved is null or is_approved =1)')->with('user:id,first_name,last_name,casual_leave,annual_leave')->orderBy('id', 'desc')->get(['id', 'user_id', 'from', 'to', 'days', 'type', 'reason', 'is_approved', 'comments']);
            return view('admin_dashboard', compact('users', 'leave', 'late', 'leaves'));
        }
        return view('home');
    }
    public function verify()
    {
        if (Auth::user()->email_verified_at == NULL) {
            return view('verify');
        } else {
            return redirect('');
        }
    }
}
