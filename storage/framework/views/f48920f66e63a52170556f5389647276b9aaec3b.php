

<?php $__env->startSection('meta'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?>
Calender
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-body">
                    <div class="card">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="eventModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" action="<?php echo e(route('calendar.insert')); ?>" id="eventModelForm" method="post">
                <?php echo csrf_field(); ?>
                <?php echo e(method_field('post')); ?>

                <div class="modal-body">
                    <div class="row mb-1">
                        <input type="hidden" id="eventDate" name="eventdate">

                        <div class="form-group col-md-6 mb-1">
                            <label for="users">Select User</label>
                            <select class="form-control" name="users" id="users">
                                <option value="">Select User</option>
                                <?php if(!empty($users)): ?>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6 mb-1">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title">
                        </div>

                        <div class="form-group col-md-6 mb-1">
                            <label for="start_time">Start Date Time</label>
                            <input type="datetime-local" id="start_time" name="start_time" class="form-control">
                        </div>
                        <div class="form-group col-md-6 mb-1">
                            <label for="end_time">End Date Time</label>
                            <input type="datetime-local" id="end_time" name="end_time" class="form-control">
                        </div>

                        <div class="form-group col-md-12 mb-1">
                            <label for="eventDescription">Description</label>
                            <textarea name="eventDescription" id="eventDescription" cols="30" rows="10" class="form-control" placeholder="Enter Description"></textarea>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('assets/js/validate.js')); ?>"></script>
<script src="<?php echo e(asset('assets/project/calendar/index.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\ami-enterprise\resources\views/calender/index.blade.php ENDPATH**/ ?>