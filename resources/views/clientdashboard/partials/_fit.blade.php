{{-- If Assignment Exists --}}
@if ($assignment)

    {{-- Completed Assignment --}}
    @if ($assignment->completed)
        <div class="fit">

            @if ($assignment->division == 1)
                <span class="status green"></span>
            @elseif ($assignment->division == 2)
                <span class="status lime"></span>
            @elseif ($assignment->division == 3)
                <span class="status yellow"></span>
            @elseif ($assignment->division == 4)
                <span class="status orange"></span>
            @elseif ($assignment->division == 5)
                <span class="status red"></span>
            @endif

        {{-- Personality --}}
        @if ($assignment->assessment()->id == get_global('personality') || $assignment->assessment()->id == get_global('safety'))
            @if ($assignment->division == 1)
                High Fit
            @elseif ($assignment->division == 2)
                Moderate-To-High Fit
            @elseif ($assignment->division == 3)
                Moderate Fit
            @elseif ($assignment->division == 4)
                Moderate-To-Low Fit
            @elseif ($assignment->division == 5)
                Low Fit
            @endif

        {{-- Ability --}}
        @elseif ($assignment->assessment()->id == get_global('ability') || $assignment->assessment()->id == get_global('aptitude'))
            {{ $assignment->percentile }}%
        @else
            {{ $assignment->score }}
        @endif
        </div>

    {{-- In Progress --}}
    @else
        <span class="text-muted text-small" style="text-transform: uppercase; letter-spacing: 0.5px;">In Progress</span>
    @endif

{{-- Never Assigned --}}
@else
    <span class="text-yellow" style="color: #ccc">Not Assigned</span>
@endif