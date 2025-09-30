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
                       <div class="h1 text-warning text-bold">Update {{ config('app.version') }}</div>
                       <span class="text-small text-muted text-upper">Changelog</span>

                       {{-- Changelog --}}
        <ul class="list-unstyled changelog">
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.34 - CSV Upload Enhancements & Production Fixes:</strong> Enhanced user bulk upload functionality with template downloads, automatic username generation, and fixed production deployment issues.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added CSV template download feature with proper headers (Name, Email, Industry, Username) for easy bulk user creation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Implemented automatic username generation from names when not provided in CSV uploads to prevent validation errors.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed CSV upload blank rows issue by adding validation to skip empty rows and rows without name/email.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced CSV parsing to support multiple formats including standard (Name, Email, Industry) and Involved-360 formats.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed production deployment issues with proper environment file creation and APP_KEY persistence across container restarts.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated production APP_URL to use correct domain (https://my.involvedtalent.com) and added comprehensive debugging for deployments.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.30 - Assignment Form Improvements & Test Coverage:</strong> Enhanced assignment forms with better email notifications, removed white label fields, and added comprehensive test coverage.</p>
            </li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed email notification field visibility in client assignment forms - field now properly displays without syntax errors.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Removed white label field from assignment forms to simplify the user interface and reduce form complexity.</p>
						</li>
						<li>
							<i class="fa-asterisk text-warning"></i>
							<p>Fixed GroupsController undefined variable error that was causing runtime issues in group management.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added comprehensive test coverage for assignment forms to ensure reliability and prevent regressions.</p>
						</li>
						<li>
							<i class="fa-asterisk text-warning"></i>
							<p>Fixed test failures related to jobs slug constraint and assessment foreign key issues in the test suite.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Optimized staging deployment workflow for faster and more reliable deployments.</p>
						</li>
						<li>
							<i class="fa-asterisk text-success"></i>
							<p><strong>v1.5.29 - Assessment Assign Tab Removal:</strong> Removed the assign tab from assessments page to streamline the assignment workflow and reduce confusion.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Removed assessment assign functionality from assessments page - assignments should now be done through the dedicated assignments section.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Cleaned up routes, controller methods, and templates related to assessment assignment functionality.</p>
						</li>
						<li>
							<i class="fa-asterisk text-success"></i>
							<p><strong>v1.5.28 - Infrastructure Planning & UI Fixes:</strong> Created comprehensive infrastructure plan for dedicated production environment, fixed login page logo, and improved asset management.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added comprehensive infrastructure documentation and deployment planning for dedicated production environment with RDS, ECS, and ALB.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed missing logo.png on login page by updating asset path from images/ to assets/images/ directory.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added critical Docker container usage rules to .cursorrules for proper command execution.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Improved asset management by standardizing image paths and running Gulp build to compile all assets.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Enhanced environment variable handling and Docker container usage documentation.</p>
						</li>
						<li>
							<i class="fa-asterisk text-warning"></i>
							<p>Fixed Involved360AssessmentSeeder question numbering to ensure sequential ordering for assessment input fields.</p>
						</li>
						<li>
							<i class="fa-asterisk text-warning"></i>
							<p>Resolved GitHub Actions MySQL client installation issues in CI/CD pipelines.</p>
						</li>
						<li>
							<i class="fa-asterisk text-warning"></i>
							<p>Fixed production and staging environment configurations and removed sensitive files from git tracking.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p><strong>v1.5.25 - S3 Configuration & Minor Fixes:</strong> Standardized S3 file uploads across all environments, fixed assessment form 500 errors, and improved assignment URL generation.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Removed conditional S3/local storage logic - all environments now use S3 consistently with CloudFront CDN integration.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed assessment form submission 500 errors and MathJax CDN warning issues for improved form reliability.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed assignment URL generation to use proper domain across all environments for consistent link behavior.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Improved password reset page logo placement and centered forgot password link for better UX.</p>
						</li>
					</ul>
				</div>
			</div>
		</div>

	</div>
@stop