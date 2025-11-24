@extends('install.layouts.master')
@section('title', trans('messages.cron_jobs_title'))

@section('content')
	
	@include('elements._cron_jobs')
	
	<div class="text-end">
		<a href="{{ data_get($stepsUrls, 'finish') }}" class="btn btn-primary bg-teal">
			{!! trans('messages.next') !!} <i class="fa-solid fa-chevron-right position-right"></i>
		</a>
	</div>
	
@endsection

@section('after_scripts')
@endsection
