<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Product;
    use App\Models\User;
    use Illuminate\Support\Str;
    use App\Http\Requests\ProductsRequest;
    use Auth, Validator, DB, Mail, DataTables, File;

    class ProductsController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Product::select('id', 'name', 'code', 'unit', 'price', 'file')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('products.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;
                                                <a href="'.route('products.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a> &nbsp;
                                                <a href="'.route('products.delete', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
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

                            ->editColumn('code' ,function($data){
                                if($data->code == '' || $data->code == null){
                                    return '-';
                                }else{
                                    return $data->code ;
                                }
                            })

                            ->editColumn('file' ,function($data){
                                if($data->file == '' || $data->file == null){
                                    return '<img src="'.url('/uploads/products/default.png').'" alt="Default image" style="width: 40px; height: 40px;">';
                                }else{
                                    return '<img src="'.url('/uploads/products').'/'.$data->file.'" alt="'.$data->file.'" style="width: 40px; height: 40px;">';
                                }
                            })

                            ->rawColumns(['action', 'status', 'code', 'file'])
                            ->make(true);
                }

                return view('products.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                return view('products.create');
            }
        /** create */

        /** insert */
            public function insert(ProductsRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'name' => ucfirst($request->name),
                        'quantity' => 0, 
                        'code' => $request->code ?? NULL, 
                        'unit' => $request->unit ?? NULL, 
                        'price' => $request->price ?? NULL, 
                        'note' => $request->note ?? NULL,
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

                        $folder_to_upload = public_path().'/uploads/products/';

                        if (!File::exists($folder_to_upload))
                            File::makeDirectory($folder_to_upload, 0777, true, true);

                        $crud["file"] = $filenameToStore;
                    }

                    $last_id = Product::insertGetId($crud);
                    
                    if($last_id){
                        if(!empty($request->file('file')))
                            $file->move($folder_to_upload, $filenameToStore);
                        
                        return redirect()->route('products')->with('success', 'Product created successfully.');
                    }else{
                        return redirect()->back()->with('error', 'Faild to create product!')->withInput();
                    }
                }else{
                    return redirect()->route('products')->with('error', 'Something went wrong');
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('products')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Product::select('id', 'name', 'code', 'unit', 'price', 'note', 'file')->where(['id' => $id])->first();
                
                if($data)
                    return view('products.view')->with('data', $data);
                else
                    return redirect()->route('products')->with('error', 'No product found');
            }
        /** view */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('products')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Product::select('id', 'name', 'code', 'unit', 'price', 'note', 'file')->where(['id' => $id])->first();
                
                if($data)
                    return view('products.edit')->with('data', $data);
                else
                    return redirect()->route('products')->with('error', 'No product found');
            }
        /** edit */ 

        /** update */
            public function update(ProductsRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'name' => ucfirst($request->name),
                        'code' => $request->code ?? NULL, 
                        'unit' => $request->unit ?? NULL, 
                        'price' => $request->price ?? NULL, 
                        'note' => $request->note ?? NULL,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    if(!empty($request->file('file'))){
                        $file = $request->file('file');
                        $filenameWithExtension = $request->file('file')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                        $extension = $request->file('file')->getClientOriginalExtension();
                        $filenameToStore = time()."_".$filename.'.'.$extension;

                        $folder_to_upload = public_path().'/uploads/products/';

                        if (!File::exists($folder_to_upload))
                            File::makeDirectory($folder_to_upload, 0777, true, true);

                        $crud["file"] = $filenameToStore;
                    }

                    $update = Product::where(['id' => $request->id])->update($crud);

                    if($update){
                        if(!empty($request->file('file')))
                            $file->move($folder_to_upload, $filenameToStore);

                        return redirect()->route('products')->with('success', 'Product updated successfully.');
                    }else{
                        return redirect()->back()->with('error', 'Faild to update product!')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** update */ 

        /** delete */
            public function delete(Request $request){
                $id = base64_decode($request->id);

                $delete = Product::where(['id' => $id])->delete();
                
                if($delete)
                    return redirect()->route('products')->with('success', 'Product deleted successfully.');
                else
                    return redirect()->route('products')->with('error', 'Faild to delete product !');
            }
        /** delete */

        /** insert-ajax */
            public function insert_ajax(ProductsRequest $request){
                if(!$request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'name' => ucfirst($request->name),
                        'quantity' => 0, 
                        'code' => $request->code ?? NULL, 
                        'unit' => $request->unit ?? NULL, 
                        'price' => $request->price ?? NULL, 
                        'note' => $request->note ?? NULL,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth()->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    $last_id = Product::insertGetId($crud);
                    
                    if($last_id)
                        return response()->json(['code' => 200]);
                    else
                        return response()->json(['code' => 201]);
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** insert-ajax */
    }