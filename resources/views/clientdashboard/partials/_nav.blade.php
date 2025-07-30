<nav class="navbar user-info-navbar"  role="navigation">

    <!-- Left links for user info navbar -->
    <ul class="user-info-menu left-links list-inline list-unstyled">
        @if (! $client->home)
            <li class="nav-icons">
                <a href="{{ url('dashboard/all-users') }}"><i class="linecons-user"></i></a>
                <a href="{{ url('dashboard/selection') }}"><i class="fa-line-chart"></i></a>
                <a href="{{ url('dashboard/assignments') }}"><i class="fa-list-ol"></i></a>
                <a href="{{ url('account') }}"><i class="linecons-cog"></i></a>
            </li>
        @endif
        <li class="client-name">
            <a href="{{ url('dashboard') }}">{{ $client->name }}</a>
        </li>
    </ul>

    <!-- Right links for user info navbar -->
    <ul class="user-info-menu right-links list-inline list-unstyled">

        <li class="dropdown user-profile">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="linecons-user user-icon"></i>
                <span>
					{{ $name }}
                    {{--<i class="fa-angle-down"></i>--}}
				</span>
            </a>
            @include('dashboard.partials._usermenu')
        </li>
    </ul>
</nav>