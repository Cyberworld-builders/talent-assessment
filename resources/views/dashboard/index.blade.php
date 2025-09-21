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
							<i class="fa-asterisk text-success"></i>
							<p><strong>v1.3.11 - Feedback System Integration:</strong> Complete feedback management system with dynamic assessment-based tabs and enhanced UX.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Integrated comprehensive feedback system into user experience with improved error handling and user guidance.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added definition field to dimensions table for detailed descriptions of what each dimension measures.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Enhanced dimension management with parent/child relationship support and '---' option for deselecting parent.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Made feedback library tabs dynamically populated from assessments instead of hardcoded tabs.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed CSRF token handling in feedback submission for proper form processing.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Updated database seeders to use core assessment suite (Involved-360, Involved-Leader, Involved-Blockers).</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Improved test coverage and CI compatibility - all 320 tests now passing.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Enhanced database migration handling and file permissions management.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed CI/CD pipeline test failures for better reliability across environments.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Optimized and sped up loading of assessment reports.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed bug where the Bulk Edit form would not show all assessments.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Client Admins can now bulk edit assignments and re-send assignment emails to applicants.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added comprehensive assignment management section to the Client Dashboard.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Client Admin can now change assignment dates for applicants with improved applicant detail views.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Secured the site and resources with HTTPS and proper SSL certificate implementation.</p>
						</li>
						@role('admin')
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Enhanced assignment views with job-specific assessment locking capabilities.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added [job] shortcode support in assessment descriptions and questions for dynamic content.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Improved job applicant management with better assessment status tracking and editing capabilities.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Enhanced duplicate assessment handling to prioritize latest completed assessment data.</p>
						</li>
						@endrole
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Comprehensive bug fixes and performance improvements throughout the application.</p>
						</li>
					</ul>
				</div>
			</div>
		</div>

	</div>
@stop