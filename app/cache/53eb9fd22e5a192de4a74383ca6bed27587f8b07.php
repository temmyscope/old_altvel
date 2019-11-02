<?php $__env->startSection('title', 'About'); ?>
<?php $__env->startSection('content'); ?>

	<?php use app\lib\HTML; ?>
	<?= HTML::Card('About Us | Feedback'); ?>

	Welcome To Larafell a.k.a Alt-Laravel.

	You'd find all the possible ways to contact us on this page <br>

	Send us an email to: <a href="mailto:Temmyscope@protonmail.com">Temmyscope@protonmail.com</a>  <br>
	Call us on: <a href="tel:2348173295697">08173295697</a>


	<?= HTML::generateForm('about', [
			'email' => [ 'rule' => 'required', 'type'=>'email', 'placeholder' => 'user@example.com'],
			'feedback' => ['rule' => 'required', 'type' => 'textarea', 'placeholder' =>'Write to Us'],
			'Submit' => [ 'type' => 'submit']
		]);
	?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\larafell\app\view/auth/about.blade.php ENDPATH**/ ?>