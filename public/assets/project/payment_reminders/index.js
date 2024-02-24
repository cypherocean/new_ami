var datatable;
var date = 'today';
class DatataleClass {
    datatable(tab = 'today') {
        var newTab = null;
        if(tab == 'today'){
            newTab = tab;
        } else {
            newTab = 'future';
        }
        if ( $.fn.DataTable.isDataTable('#' + newTab + '-data-table') ) {
            $('#' + newTab + '-data-table').DataTable().destroy();
        }
        datatable = $('#' + newTab + '-data-table').DataTable({
            processing: true,
            serverSide: true,

            "pageLength": 25,
            "responsive": true,
            "aaSorting": [],
            "order": [], //Initial no order.
            "aLengthMenu": [
                [5, 10, 25, 50, 100, -1],
                [5, 10, 25, 50, 100, "All"]
            ],

            "ajax": {
                "url": APP_URL + "payments-reminder",
                "type": "POST",
                "dataType": "json",
                data: function (d) {
                    d.date = tab;
                }
            },
            "columnDefs": [{
                "orderable": true, //set not orderable
            },],
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
}
function change_status(object) {
    var id = $(object).data("id");
    var status = $(object).data("status");
    var name = $(object).data("name");
    var msg = "Are you Sure?";

    if (confirm(msg)) {
        $.ajax({
            "url": APP_URL+"payments-reminder/change-status",
            "dataType": "json",
            "type": "POST",
            "data": {
                id: id,
                status: status,
                name: name
            },
            success: function (response) {
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

const DataTableIndexClass = new DatataleClass();
$(function () {
    _reports(0);
    $('body').on('keyup', '.digit', function (e) {
        if (/\D/g.test(this.value)) {
            this.value = this.value.replace(/\D/g, '');
        }
    });
    let tab = $(".nav-link :active").val();
    DataTableIndexClass.datatable(tab);
}).on('click', '#report_pagination .pagination a', function (event) {
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    _reports(page);
}).on("submit", ".form", function (e) {
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
        "url": APP_URL + "payments-reminder/insert",
        "dataType": "json",
        "type": "POST",
        "data": {
            party_name: party_name,
            next_date: next_date,
            next_time: next_time,
            mobile_no: mobile_no,
            amount: amount,
            note: note
        },
        success: function (response) {
            if (response.code == 200) {
                $('#examplefollowup').modal('hide');
                $('#examplefollowup').remove();
                toastr.success(response.message, 'Success');
                $('#data-table').DataTable().draw(true);
            } else {
                toastr.error(response.message, 'Error');
            }
        },
        error: function (response) {
            if (response.status === 422) {
                var errors_ = response.responseJSON;
                $.each(errors_, function (key, value) {
                    toastr.error(value, 'Error');
                });
            }
        }
    });
}).on('click', '.add_followup', function (e) {
    let id = $(this).data('id');
    if (id) {
        $.ajax({
            url: APP_URL + 'payment/add-followup',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            async: false,
            success: function (response) {
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
            error: function (response) {
                toastr.error(error, 'Faild to load model');
            }
        });
    } else {
        toastr.error(error, 'No Id Found');
    }
}).on('click', '.followup_detail', function (e) {
    let name = $(this).data('name');
    let id = $(this).data('id');
    if (name) {
        $.ajax({
            url: APP_URL + 'payment/followup-details',
            type: "POST",
            data: {
                name: name,
                id: id,
            },
            dataType: 'json',
            async: false,
            success: function (response) {
                if (response.status == 200) {
                    $('#myModel').append(response.data);
                    $('#details').modal('show');
                } else {
                    toastr.error(error, response.message);
                }
            },
            error: function (response) {
                toastr.error(error, 'Faild to load model');
            }
        });
    } else {
        toastr.error(error, 'No Party name Found!');
    }
}).on('click', '.billDetails', function (e) {
    let name = $(this).data('name');
    let id = $(this).data('id');
    if (name) {
        $.ajax({
            url: APP_URL + 'payment/bill-details',
            type: "POST",
            data: {
                name: name,
                id: id,
            },
            dataType: 'json',
            async: false,
            success: function (response) {
                if (response.status == 200) {
                    $('#myModel').append(response.data);
                    $('#infoModal').modal('show');
                } else {
                    toastr.error(error, response.message);
                }
            },
            error: function (response) {
                toastr.error(error, 'Faild to load model');
            }
        });
    } else {
        toastr.error(error, 'No Party name Found!');
    }
}).on('hidden.bs.modal', '#details', function (e) {
    $('#myModel #details').remove();
}).on('hidden.bs.modal', '#infoModal', function (e) {
    $('#myModel #infoModal').remove();
}).on('hidden.bs.modal', '#assignModal', function (e) {
    $('#myModel #assignModal').remove();
    $('.modal-backdrop').remove();
}).on("click", ".nav-link", function () {
    date = $(this).data('id');
    $(".radioParent").addClass('d-none');
    if (date == 'future') {
        $(".radioParent").removeClass('d-none');
    }
    $('.nav-item .nav-link').removeClass("active");
    $('.tab-pane').removeClass("active");
    $('#' + date).addClass("active");
    $('#' + date + 'Tab').addClass("active");
    DataTableIndexClass.datatable(date);
}).on("change", '#uiRadio', function () {
    let paymentType = $("input[name='testRadio']:checked").val();
    if(typeof paymentType != 'undefined' && paymentType != null && paymentType != ''){
        DataTableIndexClass.datatable(paymentType);
    }else{
        toastr.error(error, 'No button selected!');
    }
});


function _reports(page) {
    $.ajax({
        "url": APP_URL + "payments-reminder/reports" + "?page=" + page,
        "dataType": "json",
        "type": "Get",
        success: function (response) {
            $('#report_datatable').html(response.data);
            $('#report_pagination').html(response.pagination);
        },
        error: function (response) {
            $('#report_datatable').html('<td colspan="5" class="text-center"><h3>No data found</h3></td>');
            $('#report_pagination').html('');
        }
    });
}