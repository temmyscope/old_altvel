<?php $__env->startSection('title', 'Home'); ?>
<?php $__env->startSection('content'); ?>

	<?php use app\lib\HTML; ?>

	<?= HTML::Card('Home'); ?>
	You are now logged In
	
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\larafell\app\view/home/index.blade.php ENDPATH**/ ?>