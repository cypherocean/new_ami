<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\PaymentAssign;
use App\Models\PaymentReminder;
use App\Models\PreDefinedMessage;
use Illuminate\Support\Str;
use Auth, Validator, DB, Mail, DataTables, File;
use stdClass;

class PaymentReminderController extends Controller {
    /** index */
    public function index(Request $request) {
        if ($request->ajax()) {
            $date = $request->date ?? 'today';
            DB::enableQueryLog();
            $collection = PaymentReminder::select(
                'payment_reminder.id',
                'payment_reminder.party_name',
                'payment_reminder.mobile_no',
                'payment_reminder.date',
                'payment_reminder.next_date',
                'payment_reminder.amount',
                'payment_reminder.note',
                'u.name as user_name'
            )
                ->leftjoin('users as u', 'payment_reminder.user_id', 'u.id');

            $collection->whereIn('payment_reminder.party_name', function ($query) {
                $query->select('party_name')
                    ->from(with(new Payment)->getTable());
            });

            if ($date == 'today') {
                $collection->whereDate('payment_reminder.next_date', '=', date('Y-m-d'));
            } else {
                $collection->whereDate('payment_reminder.next_date', '!=', date('Y-m-d'));
            }

            $collection->where(['payment_reminder.is_last' => 'y']);

            $data = $collection->get();
            $get_message = PreDefinedMessage::where('status', 'active')->first('message');
            if (!$get_message) {
                $get_message = 'N/A';
            } else {
                $get_message = $get_message->message;
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) use ($get_message) {
                    $action = ' <div class="btn-group">
                                    <button type="button" title="Add followup" class="btn btn-default btn-xs add_followup" data-id="' . $data->id . '" data-toggle="modal" data-target="#followup' . $data->id . '">
                                        <i class="fa fa-plus"></i>
                                    </button> &nbsp;
                                    <button type="button" title="Followup details" class="btn btn-default btn-xs followup_detail" data-name="' . $data->party_name . '" data-id="' . $data->id . '">
                                        <i class="fa fa-exclamation-circle"></i>
                                    </button> &nbsp;
                                    <button type="button" title="Bill details" class="btn btn-default btn-xs billDetails" data-name="' . $data->party_name . '" data-id="' . $data->id . '">
                                        <i class="fa fa-file-text"></i>
                                    </button> &nbsp;
                                    <a href="javascript:;" title="Delete record" class="btn btn-default btn-xs" onclick="change_status(this);" data-name="' . $data->party_name . '" data-status="deleted" data-id="' . base64_encode($data->id) . '">
                                        <i class="fa fa-trash"></i>
                                    </a> &nbsp;';
                    if ($data->mobile_no != null || $data->mobile_no != '') {
                        $action .= '<a target="_blank" href="https://wa.me/+91' . $data->mobile_no . '?text=' . $get_message . '" title="Send Whatsapp message" class="btn btn-default btn-xs" onclick="change_status(this);" data-name="' . $data->party_name . '" data-status="deleted" data-id="' . base64_encode($data->id) . '">
                                <i class="fa fa-whatsapp" aria-hidden="true"></i>
                            </a> &nbsp;';
                    }
                    return $action;
                })

                ->editColumn('next_date', function ($data) {
                    return date('d-m-Y', strtotime($data->next_date));
                })

                ->editColumn('date', function ($data) {
                    return date('d-m-Y', strtotime($data->date));
                })

                ->rawColumns(['action', 'next_date', 'date'])
                ->make(true);
        }

        return view('payment_reminder.index');
    }
    /** index */

    /** insert */
    public function insert(Request $request) {
        if (!$request->ajax()) {
            return true;
        }

        $validator = Validator::make(
            ['party_name' => $request->party_name, 'next_date' => $request->next_date],
            ['party_name' => 'required', 'next_date' => 'required']
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            if (!empty($request->all())) {
                DB::beginTransaction();
                try {
                    $update = PaymentReminder::where(['party_name' => $request->party_name])->update(['is_last' => 'n']);

                    if ($update) {
                        $crud = [
                            'user_id' => auth()->user()->id,
                            'party_name' => $request->party_name,
                            'note' => $request->note ?? NULL,
                            'mobile_no' => $request->mobile_no ?? NULL,
                            'date' => date('Y-m-d'),
                            'next_date' => $request->next_date,
                            'next_time' => $request->next_time ?? NULL,
                            'is_last' => 'y',
                            'amount' => $request->amount ?? NULL,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                        ];

                        $last_id = PaymentReminder::insertGetId($crud);

                        if ($last_id) {
                            DB::commit();
                            return response()->json(['code' => 200, 'message' => 'Record added successfully']);
                        } else {
                            DB::rollback();
                            return response()->json(['code' => 201, 'message' => 'Failed to add record']);
                        }
                    } else {
                        DB::rollback();
                        return response()->json(['code' => 201, 'message' => 'Failed to chagne last record']);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['code' => 201, 'message' => 'Something went wrong']);
                }
            } else {
                return response()->json(['code' => 201, 'message' => 'Something went wrong']);
            }
        }
    }
    /** insert */

    /** change-status */
    public function change_status(Request $request) {
        if (!$request->ajax()) {
            exit('No direct script access allowed');
        }

        if (!empty($request->all())) {
            $id = base64_decode($request->id);
            $status = $request->status;
            $name = $request->name;

            DB::beginTransaction();
            try {
                $paymentDelete = Payment::where(['party_name' => $name])->delete();
                if ($paymentDelete) {
                    $assignDelete = PaymentAssign::where(['party_name' => $name])->delete();
                    if ($assignDelete) {
                        $reminderDelete = PaymentReminder::where(['party_name' => $name])->delete();
                        if ($reminderDelete) {
                            DB::commit();
                            return response()->json(['code' => 200]);
                        } else {
                            DB::rollback();
                            return response()->json(['code' => 201]);
                        }
                    } else {
                        DB::rollback();
                        return response()->json(['code' => 201]);
                    }
                } else {
                    DB::rollback();
                    return response()->json(['code' => 201]);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['code' => 201]);
            }
        } else {
            return response()->json(['code' => 201]);
        }
    }
    /** change-status */

    /** reports */
    public function reports(Request $request) {
        $data = PaymentReminder::select('payment_reminder.id', 'payment_reminder.party_name', DB::Raw("SUBSTRING(" . 'payment_reminder.note' . ", 1, 30) as note"), 'payment_reminder.next_date', 'u.name as user_name')
            ->leftjoin('users as u', 'u.id', 'payment_reminder.user_id')
            ->paginate(10);

        $view = view('payment_reminder.report', compact('data'))->render();
        $pagination = view('payment_reminder.report_pagination', compact('data'))->render();

        return response()->json(['success' => true, 'data' => $view, 'pagination' => $pagination]);
    }
    /** reports */

    /** reports */
    public function addFollowup(Request $request) {
        $id = $request->id;
        if ($id) {
            $data = PaymentReminder::find($id);
            if ($data) {
                $title = "Add Followup";
                $form = "<div class='row'>
                            <input type='hidden' value='$data->party_name' id='party_name' />
                            <div class='form-group col-sm-12'>
                                <label for='note'>Note </label>
                                <textarea type='note' name='note' id='note' class='form-control' style='max-width: 90%;'/></textarea>
                                <span class='kt-form__help error note'></span>
                            </div>
                            <div class='form-group col-sm-12'>
                                <label for='next_date'>Next date <span class='text-danger'>*</span></label>
                                <input type='date' name='next_date' id='next_date' class='form-control' value='" . Date('Y-m-d') . "' style='max-width: 90%;'>
                                <span class='kt-form__help error next_date'></span>
                            </div>
                            <div class='form-group col-sm-12'>
                                <label for='mobile_no'>Mobile no </label>
                                <input type='text' name='mobile_no' id='mobile_no' class='form-control digit' style='max-width: 90%;'/>
                                <span class='kt-form__help error mobile_no'></span>
                            </div>
                            <div class='form-group col-sm-12'>
                                <label for='amount'>Amount </label>
                                <input type='text' name='amount' id='amount' class='form-control digit' style='max-width: 90%;'/>
                                <span class='kt-form__help error amount'></span>
                            </div>
                        </div>";
                $data = view('myModels.followup')->with(['form' => $form, 'title' => $title])->render();
                return response()->json(['status' => 200, 'data' => $data]);
            } else {
                return response()->json(['status' => 404, "message" => "No reminder found"]);
            }
        } else {
            return response()->json(['status' => 404, "message" => "No id found"]);
        }
    }
    /** reports */

    public function followupDetails(Request $request) {
        if ($request->has('name')) {
            $data = new stdClass;
            if ($request->has('id')) {
                $data->id = $request->id;
                $data->name = $request->name;
            }
            $reminders = PaymentReminder::select(
                'payment_reminder.id',
                'payment_reminder.date',
                'payment_reminder.amount',
                'payment_reminder.next_date',
                'payment_reminder.next_time',
                'payment_reminder.note',
                'u.name as user_name'
            )
                ->leftjoin('users as u', 'payment_reminder.user_id', 'u.id')
                ->where(['payment_reminder.party_name' => $request->name])
                ->get();

            $details = '';
            if ($reminders->isNotEmpty()) {
                $details .= "<ul class='media-list media-list-divider m-0'>";
                foreach ($reminders as $row) {
                    $details .= "<li class='media followup_details'>
                                    <div class='media-body'>
                                        <div class='media-heading'>
                                            $row->user_name
                                            <span class='font-13 float-right'>$row->date</span>
                                        </div>
                                        <div class='font-13'>$row->note</div>
                                        <div class='font-13 text-danger'>Next Follow-up On $row->next_date $row->next_time</div>
                                    </div>
                                </li>
                                <br/>";
                }
                $details .= "</ul>";
            } else {
                $details = '<div class="row"><div class="col-sm-12 text-center"><h1>No Reminders Yet</h1></div></div>';
            }
            $response = view('myModels.followup_details')->with(['details' => $details, 'data' => $data])->render();
            return response()->json(['status' => 200, "message" => "Reminder found", 'data' => $response]);
        } else {
            return response()->json(['status' => 404, "message" => "No reminder found"]);
        }
    }

    public function billDetails(Request $request) {
        if ($request->has('name')) {
            $data = new stdClass;
            if ($request->has('id')) {
                $data->id = $request->id;
                $data->name = $request->name;
            }
            $rec = Payment::select('bill_no', 'bill_date', 'bill_amount')->where(['party_name' => $request->name])->get();
    
            $info = "<table class='table table-bordered'>
                            <thead class='thead-default'>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Bill No</th>
                                    <th>Bill Date</th>
                                    <th>Bill Amount</th>
                                </tr>
                            </thead>
                            <tbody>";
    
            if ($rec->isNotEmpty()) {
                $i = 1;
                foreach ($rec as $r) {
                    $info .=
                        "<tr>
                                        <td>$i</td>
                                        <td>$r->bill_no</td>
                                        <td>$r->bill_date</td>
                                        <td>$r->bill_amount</td>
                                    </tr>";
                    $i++;
                }
            }
            $info .= "</tbody></table>";
            $response = view('myModels.billInfoModel')->with(['info' => $info,'data' => $data])->render();
            return response()->json(['status' => 200, "message" => "Data found", 'data' => $response]);
        } else {
            return response()->json(['status' => 404, "message" => "No data found"]);
        }
    }
}
