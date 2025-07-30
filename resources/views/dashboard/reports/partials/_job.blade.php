@if (isset($edit))
    [job]
@else
    @if (isset($job) && $job)
        {{ do_shortcodes(['job' => $job->name], '[job]') }}
    @else
        [job]
    @endif
@endif