@if (isset($edit))
    [name]
@else
    {{ do_shortcodes(['name' => $user->name], '[name]') }}
@endif