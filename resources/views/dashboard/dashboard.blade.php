@extends('app')

@section('title')
	AOE : Dashboard
@stop

@section('body')

	{{-- @include('dashboard.partials._settings') --}}
	
	<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
			
		@include('dashboard.partials._sidebar')
	
		<div class="main-content">
					
			@include('dashboard.partials._nav')
			@yield('content')
			@include('dashboard.partials._footer')

		</div>
	
	</div>

@stop