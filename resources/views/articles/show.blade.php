@extends('app')

@section('body')
	<div class="page-container">
		<div class="main-content">
			<h1>{{ $article->title }}</h1>
			<p>{{ $article->body }}</p>
		</div>
	</div>
@stop