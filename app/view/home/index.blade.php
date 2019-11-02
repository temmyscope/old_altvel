@extends('app')
@section('title', 'Home')
@section('content')

	<?php use app\lib\HTML; ?>

	<?= HTML::Card('Home'); ?>
	You are now logged In
	
@endsection