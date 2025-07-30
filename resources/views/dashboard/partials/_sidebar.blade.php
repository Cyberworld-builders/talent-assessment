<script type="text/javascript">
	jQuery(document).ready(function($)
    {
    	$('.main-menu .menu-item').click(function(){

			var menu = $('span.title', this).html();

    		$('.sidebar-menu-inner').addClass('collapsed');

			$('.menu-category').hide();
			$('.menu-category[data-parent="'+menu+'"]').show();
    	});
    });
</script>

<!-- Add "fixed" class to make the sidebar fixed always to the browser viewport. -->
<!-- Adding class "toggle-others" will keep only one menu item open at a time. -->
<!-- Adding class "collapsed" collapse sidebar root elements and show only icons. -->
<div class="sidebar-menu toggle-others fixed">

	<div class="sidebar-menu-under">

		<div style="display:none;" class="menu-category" data-parent="Assessments">
			<h3>Assessments</h3>
			<ul class="main-menu">
				<li>
					<a href="{{ url('/dashboard/assessments/create') }}">
						<span class="title">Create</span>
					</a>
				</li>
				<li>
					<a href="{{ url('/dashboard/assessments') }}">
						<span class="title">Modify/Remove</span>
					</a>
				</li>
				<li>
					<a href="{{ url('/dashboard/assign') }}">
						<span class="title">Assign</span>
					</a>
				</li>
			</ul>
		</div>

		<div style="display:none;" class="menu-category" data-parent="Users">
			<h3>Users</h3>
			<ul class="main-menu">
				{{--<li>--}}
					{{--<a href="{{ url('/dashboard/users/create') }}">--}}
						{{--<span class="title">Create</span>--}}
					{{--</a>--}}
				{{--</li>--}}
				<li>
					<a href="{{ url('/dashboard/users') }}">
						<span class="title">View</span>
					</a>
				</li>
			</ul>
		</div>

	</div>

	<div class="sidebar-menu-inner @yield('sidebar-class')">
		
		<header class="logo-env">

			<!-- Logo -->
			<div class="logo">
				<a href="#" class="logo-expanded">
					{{-- <img src="assets/images/logo@2x.png" width="80" alt="" /> --}}
					@if (session('reseller'))
						<h2 style="font-size: 22px; margin: 15px 0;">{{ session('reseller')->name }}</h2>
					@else
						<h2>AOE Science</h2>
					@endif
				</a>

				<a href="#" class="logo-collapsed">
					{{-- <img src="assets/images/logo-collapsed@2x.png" width="40" alt="" /> --}}
				</a>
			</div>

			<!-- This will toggle the mobile menu and will be visible only on mobile devices -->
			<div class="mobile-menu-toggle visible-xs">
				<a href="#" data-toggle="user-info-menu">
					<i class="fa-bell-o"></i>
					<span class="badge badge-success">7</span>
				</a>

				<a href="#" data-toggle="mobile-menu">
					<i class="fa-bars"></i>
				</a>
			</div>

			<!-- This will open the popup with user profile settings, you can use for any purpose, just be creative -->
			{{-- <div class="settings-icon">
				<a href="#" data-toggle="settings-pane" data-animate="true">
					<i class="linecons-cog"></i>
				</a>
			</div> --}}
			
		</header>
			
				
		<ul id="main-menu" class="main-menu">
			<!-- add class "multiple-expanded" to allow multiple submenus to open -->
			<!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
			<li>
				<a href="{{ url('/dashboard') }}">
					<i class="fa-home"></i>
					<span class="title">Home</span>
				</a>
			</li>

			@role('admin')
				<li>
					<a href="{{ url('dashboard/assessments') }}">
						<i class="fa-list-alt"></i>
						<span class="title">Assessments</span>
					</a>
				</li>
				<li>
					<a href="{{ url('dashboard/clients') }}">
						<i class="fa-building-o"></i>
						<span class="title">Clients</span>
					</a>
				</li>
				<li>
					<a href="{{ url('dashboard/resellers') }}">
						<i class="fa-compass"></i>
						<span class="title">Resellers</span>
					</a>
				</li>
				<li>
					<a href="{{ url('dashboard/users') }}">
						<i class="fa-user"></i>
						<span class="title">Users</span>
					</a>
				</li>
			@endrole

			@role('reseller')
			<li>
				<a href="{{ url('dashboard/clients') }}">
					<i class="fa-building-o"></i>
					<span class="title">Clients</span>
				</a>
			</li>
			<li>
				<a href="{{ url('dashboard/users') }}">
					<i class="fa-user"></i>
					<span class="title">Users</span>
				</a>
			</li>
			@endrole

			@role('client')
				<li>
					<a href="{{ url('dashboard/selection') }}">
						<i class="fa-user"></i>
						<span class="title">Employee Selection</span>
					</a>
				</li>
				<li>
					<a href="{{ url('dashboard/development') }}">
						<i class="fa-user"></i>
						<span class="title">Employee Development</span>
					</a>
				</li>
				<li>
					<a href="{{ url('dashboard/all-users') }}">
						<i class="fa-user"></i>
						<span class="title">Users</span>
					</a>
				</li>
			@endrole

		</ul>
		
	</div>
	
</div>