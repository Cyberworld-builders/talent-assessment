<nav class="navbar user-info-navbar"  role="navigation"><!-- User Info, Notifications and Menu Bar -->

	<!-- Left links for user info navbar -->
	<ul class="user-info-menu left-links list-inline list-unstyled">

		<li class="current-time">
			{{--<h3>{{ date("l, M. j, Y, g:i a") }}</h3>--}}
			@role('admin')
				<h3><i class="linecons-user text-warning"></i> AOE Admin</h3>
			@endrole

			@role('reseller')
				<h3><i class="linecons-user text-muted"></i> Admin</h3>
			@endrole

			@role('client')
				<h3><i class="linecons-user"></i> {{ \Auth::user()->client->name }}</h3>
			@endrole
		</li>

		{{-- <li class="hidden-sm hidden-xs">
			<a href="#" data-toggle="sidebar">
				<i class="fa-bars"></i>
			</a>
		</li> --}}

		<!-- Mail -->
		{{-- <li class="dropdown hover-line">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<i class="fa-envelope-o"></i>
				<span class="badge badge-green">15</span>
			</a>

			<ul class="dropdown-menu messages">
				<li>
					
					<ul class="dropdown-menu-list list-unstyled ps-scrollbar">
				
						<li class="active"><!-- "active" class means message is unread -->
							<a href="#">
								<span class="line">
									<strong>Luc Chartier</strong>
									<span class="light small">- yesterday</span>
								</span>
				
								<span class="line desc small">
									This ain’t our first item, it is the best of the rest.
								</span>
							</a>
						</li>
				
						<li class="active">
							<a href="#">
								<span class="line">
									<strong>Salma Nyberg</strong>
									<span class="light small">- 2 days ago</span>
								</span>
				
								<span class="line desc small">
									Oh he decisively impression attachment friendship so if everything.
								</span>
							</a>
						</li>
				
						<li>
							<a href="#">
								<span class="line">
									Hayden Cartwright
									<span class="light small">- a week ago</span>
								</span>
				
								<span class="line desc small">
									Whose her enjoy chief new young. Felicity if ye required likewise so doubtful.
								</span>
							</a>
						</li>
				
						<li>
							<a href="#">
								<span class="line">
									Sandra Eberhardt
									<span class="light small">- 16 days ago</span>
								</span>
				
								<span class="line desc small">
									On so attention necessary at by provision otherwise existence direction.
								</span>
							</a>
						</li>
				
						<!-- Repeated -->
				
						<li class="active"><!-- "active" class means message is unread -->
							<a href="#">
								<span class="line">
									<strong>Luc Chartier</strong>
									<span class="light small">- yesterday</span>
								</span>
				
								<span class="line desc small">
									This ain’t our first item, it is the best of the rest.
								</span>
							</a>
						</li>
				
						<li class="active">
							<a href="#">
								<span class="line">
									<strong>Salma Nyberg</strong>
									<span class="light small">- 2 days ago</span>
								</span>
				
								<span class="line desc small">
									Oh he decisively impression attachment friendship so if everything.
								</span>
							</a>
						</li>
				
						<li>
							<a href="#">
								<span class="line">
									Hayden Cartwright
									<span class="light small">- a week ago</span>
								</span>
				
								<span class="line desc small">
									Whose her enjoy chief new young. Felicity if ye required likewise so doubtful.
								</span>
							</a>
						</li>
				
						<li>
							<a href="#">
								<span class="line">
									Sandra Eberhardt
									<span class="light small">- 16 days ago</span>
								</span>
				
								<span class="line desc small">
									On so attention necessary at by provision otherwise existence direction.
								</span>
							</a>
						</li>
				
					</ul>
				
				</li>
				
				<li class="external">
					<a href="mailbox-main.html">
						<span>All Messages</span>
						<i class="fa-link-ext"></i>
					</a>
				</li>
			</ul>
		</li> --}}

		<!-- Added in v1.2 -->
		{{-- <li class="dropdown hover-line language-switcher">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<img src="assets/images/flags/flag-uk.png" alt="flag-uk" />
				English
			</a>

			<ul class="dropdown-menu languages">
				<li>
					<a href="#">
						<img src="assets/images/flags/flag-al.png" alt="flag-al" />
						Shqip
					</a>
				</li>
				<li class="active">
					<a href="#">
						<img src="assets/images/flags/flag-uk.png" alt="flag-uk" />
						English
					</a>
				</li>
				<li>
					<a href="#">
						<img src="assets/images/flags/flag-de.png" alt="flag-de" />
						Deutsch
					</a>
				</li>
				<li>
					<a href="#">
						<img src="assets/images/flags/flag-fr.png" alt="flag-fr" />
						Fran&ccedil;ais
					</a>
				</li>
				<li>
					<a href="#">
						<img src="assets/images/flags/flag-br.png" alt="flag-br" />
						Portugu&ecirc;s
					</a>
				</li>
				<li>
					<a href="#">
						<img src="assets/images/flags/flag-es.png" alt="flag-es" />
						Espa&ntilde;ol
					</a>
				</li>
			</ul>
		</li> --}}

	</ul>


	<!-- Right links for user info navbar -->
	<ul class="user-info-menu right-links list-inline list-unstyled">

		{{-- <li class="search-form"><!-- You can add "always-visible" to show make the search input visible -->

			<form name="userinfo_search_form" method="get" action="extra-search.html">
				<input type="text" name="s" class="form-control search-field" placeholder="Type to search..." />

				<button type="submit" class="btn btn-link">
					<i class="linecons-search"></i>
				</button>
			</form>

		</li> --}}

		<!-- Notifications -->
		@include('dashboard.partials._notifications')

		<li class="dropdown user-profile">
		
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<img src="{{ asset('assets/images/user-4.png') }}" alt="user-image" class="img-circle img-inline userpic-32" width="28" />
				<span>
					{{ $name }}
					<i class="fa-angle-down"></i>
				</span>
			</a>

			@include('dashboard.partials._usermenu')

		</li>

	</ul>
</nav>