<div class="page-header">
    @if ($page % 2)
    	@if ($user->client->whitelabel && $user->client->id == 29)
            <img class="logo right" src="{{ asset('assets/images/angela-logo-small.png') }}" style="width:133px;" />
        @else
            <img class="logo right" src="{{ asset('assets/images/' . $logo) }}"
        	{{ (isset($width) ? 'style=width:'.$width.'px;' : '') }}>
        @endif
    @else
    	@if ($user->client->whitelabel && $user->client->id == 29)
            <img class="logo" src="{{ asset('assets/images/angela-logo-small.png') }}" style="width:133px; position:relative; top: 6px;" />
        @else
            <img class="logo" src="{{ asset('assets/images/logo-small.png') }}">
        @endif
    @endif
    <div class="line"></div>
    <div class="clearfix"></div>
</div>