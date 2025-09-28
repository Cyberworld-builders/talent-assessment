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