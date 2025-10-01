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
                <p><strong>v1.5.39 - Assessment Dimension Consistency Fix:</strong> Fixed dimension selection inconsistency in assessment editor where input fields showed all dimensions instead of assessment-specific ones.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated AssessmentsController.edit() method to load only dimensions belonging to the specific assessment being edited, ensuring consistency between dimensions tab and input field options.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Resolved discrepancy where assessment input fields displayed a more complete list of dimensions than what was shown in the assessment's dimensions tab.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.37 - Reports & Assignment Flow Fixes:</strong> Resolved critical JavaScript errors in assignment forms and reports functionality with enhanced user experience improvements.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed assignment form JavaScript errors by adding proper modal variable definitions to all user selection functions (From Client, From Groups, From Job Family, From Job).</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Resolved "Uncaught ReferenceError: $modal is not defined" errors that were breaking the assignment flow's user selection buttons.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed reports functionality by adding null checks to Report model's getModelAttribute() and getModelFactorsAttribute() methods.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Resolved "Undefined index: model" and "Undefined variable: intro" errors in reports customize functionality.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced language selection to automatically default to English and skip the language selection page for improved user experience.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed _cover.blade.php template by converting @php directive to regular PHP code with proper variable scoping and null checks.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.36 - Major Functionality Restoration & Error Handling:</strong> Comprehensive fixes for CSV uploads, user management, and download functionality with enhanced error handling throughout the system.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed user CSV upload industry field population by updating JavaScript to properly populate industry field after CSV upload.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Replaced Excel library with native CSV generation for user downloads to resolve PHP 7.4+ compatibility issues.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Added comprehensive error handling for user creation with specific database error messages, industry validation, and email format validation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced frontend error display with structured error messages, success notifications, and graceful AJAX error handling.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated user management system to replace Job Title/Job Family fields with Industry field throughout all interfaces.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed groups CSV upload UI refresh issues and resolved edit functionality errors.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed JavaScript error preventing page refresh after successful groups CSV upload by removing undefined 'opts' variable.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced groups CSV upload success callback with proper console logging and page reload functionality.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed groups edit functionality by removing undefined 'groupUsers' variable from compact() function.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated groups CSV upload to handle both old format (without Group Name) and new format (with Group Name) seamlessly.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.35 - Groups CSV Upload Enhancement:</strong> Converted groups bulk upload from Excel to CSV format with template downloads and enhanced processing capabilities.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added CSV template download for groups with proper headers (Target Name, Target Email, Name, Email, Role) for easy bulk group creation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Converted groups upload from Excel (.xls, .xlsx) to CSV format for better compatibility and easier file management.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Enhanced groups CSV parsing to handle multiple column naming conventions and skip empty rows automatically.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated groups upload UI with modern interface, template download button, and CSV file validation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added comprehensive logging for groups CSV upload debugging and troubleshooting.</p>
            </li>
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