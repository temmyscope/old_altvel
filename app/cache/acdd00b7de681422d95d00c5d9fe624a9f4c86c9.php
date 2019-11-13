<?php $__env->startSection('title', 'Login'); ?>
<?php $__env->startSection('content'); ?>

	<?php use app\lib\HTML; ?>

	<?= HTML::Card('Register'); ?>

	<?= HTML::generateForm('register', [
			'name' => [ 'rule' => 'required', 'type'=>'text', 'placeholder' => 'Jane Doe'],
			'email' => [ 'rule' => 'required', 'type'=>'email', 'placeholder' => 'user@example.com'],
			'password' => ['rule' => 'required', 'type' => 'password', 'placeholder' =>'*********'],
			'verify_password' => ['rule' => 'required', 'type' => 'password', 'placeholder' =>'*********'],
			'Register' => [ 'type' => 'submit']
		]);
	?>

	<div class='col-md-4 col-form-label text-md-left'>
		<span> <a href="<?php echo e(route('login')); ?>">Login</a> | <a href="<?php echo e(route('forgot_password')); ?>">Forgot password </a> </span>
	</div>
	
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\larafell\app\view/auth/register.blade.php ENDPATH**/ ?>