@extends('app')

@section('body')
	<div class="page-container">
		<div class="main-content">

			<h1>Articles</h1><br/>
			<a class="btn btn-primary" href="{{ url('/articles/create') }}">Create New</a>
			<hr/>

			@foreach ($articles as $article)
				<article>
					<h2>
						<a href="{{ url('/articles', $article->id) }}">{{ $article->title }}</a>
					</h2>
					<div class="date">Published {{ $article->date }}</div>
					<p>{{ $article->body }}</p>
				</article>
			@endforeach
		</div>
	</div>
@stop