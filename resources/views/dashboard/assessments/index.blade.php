@extends('dashboard.dashboard')

@section('styles')
	<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
	<style>
		.translation {
			background: rgb(247, 247, 247) none repeat scroll 0% 0%;
			border-bottom: 1px solid rgb(213, 213, 213);
			padding: 0px 0px 10px 100px;
			overflow: hidden;
		}
		.translation h3 {
			font-size: 18px;
		}
		.translation h3 span {
			font-size: 12px;
		}
		.translation .body {
			float: left;
		}
		.translation .controls {
			float: right;
			padding-right: 20px;
		}
		.translation .controls .control {
			padding-top: 20px;
			display: inline-block;
			margin-left: 10px;
		}
	</style>
@stop

@section('sidebar-class')
	{{--@role('admin')--}}
		{{--collapsed--}}
	{{--@endrole--}}
@stop

@section('content')

	<div class="row">
		<div class="col-sm-12">

			<!-- Title -->
			<div class="page-heading">
				<h1>AOE Assessments</h1>
			</div>

			<!-- Assessments -->
			<div class="assessments">

				@foreach ($assessments as $assessment)

					<!-- Assessment -->
					<div class="assessment">

						<!-- Checkbox -->
						{{--<div class="checkbox">--}}
							{{--<input type="checkbox" class="icheck">--}}
						{{--</div>--}}

						<!-- Body -->
						<div class="body">
							<h3>
								@role('admin')
									<a href="{{ url('dashboard/assessments/'.$assessment->id.'/edit') }}">
								@endrole
										{{ $assessment->name }}
								@role('admin')
									</a>
								@endrole
							</h3>
							<div class="date">Last Modified on {{ $assessment->updated_at->format('M jS, Y') }} by {{ $assessment->user->name }}</div>
						</div>

						<!-- Controls -->
						<div class="controls">
							@role('admin')
								{!! Form::open(['method' => 'delete', 'action' => ['AssessmentsController@destroy', $assessment->id]]) !!}
									<a class="control" href="{{ url('dashboard/assessments/'.$assessment->id) }}">
										<i class="fa-eye"></i><br/>
										<span>Preview</span>
									</a>
									<a class="control" href="{{ url('dashboard/assessments/'.$assessment->id.'/edit') }}">
										<i class="fa-edit"></i><br/>
										<span>Edit</span>
									</a>
									<a class="control" href="{{ url('dashboard/assessments/'.$assessment->id.'/translations/create') }}">
										<i class="fa-globe"></i><br/>
										<span>Translate</span>
									</a>
									<a href="#null" class="control delete" data-name="{{ $assessment->name }}">
										<i class="fa-trash"></i><br/>
										<span>Remove</span>
									</a>
								{!! Form::close() !!}
							@endrole
							@role('client')
								<a class="control" href="{{ url('dashboard/assign') }}">
									<i class="fa-paper-plane-o"></i><br/>
									<span>Assign</span>
								</a>
							@endrole
							{{--<a class="control" href="#">--}}
								{{--<i class="fa-copy"></i><br/>--}}
								{{--<span>Copy</span>--}}
							{{--</a>--}}
							{{--<a class="control" href="{{ url('dashboard/assessments/'.$assessment->id.'/assign') }}">--}}
								{{--<i class="fa-paper-plane-o"></i><br/>--}}
								{{--<span>Assign</span>--}}
							{{--</a>--}}
							{{--<a class="control" href="#">--}}
								{{--<i class="fa-bar-chart"></i><br/>--}}
								{{--<span>Reports</span>--}}
							{{--</a>--}}
							{{--<a class="control" href="#">--}}
								{{--<i class="fa-download"></i><br/>--}}
								{{--<span>Download</span>--}}
							{{--</a>--}}
						</div>

					</div>

					{{--@if ($assessment->translations)--}}
						{{--@foreach ($assessment->translations as $translation)--}}
							{{--<div class="translation">--}}

								{{--<!-- Body -->--}}
								{{--<div class="body">--}}
									{{--<i class="fa-level-up" style="position: relative; transform: rotate(90deg); color: rgb(170, 170, 170); float: left; left: -32px; top: 19px; font-size: 17px;"></i>--}}
									{{--<h3>--}}
										{{--{{ $translation->name }}--}}
										{{--<span class="language">({{ App\Language::find($translation->language_id)->name }} Translation)</span>--}}
									{{--</h3>--}}
								{{--</div>--}}

								{{--<!-- Controls -->--}}
								{{--<div class="controls">--}}
									{{--@role('admin')--}}
										{{--<a class="control" href="{{ url('dashboard/translations/'.$translation->id.'/edit') }}">--}}
											{{--<i class="fa-edit"></i> <span>Edit</span>--}}
										{{--</a>--}}
										{{--<a href="#null" class="control delete-translation" data-name="{{ $translation->name }}" data-url="/dashboard/translations/{{ $translation->id }}">--}}
											{{--<i class="linecons-trash"></i> <span>Delete</span>--}}
										{{--</a>--}}
									{{--@endrole--}}
								{{--</div>--}}
							{{--</div>--}}
						{{--@endforeach--}}
					{{--@endif--}}
				@endforeach

				@if (! $assessments->first())
					<div class="well">
						You have not been given access to any Assessments.
					</div>
				@endif

			</div>

			<!-- Add Dimension Button -->
			<div class="pull-left">
				<br/>
				<a href="{{ url('dashboard/assessments/create') }}" class="btn btn-lg btn-primary" style="padding-top: 14px;">Create New Assessment</a>
			</div>

			<script type="text/javascript">
				jQuery(document).ready(function($){

					// Delete the specified resource
					$('.delete').on('click', function() {
						var name = $(this).attr('data-name');
						var form = $(this).closest('form');

						if (confirm('WARNING: Deleting this assessment means that ALL data collected\nfor this assessment from ALL users will be wiped. You will not\nbe able to recover this data.\n\nAre you absolutely sure you wish to proceed with deleting '+name+'?\n'))
							form.submit();
					});

					// Set headers for AJAX
//					$.ajaxSetup({
//						headers: {
//							'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
//						}
//					});

					// Checkboxes
//					$('input.icheck').iCheck({
//						checkboxClass: 'icheckbox_square-aero',
//						radioClass: 'iradio_square-orange'
//					});

					// Sidebar menu default
//					$('.sidebar-menu-under .menu-category[data-parent="Assessments"]').show();

					// Delete Translation
//					$('.delete-translation').on('click', function() {
//						var name = $(this).attr('data-name');
//						var url = $(this).attr('data-url');
//
//						if (confirm('Are you sure you want to delete the translation '+name+' ?'))
//						{
//							$.ajax({
//								type: 'delete',
//								url: url,
//								dataType: 'json',
//								success: function (data) {
//									console.log(data);
//									window.location.reload();
//								},
//								error: function (data) {
//									console.log(data.status + ' ' + data.statusText);
//									$('html').prepend(data.responseText);
//								}
//							});
//						}
//					});
				});
			</script>

		</div>
	</div>
@stop

@section('scripts')
	<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
@stop