<?php
    $items = [
    	'edit' => [
    		'name' => 'Report Settings',
            'url' => '/dashboard/clients/'.$client->id.'/reports/'.(isset($report) ? $report->id : 0).'/edit',
            'icon' => 'fa-file-text-o'
        ],
		'weighting' => [
			'name' => 'Weighting',
			'url' => '/dashboard/clients/'.$client->id.'/reports/'.(isset($report) ? $report->id : 0).'/weighting',
            'icon' => 'fa-cube'
		],
		'modeling' => [
			'name' => 'Predictive Modeling',
			'url' => '/dashboard/clients/'.$client->id.'/reports/'.(isset($report) ? $report->id : 0).'/modeling',
            'icon' => 'fa-sitemap'
		],
		'customize' => [
			'name' => 'Customize Report',
			'url' => '/dashboard/clients/'.$client->id.'/reports/'.(isset($report) ? $report->id : 0).'/customize',
			'icon' => 'fa-paint-brush'
		]
    ];

    // Check to see if we're in edit mode
    $editing = false;
    if (isset($edit) && $edit)
    	$editing = true;

    // Check what the active item should be
    if (! isset($active))
    	$active = 'edit';
?>
<div class="full-width tocify">
    <ul class="tocify-header nav nav-list">
        @foreach ($items as $index => $item)
			<?php
				$disabled = false;
				if ($editing == false && $index != 'edit')
					$disabled = true;
			?>
            <li class="tocify-item {{ ($active == $index ? 'active' : '') }} {{ ($disabled ? 'disabled' : '') }}"><a href="{{ ($disabled ? '#null' : $item['url']) }}"><i class="{{ $item['icon'] }}"></i> {{ $item['name'] }}</a></li>
        @endforeach
    </ul>
</div>
<br/>
@if (! isset($edit) || !$edit)
    <div class="alert alert-info">Report must be saved first before scoring can be configured!</div>
@else
	@if (isset($report) && ! $report->fields)
		<div class="alert alert-default">Report has not been customized yet!</div>
	@endif
	@if (isset($report) && $report->score_method == 1 && !$report->weights)
		<div class="alert alert-danger">Weighting not setup for this report!</div>
	@endif
	@if (isset($report) && $report->score_method == 2 && !$report->model_configured)
		<div class="alert alert-danger">Predictive model is not configured!</div>
	@endif
	@if (isset($report) && !$report->enabled)
		<div class="alert alert-default alert-enabled">Report has not been enabled. <a href="#null" class="text text-muted switch">Enable it?</a></div>
	@endif
@endif

<script>
    jQuery(document).ready(function($){

        // Set headers for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            }
        });

        // Grabbing data and sending data
        $('.switch').click(function() {
            var url = window.location.pathname.replace('edit', 'toggle');

            $.ajax({
                type: 'post',
                url: url,
                data: {
                    enabled: 1
                },
                dataType: 'boolean',
                success: function (data) {
                    // Nothing, this is never reached because function doesn't return anything
                },
                error: function (data) {
                    console.log(data.status + ' ' + data.statusText);
                    $('html').prepend(data.responseText);
                },
                complete: function() {
                    $('.alert-enabled').fadeOut();
                }
            });
        });
    });
</script>