@extends('app')
@section('title', 'Error: Unknown Error')
@section('content')

	<?php use app\lib\HTML; ?>

	<?= HTML::Card('Error | Unknown Error'); ?>

	Oops!! something went wrong... Unknown error, please try again later.
	
@endsection