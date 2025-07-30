@if (!$task)
    <div class="heading">

        {{-- Background --}}
        @if (!$preview && $assignment->whitelabel && $user->client && $user->client->background)
            <img class="background" src="{{ $user->client->background }}" />
        @else
            <img class="background" src="{{ show_image($assessment->background) }}" />
        @endif

        {{-- Logo --}}
        <div class="logo">
            @if (!$preview && $assignment->whitelabel && $user->client && $user->client->logo)
                <img style="height: 100%;" src="{{ $user->client->logo }}" />
            @else
                <img style="height: 100%;" src="{{ $assessment->logo }}" />
            @endif
        </div>

        {{-- Title --}}
        @if ($assessment->translation() && $assessment->translation()->name)
            <div class="title">{{ $assessment->translation()->name }}</div>
        @else
            <div class="title">{{ $assessment->name }}</div>
        @endif

    </div>
@endif