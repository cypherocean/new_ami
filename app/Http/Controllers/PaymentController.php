<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentAssign;
use App\Models\PaymentReminder;
use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Requests\PaymentRequest;
use App\Imports\PaymentImport;
use App\Models\PreDefinedMessage;
use Auth, Validator, DB, Mail, DataTables, Excel;
use stdClass;

class PaymentController extends Controller {
    /** index */
    public function index(Request $request) {
 
        if ($request->ajax()) {
            $type = $request->type;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $collection = Payment::select('payments.id', 'payments.party_name', 'payments.bill_date', 'payments.balance_amount', 'payments.mobile_no')
                ->whereRaw('payments.id IN (select MAX(id) FROM payments GROUP BY payments.party_name)');

            if ($start_date && $end_date) {
                $collection->whereIn('payments.party_name', function ($query) use ($start_date, $end_date) {
                    $query->select('payments.party_name')
                        ->from(with(new PaymentReminder)->getTable())
                        ->whereBetween('next_date', [$start_date, $end_date]);
                });
            }

            if ($type && $type == 'assigned') {
                $collection->whereIn('payments.party_name', function ($query) {
                    $query->select('party_name')
                        ->from(with(new PaymentAssign)->getTable());
                })
                    ->selectRaw(DB::raw("(CASE WHEN `payment_reminder`.`note` IS NOT NULL THEN `payment_reminder`.`note` ELSE NULL END) AS `note`, (CASE WHEN `u`.`name` IS NOT NULL THEN `u`.`name` ELSE NULL END) AS `reminder`"))
                    ->leftjoin(DB::raw("(SELECT e.*, ROW_NUMBER() OVER (PARTITION BY e.party_name ORDER BY e.party_name) AS party_name_rn FROM payment_assign e) `payment_reminder`"), 'payments.party_name', 'payment_reminder.party_name_rn', DB::raw(1))
                    ->leftjoin(DB::raw("`users` AS `u`"), 'u.id', 'payment_reminder.user_id');

            } elseif ($type && $type == 'not_assigned') {

                $collection->whereNotIn('payments.party_name', function ($query) {
                    $query->select('party_name')
                        ->from(with(new PaymentAssign)->getTable());
                })->addSelect(DB::Raw("null as note"), DB::Raw("null as reminder"));

            } elseif ($type && $type == 'all') {

                $collection->selectRaw(DB::raw("(CASE WHEN `payment_reminder`.`note` IS NOT NULL THEN `payment_reminder`.`note` ELSE NULL END) AS `note`, (CASE WHEN `u`.`name` IS NOT NULL THEN `u`.`name` ELSE NULL END) AS `reminder`"))
                    ->leftjoin(DB::raw("(SELECT e.*, ROW_NUMBER() OVER (PARTITION BY e.party_name ORDER BY e.party_name) AS party_name_rn FROM payment_assign e) `payment_reminder`"), 'payments.party_name', 'payment_reminder.party_name_rn', DB::raw(1))
                    ->leftjoin(DB::raw("`users` AS `u`"), 'u.id', 'payment_reminder.user_id');

            } else {

                $collection->whereIn('payments.party_name', function ($query) use ($type) {
                    $query->select('payments.party_name')
                        ->from(with(new PaymentAssign)->getTable())
                        ->where(['user_id' => $type]);
                })
                    ->selectRaw(DB::raw("(CASE WHEN `payment_reminder`.`note` IS NOT NULL THEN `payment_reminder`.`note` ELSE NULL END) AS `note`, (CASE WHEN `u`.`name` IS NOT NULL THEN `u`.`name` ELSE NULL END) AS `reminder`"))
                    ->leftjoin(DB::raw("(SELECT e.*, ROW_NUMBER() OVER (PARTITION BY e.party_name ORDER BY e.party_name) AS party_name_rn FROM payment_assign e) `payment_reminder`"), 'payments.party_name', 'payment_reminder.party_name_rn', DB::raw(1))
                    ->leftjoin(DB::raw("`users` AS `u`"), 'u.id', 'payment_reminder.user_id');
            }

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
                    $action = '<div class="btn-group">
                                <button type="button" title="Assign reminder" class="btn btn-default btn-xs assignModel" data-name="' . $data->party_name . '" data-id="' . $data->id . '">
                                    <i class="fa fa-plus"></i>
                                </button> &nbsp;
                                <button type="button" title="Bill details" class="btn btn-default btn-xs infoModel" data-name="' . $data->party_name . '" data-id="' . $data->id . '">
                                    <i class="fa fa-file-text"></i>
                                </button> &nbsp;';
                    if ($data->mobile_no != null || $data->mobile_no != '') {
                        $action .= '<a target="_blank" href="https://wa.me/+91' . $data->mobile_no . '?text=' . $get_message . '" title="Send Whatsapp Message" class="btn btn-default btn-xs">
                                    <i class="fa fa-whatsapp" aria-hidden="true"></i>
                                    </a> &nbsp;';
                    }
                    $action .= '</div>';

                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('payment.index');
    }
    /** index */

    /** assign */
    public function assign(Request $request) {
        if (!$request->ajax()) {
            return redirect()->back()->with(['error', 'something went wrong.']);
        }

        $validator = Validator::make(
            ['user' => $request->user, 'party_name' => $request->party_name, 'date' => $request->date, 'note' => $request->note],
            ['user' => 'required', 'party_name' => 'required', 'date' => 'required']
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            DB::beginTransaction();
            // try {
                if ($request->assign_id != '' ||  $request->assign_id != null) {
                    $exst_assign = PaymentAssign::where(['id' => $request->assign_id])->first();

                    $crud = [
                        'user_id' => $request->user,
                        'date' => $request->date,
                        'note' => $request->note ?? NULL,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];
                    $update = PaymentAssign::where(['id' => $request->assign_id])->update($crud);
               
                    if ($update) {
                        $payment_reminder = PaymentReminder::select('id')->where(['party_name' => $request->party_name, 'user_id' => $exst_assign->user_id])->first();

                        $remider_crud = [
                            'user_id' => $request->user,
                            'date' => date('Y-m-d H:i:s'),
                            'next_date' => $request->date ?? date('Y-m-d H:i:s'),
                            'next_time' => '00:00',
                            'note' => $request->note ?? NULL,
                            'amount' => NULL,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                        ];

                        $remider_update = PaymentReminder::where(['id' => $payment_reminder->id])->update($remider_crud);

                        if ($remider_update) {
                            DB::commit();
                            return response()->json(['code' => 200, 'message' => 'User assigned updated successfully']);
                        } else {
                            DB::rollback();
                            return response()->json(['code' => 201, 'message' => 'Failed to update remider']);
                        }
                    } else {
                        DB::rollback();
                        return response()->json(['code' => 202, 'message' => 'Failed to update assign']);
                    }
                } else {
                    $crud = [
                        'user_id' => $request->user,
                        'party_name' => $request->party_name,
                        'date' => $request->date,
                        'note' => $request->note ?? NULL,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth()->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    $last_id = PaymentAssign::insertGetId($crud);

                    if ($last_id) {
                        $payment = Payment::select('mobile_no')->where(['party_name' => $request->party_name])->where('mobile_no', '!=', NULL)->first();

                        $mobile_no = NULL;
                        if ($payment)
                            $mobile_no = $payment->mobile_no;

                        $remider_crud = [
                            'user_id' => $request->user,
                            'party_name' => $request->party_name,
                            'mobile_no' => $request->mobile_no,
                            'date' => date('Y-m-d H:i:s'),
                            'next_date' => $request->date ?? date('Y-m-d H:i:s'),
                            'next_time' => '00:00',
                            'is_last' => 'y',
                            'note' => $request->note ?? NULL,
                            'amount' => NULL,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                        ];

                        $remider_id = PaymentReminder::insertGetId($remider_crud);

                        if ($remider_id) {
                            DB::commit();
                            return response()->json(['code' => 200, 'message' => 'User assigned successfully']);
                        } else {
                            DB::rollback();
                            return response()->json(['code' => 201, 'message' => 'Failed to insert reminder']);
                        }
                    } else {
                        DB::rollback();
                        return response()->json(['code' => 202, 'message' => 'Failed to assign user']);
                    }
                }
            // } catch (\Exception $e) {
            //     DB::rollback();
            //     return response()->json(['code' => 203, 'message' => 'Something went wrong']);
            // }
        }
    }
    /** assign */

    /** import-view */
    public function file_import() {
        return view('payment.import');
    }
    /** import-view */

    /** import */
    public function import(PaymentRequest $request) {
        DB::table('payments')->truncate();
        DB::statement("ALTER TABLE payments AUTO_INCREMENT = 1");

        Excel::import(new PaymentImport, $request->file('file'));

        return redirect()->route('payment');
    }
    /** import */

    /** assigned-users */
    public function assigned_users(Request $request) {
        $options = '<option value="all">All</option><option value="assigned">Assigned</option><option value="not_assigned">Not Assigned</option>';
        $data = PaymentAssign::select('users.id', 'users.name')->leftjoin('users', 'users.id', 'payment_assign.user_id')->groupBy('payment_assign.user_id')->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $row) {
                $options .= "<option value='$row->id'>$row->name</option>";
            }
        }
        return response()->json(['code' => 200, 'data' => $options]);
    }
    /** assigned-users */

    public function infoModel(Request $request) {
        if ($request->has('id')) {
            $data = new stdClass;
            $data->id = $request->id;
            $data->name = $request->name;
            $rec = Payment::select('bill_no', 'bill_date', 'bill_amount')->where(['party_name' => $data->name])->get();
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
                    $info .= "<tr>
                                        <td>$i</td>
                                        <td>$r->bill_no</td>
                                        <td>$r->bill_date</td>
                                        <td>$r->bill_amount</td>
                                    </tr>";
                    $i++;
                }
            }

            $info .= "</tbody></table>";
            $response = view('myModels.infoModel')->with(['info' => $info, 'data' => $data])->render();
            return response()->json(['status' => 200, "message" => "Data found", 'data' => $response]);
        } else {
            return response()->json(['status' => 404, "message" => "No data found"]);
        }
    }

    public function assignModel(Request $request) {
        if ($request->has('id')) {
            $data = new stdClass;
            $data->id = $request->id;
            $data->name = $request->name;
            $assigned = PaymentAssign::select('id', 'note', 'user_id', 'date')->where(['party_name' => $data->name])->orderBy('id', 'desc')->first();

            $user_id = null;
            $note = null;
            $date = date("Y-m-d");
            $assign_id = null;
            if ($assigned) {
                $user_id = $assigned->user_id;
                $note = $assigned->note;
                $date = $assigned->date;
                $assign_id = $assigned->id;
            }

            $users = User::select('id', 'name')->where(['is_admin' => 'n', 'status' => 'active'])->get();

            $usersList = '<option value="">Selet user</option>';
            if ($users->isNotEmpty()) {
                foreach ($users as $u) {
                    $select = '';
                    if ($u->id == $user_id)
                        $select = 'selected';

                    $usersList .= "<option value='$u->id' $select>$u->name</option>";
                }
            }

            $form = "<div class='row'>
                                <input type='hidden' value='$data->name' id='party_name$data->id' />
                                <input type='hidden' value='$assign_id' name='assign_id' id='assign_id$data->id' />
                                <div class='form-group col-sm-12'>
                                    <label for='date$data->id'>Date <span class='text-danger'>*</span></label>
                                    <input type='date'  style='max-width: 90%;' name='date$data->id' id='date$data->id' class='form-control date' placeholder='Plese enter date' value='$date'/>
                                    <span class='kt-form__help error date$data->id'></span>
                                </div>
                                <div class='form-group col-sm-12'>
                                    <label for='user$data->id'>User <span class='text-danger'>*</span></label>
                                    <select name='user$data->id' id='user$data->id' class='form-control' style='max-width: 90%;'>
                                        $usersList
                                    </select>
                                    <span class='kt-form__help error user$data->id'></span>
                                </div>
                                <div class='form-group col-sm-12'>
                                    <label for='note'>Note </label>
                                    <textarea type='note' name='note$data->id' id='note$data->id' class='form-control' style='max-width: 90%;'/>$note</textarea>
                                    <span class='kt-form__help error note$data->id'></span>
                                </div>
                            </div>";
            $response = view('myModels.assignModel')->with(['form' => $form, 'data' => $data])->render();
            return response()->json(['status' => 200, "message" => "Reminder found", 'data' => $response]);
        } else {
            return response()->json(['status' => 404, "message" => "No data found"]);
        }
    }
}
