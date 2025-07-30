@extends('dashboard.dashboard')

@section('styles')
    <style>
        .text-available {
            color: #8dc63f;
        }
        .text-creating,
        .text-backing-up,
        .text-deleting,
        .text-maintenance,
        .text-modifying,
        .text-rebooting,
        .text-renaming,
        .text-resetting-master-credentials,
        .text-upgrading {
            color: #ffba00;
        }
        .text-failed,
        .text-inaccessible-encryption-credentials,
        .text-incompatible-credentials,
        .text-incompatible-network,
        .text-incompatible-option-group,
        .text-incompatible-parameters,
        .text-incompatible-restore,
        .text-restore-error,
        .text-storage-full,
        .text-cannot-access-aws-host {
            color: #cc3f44;
        }
    </style>
@stop

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Resellers</h1>
            <p class="description">Add or manage AOE Resellers.</p>
        </div>
    </div>

    <div class="row">

        <!-- Add Client Button -->
        <div class="col-md-12">
            <div class="pull-right">
                <a href="{{ url('dashboard/resellers/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add Reseller</a>
            </div>
        </div>

        <!-- Clients -->
        <div class="col-md-12">
            <div class="tab-content">
                <div class="tab-pane active">

                    <table class="table table-hover members-table middle-align">
                        <thead>
                        <tr>
                            {{--<th></th>--}}
                            <th class="hidden-xs hidden-sm"></th>
                            <th>Reseller Name</th>
                            <th># of Clients</th>
                            <th>Database</th>
                            <th>Settings</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($resellers as $reseller)

                            <tr>
                                {{--<td class="user-cb">--}}
                                {{--<input type="checkbox" class="cbr" name="members-list[]" value="1" checked />--}}
                                {{--</td>--}}
                                <td class="user-image hidden-xs hidden-sm">
                                    @if ($reseller->logo)
                                        <img src="{{ show_image($reseller->logo) }}" class="img-circle" alt="reseller-pic" />
                                    @else
                                        <img src="{{ asset('assets/images/client-1.png') }}" class="img-circle" alt="user-pic" />
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('dashboard/resellers/'.$reseller->id) }}" class="name">{{ $reseller->name }}</a>
                                    <span>Created {{ $reseller->created_at->diffForHumans() }}</span>
                                </td>
                                <td>
                                    <i class="linecons-user"></i> <span>{{ $reseller->clientsCount() }}</span>
                                </td>
                                <td>
                                    <span class="text-{{ $reseller->db_status }}">{{ readable_string($reseller->db_status) }}</span>
                                </td>
                                <td>
                                    {!! Form::open(['method' => 'delete', 'action' => ['ResellersController@destroy', $reseller->id]]) !!}
                                        <a href="{{ url('dashboard/resellers/'.$reseller->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                        @if ($reseller->db_status == 'available')
                                            <a href="#null" class="edit delete" data-name="{{ $reseller->name }}"><i class="linecons-trash"></i> Delete</a>
                                        @endif
                                    {!! Form::close() !!}
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete '+name+'? \n\nNOTE: This will delete ALL of this resellers\'s users, clients, and ALL of their data.'))
                    form.submit();
            });
        });
    </script>

@stop