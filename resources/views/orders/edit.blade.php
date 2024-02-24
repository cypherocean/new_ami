@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit Order
@endsection

@section('styles')
    <link href="{{ asset('assets/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('assets/css/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sweetalert2.bundle.css') }}" rel="stylesheet">

    <style>
        .select2-container--default .select2-selection--single{
            height: 35px;
        }
    </style>
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Edit Order</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('orders.update') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="id" value="{{ $data->id }}">
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <select name="name" id="name" class="form-control select2_demo_2" placeholder="Plese enter name">
                                        <option></option>
                                        @if(isset($customers) && $customers->isNotEmpty())
                                            @foreach($customers as $row)
                                                <option value="{{ $row->party_name }}" @if(isset($data) && $data->name != '' && $data->name == $row->party_name) selected @endif>{{ $row->party_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="kt-form__help error name"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="order_date">Order Date <span class="text-danger"></span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                        <input type="text" name="order_date" id="order_date" class="form-control" placeholder="Plese enter order date" value="{{ date('d-m-Y' ,strtotime($data->order_date)) }}" />
                                    </div>
                                        <i class="fa fa-calender"></i>
                                    <span class="kt-form__help error order_date"></span>
                                </div>
                                <div class="row" id="customer_details"></div>
                            </div>
                            <div class="row" id="table">
                                <div class="col-sm-12">
                                    <h4>Products</h4>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:05%">Sr. No</th>
                                                <th style="width:50%">Product</th>
                                                <th style="width:10%">Quantity</th>
                                                <th style="width:10%">Price</th>
                                                <th style="width:10%">Remark</th>
                                                <th style="width:15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($data) && $data->order_details->isNotEmpty())
                                                @php $i=1; @endphp
                                                @foreach($data->order_details as $product)
                                                    <tr class="clone" id="clone_{{ $i }}">
                                                        <th style="width:05%">1</th>
                                                        <th style="width:50%">
                                                            <select class="form-control select2_demo_2 product_id" name="product_id[]" id="product_{{ $i }}" data-id="{{ $i }}">
                                                                @if(isset($products) && $products->isNotEmpty())
                                                                    <option value="">Select Product</option>
                                                                    @foreach($products as $row)
                                                                        <option value="{{ $row->id }}" @if($product->product_id == $row->id) selected @endif>{{ $row->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </th>
                                                        <th style="width:10%">
                                                            <input type="text" name="quantity[]" id="quantity_{{ $i }}" value="{{ $product->quantity }}" class="form-control digit">
                                                        </th>
                                                        <th style="width:10%">
                                                            <input type="text" name="price[]" id="price_{{ $i }}" value="{{ $product->price }}" class="form-control digit">
                                                        </th>
                                                        <th style="width:10%">
                                                            <textarea name="remarks[]" id="remarks_{{ $i }}" cols="1" rows="1" class="form-control">{{ $product->remark ?? '' }}</textarea>
                                                        </th>
                                                        <th style="width:15%">
                                                            @if($i > 1)
                                                                @php $style = 'block'; @endphp
                                                            @else
                                                                @php $style = 'none'; @endphp
                                                            @endif
                                                            <button type="button" style="display:{{ $style }}" class="btn btn-danger delete" data-detail="{{ $product->id }}" data-id="{{ $i }}">Remove</button>
                                                        </th>
                                                    </tr>
                                                    @php $i++; @endphp
                                                @endforeach
                                            @else
                                                <tr class="clone" id="clone_1">
                                                    <th style="width:05%">1</th>
                                                    <th style="width:50%">
                                                        <select class="form-control select2_demo_2 product_id" name="product_id[]" id="product_1" data-id="1">
                                                            @if(isset($products) && $products->isNotEmpty())
                                                                <option value="">Select Product</option>
                                                                @foreach($products as $row)
                                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </th>
                                                    <th style="width:10%">
                                                        <input type="text" name="quantity[]" id="quantity_1" class="form-control digit">
                                                    </th>
                                                    <th style="width:10%">
                                                        <input type="text" name="price[]" id="price_1" class="form-control digit">
                                                    </th>
                                                    <th style="width:10%">
                                                        <textarea name="remarks[]" id="remarks_1" cols="1" rows="1" class="form-control"></textarea>
                                                    </th>
                                                    <th style="width:15%">
                                                        <button type="button" class="btn btn-danger delete" style="display:none;" data-id="1">Remove</button>
                                                    </th>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div> 
                                <div class="col-sm-2 ml-auto">
                                    <button type="button" class="btn btn-md btn-primary m-4" id="add_product">Add Product</button>
                                </div>
                            </div>
                            <div class="row" id="st_table">
                                <div class="col-sm-12">
                                    <h4>Strip Lights</h4>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:05%">Sr. No</th>
                                                <th style="width:20%">Strip</th>
                                                <th style="width:10%">Quantity</th>
                                                <th style="width:10%">Unit</th>
                                                <th style="width:10%">Choke per Unit</th>
                                                <th style="width:10%">Total Choke</th>
                                                <th style="width:10%">Price</th>
                                                <th style="width:7%">AMP</th>
                                                <th style="width:10%">Remark</th>
                                                <th style="width:8%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($data) && $data->order_strips->isNotEmpty())
                                                @php $i=1; @endphp
                                                @foreach($data->order_strips as $strip)
                                                    <tr class="st_clone" id="st_clone_{{ $i }}">
                                                        <th style="width:05%">{{ $i }}</th>
                                                        <th style="width:20%">
                                                            <select class="form-control select2_demo_2 strip_id" name="strip_id[]" id="strip_{{ $i }}" data-id="{{ $i }}">
                                                                @if(isset($strips) && $strips->isNotEmpty())
                                                                    <option value="">Select Strip</option>
                                                                    @foreach($strips as $row)
                                                                        <option value="{{ $row->id }}" @if($strip->strip_id == $row->id) selected @endif>{{ $row->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </th>
                                                        <th style="width:10%">
                                                            <input type="text" name="st_quantity[]" id="st_quantity_{{ $i }}" value="{{ $strip->quantity }}" class="form-control digit st_quantity" data-id="{{ $i }}">
                                                        </th>
                                                        <th style="width:10%">
                                                            <select class="form-control st_unit" name="st_unit[]" id="st_unit_{{ $i }}" data-id="{{ $i }}">
                                                                <option value="inch" @if($strip->unit == 'inch') selected @endif>Inch</option>
                                                                <option value="feet" @if($strip->unit == 'feet') selected @endif>Feet</option>
                                                                <option value="meter" @if($strip->unit == 'meter') selected @endif>Meter</option>
                                                            </select>
                                                        </th>
                                                        <th style="width:10%"> 
                                                            <input type="text" name="st_choke[]" id="st_choke_{{ $i }}" value="{{ $strip->choke }}" class="form-control digit st_choke" data-id="{{ $i }}">
                                                        </th>
                                                        <th style="width:10%">
                                                            <input type="text" name="st_calc[]" id="st_calc_{{ $i }}" value="{{ $strip->calc }}" class="form-control st_calc" data-id="{{ $i }}" readonly="readonly">
                                                        </th>
                                                        <th style="width:10%">
                                                            <input type="text" name="st_price[]" id="st_price_{{ $i }}" value="{{ round($strip->price) }}" class="form-control digit st_price">
                                                        </th>
                                                        <th style="width:7%">
                                                            <input type="text" name="st_amp[]" id="st_amp_{{ $i }}" class="form-control st_amp" value="{{ $strip->amp }}">
                                                        </th>
                                                        <th style="width:10%">
                                                            <textarea name="st_remarks[]" id="st_remarks_{{ $i }}" cols="1" rows="1" class="form-control">{{ $strip->remark }}</textarea>
                                                        </th>
                                                        <th style="width:8%">
                                                            @if($i > 1)
                                                                @php $st_style = 'block'; @endphp
                                                            @else
                                                                @php $st_style = 'none'; @endphp
                                                            @endif
                                                            <button type="button" style="display:{{ $st_style }}"  class="btn btn-danger st_delete" data-detail="{{ $strip->id }}" data-id="{{ $i }}">Remove</button>
                                                        </th>
                                                    </tr>
                                                    @php $i++; @endphp
                                                @endforeach
                                            @else
                                                <tr class="st_clone" id="st_clone_1">
                                                    <th style="width:05%">1</th>
                                                    <th style="width:20%">
                                                        <select class="form-control select2_demo_2 strip_id" name="strip_id[]" id="strip_1" data-id="1">
                                                            @if(isset($strips) && $strips->isNotEmpty())
                                                                <option value="">Select Strip</option>
                                                                @foreach($strips as $row)
                                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </th>
                                                    <th style="width:10%">
                                                        <input type="text" name="st_quantity[]" id="st_quantity_1" class="form-control digit st_quantity" data-id="1">
                                                    </th>
                                                    <th style="width:10%">
                                                        <select class="form-control st_unit" name="st_unit[]" id="st_unit_1" data-id="1">
                                                            <option value="inch">Inch</option>
                                                            <option value="feet">Feet</option>
                                                            <option value="meter">Meter</option>
                                                        </select>
                                                    </th>
                                                    <th style="width:10%">
                                                        <input type="text" name="st_choke[]" id="st_choke_1" class="form-control digit st_choke" data-id="1">
                                                    </th>
                                                    <th style="width:10%">
                                                        <input type="text" name="st_calc[]" id="st_calc_1" class="form-control st_calc" data-id="1" readonly="readonly">
                                                    </th>
                                                    <th style="width:10%">
                                                        <input type="text" name="st_price[]" id="st_price_1" class="form-control digit st_price">
                                                    </th>
                                                    <th style="width:7%">
                                                        <input type="text" name="st_amp[]" id="st_amp_1" class="form-control st_amp">
                                                    </th>
                                                    <th style="width:10%">
                                                        <textarea name="st_remarks[]" id="st_remarks_1" cols="1" rows="1" class="form-control"></textarea>
                                                    </th>
                                                    <th style="width:8%">
                                                        <button type="button" class="btn btn-danger st_delete" style="display:none;" data-id="1">Remove</button>
                                                    </th>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-10 text-center">
                                    <button type="button" class="btn btn-md btn-primary m-4" id="process" onClick="calculate()">Process</button>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-md btn-primary m-4" id="add_strip">Add Strip</button>
                                </div>  
                            </div>
                            <div id="processDiv"></div>
                            <div class="form-group col-sm-12">
                                <label for="remark">Remark <span class="text-danger"></span></label>
                                <textarea name="remark" id="remark" cols="30" rows="5" class="form-control" placeholder="Plese enter remark">{{ $data->remark ?? '' }}</textarea>
                                <span class="kt-form__help error remark"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                @if(isset($data->file) && !empty($data->file))
                                    @php $file = url('/uploads/orders/').'/'.$data->file; @endphp
                                @else
                                    @php $file = ''; @endphp
                                @endif
                                <label for="file">Attechment <span class="text-danger"></span></label>
                                <input type="file" name="file" id="file" class="form-control dropify" placeholder="Plese select attachment" data-default-file="{{ $file }}" data-show-remove="false" />
                                <span class="kt-form__help error file"></span>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('orders') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts/form-plugins.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.js') }}"></script>

    <script src="{{ asset('assets/js/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/js/promise.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.bundle.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop file here or click',
                    'remove':  'Remove',
                    'error':   'Ooops, something wrong happended.'
                }
            });
            var drEvent = $('.dropify').dropify(); 

            $('#order_date').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true
            });
            
            $('#add_product').click(function(){
                $('#table').css('display', 'block');

                var regex = /^(.+?)(\d+)$/i;
                var cloneIndex = $("#table tbody tr").length;

                if(cloneIndex !== 0){
                    let num = parseInt(cloneIndex) + 1;

                    var clone = clone_div(num);
                    $("#table tbody").append(clone);
                    $("#product_"+num).select2();
                    $("#product_"+num).focus();
                    $("#product_"+num).select2('open');
                }else{
                    var clone = clone_div(1);
                    $("#table tbody").append(clone);
                }
            });

            function clone_div(id){
                return '<tr class="clone" id="clone_'+id+'">'+
                        '<th style="width:05%">'+id+'</th>'+
                        '<th style="width:50%">'+
                            '<select name="product_id[]" id="product_'+id+'" data-id="'+id+'" class="form-control select2_demo_2 product_id"> <option value="">Select</option> @foreach($products as $row)<option value="{{ $row->id }}">{{ $row->name }}</option>@endforeach </select>'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<input type="text" name="quantity[]" id="quantity_'+id+'"class="form-control">'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<input type="text" name="price[]" id="price_'+id+'" class="form-control">'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<textarea name="remarks[]" id="remarks_'+id+'" cols="1" rows="1" class="form-control"></textarea>'+
                        '</th>'+
                        '<th style="width:15%">'+
                            '<button type="button" class="btn btn-danger delete" data-id="'+id+'">Remove</button>'+
                        '</th>'+
                    '</tr>';
            }

            $(document).on('click', ".delete", function () {
                let id = $(this).data('id');
                let detail_id = $(this).data('detail');

                let con = confirm('Are you sure to delete?');
                if (con) {
                    if(detail_id != null){
                        $.ajax({
                            "url": "{!! route('orders.delete.detail') !!}",
                            "dataType": "json",
                            "type": "POST",
                            "data":{
                                id: detail_id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response){
                                if (response.code == 200){
                                    $('#clone_'+id).remove();
                                    toastr.success('Record deleted changed successfully.', 'Success');
                                }else{
                                    toastr.error('Failed to delete record.', 'Error');
                                }
                            }
                        });
                    } else {
                        $('#clone_'+id).remove();
                        toastr.success('Record deleted changed successfully.', 'Success');
                    }
                }
            });

            $('#add_strip').click(function(){                
                var regex = /^(.+?)(\d+)$/i;
                var index = $("#st_table tbody tr").last().attr('id');
                cloneIndex = index.replace('st_clone_', '');

                if(cloneIndex !== 0){
                    let num = parseInt(cloneIndex) + 1;
                    var clone = st_clone_div(num);
                    $("#st_table tbody").append(clone);
                    $("#strip"+num).select2();
                    $("#strip"+num).focus();
                    $("#strip"+num).select2('open');
                }else{
                    var clone = clone_div(1);
                    $("#st_table tbody").append(clone);
                    $("#strip"+num).select2();
                    $("#strip"+num).focus();
                }
            });

            function st_clone_div(id){
                return '<tr class="clone" id="st_clone_'+id+'">'+
                        '<th style="width:05%">'+id+'</th>'+
                        '<th style="width:20%">'+
                            '<select name="strip_id[]" id="strip_'+id+'" data-id="'+id+'" class="form-control select2_demo_2 strip_id"> <option value="">Select</option> @foreach($strips as $row)<option value="{{ $row->id }}">{{ $row->name }}</option>@endforeach </select>'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<input type="text" name="st_quantity[]" id="st_quantity_'+id+'" class="form-control st_quantity" data-id="'+id+'">'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<select class="form-control st_unit" name="st_unit[]" id="st_unit_'+id+'" data-id="'+id+'">'+
                                '<option value="inch">Inch</option>'+
                                '<option value="feet">Feet</option>'+
                                '<option value="meter">Meter</option>'+
                            '</select>'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<input type="text" name="st_choke[]" id="st_choke_'+id+'" class="form-control st_choke" data-id="'+id+'">'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<input type="text" name="st_calc[]" id="st_calc_'+id+'" class="form-control st_calc" data-id="'+id+'" readonly="readonly">'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<input type="text" name="st_price[]" id="st_price_'+id+'" class="form-control st_price">'+
                        '</th>'+
                        '<th style="width:07%">'+
                            '<input type="text" name="st_amp[]" id="st_amp_'+id+'" class="form-control st_amp" data-id="'+id+'">'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<textarea name="st_remarks[]" id="st_remarks_'+id+'" cols="1" rows="1" class="form-control"></textarea>'+
                        '</th>'+
                        '<th style="width:08%">'+
                            '<button type="button" class="btn btn-danger st_delete" data-id="'+id+'">Remove</button>'+
                        '</th>'+
                    '</tr>';
            }

            $(document).on('click', ".st_delete", function () {
                let id = $(this).data('id');
                let detail_id = $(this).data('detail');

                let con = confirm('Are you sure to delete?');
                if (con) {
                    if(detail_id != null){
                        $.ajax({
                            "url": "{!! route('orders.delete.strip') !!}",
                            "dataType": "json",
                            "type": "POST",
                            "data":{
                                id: detail_id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response){
                                if (response.code == 200){
                                    $('#st_clone_'+id).remove();
                                    toastr.success('Record deleted changed successfully.', 'Success');
                                }else{
                                    toastr.error('Failed to delete record.', 'Error');
                                }
                            }
                        });
                    } else {
                        $('#st_clone_'+id).remove();
                        toastr.success('Record deleted changed successfully.', 'Success');
                    }
                }
            });

            calculate();
        });

        $(document).ready(function () {
            var form = $('#form');
            $('.kt-form__help').html('');
            form.submit(function(e) {
                $('.help-block').html('');
                $('.m-form__help').html('');
                $.ajax({
                    url : form.attr('action'),
                    type : form.attr('method'),
                    data : form.serialize(),
                    dataType: 'json',
                    async:false,
                    success : function(response){
                        return true;
                    },
                    error: function(response){
                        if(response.status === 422) {
                            e.preventDefault();
                            var errors_ = response.responseJSON;
                            $('.kt-form__help').html('');
                            $.each(errors_.errors, function (key, value) {
                                $('.'+key).html(value);
                            });
                        }
                    }
                });
            });
        });

        $(document).ready(function () {
            $('#name').change(function () {
                var name = $(this).val();
                if(name != '' || name != null){
                    $("#customer_details").html('');
                    _customer_details(name);
                }
            });

            let exst_name = "{{ $data->name ?? '' }}";
            
            if(exst_name != '' || exst_name != null){
                $("#customer_details").html('');
                _customer_details(exst_name);
            }

            $(document).on('change', ".product_id", function () {
                var id = $(this).val();
                var div_id = $(this).data('id');
                
                if(id != '' || id != null){
                    _product_price(id, div_id);
                }
            });

            $(document).on('change', ".strip_id", function () {
                var id = $(this).val();
                var div_id = $(this).data('id');

                if(id != '' || id != null){
                    _strip_price(id, div_id);
                }
            });
        });

        function _customer_details(name){
            $.ajax({
                url : "{{ route('orders.customer.details') }}",
                type : 'post',
                data : { "_token": "{{ csrf_token() }}", "name": name},
                dataType: 'json',
                async: false,
                success : function(response){
                    $("#customer_details").append(
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Name: </span><span>'+response.data.party_name+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Billing Name: </span><span>'+response.data.billing_name+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Contact Person: </span><span>'+response.data.contact_person+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Mobile Number: </span><span>'+response.data.mobile_number+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Billing Address: </span><span>'+response.data.billing_address+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Delivery Address: </span><span>'+response.data.delivery_address+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Electrician: </span><span>'+response.data.electrician+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Electrician Number: </span><span>'+response.data.electrician_number+'</span></div>');
                }
            });
        }

        function _product_price(id, div_id){
            $.ajax({
                url : "{{ route('orders.product.price') }}",
                type : 'post',
                data : { "_token": "{{ csrf_token() }}", "id": id},
                dataType: 'json',
                async: false,
                success : function(response){
                    if(response.code == 200){
                        $('#price_'+div_id).val(response.data.price);
                    }
                }
            });
        }

        function _strip_price(id, quantity, unit, div_id){
            $.ajax({
                url : "{{ route('orders.strip.price') }}",
                type : 'post',
                data : { "_token": "{{ csrf_token() }}", "id": id, "quantity": quantity, "unit": unit},
                dataType: 'json',
                async: false,
                success : function(response){
                    if(response.code == 200){
                        $('#st_price_'+div_id).val(response.data);
                    }
                }
            });
        }

        $(document).on('change', ".st_quantity", function () {
            var val = $(this).val();
            var id = $(this).data('id');
            var choke = $('#st_choke_'+id).val();
            
            var unit = $('#st_unit_'+id).val();
            var strip = $('#strip_'+id).val();
            
            if((val != '' || val != null) && (choke != '' && choke != null)){
                let calc = parseInt(val) * parseInt(choke);
                $('#st_calc_'+id).val(calc);

                if(strip != '' && strip != null){
                    _strip_price(strip, val, unit, id);
                }
            }            
        });

        $(document).on('change', ".st_choke", function () {
            var val = $(this).val();
            var id = $(this).data('id');
            var quantity = $('#st_quantity_'+id).val();

            var unit = $('#st_unit_'+id).val();
            var strip = $('#strip_'+id).val();

            if((val != '' || val != null) && (quantity != '' && quantity != null)){
                let calc = parseInt(val) * parseInt(quantity);
                $('#st_calc_'+id).val(calc);

                if(strip != '' && strip != null){
                    _strip_price(strip, quantity, unit, id);
                }
            }
        });

        $(document).on('change', ".strip_id", function () {
            var val = $(this).val();
            var id = $(this).data('id');

            var quantity = $('#st_quantity_'+id).val();
            var unit = $('#st_unit_'+id).val();

            if((quantity != '' && quantity != null) && (unit != '' && unit != null)){
                _strip_price(val, quantity, unit, id);
            }
            _strip_amp(val, id);
        });

        $(document).on('change', ".st_unit", function () {
            var val = $(this).val();
            var id = $(this).data('id');

            var quantity = $('#st_quantity_'+id).val();
            var choke = $('#st_choke_'+id).val();
            
            var strip = $('#strip_'+id).val();

            if((quantity != '' || quantity != null) && (choke != '' && choke != null)){
                let calc = parseInt(quantity) * parseInt(choke);
                $('#st_calc_'+id).val(calc);

                if(strip != '' && strip != null){
                    _strip_price(strip, quantity, val, id);
                }
            }            
        });

        function _strip_amp(val, id){
            $.ajax({
                url : "{{ route('orders.strip.amp') }}",
                type : 'post',
                data : { "_token": "{{ csrf_token() }}", "id": val},
                dataType: 'json',
                async: false,
                success : function(response){
                    if(response.code == 200){
                        $('#st_quantity_'+id).val(response.data.quantity);
                        $('#st_unit_'+id).val(response.data.unit);
                        $('#st_choke_'+id).val(response.data.choke);
                        $('#st_calc_'+id).val(parseInt(response.data.quantity) * parseInt(response.data.choke));
                        $('#st_price_'+id).val(response.data.price);
                        $('#st_amp_'+id).val(response.data.amp);
                    }
                }
            });
        }

        function calculate(){
            $('#processDiv').html('');
            let html = '';

            let exst_stripes = [];
            let stripes = []; 

            $("#st_table tbody tr").each(function(){
                let strip_val = '';
                let strip_text = '';
                let quantity = '';
                let unit = '';
                let choke = '';
                let calc = '';
                let price = '';
                let amp = '';
                
                strip_val = $(this).find('.strip_id option:selected').val();
                strip_text = $(this).find('.strip_id option:selected').text();
                quantity = $(this).find('.st_quantity').val();
                unit = $(this).find('.st_unit').val();
                choke = $(this).find('.st_choke').val();
                calc = $(this).find('.st_calc').val();
                price = $(this).find('.st_price').val();
                amp = $(this).find('.st_amp').val();

                if(unit == 'feet'){
                    quantity = parseInt(quantity) * parseInt(12);
                } else if(unit == 'meter'){
                    quantity = parseInt(quantity) * parseInt(40);
                }
                
                if(jQuery.inArray(strip_val, exst_stripes) === -1){
                    exst_stripes.push(strip_val);
                    
                    let temp = {'strip': strip_text, 'quantity': quantity, 'unit': 'inch', 'choke': choke, 'calc': calc, 'price': price, 'amp': amp};
                    stripes[strip_val] = temp;
                } else {
                    let exst_temp = stripes[strip_val];

                    let temp = {'strip': exst_temp.strip, 
                                'quantity': parseInt(exst_temp.quantity) + parseInt(quantity), 
                                'unit': 'inch', 
                                'choke': parseInt(exst_temp.choke) + parseInt(choke), 
                                'calc': parseInt(exst_temp.calc) + parseInt(calc), 
                                'price': parseInt(exst_temp.price) + parseInt(price), 
                                'amp': parseInt(exst_temp.amp) + parseInt(amp)};
                    stripes[strip_val] = temp;
                }
            });

            stripes = stripes.filter(item => item);
            
            if(stripes.length !== 0) {
                html = '<table class="table table-bordered">'+
                            '<thead>'+
                                '<tr>'+
                                    '<th style="width:05%">Sr. No</th>'+
                                    '<th style="width:20%">Strip</th>'+
                                    '<th style="width:10%">Quantity</th>'+
                                    '<th style="width:10%">Unit</th>'+
                                    '<th style="width:10%">Choke per Unit</th>'+
                                    '<th style="width:10%">Price</th>'+
                                    '<th style="width:10%">AMP</th>'+
                                '</tr>'+
                            '</thead>'+
                            '<tbody>';

                var j = 1;
                $.each(stripes, function(key, value) {
                    html = html +   '<tr>'+
                                        '<th style="width:05%">'+j+'</th>'+
                                        '<th style="width:20%">'+value.strip+'</th>'+
                                        '<th style="width:10%">'+value.quantity+'</th>'+
                                        '<th style="width:10%">'+value.unit+'</th>'+
                                        '<th style="width:10%">'+value.choke+'</th>'+
                                        '<th style="width:10%">'+value.price+'</th>'+
                                        '<th style="width:10%">'+value.amp+'</th>'+
                                    '</tr>';                        
                    j++;
                });
                html = html + '</tbody></table>';
            }
            
            $('#processDiv').append(html);
        }
    </script>
@endsection

