
const calendar = $('#calendar');
class FormSubmit {
    submitForm() {
        $("#eventModelForm").validate({
            submitHandler: function (form) {
                $('#submit').html('Please Wait...');
                $("#submit").attr("disabled", true);
                $.ajax({
                    url: APP_URL + "calendar/insert",
                    type: "POST",
                    data: $('#eventModelForm').serialize(),
                    success: function (response) {
                        $('#submit').html('Submit');
                        $("#submit").attr("disabled", false);
                        toastr.success("Event Created successfully.", 'Success');
                        calendar.fullCalendar('refetchEvents');
                        document.getElementById("eventModelForm").reset();
                        $("#eventModel").modal("hide");
                    },
                    error: function (response) {
                        if (response.status === 422) {
                            var errors_ = response.responseJSON;
                            $.each(errors_, function (key, value) {
                                toastr.error(value, 'Error');
                            });
                            $('#submit').html('Submit');
                            $("#submit").attr("disabled", false);
                        }
                    }
                });
            }
        })
    }
}
const formSubmitClass = new FormSubmit();
$(function () {
    var originalDate;
    calendar.fullCalendar({
        
        aspectRatio: 2.2,
        locale: 'in',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,basicWeek,basicDay'
        },
        defaultDate: new Date(),
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        droppable: true, // this allows things to be dropped onto the calendar
        selectable: true,
        selectHelper: true,
        dragScroll: true,
        events: function (start, end, timezone, callback ,revert) {
            $.ajax({
                url: APP_URL + "calendar/fetch-events",
                type: "POST",
                dataType: 'JSON',
                success: function (doc) {
                    var events = [];
                    doc = JSON.parse(doc.calendar);
                    $(doc).each(function () {
                        events.push({
                            id: this.id,
                            title: this.title,
                            start: this.start,
                            end: this.end,
                            allDay: true
                        });
                    });
                    callback(events);
                }
            });
        },
        drop: function () {
            if ($('#drop-remove').is(':checked')) {
                $(this).remove();
            }
        },
        dayClick: function (e) {
            $("#eventModel").modal('show');
            var date = moment(e).format();
            $("#start_time").val(new Date(date).toJSON().slice(0,19));;
            $("#end_time").val(new Date(date).toJSON().slice(0,19));;
        },
        eventDragStart: function (event) {
            originalDate = new Date(event.start); // Make a copy of the event date
        },
        eventResize: function (info) {
            let id = info.id;
            let start = info.start.toISOString();
            let end = info.end.toISOString();
            $.ajax({
                url: APP_URL + 'calendar/update',
                type: "POST",
                data: {
                    'id': id,
                    'start_time': start,
                    'end_time': end,
                },
                success: function (response) {
                    toastr.success("Event rescheduled successfully.", 'Success');
                },
                error: function (response) {
                    if (response.status === 422) {
                        toastr.error(response.message, 'Error');
                    }
                }
            });
          
        },
        eventDrop: function (e, dayDelta) {
            let id = e.id;
            let start_time = new Date(e.start);
            let end_time = new Date(e.end);
            $.ajax({
                url: APP_URL + 'calendar/update',
                type: "POST",
                data: {
                    'id': id,
                    'start_time': start_time,
                    'end_time': end_time,
                },
                success: function (response) {
                    toastr.success("Event rescheduled successfully.", 'Success');
                },
                error: function (response) {
                    if (response.status === 422) {
                        toastr.error(response.message, 'Error');
                    }
                }
            });
        },

        eventClick: function (info) {
            $.ajax({
                url: APP_URL + "calendar/edit/" + info.id,
                type: "GET",
                success: function (response) {
                    console.log(new Date(response.data.start_date).toJSON().slice(0,19), new Date(response.data.end_date).toJSON().slice(0,19));
                    if (response.status == 200) {
                        $("form").append('<input type="hidden" id="eventID" name="eventID" value="' + info.id + '">');
                        $("#eventDate").val(new Date(response.data.start_date).toJSON().slice(0,19));
                        $("#start_time").val(new Date(response.data.start_date).toJSON().slice(0,19));
                        $("#end_time").val(new Date(response.data.end_date).toJSON().slice(0,19));
                        $("#users").val(response.data.user_id);
                        $("#title").val(response.data.title);
                        $("#eventDescription").val(response.data.event_description);
                        $("#eventModel").modal('show');
                    }
                },
                error: function (response) {
                    if (response.status === 422) {
                        toastr.error(response.message, 'Error');
                    }
                }
            });
        }
    });
    // calendar.render();
}).on("submit", "#eventModelForm", function (e) {
    if ($("#eventModelForm").length > 0) {
        e.preventDefault();
        formSubmitClass.submitForm();
    }
}).on('hidden.bs.modal', '#eventModel', function (e) {
    $("#eventID").remove();
    $('#submit').html('Submit');
    $("#submit").attr("disabled", false);
    document.getElementById("eventModelForm").reset();
});