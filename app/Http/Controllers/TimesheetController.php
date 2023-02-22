<?php

namespace App\Http\Controllers;

use App\Http\Requests\Timesheets\StoreTimesheetRequest;
use App\Http\Requests\Timesheets\UpdateTimesheetRequest;
use App\Models\Task;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class TimesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role == 0) {
            $timesheet = Timesheet::has('tasks')->has('creator')->where('user_id', $user->id)->get();
        } elseif ($user->role == 1) {
            $timesheet = Timesheet::has('tasks')->get();
        } elseif ($user->role == 2) {
            $timesheet = Timesheet::has('tasks')->has('creator')->where('manager_id', $user->id)->orWhere('user_id', $user->id)->get();
        }
        return view('timesheet', ['timesheet' => $timesheet]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Timesheets\StoreTimesheetRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTimesheetRequest $request)
    {
        $req = $request->validated();
        $check = Timesheet::where('date', Carbon::now()->toDateString())->where('user_id', Auth::user()->id)->get();
        if (!empty($check[0])) {
            return Redirect::back()->withErrors('cannot create timesheet');
        }

        $user = Auth::user();
        $timesheet = $user->timesheets()->create([
            'difficult' => $req['difficult'],
            'schedule' => $req['schedule'],
            'date' => Carbon::now(),
            'manager_id' => $user->manager_id ?? 0
        ]);
        foreach ($request->task as $index => $task) {
            $timesheet->tasks()->create([
                'task_id' => $index,
                'content' => $task
            ]);
        }
        return Redirect::route('timesheet');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Timesheet  $timesheet
     * @return \Illuminate\Http\Response
     */
    public function show(Timesheet $timesheet)
    {

        return view('timesheet-detail', ['timesheet' => $timesheet]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Timesheet $timesheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Timesheet $timesheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Timesheets\UpdateTimesheetRequest $request
     * @param \App\Models\Timesheet $timesheet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Timesheet $timesheet, UpdateTimesheetRequest $request)
    {
        $this->authorize('update', $timesheet);

        $req = $request->validated();
        $tasks = Task::where('timesheet_id', $timesheet->id)->get();
        foreach ($tasks as $task) {
            $task->delete();
        }

        Timesheet::find($timesheet->id)->update([
            'difficult' => $req['difficult'],
            'schedule' => $req['schedule'],
        ]);

        foreach ($request->task as $index => $task) {
            $tasks[$index] = Task::create([
                'task_id' => $index,
                'content' => $task,
                'timesheet_id' => $timesheet->id
            ]);
        }
        return Redirect::route('timesheet');

    }

    public function updateStatus(Timesheet $timesheet, UpdateTimesheetRequest $request)
    {
        $this->authorize('updateStatus', $timesheet);
        $req = $request->validated();

        Timesheet::find($timesheet->id)->update([
            'status' => $req['status'],
        ]);

        return Redirect::route('timesheet');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Timesheet $timesheet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Timesheet $timesheet)
    {
        $timesheet->delete();
        return Redirect::route('timesheet');
    }
}
