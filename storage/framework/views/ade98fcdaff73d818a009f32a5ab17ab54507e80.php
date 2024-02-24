<?php $__env->startSection('meta'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?>
    Login
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="content mt-5">
        <div class="brand">
            <!-- <a class="link" href="<?php echo e(route('dashboard')); ?>"><?php echo e(_site_title()); ?></a> -->
            <a class="link" href="<?php echo e(route('dashboard')); ?>">
                <img src="<?php echo e(asset('logo.png')); ?>" alt="<?php echo e(_site_title()); ?>" style="width: 150px; height: 150px;">
            </a>
        </div>
        <form id="form" action="<?php echo e(route('signin')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <?php echo method_field('post'); ?>

            <h2 class="login-title">Log in</h2>
            <div class="form-group">
                <div class="input-group-icon right">
                    <div class="input-icon"><i class="fa fa-envelope"></i></div>
                    <input type="text" name="email" class="form-control" placeholder="Email Or Phone">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback" style="display: block;">
                            <strong><?php echo e($message); ?></strong>
                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group-icon right">
                    <div class="input-icon"><i class="fa fa-lock font-16"></i></div>
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback" style="display: block;">
                            <strong><?php echo e($message); ?></strong>
                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div class="form-group d-flex justify-content-between">
                <a href="<?php echo e(route('forget.password')); ?>">Forgot password?</a>
            </div>
            <div class="form-group">
                <button class="btn btn-info btn-block" type="submit">Login</button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('auth.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\ami-enterprise\resources\views/auth/login.blade.php ENDPATH**/ ?>