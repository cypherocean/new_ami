<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">

    <?php echo $__env->yieldContent('meta'); ?>

    <title><?php echo e(_site_title()); ?> | <?php echo $__env->yieldContent('title'); ?></title>
    
    <link href="<?php echo e(asset('assets/vendors/bootstrap/dist/css/bootstrap.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/vendors/font-awesome/css/font-awesome.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/vendors/themify-icons/css/themify-icons.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/main.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/pages/auth-light.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/toastr.css')); ?>" rel="stylesheet" />

    <?php echo $__env->yieldContent('styles'); ?>
</head>

<body class="bg-silver-300">
    <div class="content">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    
    <script src="<?php echo e(asset('assets/vendors/jquery/dist/jquery.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('assets/vendors/popper.js/dist/umd/popper.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('assets/vendors/bootstrap/dist/js/bootstrap.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('assets/vendors/jquery-validation/dist/jquery.validate.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('assets/js/app.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('assets/js/toastr.js')); ?>" type="text/javascript"></script>

    <script>
        <?php
            $success = '';
            if(\Session::has('success'))
                $success = \Session::get('success');

            $error = '';
            if(\Session::has('error'))
                $error = \Session::get('error');
        ?>

        var success = "<?php echo e($success); ?>";
        var error = "<?php echo e($error); ?>";

        if(success != ''){
            toastr.success(success, 'Success');
        }

        if(error != ''){
            toastr.error(error, 'error');
        }
    </script>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html><?php /**PATH C:\xampp\htdocs\ami-enterprise\resources\views/auth/layout/app.blade.php ENDPATH**/ ?>