@extends('app')

@section('title')
    AOE : Dashboard
@stop

@section('body-class')
    client-dashboard
@stop

@section('body')

    <style>
        body {
            background: #fff;
        }
        .page-body.client-dashboard {
            font-family: 'Avenir Next LT Pro';
        }
        .user-info-navbar {
            background: transparent;
            font-size: 16px;
            padding: 25px;
            color: #fff;
        }
        @if (!$client->home)
        .user-info-navbar {
            background: #fff;
            font-size: 16px;
            color: #3d3d3d;
            height: 64px;
            margin-bottom: 0;
        }
        @endif
        .user-info-navbar .client-name {
            font-weight: bold;
            text-transform: uppercase;
            border: none !important;
            min-height: 0 !important;
        }
        .user-info-navbar .user-profile {
            border: none !important;
            min-height: 0 !important;
            top: -3px;
        }
        .user-info-navbar .user-profile .user-icon {
            color: #e77928;
            font-size: 20px;
        }
        .user-info-navbar .user-info-menu > li > a {
            padding: 0;
            border: none;
            color: #fff;
        }
        .user-info-navbar .user-info-menu > li > a:hover {
            color: #e77928;
        }
        @if (!$client->home)
        .user-info-navbar .user-info-menu > li > a {
            color: #3d3d3d;
        }
        @endif
        .user-info-navbar .user-info-menu > li.nav-icons {
            border: none !important;
            min-height: 0 !important;
            margin: -26px 50px 0px -26px;
        }
        .user-info-navbar .user-info-menu > li.nav-icons > a {
            display: inline-block;
            color: #fff;
            background: #14212f;
            height: 64px;
            width: 64px;
            text-align: center;
            margin-right: -4px;
        }
        .user-info-navbar .user-info-menu > li.nav-icons > a:nth-child(2) {
            background: #e77928;
        }
        .user-info-navbar .user-info-menu > li.nav-icons > a:nth-child(3) {
            background: #c4c4c4;
        }
        .user-info-navbar .user-info-menu > li.nav-icons > a i {
            margin: 0;
            font-size: 24px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 17px;
        }
        footer.main-footer {
            font-size: 16px;
            border: none;
            background: transparent;
            color: #fff;
        }
        @if (! $client->home)
            footer.main-footer {
                color: #c4c4c4;
            }
        @endif
        .page-container {
            background: url('{{ asset('assets/images/bg-dashboard.jpg') }}') no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
        @if (! $client->home)
            .page-container {
                background: none;
            }
        @endif
        .wrapper {
            max-width: 1180px;
            margin: 0 auto;
        }
        .intro-box {
            color: #fff;
        }
        .intro-box h1 {
            margin: 0;
            font-size: 54px;
        }
        .intro-box h3 {
            margin: 0;
            font-size: 22px;
        }
        .searchbox {
            position: relative;
        }
        .searchbox .form-control {
            background: transparent;
        }
        .form-control.select2-container {
            border: none;
        }
        html .select2-container.select2-container-multi .select2-choices {
            background: transparent;
        }
        #user_search_form {
            width: 100%;
            color: #e77928;
        }
        .searchbox input,
        .searchbox .select2-container-multi .select2-choices .select2-search-field input {
            background: rgba(0,0,0,0.35) !important;
            border: 2px solid #e77928;
            -webkit-border-radius: 100px;
            -moz-border-radius: 100px;
            border-radius: 100px;
            color: #e77928 !important;
            font-size: 22px;
            padding: 18px 40px 15px;
            width: 100% !important;
        }
        .select2-container-multi .select2-choices .select2-search-field {
            width: 100%;
        }
        .searchbox i {
            position: absolute;
            right: 18px;
            top: 14px;
            font-size: 32px;
            color: #e77928;
            z-index: 1;
        }
        .touts {
            margin-top: 60px;
        }
        .tout {
            display: block;
            background: rgba(20,33,47,0.9);
            padding: 70px 30px;
            text-align: center;
            transition: 0.1s all ease;
            cursor: pointer;
        }
        .tout i {
            font-size: 84px;
            color: #e77928;
            margin-bottom: 20px;
        }
        .tout p {
            color: #fff;
            font-size: 22px;
            line-height: 34px;
            font-weight: bold;
        }
        .tout p.text-small {
            line-height: 30px;
            font-size: 14px;
            font-weight: 300;
        }
        .touts div:nth-child(even) .tout {
            background: rgba(231,121,40,0.9);
        }
        .touts div:nth-child(even) .tout i {
            color: #fff;
        }
        .touts div .tout:hover {
            background: #e77928;
        }
        .touts div .tout:hover i {
            color: #fff;
        }
        .header {
            text-align: center;
            padding: 50px 0;
            color: #fff;
            background: url('{{ asset('assets/images/bg-dashboard.jpg') }}') no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            margin: 0 -30px;
        }
        .header h1 {
            font-size: 40px;
        }
        .header h1 i {
            font-size: 72px;
            margin-bottom: 10px;
        }
        .content {
            padding: 60px 0;
        }
        .title-env h1 {
            font-size: 32px;
            margin: 0;
        }
        .title-env p {
            margin-top: 5px;
            font-size: 16px;
        }
        .btn.btn-orange.btn-lg {
            font-size: 16px;
            padding: 18px 20px;
            min-height: 50px;
            background-color: #e77928;
            border-color: #e77928;
        }
        .btn.btn-orange.btn-lg i {
            font-size: 22px;
            height: 0;
            position: relative;
            top: 2px;
            left: 0px;
            margin-right: 7px;
        }
        .btn.btn-orange:active, .btn.btn-orange:hover, .btn.btn-orange:focus {
            background-color: #f69f2f;
            border-color: #f69a25;
        }
        .tab-content {
            padding: 30px 0;
        }
        .members-table thead tr th {
            background: transparent;
            font-size: 11px;
            color: #000;
            border-color: #000;
            padding: 13px 2px;
        }
        .members-table tbody tr td {
            padding: 13px 2px;
            font-size: 14px;
        }
        .members-table tbody tr td.user-name i {
            font-size: 23px;
            color: #e77928;
            position: relative;
            top: 3px;
        }
        .members-table tbody tr td .name {
            font-size: 15px;
        }
        .members-table tbody tr td .email {
            color: #c4c4c4;
        }
        .members-table a.orange {
            color: #e77928;
        }
        .client-dashboard .tab-content .tab-pane .table {
            border-left: none;
            border-right: none;
        }
        .client-dashboard .members-table tbody tr td .name {
            font-size: 14px;
            font-weight: 400;
        }
        .client-dashboard .panel {
            padding: 0;
        }
    </style>

    <div class="page-container">
        <div class="main-content">

            @include('clientdashboard.partials._nav')
            @yield('content')
            @include('clientdashboard.partials._footer')

        </div>
    </div>

@stop