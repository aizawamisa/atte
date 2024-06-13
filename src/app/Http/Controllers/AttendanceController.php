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
            $endWorkTime = Carbon::now();
            $startWorkTime = Carbon::parse($work->start_work);

            if ($endWorkTime->format('Y-m-d') !== $startWorkTime->format('Y-m-d')) {
                $endOfStartDay = $startWorkTime->copy()->endOfDay();
                $startOfEndDay = $endWorkTime->copy()->startOfDay();

                $work->update(['end_work' => $endOfStartDay->format('Y-m-d H:i:s')]);

                $newWork = Work::create([
                    'user_id' => $user->id,
                    'start_work' => $startOfEndDay->format('Y-m-d H:i:s'),
                    'end_work' => $endWorkTime->format('Y-m-d H:i:s')
                ]);

                $this->createRestsForWork($work);
                $this->createRestsForWork($newWork);
            } else {
                $work->update(['end_work' => $endWorkTime->format('Y-m-d H:i:s')]);
                $this->createRestsForWork($work);
            }

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

    public function indexDate(Request $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $displayDate = Carbon::parse($date);

        $attendances = $this->fetchAttendancesByDate($displayDate);

        return view('attendance_date', compact('attendances', 'displayDate'));
    }

    public function perDate(Request $request)
    {
        $displayDate = Carbon::parse($request->input('displayDate'));

        if ($request->has('prevDate')) {
            $displayDate->subDay();
        }

        if ($request->has('nextDate')) {
            $displayDate->addDay();
        }

        $attendances = $this->fetchAttendancesByDate($displayDate);

        return view('attendance_date', compact('attendances', 'displayDate'));
    }

    protected function fetchAttendancesByDate(Carbon $displayDate)
    {
        $attendancesQuery = DB::table('works')
            ->join('users', 'works.user_id', '=', 'users.id')
            ->leftJoin('rests', 'works.id', '=', 'rests.work_id')
            ->where(function ($query) use ($displayDate) {
                $query->whereDate('works.start_work', $displayDate->format('Y-m-d'))
                    ->orWhereDate('works.end_work', $displayDate->format('Y-m-d'))
                    ->orWhere(function ($query) use ($displayDate) {
                        $query->whereDate('works.start_work', '<=', $displayDate->format('Y-m-d'))
                            ->whereDate('works.end_work', '>=', $displayDate->format('Y-m-d'));
                    });
            })
            ->select(
                'users.name',
                'works.id',
                'works.start_work',
                'works.end_work'
            );

        $attendances = $attendancesQuery->paginate(5);

        $attendances->getCollection()->transform(function ($attendance) use ($displayDate) {
            $this->formatAttendance($attendance, $displayDate);
            return $attendance;
        });

        return $attendances;
    }

    protected function formatAttendance($attendance, $displayDate)
    {
        $startWork = Carbon::parse($attendance->start_work);
        $endWork = Carbon::parse($attendance->end_work);

        if ($startWork->format('Y-m-d') != $endWork->format('Y-m-d')) {
            if ($displayDate->format('Y-m-d') == $startWork->format('Y-m-d')) {
                $attendance->start_work = $startWork;
                $attendance->end_work = $startWork->copy()->endOfDay();
                $attendance->total_work = $startWork->diffInMinutes($startWork->copy()->endOfDay());
            } elseif ($displayDate->format('Y-m-d') == $endWork->format('Y-m-d')) {
                $attendance->start_work = $endWork->copy()->startOfDay();
                $attendance->end_work = $endWork;
                $attendance->total_work = $endWork->diffInMinutes($endWork->copy()->startOfDay());
            } else {
                $attendance->start_work = $startWork->copy()->startOfDay();
                $attendance->end_work = $startWork->copy()->endOfDay();
                $attendance->total_work = $startWork->copy()->diffInMinutes($startWork->copy()->endOfDay());
            }
        } else {
            $attendance->start_work = $startWork;
            $attendance->end_work = $endWork;
            $attendance->total_work = $startWork->diffInMinutes($endWork);
        }

        $attendance->total_rest = DB::table('rests')
            ->where('work_id', $attendance->id)
            ->when($startWork->format('Y-m-d') != $endWork->format('Y-m-d'), function ($query) use ($startWork, $endWork, $attendance) {
                $query->whereDate('start_rest', '>=', $startWork->format('Y-m-d'))
                    ->whereDate('end_rest', '<=', $endWork->format('Y-m-d'));
            })
            ->sum(DB::raw('TIMESTAMPDIFF(MINUTE, start_rest, end_rest)')); 

        $attendance->total_work -= $attendance->total_rest;
    }

        protected function createRestsForWork($work)
    {
        $startWork = Carbon::parse($work->start_work);
        $endWork = Carbon::parse($work->end_work);

        $current = $startWork->copy();
        $rests = [];

        $existingRests = Rest::where('work_id', $work->id)->get();

        foreach ($existingRests as $existingRest) {
            $startRest = Carbon::parse($existingRest->start_rest);
            $endRest = Carbon::parse($existingRest->end_rest);

                if ($startRest >= $startWork && $endRest <= $endWork) {
                if ($startRest > $current) {
                    $rests[] = [
                        'start_rest' => $current->toDateTimeString(),
                        'end_rest' => $startRest->toDateTimeString(),
                        'work_id' => $work->id,
                    ];
                }
                $current = $endRest;
            }
        }

        if ($current < $endWork) {
            $rests[] = [
                'start_rest' => $current->toDateTimeString(),
                'end_rest' => $endWork->toDateTimeString(),
                'work_id' => $work->id,
            ];
        }

        foreach ($rests as $rest) {
            Rest::create($rest);
        }
    }
}
