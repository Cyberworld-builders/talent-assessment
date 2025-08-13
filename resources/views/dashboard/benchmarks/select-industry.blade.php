@extends('dashboard.dashboard')

@section('content')

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Benchmarks - {{ $assessment->name }}</h1>
            <p class="description">Select an industry to manage benchmarks.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Select Industry</h3>
                </div>
                <div class="panel-body">
                    
                    @if($industries->count() > 0)
                        <div class="row">
                            @foreach($industries as $industry)
                                <div class="col-md-4 col-sm-6">
                                    <div class="panel panel-success">
                                        <div class="panel-body text-center">
                                            <h4>{{ $industry->name }}</h4>
                                            <a href="{{ url('dashboard/benchmarks/' . $assessment->id . '/' . $industry->id) }}" 
                                               class="btn btn-success">
                                                Manage Benchmarks
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center">
                            <p class="text-muted">No industries found.</p>
                            <a href="{{ url('dashboard/industries/create') }}" class="btn btn-primary">
                                Create Industry
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>

@stop
