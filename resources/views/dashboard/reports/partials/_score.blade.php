@if (isset($edit))
    [{{ $type }}]
@else
    {{ $scores[$assessment->id][$type] }}
@endif