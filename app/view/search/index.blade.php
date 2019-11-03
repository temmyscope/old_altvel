@extends('app')
@section('title', 'Search')
@section('content')

	<?php use app\lib\{HTML}; use app\model\Request;  ?>

	<?= HTML::Card('Search Results'); ?>

	You searched for: <?php print_r((Request::post())->search); ?>
	
@endsection