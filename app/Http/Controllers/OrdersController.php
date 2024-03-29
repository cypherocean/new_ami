<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Order;
    use App\Models\OrderDetails;
    use App\Models\OrderStrips;
    use App\Models\Product;
    use App\Models\Strip;
    use App\Models\Customer;
    use Illuminate\Support\Str;
    use App\Http\Requests\OrderRequest;
    use Auth, Validator, DB, Mail, DataTables, File;

    class OrdersController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Order::select('id', 'name', DB::Raw("DATE_FORMAT(".'order_date'.", '%d-%m-%Y') as order_date"), 'status')
                                        ->orderByRaw("FIELD(status, 'delivery', 'pending', 'completed')")
                                        ->orderBy(DB::raw("DATE_FORMAT(".'order_date'.", '%Y-%m-%d')"), 'desc')
                                        ->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                $return = '<div class="btn-group">
                                                <a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;';
                                if($data->status != 'delivered'){
                                    $return .= '<a href="javascript:;" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                                    <i class="fa fa-bars"></i>
                                                                </a> &nbsp;
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="pending" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Pending</a></li>
                                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="delivery" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Delivery</a></li>
                                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="completed" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Completed</a></li>
                                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="delete" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Delete</a></li>
                                                                </ul>
                                                            </div>';
                                }
                                return $return;
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'pending')
                                    return '<span class="badge badge-pill badge-info">Pending</span>';
                                else if($data->status == 'completed')
                                    return '<span class="badge badge-pill badge-success">Completed</span>';
                                else if($data->status == 'delivery')
                                    return '<span class="badge badge-pill badge-warning">Out For Delivery</span>';
                                else
                                    return '-';
                            })

                            ->editColumn('name', function($data) {
                                return '<a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'" class="text-dark">'.$data->name.'</a>';
                            })

                            ->editColumn('order_date', function($data) {
                                return '<a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'" class="text-dark">'.$data->order_date.'</a>';
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'pending')
                                    return '<a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'"><span class="badge badge-pill badge-info">Pending</span></a>';
                                else if($data->status == 'completed')
                                    return '<a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'"><span class="badge badge-pill badge-success">Completed</span></a>';
                                else if($data->status == 'delivery')
                                    return '<a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'"><span class="badge badge-pill badge-warning">Out For Delivery</span></a>';
                                else
                                    return '-';
                            })

                            ->rawColumns(['action', 'status', 'name', 'order_date'])
                            ->make(true);
                }

                return view('orders.index');
            }
        /** index */

        /** select-customer */
            public function select_customer(Request $request){
                return view('orders.select_customer');
            }
        /** select-customer */

        /** customer-details */
            public function customer_details(Request $request){
                if(isset($request->name) && $request->name != null && $request->name != ''){
                    $data = Customer::where(['party_name' => $request->name])->first();

                    if($data)
                        return response()->json(['code' => 200, 'data' => $data]);
                    else
                        return response()->json(['code' => 201]);
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** customer-details */

        /** product-price */
            public function product_price(Request $request){
                if(isset($request->id) && $request->id != null && $request->id != ''){
                    $data = Product::select('price')->where(['id' => $request->id])->first();

                    if($data)
                        return response()->json(['code' => 200, 'data' => $data]);
                    else
                        return response()->json(['code' => 201]);
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** product-price */

        /** strip-price */
            public function strip_price(Request $request){
                $quantity = $request->quantity;
                $unit = $request->unit;

                if(isset($request->id) && $request->id != null && $request->id != ''){
                    $data = Strip::select('inch_price')->where(['id' => $request->id])->first();

                    if($data){
                        $qnt = _converter($unit, $quantity);

                        $total = $data->inch_price * $qnt;

                        return response()->json(['code' => 200, 'data' => $total]);
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** strip-price */

        /** strip-amp */
            public function strip_amp(Request $request){
                if(isset($request->id) && $request->id != null && $request->id != ''){
                    $data = Strip::select('quantity', 'unit', 'choke', 'price', 'amp')->where(['id' => $request->id])->first();

                    if($data)
                        return response()->json(['code' => 200, 'data' => $data]);
                    else
                        return response()->json(['code' => 201]);
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** strip-amp */

        /** create */
            public function create(Request $request, $customer_id=''){
                $products = Product::select('id', 'name')->get();
                $strips = Strip::select('id', 'name')->get();
                $customers = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address', 'office_contact_person')
                                        ->where(['status' => 'active'])
                                        ->get();
                                        
                return view('orders.create', ['products' => $products, 'customers' => $customers, 'customer_id' => $customer_id, 'strips' => $strips]);
            }
        /** create */

        /** insert */
            public function insert(OrderRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'name' => $request->name,
                        'order_date' => Date('Y-m-d', strtotime($request->order_date)) ?? NULL,
                        'status' => 'pending',
                        'remark' => $request->remark ?? NULL,
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

                        $folder_to_upload = public_path().'/uploads/orders/';

                        if (!File::exists($folder_to_upload))
                            File::makeDirectory($folder_to_upload, 0777, true, true);

                        $crud["file"] = $filenameToStore;
                    }

                    DB::beginTransaction();
                    try {
                        $last_id = Order::insertGetId($crud);
                        
                        if($last_id){
                            $product_id = $request->product_id ?? NULL;
                            $quantity = $request->quantity ?? NULL;
                            $price = $request->price ?? NULL;
                            $remarks = $request->remarks ?? NULL;

                            if($product_id != null){
                                for($i=0; $i<count($product_id); $i++){
                                    if($product_id[$i] != null){
                                        $order_detail_crud = [
                                            'order_id' => $last_id,
                                            'product_id' => $product_id[$i] ?? NULL,
                                            'quantity' => $quantity[$i] ?? NULL,
                                            'price' => $price[$i] ?? NULL,
                                            'remark' => $remarks[$i] ?? NULL,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'created_by' => auth()->user()->id,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => auth()->user()->id
                                        ];
                                     
                                        OrderDetails::insertGetId($order_detail_crud);
                                    }
                                }
                            }

                            $strip_id = $request->strip_id ?? NULL;
                            $st_quantity = $request->st_quantity ?? NULL;
                            $st_unit = $request->st_unit ?? NULL;
                            $st_choke = $request->st_choke ?? NULL;
                            $st_calc = $request->st_calc ?? NULL;
                            $st_price = $request->st_price ?? NULL;
                            $st_remarks = $request->st_remarks ?? NULL;
                            $st_amp = $request->st_amp ?? NULL;

                            if($strip_id != null){
                                for($i=0; $i<count($strip_id); $i++){
                                    if($strip_id[$i] != null){
                                        $order_strip_crud = [
                                            'order_id' => $last_id,
                                            'strip_id' => $strip_id[$i] ?? NULL,
                                            'quantity' => $st_quantity[$i] ?? NULL,
                                            'unit' => $st_unit[$i] ?? NULL,
                                            'choke' => $st_choke[$i] ?? NULL,
                                            'calc' => $st_calc[$i] ?? NULL,
                                            'price' => $st_price[$i] ?? NULL,
                                            'amp' => $st_amp[$i] ?? NULL,
                                            'remark' => $st_remarks[$i] ?? NULL,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'created_by' => auth()->user()->id,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => auth()->user()->id
                                        ];
                                     
                                        OrderStrips::insertGetId($order_strip_crud);
                                    }
                                }
                            }

                            if(!empty($request->file('file')))
                                $file->move($folder_to_upload, $filenameToStore);

                            DB::commit();
                            return redirect()->route('orders')->with('success', 'Order created successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('error', 'Faild to create order!')->withInput();
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('error', 'Faild to create order!')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('orders')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $products = Product::select('id', 'name')->get();
                $strips = Strip::select('id', 'name')->get();
                $customer = collect();

                $data = Order::select('id', 'name', 'file', 'remark', 'order_date')->where(['id' => $id])->first();
                
                if($data){
                    $customer = Customer::where(['party_name' => $data->name])->first();

                    $order_details = DB::table('orders_details as od')
                                        ->select('od.id', 'od.product_id', 'od.quantity', 'od.price', 'od.remark', 'p.file', 'p.name as product_name')
                                        ->leftjoin('products as p', 'p.id', 'od.product_id')
                                        ->where(['od.order_id' => $data->id])
                                        ->get();

                    if($order_details->isNotEmpty())
                        $data->order_details = $order_details;
                    else
                        $data->order_details = collect();
                    
                    $order_strips = DB::table('orders_strips as os')
                                        ->select('os.id', 'os.strip_id', 'os.quantity', 'os.unit', 'os.choke', 'os.calc', 'os.price', 'os.amp', 'os.remark', 's.file', 's.name as strip_name')
                                        ->leftjoin('strips as s', 's.id', 'os.strip_id')
                                        ->where(['os.order_id' => $data->id])
                                        ->get();

                    if($order_strips->isNotEmpty())
                        $data->order_strips = $order_strips;
                    else
                        $data->order_strips = collect();

                       return view('orders.view', ['products' => $products, 'data' => $data, 'customer' => $customer, 'strips' => $strips]);
                }else{
                    return redirect()->route('orders')->with('error', 'No data found');
                }
            }
        /** view */ 

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('orders')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $products = Product::select('id', 'name')->get();
                $strips = Strip::select('id', 'name')->get();
                $customers = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address', 'office_contact_person')
                                        ->where(['status' => 'active'])
                                        ->get();

                $data = Order::select('id', 'name', 'file', 'remark', 'order_date')->where(['id' => $id])->first();
                
                if($data){
                    $order_details = DB::table('orders_details as od')
                                        ->select('od.id', 'od.product_id', 'od.quantity', 'od.price', 'od.remark', 'p.name as product_name')
                                        ->leftjoin('products as p', 'p.id', 'od.product_id')
                                        ->where(['od.order_id' => $data->id])
                                        ->get();

                    if($order_details->isNotEmpty())
                        $data->order_details = $order_details;
                    else
                        $data->order_details = collect();

                    $order_strips = DB::table('orders_strips as os')
                                        ->select('os.id', 'os.strip_id', 'os.quantity', 'os.unit', 'os.choke', 'os.calc', 'os.price', 'os.remark', 'os.amp', 's.name as strip_name')
                                        ->leftjoin('strips as s', 's.id', 'os.strip_id')
                                        ->where(['os.order_id' => $data->id])
                                        ->get();

                    if($order_strips->isNotEmpty())
                        $data->order_strips = $order_strips;
                    else
                        $data->order_strips = collect();

                    return view('orders.edit', ['products' => $products, 'data' => $data, 'customers' => $customers, 'strips' => $strips]);
                }else{
                    return redirect()->route('orders')->with('error', 'No data found');
                }
            }
        /** edit */ 

        /** update */
            public function update(OrderRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'name' => $request->name,
                        'order_date' => Date('Y-m-d', strtotime($request->order_date)) ?? NULL,
                        'remark' => $request->remark ?? '',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    if(!empty($request->file('file'))){
                        $file = $request->file('file');
                        $filenameWithExtension = $request->file('file')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                        $extension = $request->file('file')->getClientOriginalExtension();
                        $filenameToStore = time()."_".$filename.'.'.$extension;

                        $folder_to_upload = public_path().'/uploads/orders/';

                        if (!File::exists($folder_to_upload))
                            File::makeDirectory($folder_to_upload, 0777, true, true);

                        $crud["file"] = $filenameToStore;
                    }

                    DB::beginTransaction();
                    try {
                        $update = Order::where(['id' => $request->id])->update($crud);
                       
                        if($update){
                            $product_id = $request->product_id ?? NULL;
                            $quantity = $request->quantity ?? NULL;
                            $price = $request->price ?? NULL;
                            $remarks = $request->remarks ?? NULL;

                            if($product_id != null){
                                for($i=0; $i<count($product_id); $i++){
                                    if($product_id[$i] != null){
                                        $exst_detail = OrderDetails::select('id')->where(['order_id' => $request->id, 'product_id' => $product_id[$i]])->first();

                                        if(!empty($exst_detail)){
                                            $order_detail_crud = [
                                                'order_id' => $request->id,
                                                'product_id' => $product_id[$i] ?? NULL,
                                                'quantity' => $quantity[$i] ?? NULL,
                                                'price' => $price[$i] ?? NULL,
                                                'remark' => $remarks[$i] ?? NULL,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => auth()->user()->id
                                            ];

                                            OrderDetails::where(['id' => $exst_detail->id])->update($order_detail_crud);
                                        }else{
                                            $order_detail_crud = [
                                                'order_id' => $request->id,
                                                'product_id' => $product_id[$i] ?? NULL,
                                                'quantity' => $quantity[$i] ?? NULL,
                                                'price' => $price[$i] ?? NULL,
                                                'remark' => $remarks[$i] ?? NULL,
                                                'created_at' => date('Y-m-d H:i:s'),
                                                'created_by' => auth()->user()->id,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => auth()->user()->id
                                            ];

                                            OrderDetails::insertGetId($order_detail_crud);
                                        }
                                    }
                                }
                            }

                            $strip_id = $request->strip_id ?? NULL;
                            $st_quantity = $request->st_quantity ?? NULL;
                            $st_unit = $request->st_unit ?? NULL;
                            $st_choke = $request->st_choke ?? NULL;
                            $st_calc = $request->st_calc ?? NULL;
                            $st_price = $request->st_price ?? NULL;
                            $st_remarks = $request->st_remarks ?? NULL;
                            $st_amp = $request->st_amp ?? NULL;

                            if($strip_id != null){
                                for($i=0; $i<count($strip_id); $i++){
                                    if($strip_id[$i] != null){
                                        $exst_detail = OrderStrips::select('id')->where(['order_id' => $request->id, 'strip_id' => $strip_id[$i]])->first();

                                        if(!empty($exst_detail)){
                                            $order_strip_crud = [
                                                'order_id' => $request->id,
                                                'strip_id' => $strip_id[$i] ?? NULL,
                                                'quantity' => $st_quantity[$i] ?? NULL,
                                                'unit' => $st_unit[$i] ?? NULL,
                                                'choke' => $st_choke[$i] ?? NULL,
                                                'calc' => $st_calc[$i] ?? NULL,
                                                'price' => $st_price[$i] ?? NULL,
                                                'amp' => $st_amp[$i] ?? NULL,
                                                'remark' => $st_remarks[$i] ?? NULL,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => auth()->user()->id
                                            ];

                                            OrderStrips::where(['id' => $exst_detail->id])->update($order_strip_crud);
                                        }else{
                                            $order_strip_crud = [
                                                'order_id' => $request->id,
                                                'strip_id' => $strip_id[$i] ?? NULL,
                                                'quantity' => $st_quantity[$i] ?? NULL,
                                                'unit' => $st_unit[$i] ?? NULL,
                                                'choke' => $st_choke[$i] ?? NULL,
                                                'calc' => $st_calc[$i] ?? NULL,
                                                'price' => $st_price[$i] ?? NULL,
                                                'amp' => $st_amp[$i] ?? NULL,
                                                'remark' => $st_remarks[$i] ?? NULL,
                                                'created_at' => date('Y-m-d H:i:s'),
                                                'created_by' => auth()->user()->id,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => auth()->user()->id
                                            ];
                                        
                                            OrderStrips::insertGetId($order_strip_crud);
                                        }
                                    }
                                }
                            }

                            if(!empty($request->file('file')))
                                $file->move($folder_to_upload, $filenameToStore);

                            DB::commit();
                            return redirect()->route('orders')->with('success', 'Order updated successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('error', 'Faild to update order!')->withInput();
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('error', 'Faild to update order!')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** update */

        /** change-status */
            public function change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $status = $request->status;

                    $data = Order::where(['id' => $id])->first();
                    $orders = OrderDetails::where(['order_id' => $id])->get();

                    if(!empty($data)){
                        DB::beginTransaction();
                        try {
                            if($status == 'delete'){
                                $update = Order::where('id',$id)->delete();
                            }else{
                                $update = Order::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);

                                if($data->status == 'pending' && ($status == 'completed' || $status == 'delivery')){
                                    if($orders->isNotEmpty()){
                                        foreach($orders as $order){
                                            $product = Product::select('quantity')->where(['id' => $order->product_id])->first();
                                       
                                            $qty = $product->quantity - $order->quantity;
    
                                            $product_update = Product::where(['id' => $order->product_id])->update(['quantity' => $qty, 'updated_at' => date('Y-m-d H:i:s')]);
    
                                            if(!$product_update){
                                                DB::rollback();
                                                return response()->json(['code' => 201]);
                                            }
                                        }
                                    }
                                }elseif(($data->status == 'completed' || $data->status == 'delivery') && $status == 'pending'){
                                    if($orders->isNotEmpty()){
                                        foreach($orders as $order){
                                            $product = Product::select('quantity')->where(['id' => $order->product_id])->first();
                                       
                                            $qty = $product->quantity + $order->quantity;
    
                                            $product_update = Product::where(['id' => $order->product_id])->update(['quantity' => $qty, 'updated_at' => date('Y-m-d H:i:s')]);
    
                                            if(!$product_update){
                                                DB::rollback();
                                                return response()->json(['code' => 201]);
                                            }
                                        }
                                    }
                                }
                            }
                        
                            if($update){
                                DB::commit();
                                return response()->json(['code' => 200]);
                            }else{
                                DB::rollback();
                                return response()->json(['code' => 201]);
                            }
                        }catch (\Exception $e) {
                            DB::rollback();
                            return response()->json(['code' => 201]);
                        }
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** change-status */

        /** delete-detail */
            public function delete_detail(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = $request->id;

                    $data = OrderDetails::where(['id' => $id])->first();

                    if(!empty($data)){
                        $update = OrderDetails::where(['id' => $id])->delete();                        
                        
                        if($update)
                            return response()->json(['code' => 200]);
                        else
                            return response()->json(['code' => 201]);
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** delete-detail */

        /** delete-strip */
            public function delete_strip(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = $request->id;

                    $data = OrderStrips::where(['id' => $id])->first();

                    if(!empty($data)){
                        $update = OrderStrips::where(['id' => $id])->delete();                        
                        
                        if($update)
                            return response()->json(['code' => 200]);
                        else
                            return response()->json(['code' => 201]);
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** delete-strip */
    }