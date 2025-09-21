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

					<div class="h1 text-muted text-bold">Welcome To Involved Talent</div>

					{{-- Heading --}}
					<div class="h1 text-warning text-bold">Update v1.5.9-release</div>
					<span class="text-small text-muted text-upper">Changelog</span>

					{{-- Changelog --}}
					<ul class="list-unstyled changelog">
						<li>
							<i class="fa-asterisk text-success"></i>
							<p><strong>v1.5.9 - Critical User Loading & Error Handling Fixes:</strong> Resolved critical issues preventing users from loading myinvolvedtalent.com with enhanced error handling and validation.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed CSRF token mismatch handling with graceful session invalidation and user-friendly error messages.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added comprehensive industry field validation for user creation in both UsersController and ResellersController.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Enhanced authentication error handling with proper redirects and clear error messaging.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Improved form data preservation on validation errors while excluding sensitive password fields.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added proper exception handling for TokenMismatchException, UnauthorizedException, and ValidationException.</p>
						</li>
						<li>
							<i class="fa-asterisk text-success"></i>
							<p><strong>v1.5.8 - Production Deployment & Docker Optimization:</strong> Complete overhaul of deployment infrastructure with multi-stage Docker builds and enhanced CI/CD workflows.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Implemented multi-stage Docker builds with separate npm and composer stages for optimized production images.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added workflow_dispatch support to both staging and production deployment workflows for manual triggering.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added skip_tests option to deployment workflows for faster deployments when tests are not needed.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed dev environment ownership issues by removing www-data user switching for artisan serve.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added composer.lock to repository for consistent dependency versions in production builds.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Consolidated Dockerfiles and updated docker-compose configurations for better environment management.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Enhanced hot reloading support for development environment with proper volume mappings.</p>
						</li>
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
						<li>
							<i class="fa-asterisk text-success"></i>
							<p><strong>v1.5.4 - Dashboard UI/UX Improvements:</strong> Complete dashboard redesign to match involved-legacy styling and enhanced user experience.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Updated dashboard sidebar with dark blue theme (#1d3a51) and proper logo integration.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Reordered sidebar navigation menu: Home, Assessments, Clients, Users, Industries, Benchmarks, Feedback.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Removed admin text and megaphone notifications icon from top navigation bar for cleaner interface.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added "Welcome To Involved Talent" message to main dashboard content area.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed CSS specificity issues with sidebar background color overrides.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Enhanced CI/CD workflow with Node.js setup and gulp build integration for frontend assets.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Improved hot reloading support for development environment with proper volume mappings.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Cleaned up malformed comment syntax and template structure for better maintainability.</p>
						</li>
					</ul>
				</div>
			</div>
		</div>

	</div>
@stop