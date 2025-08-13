@extends('dashboard.dashboard')

@section('content')

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Benchmarks</h1>
            <p class="description">Select an assessment to manage benchmarks.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Select Assessment</h3>
                </div>
                <div class="panel-body">
                    
                    @if($assessments->count() > 0)
                        <div class="row">
                            @foreach($assessments as $assessment)
                                <div class="col-md-4 col-sm-6">
                                    <div class="panel panel-info">
                                        <div class="panel-body text-center">
                                            <h4>{{ $assessment->name }}</h4>
                                            <p class="text-muted">{{ $assessment->description }}</p>
                                            <a href="{{ url('dashboard/benchmarks/' . $assessment->id) }}" 
                                               class="btn btn-primary">
                                                Manage Benchmarks
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center">
                            <p class="text-muted">No assessments found.</p>
                            <a href="{{ url('dashboard/assessments/create') }}" class="btn btn-primary">
                                Create Assessment
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>

@stop
