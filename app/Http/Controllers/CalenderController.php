<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CalendarRequest;
use App\Models\Calendar;
use App\Models\User;
use Carbon\Carbon;

class CalenderController extends Controller {
    public function index() {
        $users = User::get(['id', 'name']);
        $calendar = Calendar::all(['id', 'title', 'start_date AS start' ,'end_date AS end'])->toJson();
        return view('calender.index', compact('calendar', 'users'));
    }

    public function insert(CalendarRequest $request) {
        if ($request->has('eventID')) {
            $calendar = Calendar::find($request->eventID);
        } else {
            $calendar = new Calendar;
            $calendar->created_at = Carbon::now()->setTimezone("Asia/Kolkata")->format("Y-m-d H:i:s");
        }

        $calendar->user_id = $request->users;
        $calendar->title = $request->title;
        $calendar->event_description = $request->eventDescription;
        $calendar->start_date = $request->start_time;
        $calendar->end_date = $request->end_time;
        $calendar->updated_at = Carbon::now()->setTimezone("Asia/Kolkata")->format("Y-m-d H:i:s");

        if ($calendar->save()) {
            return response()->json(['status' => 200, 'message' => 'Record Inserted Successfuly', "data" => $calendar]);
        } else {
            return response()->json(['status' => 422, 'message' => 'Failed to insert record!']);
        }
    }

    public function update(Request $request) {
        if ($request->has('id')) {
            $id = $request->id;
            $calendarData = Calendar::find($id);
            if ($calendarData) {
                $calendarData->start_date = $request->start_time;
                $calendarData->end_date = $request->end_time;
                $calendarData->updated_at = Carbon::now()->setTimezone("Asia/Kolkata")->format("Y-m-d H:i:s");
                if ($calendarData->save()) {
                    return response()->json(['status' => 200, 'message' => 'Event rescheduled successfuly.']);
                } else {
                    return response()->json(['status' => 422, 'message' => 'Faild to reschedule event!']);
                }
            } else {
                return response()->json(['status' => 422, 'message' => 'No Data found!']);
            }
        } else {
            return response()->json(['status' => 422, 'message' => 'Id is required!']);
        }
    }

    public function edit(Request $request, $id) {
        if ($id) {
            $calendar = Calendar::find($id);
            if ($calendar) {
                return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $calendar]);
            } else {
                return response()->json(['status' => 422, 'message' => 'No Data found!']);
            }
        } else {
            return response()->json(['status' => 422, 'message' => 'Id is required!']);
        }
    }

    public function fetchEvents() {
        $calendar = Calendar::all(['id', 'title', 'start_date AS start' ,'end_date AS end'])->toJson();
        return response()->json(['status' => 200, "calendar" => $calendar]);
    }
}
