<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Strip;
    use App\Models\User;
    use Illuminate\Support\Str;
    use App\Http\Requests\StripsRequest;
    use Auth, Validator, DB, Mail, DataTables, File;

    class StripsController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Strip::select('id', 'name', 'quantity', 'unit', 'choke', 'price', 'file')->orderBy('id','desc')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('strips.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;
                                                <a href="'.route('strips.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a> &nbsp;
                                                <a href="'.route('strips.delete', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </a> &nbsp;
                                            </div>';
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'active')
                                    return '<span class="badge badge-pill badge-success">Active</span>';
                                else if($data->status == 'inactive')
                                    return '<span class="badge badge-pill badge-warning">Inactive</span>';
                                else if($data->status == 'deleted')
                                    return '<span class="badge badge-pill badge-danger">Delete</span>';
                                else
                                    return '-';
                            })

                            ->editColumn('file' ,function($data){
                                if($data->file == '' || $data->file == null){
                                    return '<img src="'.url('/uploads/strips/default.png').'" alt="Default image" style="height: 40px;display: block; margin-left: auto; margin-right:auto;width:50%;">';
                                }else{
                                    return '<img src="'.url('/uploads/strips').'/'.$data->file.'" alt="'.$data->file.'" style="height: 40px;display: block; margin-left: auto; margin-right:auto;width:50%;">';
                                }
                            })

                            ->rawColumns(['action', 'status', 'file'])
                            ->make(true);
                }

                return view('strips.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                return view('strips.create');
            }
        /** create */

        /** insert */
            public function insert(StripsRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $quantity = $request->quantity ?? 0;
                    $price = $request->price ?? 0;
                    $unit = $request->unit;

                    $inch_price = 0;

                    if($unit != 'inch')
                        $inch_price = $price / _converter($unit, $quantity);
                    else
                        $inch_price = $price / $quantity;
                        
                    $inch_price = round($inch_price);

                    $crud = [
                        'name' => ucfirst($request->name),
                        'quantity' => $quantity, 
                        'unit' => $unit, 
                        'choke' => $request->choke ?? NULL, 
                        'amp' => $request->amp ?? NULL, 
                        'price' => $price, 
                        'note' => $request->note ?? NULL,
                        'inch_price' => $inch_price,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth()->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    if(!empty($request->file('file'))){
                        $file = $request->file('file');
                        $filenameWithExtension = $request->file('file')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                        $extension = $request->file('file')->getClientOriginalExtension();
                        $filenameToStore = time()."_".$filename.'.'.$extension;

                        $folder_to_upload = public_path().'/uploads/strips/';

                        if (!File::exists($folder_to_upload))
                            File::makeDirectory($folder_to_upload, 0777, true, true);

                        $crud["file"] = $filenameToStore;
                    }

                    $last_id = Strip::insertGetId($crud);
                    
                    if($last_id){
                        if(!empty($request->file('file')))
                            $file->move($folder_to_upload, $filenameToStore);
                        
                        return redirect()->route('strips')->with('success', 'Strip light created successfully');
                    }else{
                        return redirect()->back()->with('error', 'Faild to create strip light')->withInput();
                    }
                }else{
                    return redirect()->route('strips')->with('error', 'Something went wrong');
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('strips')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Strip::select('id', 'name', 'quantity', 'unit', 'choke', 'amp','price', 'note', 'file')->where(['id' => $id])->first();
                
                if($data)
                    return view('strips.view')->with('data', $data);
                else
                    return redirect()->route('strips')->with('error', 'No strip light found');
            }
        /** view */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('strips')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Strip::select('id', 'name', 'quantity', 'amp','unit', 'choke', 'price', 'note', 'file')->where(['id' => $id])->first();
                
                if($data)
                    return view('strips.edit')->with('data', $data);
                else
                    return redirect()->route('strips')->with('error', 'No strip light found');
            }
        /** edit */ 

        /** update */
            public function update(StripsRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $quantity = $request->quantity ?? 0;
                    $price = $request->price ?? 0;
                    $unit = $request->unit;

                    $inch_price = 0;

                    if($unit != 'inch')
                        $inch_price = $price / _converter($unit, $quantity);
                    else
                        $inch_price = $price / $quantity;
                        
                    $inch_price = round($inch_price);

                    $crud = [
                        'name' => ucfirst($request->name),
                        'quantity' => $quantity, 
                        'unit' => $unit, 
                        'choke' => $request->choke ?? NULL, 
                        'amp' => $request->amp ?? NULL, 
                        'price' => $price, 
                        'note' => $request->note ?? NULL,
                        'inch_price' => $inch_price,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    if(!empty($request->file('file'))){
                        $file = $request->file('file');
                        $filenameWithExtension = $request->file('file')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                        $extension = $request->file('file')->getClientOriginalExtension();
                        $filenameToStore = time()."_".$filename.'.'.$extension;

                        $folder_to_upload = public_path().'/uploads/strips/';

                        if (!File::exists($folder_to_upload))
                            File::makeDirectory($folder_to_upload, 0777, true, true);

                        $crud["file"] = $filenameToStore;
                    }

                    $update = Strip::where(['id' => $request->id])->update($crud);

                    if($update){
                        if(!empty($request->file('file')))
                            $file->move($folder_to_upload, $filenameToStore);

                        return redirect()->route('strips')->with('success', 'Strip light updated successfully');
                    }else{
                        return redirect()->back()->with('error', 'Faild to update strip light')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** update */ 

        /** delete */
            public function delete(Request $request){
                $id = base64_decode($request->id);

                $delete = Strip::where(['id' => $id])->delete();
                
                if($delete)
                    return redirect()->route('strips')->with('success', 'Strip light deleted successfully');
                else
                    return redirect()->route('strips')->with('error', 'Faild to delete strip light');
            }
        /** delete */
    }