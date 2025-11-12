@extends('dashboard.dashboard')

@section('body-class')
    page-assessments
@stop

@section('content')

	<!-- Title -->
	<div class="page-title">
		<div class="title-env">
			<h1 class="title">Create New Assessment</h1>
			<p class="description">Modern, flexible assessment editor with drag-and-drop reordering.</p>
		</div>
	</div>

	<div class="row">

		<!-- Errors -->
		@include('errors.list')

		{!! Form::open(['url' => 'dashboard/assessments', 'enctype' => 'multipart/form-data']) !!}
		@include('dashboard.assessments.partials._form-new', [
            'edit' => false,
            'button_name' => 'Create Assessment'
        ])
		{!! Form::close() !!}

		<!-- Vue.js handles all templates now -->

	</div>
@stop