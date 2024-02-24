@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View Order
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

        @media print{
            .hide{
                display: none;
            }
        }

        .table>tbody>tr>td{
            border-top: none !important;
            padding: 0.50rem;
        }
    </style>
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row" id="printableArea">
            <div class="col-md-12 hide" id="mainArea">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">View Order</div>
                    </div>
                    <div class="ibox-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $data->name ?? '' }}" placeholder="Plese enter name" disabled />
                                <span class="kt-form__help error name"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="order_date">Order Date <span class="text-danger"></span></label>
                                <input type="date" name="order_date" id="order_date" class="form-control" value="{{ $data->order_date ?? '' }}" placeholder="Plese enter order date" disabled />
                                <span class="kt-form__help error order_date"></span>
                            </div>
                            <div class="row" id="customer_details"></div>
                        </div>
                        @if(isset($data->order_details) && $data->order_details->isNotEmpty())
                            <div class="row" id="table" style="display:block">
                        @else
                            <div class="row" id="table" style="display:none">
                        @endif
                            <div class="col-sm-12">
                                <h4>Products</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:05%">Sr. No</th>
                                            <th style="width:45%">Product</th>
                                            <th style="width:15%">Quantity</th>
                                            <th style="width:15%">Price</th>
                                            <th style="width:20%">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($data->order_details) && $data->order_details->isNotEmpty())
                                            @php $i=1; @endphp
                                            @foreach($data->order_details as $row)
                                                <tr class="clone" id="clone_{{ $i }}">
                                                    <th style="width:05%">{{ $i }}</th>
                                                    <th style="width:45%">
                                                        <div style="display: flex; justify-content: space-between;">
                                                            <span>{{ $row->product_name }}</span>
                                                            @if(isset($row->file) && !empty($row->file))
                                                                @php $file = url('/uploads/products/').'/'.$row->file; @endphp
                                                            @else
                                                                @php $file = url('/uploads/products/default.png'); @endphp
                                                            @endif
                                                            <img src="{{ $file }}" alt="" style="width:40px; height:40px">
                                                        </div>
                                                    <th style="width:15%">{{ $row->quantity }}</th>
                                                    <th style="width:15%">{{ $row->price }}</th>
                                                    <th style="width:20%">{{ $row->remark ?? '' }}</th>
                                                </tr>
                                                @php $i++; @endphp
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div> 
                        </div>
                        @if(isset($data->order_strips) && $data->order_strips->isNotEmpty())
                            <div class="row" id="st_table" style="display:block">
                        @else
                            <div class="row" id="st_table" style="display:none">
                        @endif
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
                                                            <select class="form-control strip_id" name="strip_id[]" id="strip_{{ $i }}" data-id="{{ $i }}" readonly="readonly">
                                                                @if(isset($strips) && $strips->isNotEmpty())
                                                                    <option value="">Select Strip</option>
                                                                    @foreach($strips as $row)
                                                                        <option value="{{ $row->id }}" @if($strip->strip_id == $row->id) selected @endif>{{ $row->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </th>
                                                        <th style="width:10%">
                                                            <input type="text" name="st_quantity[]" id="st_quantity_{{ $i }}" value="{{ $strip->quantity }}" class="form-control digit st_quantity" data-id="{{ $i }}" readonly="readonly">
                                                        </th>
                                                        <th style="width:10%">
                                                            <select class="form-control st_unit" name="st_unit[]" id="st_unit_{{ $i }}" data-id="{{ $i }}" readonly="readonly">
                                                                <option value="inch" @if($strip->unit == 'inch') selected @endif>Inch</option>
                                                                <option value="feet" @if($strip->unit == 'feet') selected @endif>Feet</option>
                                                                <option value="meter" @if($strip->unit == 'meter') selected @endif>Meter</option>
                                                            </select>
                                                        </th>
                                                        <th style="width:10%"> 
                                                            <input type="text" name="st_choke[]" id="st_choke_{{ $i }}" value="{{ $strip->choke }}" class="form-control digit st_choke" data-id="{{ $i }}" readonly="readonly">
                                                        </th>
                                                        <th style="width:10%">
                                                            <input type="text" name="st_calc[]" id="st_calc_{{ $i }}" value="{{ $strip->calc }}" class="form-control st_calc" data-id="{{ $i }}" readonly="readonly">
                                                        </th>
                                                        <th style="width:10%">
                                                            <input type="text" name="st_price[]" id="st_price_{{ $i }}" value="{{ round($strip->price) }}" class="form-control digit st_price" readonly="readonly">
                                                        </th>
                                                        <th style="width:7%">
                                                            <input type="text" name="st_amp[]" id="st_amp_{{ $i }}" class="form-control st_amp" value="{{ $strip->amp }}" readonly="readonly">
                                                        </th>
                                                        <th style="width:10%">
                                                            <textarea name="st_remarks[]" id="st_remarks_{{ $i }}" cols="1" rows="1" class="form-control" readonly="readonly">{{ $strip->remark }}</textarea>
                                                        </th>
                                                    </tr>
                                                    @php $i++; @endphp
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                        <div id="processDiv"></div>
                        <div class="row">
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
                            <div class="form-group col-sm-12">
                                <label for="remark">Remark <span class="text-danger"></span></label>
                                <textarea name="remark" id="remark" cols="30" rows="5" class="form-control" placeholder="Plese enter remark" disabled>{{ $data->remark ?? '' }}</textarea>
                                <span class="kt-form__help error remark"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('orders.edit', ['id' => base64_encode($data->id)]) }}" class="btn btn-primary hide">Edit</a>
                            <a href="{{ route('orders') }}" class="btn btn-default hide">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-12" id="subArea" style="display:none">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">View Order</div>
                    </div>
                    <div class="ibox-body">
                        <table class="table">
                            <tr>
                                <td>Name: {{ $data->name ?? '' }}</td>
                                <td>Order Date: {{ $data->order_date ?? '' }}</td>
                            </tr>
                            <tr class="mt-5">
                                <td>Billing Name: {{ $customer->billing_name ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Contact Person: {{ $customer->contact_person ?? '' }}</td>
                                <td>Mobile Number: {{ $customer->mobile_number ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Billing Address: {{ $customer->billing_address ?? '' }}</td>
                                <td>Delivery Address: {{ $customer->delivery_address ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Electrician: {{ $customer->electrician ?? '' }}</td>
                                <td>Electrician Number: {{ $customer->electrician_number ?? '' }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    Remark: <br/> 
                                    {{ $data->remark ?? '' }}
                                </td>
                            </tr>
                        </table>
                        @if(isset($data->order_details) && $data->order_details->isNotEmpty())
                            <div class="row" id="table" style="display:block">
                        @else
                            <div class="row" id="table" style="display:none">
                        @endif
                            <div class="col-sm-12">
                                <h4>Products</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:05%">Sr. No</th>
                                            <th style="width:45%">Product</th>
                                            <th style="width:15%">Quantity</th>
                                            <th style="width:10%">Price</th>
                                            <th style="width:25%">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($data->order_details) && $data->order_details->isNotEmpty())
                                            @php $i=1; @endphp
                                            @foreach($data->order_details as $row)
                                                <tr class="clone" id="clone_{{ $i }}">
                                                    <th style="width:05%">{{ $i }}</th>
                                                    <th style="width:45%">
                                                        <div style="display: flex; justify-content: space-between;">
                                                            <span>{{ $row->product_name }}</span>
                                                            @if(isset($row->file) && !empty($row->file))
                                                                @php $file = url('/uploads/products/').'/'.$row->file; @endphp
                                                            @else
                                                                @php $file = url('/uploads/products/default.png'); @endphp
                                                            @endif
                                                            <img src="{{ $file }}" alt="" style="width:40px; height:40px">
                                                        </div>
                                                    </th>
                                                    <th style="width:15%">{{ $row->quantity }}</th>
                                                    <th style="width:10%">{{ $row->price }}</th>
                                                    <th style="width:25%">{{ $row->remark ?? '' }}</th>
                                                </tr>
                                                @php $i++; @endphp
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if(isset($data->order_strips) && $data->order_strips->isNotEmpty())
                            <div class="row" id="st_table" style="display:block">
                        @else
                            <div class="row" id="st_table" style="display:none">
                        @endif
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
                                            <th style="width:10%">AMP</th>
                                            <th style="width:10%">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($data->order_strips) && $data->order_strips->isNotEmpty())
                                            @php $i=1; @endphp
                                            @foreach($data->order_strips as $strip)
                                                <tr class="clone" id="clone_{{ $i }}">
                                                    <th style="width:05%">{{ $i }}</th>
                                                    <th style="width:20%">
                                                        <div style="display: flex; justify-content: space-between;">
                                                            <span>{{ $strip->strip_name }}</span>
                                                            @if(isset($strip->file) && !empty($strip->file))
                                                                @php $file = url('/uploads/strips/').'/'.$strip->file; @endphp
                                                            @else
                                                                @php $file = url('/uploads/strips/default.png'); @endphp
                                                            @endif
                                                            <img src="{{ $file }}" alt="" style="width:40px; height:40px">
                                                        </div>
                                                    </th>
                                                    <th style="width:10%">{{ $strip->quantity }}</th>
                                                    <th style="width:10%">{{ $strip->unit }}</th>
                                                    <th style="width:10%">{{ $strip->choke }}</th>
                                                    <th style="width:10%">{{ $strip->calc }}</th>
                                                    <th style="width:10%">{{ round($strip->price) }}</th>
                                                    <th style="width:10%">{{ $strip->amp }}</th>
                                                    <th style="width:10%">{{ $strip->remark }}</th>
                                                </tr>
                                                @php $i++; @endphp
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-right">
                <input type="button" class="btn btn-primary mr-3" style="cursor:pointer" onclick="printDiv('printableArea')" value="Print" />
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
        $(document).ready(function () {
            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop file here or click',
                    'remove':  'Remove',
                    'error':   'Ooops, something wrong happended.'
                }
            });
            var drEvent = $('.dropify').dropify(); 

            let exst_name = "{{ $data->name ?? '' }}";
            
            if(exst_name != '' || exst_name != null){
                $("#customer_details").html('');
                _customer_details(exst_name);
            }

            $('#order_date').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true
            });

            calculate();
        });

        function _customer_details(name){
            $.ajax({
                url : "{{ route('orders.customer.details') }}",
                type : 'post',
                data : { "_token": "{{ csrf_token() }}", "name": name},
                dataType: 'json',
                async: false,
                success : function(json){
                    $("#customer_details").append(
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Billing Name: </span><span>'+json.data.billing_name+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Contact Person: </span><span>'+json.data.contact_person+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Mobile Number: </span><span>'+json.data.mobile_number+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Billing Address: </span><span>'+json.data.billing_address+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Delivery Address: </span><span>'+json.data.delivery_address+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Electrician: </span><span>'+json.data.electrician+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Electrician Number: </span><span>'+json.data.electrician_number+'</span></div>');
                }
            });
        }

        function printDiv(divName) {
            $('#subArea').css('display', 'block');
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
            $('#subArea').css('display', 'none');
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

