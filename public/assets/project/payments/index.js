let serverSide = true;
var datatable;
class DataTableClass {
    indexDataTable() {
        datatable = $('#data-table').DataTable({
            processing: true,
            serverSide: serverSide,

            "pageLength": 25,
            "responsive": true,
            "aaSorting": [],
            "order": [], //Initial no order.
            "aLengthMenu": [
                [5, 10, 25, 50, 100, -1],
                [5, 10, 25, 50, 100, "All"]
            ],
            "ajax": {
                "url": APP_URL + "payments",
                "type": "POST",
                "dataType": "JSON",
                data: function (d) {
                    d.type = $('#type').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
            },
            "columnDefs": [{
                //"targets": [0, 5], //first column / numbering column
                "orderable": true, //set not orderable
            },],
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false
            },
            {
                data: 'party_name',
                name: 'party_name'
            },
            {
                data: 'balance_amount',
                name: 'balance_amount'
            },
            {
                data: 'reminder',
                name: 'reminder',
            },
            {
                data: 'note',
                name: 'note',
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
            ]
        });
    }

    type() {
        serverSide = false;
        datatable.ajax.reload();
        serverSide = false;
    }

    reset() {
        serverSide = true;
        $("#type").val("all").attr("selected", "selected");
        $('#start_date').val('');
        $('#end_date').val('');
        datatable.ajax.reload();
        serverSide = false;
    }

    date() {
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();

        $("#type").val("assigned").attr("selected", "selected");

        if (startDate && endDate) {
            serverSide = true;
            // $('#data-table').DataTable().draw(true);            
            datatable.ajax.reload();
            serverSide = false;
        }
    }
}

class FormSubmitClass {

    formSubmit() {
        serverSide = true;
        $('.error').html('');

        let id = $(this).attr('id');

        let assign_id = $('#assign_id').val();
        let party_name = $('#party_name').val();
        let date = $('#date').val();
        let user = $('#user' + ' option').filter(':selected').val();
        let note = $('#note').val();
        $.ajax({
            "url": APP_URL + "payment/assign",
            "dataType": "json",
            "type": "POST",
            "data": {
                assign_id: assign_id,
                party_name: party_name,
                date: date,
                user: user,
                note: note
            },
            success: function (response) {
                if (response.code == 200) {
                    $('#date' + id).val('');
                    $('#note' + id).val('');
                    $('#user' + id + ' option').filter(':selected').prop('selected', false);
                    $('#assignModal').modal('hide');

                    toastr.success(response.message, 'Success');

                    _assigned_users();

                    datatable.ajax.reload();

                    serverSide = false;
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
    }

    messageFormSubmit() {
        var number = null;
        if ($("#number").val()) {
            number = $("#number").val();
        } else {
            number = null;
        }
        var tempMessage = $("#message");

        if (tempMessage.val() != null) {

            var message = $('#message option:selected').text();
        } else if ($('#custom_message').val() !== null || $('#custom_message').val() !== '') {

            var message = $('#custom_message').val();
        } else {
            var message = null;
        }
        if (message != null && number != null) {
            window.open(`https://wa.me/+91${number}?text=${message}`, "_blank");
            $('#sendMessage').modal('hide');
        } else {
            toastr.error(error, 'No Message found');
        }
    }

    sendMessage() {
        let number = $(".sendMessage").data('number');
        if (number) {
            $.ajax({
                url: APP_URL + 'send-message',
                type: "POST",
                data: {
                    number: number,
                },
                dataType: 'json',
                async: false,
                success: function (response) {
                    if (response.status == 200) {
                        $('#myModel').append(response.data)
                        $('#sendMessage').modal('show');
                    } else {
                        toastr.error(error, response.message);
                    }
                },
                error: function (response) {
                    toastr.error(error, 'Faild to load model');
                }
            });
        } else {
            toastr.error(error, 'No Number Found');
        }
    }

}

class ModelOpenClass {

    assignModel() {
        let id = $('.infoModel').data('id');
        let name = $('.infoModel').data('name');
        if (id) {
            $.ajax({
                url: APP_URL + 'payment/assign-model',
                type: "POST",
                data: {
                    id: id,
                    name: name,
                },
                dataType: 'json',
                async: false,
                success: function (response) {
                    if (response.status == 200) {
                        $('#myModel').append(response.data)
                        $('#assignModal').modal('show');
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
    }

    infoModel() {
        let id = $('.infoModel').data('id');
        let name = $('.infoModel').data('name');
        if (id) {
            $.ajax({
                url: APP_URL + 'payment/info-model',
                type: "POST",
                data: {
                    id: id,
                    name: name,
                },
                dataType: 'json',
                async: false,
                success: function (response) {
                    if (response.status == 200) {
                        $('#myModel').append(response.data)
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
            toastr.error(error, 'No Id Found');
        }
    }
}

class ModelCloseClass{
    assignModel(){
        $('#myModel #assignModal').remove();
        $('.modal-backdrop').remove();
    }
    
    infoModel(){
        $('#myModel #infoModal').remove();
        $('.modal-backdrop').remove();
    }
    
    sendMessage(){
        $('#myModel #sendMessage').remove();
        $('.modal-backdrop').remove();
    }
}
const DataTableIndexClass = new DataTableClass();
const FormSubmitIndexClass = new FormSubmitClass();
const ModelOpenIndexClass = new ModelOpenClass();
const ModelCloseIndexClass = new ModelCloseClass();
$(function () {
    _assigned_users();
    if ($('#data-table').length > 0) {
        DataTableIndexClass.indexDataTable();
    }
}).on("click", ".assignModel", function () {
    ModelOpenIndexClass.assignModel();
}).on('click', '.infoModel', function (e) {
    ModelOpenIndexClass.infoModel();
}).on("click", ".sendMessage", function (e) {
    FormSubmitIndexClass.sendMessage();
}).on("submit", ".Messageform", function (e) {
    e.preventDefault();
    FormSubmitIndexClass.messageFormSubmit();
}).on("change", "#type", function () {
    DataTableIndexClass.type();
}).on("click", "#reset", function () {
    DataTableIndexClass.reset();
}).on("change", ".date", function () {
    DataTableIndexClass.date();
}).on("submit", ".form", function (e) {
    e.preventDefault();
    FormSubmitIndexClass.formSubmit();
}).on('hidden.bs.modal', '#assignModal', function (e) {
    ModelCloseIndexClass.assignModel();
}).on('hidden.bs.modal', '#infoModal', function (e) {
    ModelCloseIndexClass.infoModel();
}).on('hidden.bs.modal', '#sendMessage', function (e) {
    ModelCloseIndexClass.sendMessage();
});

function _assigned_users() {
    $.ajax({
        "url": APP_URL + "payment/assigned-users",
        "dataType": "json",
        "type": "get",
        beforeSend: function(){
            
        },
        success: function (response) {
            $('#type').html('');
            $('#type').html(response.data);
        },
        error: function (response) {
            $('#type').html('');
            $('#type').html('<option value="all">All</option><option value="assigned">Assigned</option><option value="not_assigned">Not Assigned</option>');
        }
    });
}