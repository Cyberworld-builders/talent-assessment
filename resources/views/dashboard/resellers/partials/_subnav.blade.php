<?php
$tabs = [
    [
        'name' => 'General',
        'link' => '/dashboard/resellers/'.$reseller->id,
    ],
    [
        'name' => 'Edit',
        'link' => '/dashboard/resellers/'.$reseller->id.'/edit',
    ],
];

if ($reseller->db_status == 'available')
{
    $tabs[] = [
        'name' => 'Clients',
        'link' => '/dashboard/resellers/'.$reseller->id.'/clients',
    ];
    $tabs[] = [
        'name' => 'Users',
        'link' => '/dashboard/resellers/'.$reseller->id.'/users',
    ];
//    $tabs[] = [
//        'name' => 'Database',
//        'link' => '/dashboard/resellers/'.$reseller->id.'/database',
//    ];
}

$tabs[] = [
	'name' => 'Jobs',
	'link' => '/dashboard/resellers/'.$reseller->id.'/jobs',
];
$tabs[] = [
	'name' => 'Predictive Modeling',
	'link' => '/dashboard/resellers/'.$reseller->id.'/models',
];
$tabs[] = [
	'name' => 'Weighting',
	'link' => '/dashboard/resellers/'.$reseller->id.'/weights',
];
?>

@if ($reseller->dbNotReady())
    <div class="alert alert-warning">
        <strong>Database:</strong> {{ readable_string($reseller->db_status) }}<br>
        The database for this reseller is undergoing modifications. Some options may not be available.
    </div>
@endif

@if ($reseller->dbError())
    <div class="alert alert-danger">
        <strong>Database:</strong> {{ readable_string($reseller->db_status) }}<br>
        The database for this reseller has encountered a critical error. Some options may not be available.
    </div>
@endif

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
