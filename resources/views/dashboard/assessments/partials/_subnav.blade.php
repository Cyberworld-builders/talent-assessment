<?php
$tabs = [
    [
        'name' => 'General',
        'link' => '/dashboard/assessments/'.$assessment->id.'/edit-new',
    ],
    [
        'name' => 'Dimensions',
        'link' => '/dashboard/assessments/'.$assessment->id.'/dimensions',
    ],
    [
        'name' => 'Translations',
        'link' => '/dashboard/assessments/'.$assessment->id.'/translations',
    ],
    [
        'name' => 'Preview',
        'link' => '/dashboard/assessments/'.$assessment->id,
    ],
];
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