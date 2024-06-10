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
    public function punch()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $now_date = Carbon::now()->format('Y-m-d');
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

    //  日別一覧表示
    public function indexDate(Request $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $displayDate = Carbon::parse($date)->format('Y-m-d');


        $attendances = DB::table('works')
        ->join('users', 'works.user_id', '=', 'users.id')
        ->leftJoin('rests', 'works.id', '=', 'rests.work_id')
        ->whereDate('works.start_work', $displayDate)
        ->select(
            'users.name',
            'works.start_work',
            'works.end_work',
            DB::raw('TIMESTAMPDIFF(MINUTE, works.start_work, works.end_work) AS work_minutes'),
            DB::raw('COALESCE(SUM(TIMESTAMPDIFF(MINUTE, rests.start_rest, rests.end_rest)), 0) AS total_rest'),
            DB::raw('TIMESTAMPDIFF(MINUTE, works.start_work, works.end_work) - COALESCE(SUM(TIMESTAMPDIFF(MINUTE, rests.start_rest, rests.end_rest)), 0) AS total_work')
        )
         ->groupBy('works.id', 'users.name', 'works.start_work', 'works.end_work')
        ->paginate(5);

        return view('attendance_date', compact('attendances', 'displayDate'));
    }

    // 日別一覧 / 抽出処理
    public function perDate(Request $request)
    {
        $displayDate = Carbon::parse($request->input('displayDate'));

        if ($request->has('prevDate')) {
            $displayDate->subDay();
        }

        if ($request->has('nextDate')) {
            $displayDate->addDay();
        }

        $attendances = DB::table('works')
            ->join('users', 'works.user_id', '=', 'users.id')
            ->leftJoin('rests', 'works.id', '=', 'rests.work_id')
            ->whereDate('works.start_work', $displayDate)
            ->select(
            'users.name',
            'works.start_work',
            'works.end_work',
            DB::raw('TIMESTAMPDIFF(MINUTE, works.start_work, works.end_work) AS work_minutes'),
            DB::raw('COALESCE(SUM(TIMESTAMPDIFF(MINUTE, rests.start_rest, rests.end_rest)), 0) AS total_rest'),
            DB::raw('TIMESTAMPDIFF(MINUTE, works.start_work, works.end_work) - COALESCE(SUM(TIMESTAMPDIFF(MINUTE, rests.start_rest, rests.end_rest)), 0) AS total_work')
            )
            ->groupBy('works.id', 'users.name', 'works.start_work', 'works.end_work')
            ->paginate(5);

        return view('attendance_date', compact('attendances', 'displayDate'));
    }

}
