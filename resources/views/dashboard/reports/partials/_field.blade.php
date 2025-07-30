<?php
    // Show customized field (if exists)
    // Else show default field
    $content = ($report->fields && array_key_exists($i, $report->fields) && $report->fields[$i] ? $report->fields[$i] : $field);
?>

@if (isset($edit))

    {{-- Show form field for editing --}}
    @if (isset($hidden))
        <p>{!! Form::hidden('fields[]', $content) !!}</p>
    @else
        <p>{!! Form::textarea('fields[]', $content, ['class' => 'autosize']) !!}</p>
    @endif
@else

    {{-- Show the content, with shortcodes --}}
    <?php //dd($scores[$assessment->id]); ?>
    <?php //dd($scores); ?>
    <?php
        $shortcodes = [
            'name' => $user->name,
            'job' => ($job ? $job->name : '[job]')
        ];
        if (! isset($cover))
        {
            $shortcodes['score'] = (array_key_exists('score', $scores[$assessment->id]) ? $scores[$assessment->id]['score'] : '[score]');
            $shortcodes['accuracy'] = (array_key_exists('accuracy', $scores[$assessment->id]) ? $scores[$assessment->id]['accuracy'] : '[accuracy]');
            $shortcodes['total'] = (array_key_exists('total', $scores[$assessment->id]) ? $scores[$assessment->id]['total'] : '[total]');
            $shortcodes['percentile'] = (array_key_exists('accuracy', $scores[$assessment->id]) ? $scores[$assessment->id]['percentile'] : '[percentile]');
        }
    ?>
    <p>
        {!! do_shortcodes($shortcodes, nl2br($content)) !!}
    </p>
@endif