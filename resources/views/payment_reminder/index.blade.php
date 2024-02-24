@extends('layout.app')

@section('meta')
@endsection

@section('title')
Payments Reminders
@endsection

@section('styles')
<style>
    .followup_details {
        margin: 0px 5px;
        padding: 8px 10px !important;
        border-left: 4px solid #4CAF50;
        word-wrap: break-word;
        box-shadow: 0px 0px 6px 3px silver;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Payments Reminders</div>
                    <h1 class="pull-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#report">Report </button>
                    </h1>
                </div>

                <div class="ibox-body">
                    <ul class="nav nav-tabs tabs-line">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab" data-id="today" data-toggle="tab" aria-expanded="true">Today</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab" data-id="future" data-toggle="tab" aria-expanded="false">Upcoming</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab" aria-expanded="false">
                            <div class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <table class="table table-bordered data-table" id="data-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Date</th>
                                            <th>User Name</th>
                                            <th>Party Name</th>
                                            <th>Mobile No</th>
                                            <th>Note</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="text-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="report" tabindex="-1" role="dialog" aria-labelledby="reportTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">Sr.No</th>
                                    <th width="25%">User Name</th>
                                    <th width="25%">Party Name</th>
                                    <th width="15%">Next Date</th>
                                    <th width="30%">Note</th>
                                </tr>
                            </thead>
                            <tbody id="report_datatable"></tbody>
                        </table>
                        <div id="report_pagination"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="myModel">
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    var datatable;
    var date = 'today';

    $(document).ready(function() {
        // $('.digit').on('keyup', function(e){
        $('body').on('keyup', '.digit', function(e) {
            if (/\D/g.test(this.value)) {
                this.value = this.value.replace(/\D/g, '');
            }
        });

        $('.nav-link').click(function() {
            date = $(this).data('id');
            $('#data-table').DataTable().draw(true);
        });

        if ($('#data-table').length > 0) {
            datatable = $('#data-table').DataTable({
                processing: true,
                serverSide: true,

                "pageLength": 25,
                // "iDisplayLength": 10,
                "responsive": true,
                "aaSorting": [],
                "order": [], //Initial no order.
                "aLengthMenu": [
                    [5, 10, 25, 50, 100, -1],
                    [5, 10, 25, 50, 100, "All"]
                ],

                // "scrollX": true,
                // "scrollY": '',
                // "scrollCollapse": false,
                // scrollCollapse: true,

                // lengthChange: false,

                "ajax": {
                    "url": "{{ route('payments.reminders') }}",
                    "type": "POST",
                    "dataType": "json",
                    // "data":{
                    //     _token: "{{csrf_token()}}"
                    // }
                    data: function(d) {
                        d._token = "{{csrf_token()}}";
                        d.date = date;
                    }
                },
                "columnDefs": [{
                    //"targets": [0, 5], //first column / numbering column
                    "orderable": true, //set not orderable
                }, ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'next_date',
                        name: 'next_date'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'party_name',
                        name: 'party_name'
                    },
                    {
                        data: 'mobile_no',
                        name: 'mobile_no'
                    },
                    {
                        data: 'note',
                        name: 'note'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ]
            });
        }
    }).on('click', '.add_followup', function(e) {
        let id = $(this).data('id');
        if (id) {
            $.ajax({
                url: '{!! route("payment.add.followup") !!}',
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                dataType: 'json',
                async: false,
                success: function(response) {
                    if (response.status == 200) {
                        $('#myModel').append(response.data)
                        $('#examplefollowup').modal('show');
                        $("#examplefollowup form").attr('id', 'followup' + id);
                        $("#examplefollowup form").attr('action', 'route("payments.reminders.insert")');
                        $("#examplefollowup form").attr('method', 'post');
                    } else {
                        toastr.error(error, response.message);
                    }
                },
                error: function(response) {
                    toastr.error(error, 'Faild to load model');
                }
            });
        } else {
            toastr.error(error, 'No Id Found');
        }
    }).on('click' ,'.followup_detail' ,function(e){
        let name = $(this).data('name');
        let id = $(this).data('id');
        if (name) {
            $.ajax({
                url: '{!! route("payment.followup.details") !!}',
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    name: name,
                    id: id,
                },
                dataType: 'json',
                async: false,
                success: function(response) {
                    if (response.status == 200) {
                        $('#myModel').append(response.data);
                        $('#details').modal('show');
                    } else {
                        toastr.error(error, response.message);
                    }
                },
                error: function(response) {
                    toastr.error(error, 'Faild to load model');
                }
            });
        }else{
            toastr.error(error, 'No Party name Found!');
        }
    }).on('click' ,'.billDetails' ,function(e){
        let name = $(this).data('name');
        let id = $(this).data('id');
        if (name) {
            $.ajax({
                url: '{!! route("payment.bill.details") !!}',
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    name: name,
                    id: id,
                },
                dataType: 'json',
                async: false,
                success: function(response) {
                    if (response.status == 200) {
                        $('#myModel').append(response.data);
                        $('#infoModal').modal('show');
                    } else {
                        toastr.error(error, response.message);
                    }
                },
                error: function(response) {
                    toastr.error(error, 'Faild to load model');
                }
            });
        }else{
            toastr.error(error, 'No Party name Found!');
        }
    }).on('hidden.bs.modal' ,'#details', function (e) {
        $('#myModel #details').remove();
    }).on('hidden.bs.modal' ,'#infoModal', function (e) {
        $('#myModel #infoModal').remove();
    }).on('hidden.bs.modal' ,'#assignModal', function (e) {
        $('#myModel #assignModal').remove();
    });

    $(document).on("submit", ".form", function(e) {
        e.preventDefault();
        $('.error').html('');

        let id = $(this).attr('id');
        let party_name = $('#party_name').val();
        let next_date = $('#next_date').val();
        let next_time = $('#next_time').val();
        let mobile_no = $('#mobile_no').val();
        let amount = $('#amount').val();
        let note = $('#note').val();
        console.log(party_name, next_date);
        $.ajax({
            "url": "{!! route('payments.reminders.insert') !!}",
            "dataType": "json",
            "type": "POST",
            "data": {
                party_name: party_name,
                next_date: next_date,
                next_time: next_time,
                mobile_no: mobile_no,
                amount: amount,
                note: note,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.code == 200) {
                    $('#examplefollowup').modal('hide');
                    $('#examplefollowup').remove();
                    toastr.success(response.message, 'Success');
                    $('#data-table').DataTable().draw(true);
                } else {
                    toastr.error(response.message, 'Error');
                }
            },
            error: function(response) {
                if (response.status === 422) {
                    var errors_ = response.responseJSON;
                    $.each(errors_, function(key, value) {
                        toastr.error(value, 'Error');
                    });
                }
            }
        });
    });

    function change_status(object) {
        var id = $(object).data("id");
        var status = $(object).data("status");
        var name = $(object).data("name");
        var msg = "Are you Sure?";

        if (confirm(msg)) {
            $.ajax({
                "url": "{!! route('payments.reminders.change.status') !!}",
                "dataType": "json",
                "type": "POST",
                "data": {
                    id: id,
                    status: status,
                    name: name,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.code == 200) {
                        datatable.ajax.reload();
                        toastr.success('Record status changed successfully.', 'Success');
                    } else {
                        toastr.error('Failed to delete record.', 'Error');
                    }
                }
            });
        }
    }

    $(document).ready(function() {
        _reports(0);

        $(document).on('click', '#report_pagination .pagination a', function(event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            _reports(page);
        });
    });


    function _reports(page) {
        $.ajax({
            "url": "{!! route('payments.reminders.reports') !!}" + "?page=" + page,
            "dataType": "json",
            "type": "Get",
            success: function(response) {
                $('#report_datatable').html(response.data);
                $('#report_pagination').html(response.pagination);
            },
            error: function(response) {
                $('#report_datatable').html('<td colspan="5" class="text-center"><h3>No data found</h3></td>');
                $('#report_pagination').html('');
            }
        });
    }
</script>
@endsection