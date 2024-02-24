<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreDefinedMessageRequest;
use App\Models\PreDefinedMessage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PreDefinedMessageController extends Controller {
    /** index */
    public function index(Request $request) {
        if ($request->ajax()) {
            $data = PreDefinedMessage::all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return ' <div class="btn-group">
                                                    <a href="' . route('pre_defined_message.view', ['id' => base64_encode($data->id)]) . '" class="btn btn-default btn-xs">
                                                        <i class="fa fa-eye"></i>
                                                    </a> &nbsp;
                                                    <a href="' . route('pre_defined_message.edit', ['id' => base64_encode($data->id)]) . '" class="btn btn-default btn-xs">
                                                        <i class="fa fa-edit"></i>
                                                    </a> &nbsp;
                                                    <a href="javascript:;" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                        <i class="fa fa-bars"></i>
                                                    </a> &nbsp;
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="active" data-old_status="' . $data->status . '" data-id="' . base64_encode($data->id) . '">Active</a></li>
                                                        <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="inactive" data-old_status="' . $data->status . '" data-id="' . base64_encode($data->id) . '">Inactive</a></li>
                                                        <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="deleted" data-old_status="' . $data->status . '" data-id="' . base64_encode($data->id) . '">Delete</a></li>
                                                    </ul>
                                                </div>';
                })

                ->editColumn('status', function ($data) {
                    if ($data->status == 'active')
                        return '<span class="badge badge-pill badge-success">Active</span>';
                    else if ($data->status == 'inactive')
                        return '<span class="badge badge-pill badge-warning">Inactive</span>';
                    else if ($data->status == 'deleted')
                        return '<span class="badge badge-pill badge-danger">Delete</span>';
                    else
                        return '-';
                })

                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('preDefineMessage.index');
    }
    /** index */

    /** create */
    public function create(Request $request) {
        return view('preDefineMessage.create');
    }
    /** create */

    /** insert */
    public function insert(PreDefinedMessageRequest $request) {
        if ($request->ajax()) {
            return true;
        }
        $message = PreDefinedMessage::first();
        $crud = [
            'message' => $request->message,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => auth()->user()->id,
        ];
         
        if ($message) {
            $update = PreDefinedMessage::where(['id' => $message->id])->update($crud);
            if ($update){
                return redirect()->route('pre_defined_message')->with('success', 'Message updated successfully.');
            }else{
                return redirect()->back()->with('error', 'Faild to update message!')->withInput();
            }
        }else{
            $last_id = PreDefinedMessage::insertGetId($crud);
            if ($last_id) {
                return redirect()->route('pre_defined_message')->with('success', 'Pre-define Message created successfully.');
            } else {
                return redirect()->back()->with('error', 'Faild to create Pre-define Message!')->withInput();
            }
        }



    }
    /** insert */

    /** view */
    public function view(Request $request, $id = '') {
        if ($id == '')
            return redirect()->route('pre_defined_message')->with('error', 'Something went wrong');

        $id = base64_decode($id);

        $data = PreDefinedMessage::find($id);

        if ($data) {
            return view('preDefineMessage.view')->with('data', $data);
        } else {
            return redirect()->route('pre_defined_message')->with('error', 'No Message found');
        }
    }
    /** view */

    /** edit */
    public function edit(Request $request, $id = '') {
        if ($id == '')
            return redirect()->route('pre_defined_message')->with('error', 'Something went wrong');

        $id = base64_decode($id);

        $data = PreDefinedMessage::find($id);

        if ($data) {
            return view('preDefineMessage.edit')->with('data', $data);
        } else {
            return redirect()->route('pre_defined_message')->with('error', 'No Message found');
        }
    }
    /** edit */

    /** update */
    public function update(PreDefinedMessageRequest $request) {
        if ($request->ajax()) {
            return true;
        }

        $message = PreDefinedMessage::first();
        $crud = [
            'message' => $request->message,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => auth()->user()->id
        ];
        if ($message) {
            dd("if");
            $update = PreDefinedMessage::where(['id' => $request->id])->update($crud);
            if ($update){
                return redirect()->route('pre_defined_message')->with('success', 'Message updated successfully.');
            }else{
                return redirect()->back()->with('error', 'Faild to update message!')->withInput();
            }
        } else {
            dd("else");
            $last_id = PreDefinedMessage::insertGetId($crud);
            if($last_id){
                return redirect()->route('pre_defined_message')->with('success', 'Message updated successfully.');
            }else{
                return redirect()->back()->with('error', 'Faild to update Message!')->withInput();
            }
        }
    }
    /** update */

    /** change-status */
    public function change_status(Request $request) {
        if (!$request->ajax()) {
            exit('No direct script access allowed');
        }

        if (!empty($request->all())) {
            $id = base64_decode($request->id);
            $status = $request->status;

            $data = PreDefinedMessage::where(['id' => $id])->first();
            if (!empty($data)) {
                $update = PreDefinedMessage::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                if ($update){
                    return response()->json(['code' => 200]);
                }else{
                    return response()->json(['code' => 201]);
                }
            } else {
                return response()->json(['code' => 201]);
            }
        } else {
            return response()->json(['code' => 201]);
        }
    }
    /** change-status */
}
