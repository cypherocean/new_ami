<script src="<?php echo e(asset('assets/vendors/jquery/dist/jquery.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/vendors/popper.js/dist/umd/popper.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/vendors/bootstrap/dist/js/bootstrap.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/vendors/metisMenu/dist/metisMenu.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/vendors/jquery-slimscroll/jquery.slimscroll.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/vendors/chart.js/dist/Chart.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/vendors/jvectormap/jquery-jvectormap-2.0.3.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/vendors/jvectormap/jquery-jvectormap-us-aea-en.js')); ?>" type="text/javascript"></script>

<script src="<?php echo e(asset('assets/vendors/moment/min/moment.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendors/fullcalendar/dist/fullcalendar.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendors/jquery-ui/jquery-ui.min.js')); ?>"></script>

<script src="<?php echo e(asset('assets/js/app.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/vendors/toastr/toastr.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('assets/vendors/DataTables/datatables.min.js')); ?>"></script>
<?php echo $__env->yieldContent('scripts'); ?>

<script>
     const APP_URL = '<?php echo e(env("APP_URL")); ?>';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    <?php
    $success = '';
    if (\Session::has('success'))
        $success = \Session::get('success');

    $error = '';
    if (\Session::has('error'))
        $error = \Session::get('error');
    ?>

    var success = "<?php echo e($success); ?>";
    var error = "<?php echo e($error); ?>";

    if (success != '') {
        toastr.success(success, 'Success');
    }

    if (error != '') {
        toastr.error(error, 'error');
    }
</script><?php /**PATH C:\xampp\htdocs\ami-enterprise\resources\views/layout/scripts.blade.php ENDPATH**/ ?>