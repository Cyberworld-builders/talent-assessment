<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf_token" content="{{ csrf_token() }}" />

	<title>@yield('title')</title>

	{{--<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Arimo:300,400,700,400italic">--}}
	{{--<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700">--}}
	<link rel="stylesheet" href="{{ asset('assets/css/fonts/linecons/css/linecons.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/fonts/fontawesome/css/font-awesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
	@yield('styles')

	<script src="{{ asset('assets/js/jquery-1.11.1.min.js') }}"></script>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body class="page-body @yield('body-class')">

	@yield('body')
	
	<!-- Bottom Scripts -->
	<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('assets/js/TweenMax.min.js') }}"></script>
	<script src="{{ asset('assets/js/resizeable.js') }}"></script>
	<script src="{{ asset('assets/js/xenon-api.js') }}"></script>
	<script src="{{ asset('assets/js/xenon-api.js') }}"></script>
	<script src="{{ asset('assets/js/xenon-toggles.js') }}"></script>
	<script src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>
	@yield('scripts')

	<!-- JavaScripts initializations and stuff -->
	<script src="{{ asset('assets/js/xenon-custom.js') }}"></script>

	<!-- Page loading overlay -->
	{{--<div class="page-loading-overlay">--}}
		{{--<div class="loader-2"></div>--}}
	{{--</div>--}}

	<!-- Scripts -->
	<script type="text/javascript">
		jQuery(document).ready(function($){

			// Status Messages
			var opts = {
				"closeButton": true,
				"debug": false,
				"positionClass": "toast-top-right",
				"onclick": null,
				"showDuration": "300",
				"hideDuration": "1000",
				"timeOut": "5000",
				"extendedTimeOut": "1000",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			};
			@if (Session::get('success'))
				toastr.success("{{ Session::get('success') }}", "Success", opts);
			@elseif (Session::get('error'))
				toastr.error("{{ Session::get('error') }}", "Error", opts);
			@elseif (Session::get('warning'))
				toastr.warning("{{ Session::get('warning') }}", "Warning", opts);
			@elseif (Session::get('info'))
				toastr.info("{{ Session::get('info') }}", "", opts);
			@endif
		});
	</script>

</body>
</html>