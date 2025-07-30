@extends('app')

@section('body')
	<div class="container">
		<h1>Write A New Article</h1>
		<hr/>

		{!! Form::open(['url' => 'articles']) !!}

			<!-- Title Field -->
			<div class="form-group">
				{!! Form::label('title', 'Title: ') !!}
				{!! Form::text('title', null, ['class' => 'form-control']) !!}
			</div>

			<!-- Body Field -->
			<div class="form-group">
				{!! Form::label('body', 'Body: ') !!}
				{!! Form::textarea('body', null, ['class' => 'form-control']) !!}
			</div>

			<!-- Date Field -->
			<div class="form-group">
				{!! Form::label('published_at', 'Published On: ') !!}
				{!! Form::input('date', 'published_at', date('Y-m-d'), ['class' => 'form-control']) !!}
			</div>

			<!-- Submit Field -->
			<div class="form-group"> 
				{!! Form::submit('Add Article', ['class' => 'btn btn-primary form-control']) !!}
			</div>
		{!! Form::close() !!}
	</div>
@stop