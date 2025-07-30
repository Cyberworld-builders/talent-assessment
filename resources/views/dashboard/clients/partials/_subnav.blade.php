<?php
    // Get the base url
	$baseUrl = '/dashboard/clients/'.$client->id;
	if (isset($reseller))
		$baseUrl = '/dashboard/resellers/'.$reseller->id.'/clients/'.$client->id;

	// Setup the tabs
    $generalTab = [
		'name' => 'General',
		'link' => $baseUrl,
	];
    $editTab = [
		'name' => 'Edit',
		'link' => $baseUrl.'/edit',
	];
    $usersTab = [
		'name' => 'Users',
		'link' => $baseUrl.'/users',
	];
    $groupsTab = [
		'name' => 'Groups',
		'link' => $baseUrl.'/groups',
	];
    $selectionTab = [
		'name' => 'Selection',
		'link' => $baseUrl.'/jobs',
	];
    $developmentTab = [
		'name' => 'Development',
		'link' => $baseUrl.'/surveys',
	];
    $predictiveModelingTab = [
		'name' => 'Predictive Modeling',
		'link' => $baseUrl.'/models',
	];
    $weightingTab = [
		'name' => 'Weighting',
		'link' => $baseUrl.'/weights',
	];
    $assignmentsTab = [
		'name' => 'Assignments',
		'link' => $baseUrl.'/assignments',
	];
    $jobAnalysisTab = [
		'name' => 'Job Analysis',
		'link' => $baseUrl.'/analysis',
	];
    $reportsTab = [
		'name' => 'Reports',
		'link' => $baseUrl.'/reports',
	];

    // Show different tabs depending on permissions
    $tabs = [];
    $tabs[] = $generalTab;
    $tabs[] = $editTab;
    $tabs[] = $usersTab;

    // Will hide these tabs for Master AOE when viewing a reseller
	if (!isset($reseller))
	{
		$tabs[] = $groupsTab;
		$tabs[] = $selectionTab;
	}

    if (Auth::user()->is('admin') && !isset($reseller))
    {
		$tabs[] = $developmentTab;
		$tabs[] = $predictiveModelingTab;
		$tabs[] = $weightingTab;
    }

	if (Auth::user()->is('reseller'))
	{
		$tabs[] = $developmentTab;
	}

    if (!isset($reseller))
		$tabs[] = $assignmentsTab;

	if (Auth::user()->is('admin') && !isset($reseller))
    {
		$tabs[] = $jobAnalysisTab;
		$tabs[] = $reportsTab;
    }
?>

<ul class="nav nav-tabs">
    @foreach ($tabs as $tab)
        @if (isset($active) and $tab['name'] == $active)
            <li class="active">
                <a href="#">
                    {{ $tab['name'] }}
                </a>
            </li>
        @else
            <li>
                <a href="{{ $tab['link'] }}">
                    {{ $tab['name'] }}
                </a>
            </li>
        @endif
    @endforeach
</ul>
