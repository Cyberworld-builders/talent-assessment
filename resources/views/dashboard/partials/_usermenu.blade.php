<ul class="dropdown-menu user-profile-menu list-unstyled">
	{{-- <li>
		<a href="#edit-profile">
			<i class="fa-edit"></i>
			New Post
		</a>
	</li>
	<li>
		<a href="#settings">
			<i class="fa-wrench"></i>
			Settings
		</a>
	</li>
	<li>
		<a href="#help">
			<i class="fa-info"></i>
			Help
		</a>
	</li> --}}
	<li class="last">
		<a href="{{ url('/account') }}">
			<i class="fa-user"></i>
			My Account
		</a>
	</li>
	<li class="last">
		<a href="{{ url('/logout') }}">
			<i class="fa-lock"></i>
			Logout
		</a>
	</li>
</ul>