@extends('dashboard.dashboard')

@section('styles')
	<style>
		.highlights .entry-description h3 {
			color: #333;
		}
		.chart-item-bg .chart-label {
			position: relative;
		}
		.changelog li {
			margin-top: 20px;
			margin-top: 10px;
		}
		.changelog li .status-date {
			font-size: 17px;
			color: #555;
		}
		.changelog li i {
			float: left;
			font-size: 31px;
			margin-right: 10px;
			color: #e5e5e5;
			font-size: 13px;
			color: gold;
		}
		.changelog li p {
			font-size: 13px;
			color: #aaa;
			color: #a0a0a0;
		}
	</style>
@stop

@section('content')

	<div class="row">

		<div class="col-sm-12">
			<div class="chart-item-bg">
				<div class="chart-label">

					{{-- Heading --}}
					<div class="h1 text-warning text-bold">Update {{ Config::get('app.version') }}</div>
					<span class="text-small text-muted text-upper">Changelog</span>

					{{-- Changelog --}}
					<ul class="list-unstyled changelog">
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Optimized and sped up loading of AOE-L reports.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed bug where the Bulk Edit form would not show all assessments.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Updated the Bulk Edit form.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Client Admins can now bulk edit assignments.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Client Admins can now re-send assignment emails to applicants.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added a new section, Assignments, to the Client Dashboard.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Client Admin can now change assignment dates for an applicant on the Applicant Detail view.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Applicant Detail view on the Client Dashboard now shows the applicant's assigned assessments.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Secured the site and resources with HTTPS.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Installed SSL certificate on my.aoescience.com.</p>
						</li>
						@role('admin')
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Edit Assignment view updated to be the same as the Assign Assessments view.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Can now lock assessments to specific jobs when assigning them to users.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Can use the shortcode [job] in assessment descriptions and questions.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Updated job applicant view. You can now see the status of each assessment and can easily edit them if needed, such as to un-expire an assessment.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>For jobs, if an applicant has been assigned duplicate assessments, the data of the last completed assessment will be factored in, instead of the first.</p>
						</li>
						@endrole
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Tons of bug fixes!</p>
						</li>
					</ul>
				</div>
			</div>
		</div>

	</div>
@stop