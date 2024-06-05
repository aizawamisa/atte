<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    // ホーム画面表示
    public function punch()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $now_date = Carbon::now()->format('Y-m-d H:i:s');
        $user_id = $user->id;

        $confirm_date = Work::where('user_id', $user_id)
            ->whereDate('start_work', $now_date)
            ->first();

        if (!$confirm_date) {
            $status = 0;
        } else {
            $status = $user->status;
        }
        return view('index', compact('status'));
    }

    // 勤務開始
    public function startWork()
    {
        $user = Auth::user();

        if ($user->status != 0) {
            return redirect()->route('index');
        }

        Work::create([
            'user_id' => $user->id,
            'start_work' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        $user->status = 1; // 勤務中
        $user->save();

        return redirect()->route('index');
    }

    // 勤務終了
    public function endWork()
    {
        $user = Auth::user();

        if ($user->status != 1) {
            return redirect()->route('index');
        }

        $work = Work::where('user_id', $user->id)->latest()->first();

        if ($work) {
            $work->update(['end_work' => Carbon::now()->format('Y-m-d H:i:s')]);

            $user->status = 0; // 勤務前にリセット
            $user->save();
        }

        return redirect()->route('index');
    }

    // 休憩開始
    public function startRest()
    {
        $user = Auth::user();

        if ($user->status != 1) {
            return redirect()->route('index');
        }

        $work = Work::where('user_id', $user->id)->latest()->first();

        if ($work) {
            Rest::create([
                'work_id' => $work->id,
                'start_rest' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            $user->status = 2; // 休憩中
            $user->save();
        }

        return redirect()->route('index');
    }

    // 休憩終了
    public function endRest()
    {
        $user = Auth::user();

        if ($user->status != 2) {
            return redirect()->route('index');
        }

        $rest = Rest::whereHas('work', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->first();

        if ($rest) {
            $rest->update(['end_rest' => Carbon::now()->format('Y-m-d H:i:s')]);

            $user->status = 1; // 勤務中
            $user->save();
        }

        return redirect()->route('index');
    }

    // 日別一覧表示
    public function indexDate(Request $request)
    {
        $displayDate = Carbon::now();

        $users = DB::table('attendance_view_table')
            ->whereDate('date', $displayDate)
            ->paginate(5);

        return view('attendance_date', compact('users', 'displayDate'));
    }

 
}
