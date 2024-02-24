<script src="{{ asset('assets/vendors/jquery/dist/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/popper.js/dist/umd/popper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/metisMenu/dist/metisMenu.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/chart.js/dist/Chart.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap-2.0.3.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap-us-aea-en.js') }}" type="text/javascript"></script>

<script src="{{ asset('assets/vendors/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/vendors/fullcalendar/dist/fullcalendar.min.js') }}"></script>
<script src="{{ asset('assets/vendors/jquery-ui/jquery-ui.min.js') }}"></script>

<script src="{{ asset('assets/js/app.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/toastr/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/DataTables/datatables.min.js') }}"></script>
@yield('scripts')

<script>
     const APP_URL = '{{ env("APP_URL") }}';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    @php
    $success = '';
    if (\Session::has('success'))
        $success = \Session::get('success');

    $error = '';
    if (\Session::has('error'))
        $error = \Session::get('error');
    @endphp

    var success = "{{ $success }}";
    var error = "{{ $error }}";

    if (success != '') {
        toastr.success(success, 'Success');
    }

    if (error != '') {
        toastr.error(error, 'error');
    }
</script>