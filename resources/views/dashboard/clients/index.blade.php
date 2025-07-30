@extends('dashboard.dashboard')

@section('content')

    {{-- Reseller Title --}}
    @if (isset($reseller))
        <div class="page-title orange">
            <div class="title-env">
                <h1 class="title">{{ $reseller->name }}</h1>
            </div>
        </div>
    @endif

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Clients</h1>
            <p class="description">Add or manage clients.</p>
        </div>
    </div>

    <div class="row">

        {{-- Reseller Sub Navigation --}}
        @if (isset($reseller))
            @include('dashboard.resellers.partials._subnav', ['active' => 'Clients'])
        @endif

        {{-- Reseller Panel --}}
        @if (isset($reseller))
            <div class="panel panel-headerless">
            <div class="panel-body">
        @endif

        {{-- Add Client Button --}}
        <div class="{{ (isset($reseller) ? '' : 'col-md-12') }}">
            <div class="pull-right">
                @if (isset($reseller))
                    <a href="{{ url('dashboard/resellers/'.$reseller->id.'/clients/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add Client</a>
                @else
                    <a href="{{ url('dashboard/clients/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add Client</a>
                @endif
            </div>
        </div>

        {{-- Clients --}}
        <div class="{{ (isset($reseller) ? '' : 'col-md-12') }}">
            <div class="tab-content">
                <div class="tab-pane active">

                    <table class="table table-hover members-table middle-align">
                        <thead>
                        <tr>
                            <th class="hidden-xs hidden-sm"></th>
                            <th>Client Name</th>
                            <th># of Users</th>
                            <th>Settings</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)

                                <tr>
                                    <td class="user-image hidden-xs hidden-sm">
                                        @if ($client->logo)
                                            <img src="{{ show_image($client->logo) }}" class="img-circle" alt="client-pic" />
                                        @else
                                            <img src="{{ asset('assets/images/client-1.png') }}" class="img-circle" alt="user-pic" />
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($reseller))
                                            <a href="{{ url('dashboard/resellers/'.$reseller->id.'/clients/'.$client->id) }}" class="name">{{ $client->name }}</a>
                                        @else
                                            <a href="{{ url('dashboard/clients/'.$client->id) }}" class="name">{{ $client->name }}</a>
                                        @endif
                                        <span>Created {{ $client->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <i class="linecons-user"></i> <span>{{ count($client->users) }}</span>
                                    </td>
                                    <td>
                                        @if (isset($reseller))
                                            {!! Form::open(['method' => 'delete', 'action' => ['ResellersController@destroyClient', $reseller->id, $client->id]]) !!}
                                                <a href="{{ url('dashboard/resellers/'.$reseller->id.'/clients/'.$client->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="#null" class="edit delete" data-name="{{ $client->name }}"><i class="linecons-trash"></i> Delete</a>
                                            {!! Form::close() !!}
                                        @else
                                            {!! Form::open(['method' => 'delete', 'action' => ['ClientsController@destroy', $client->id]]) !!}
                                                <a href="{{ url('dashboard/clients/'.$client->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="#null" class="edit delete" data-name="{{ $client->name }}"><i class="linecons-trash"></i> Delete</a>
                                            {!! Form::close() !!}
                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        {{-- End Reseller Panel --}}
        @if (isset($reseller))
            </div>
            </div>
        @endif

    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete '+name+'? \n\nNOTE: This will delete ALL of this client\'s users and ALL of their collected data.'))
                    form.submit();
            });
        });
    </script>

@stop