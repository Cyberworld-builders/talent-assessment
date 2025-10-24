<?php
    // Template urls
    $baseUrl = $export ? '{{ url("/") }}' : '';
    $fontsUrl = '/assets/fonts';
    if ($export) $fontsUrl = (env('APP_ENV') == 'local') ? '/var/www/public/assets/fonts' : '/var/app/current/assets/fonts';

    // Available templates and their blade names
    $templates = [
        'ability' => 'a',
        'personality' => 'p',
        'safety' => 's',
        'leader' => 'l',
        'ospan' => 'wmo',
        '360' => 'sixty',
    ];
    
    // Direct assessment ID mappings (for assessments without globals)
    $directTemplates = [
        1 => 'sixty', // Involved-360 assessment
    ];
?>

<html moznomarginboxes="" mozdisallowselectionprint="">
<head>
    <meta name="viewport" content="width=device-width">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="{{ $baseUrl }}/assets/js/highcharts.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    @include('dashboard.reports.partials._styles')
    @include('dashboard.reports.partials._scripts')
</head>
<body>

<?php
    global $page;
    global $i;
    $page = 1;
    $i = 0;
?>

{{-- Cover Page --}}
@include('dashboard.reports.partials._cover')
<?php $i = 1; ?>

{{-- Assessments --}}
@foreach ($assessments as $assessment)
    <?php $found = false; ?>
    
    {{-- Check direct assessment ID mappings first --}}
    @if (isset($directTemplates[$assessment->id]))
        @if (View::exists('dashboard.reports.templates.'.$directTemplates[$assessment->id]))
            <?php $found = true; ?>
            @include('dashboard.reports.templates.'.$directTemplates[$assessment->id])
        @endif
    @endif
    
    {{-- Check global mappings if not found --}}
    @if (! $found)
        @foreach ($templates as $global => $template)
            @if ($assessment->id == get_global($global))
                @if (View::exists('dashboard.reports.templates.'.$template))
                    <?php $found = true; ?>
                    @include('dashboard.reports.templates.'.$template)
                @endif
            @endif
        @endforeach
    @endif

    @if (! $found)
        <div class="alert alert-danger" role="alert">
            Didn't find a template for {{ $assessment->name }} (ID: {{ $assessment->id }})
        </div>
    @endif
@endforeach

@if (! $export)
    <p class="text-center white">Powered by <a href="{{ url("/") }}">Involved Talent</a></p>
@endif
</body>
</html>