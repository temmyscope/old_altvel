@extends('app')
@section('title', 'Error')
@section('content')

	<?php use app\lib\HTML; ?>

	<?= HTML::Card('Error'); ?>

	An Error Occurred. That content does not exist.
	
@endsection