<?php $__env->startSection('title', 'Error: Bad Request'); ?>
<?php $__env->startSection('content'); ?>
<?php use app\lib\HTML; ?>

<?= HTML::Card('Error | Bad Request'); ?>

Bad Request. The server does not know how to handle this request.
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\larafell\app\view/errors/bad.blade.php ENDPATH**/ ?>