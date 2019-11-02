<?php $__env->startSection('title', 'Search'); ?>
<?php $__env->startSection('content'); ?>

	<?php use app\lib\{HTML}; use app\model\Request;  ?>

	<?= HTML::Card('Search Results'); ?>

	You searched for: <?php print_r((Request::post())->search); ?>
	
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\larafell\app\view/search/index.blade.php ENDPATH**/ ?>