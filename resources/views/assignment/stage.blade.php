@extends('app')

@section('title')
    Assignments
@stop

@section('body')

    {{-- @include('dashboard.partials._settings') --}}

    {{-- @include('assignment.partials._nav') --}}

    <div class="page-container assignment">

        @include('assignment.partials._header', ['preview' => false])

        <div class="main-content">

            {{-- Warning --}}
            {{--<div class="alert alert-danger">--}}
                {{--{!! translate('All content associated with the test is copyrighted and may not be reproduced in any form.') !!}--}}
                {{--<ul>--}}
                    {{--<li>{!! translate('You may not record images of the test items in any form.') !!}</li>--}}
                    {{--<li>{!! translate('You may not use outside sources to help you answer the test items.') !!}</li>--}}
                    {{--<li>{!! translate('You must verify that the person taking the test is the same person whose name is on the email that contained the test link.') !!}</li>--}}
                {{--</ul>--}}
            {{--</div><br/>--}}

            {{-- Description --}}
            <div class="description">
                @if ($assignment->translation() && $assignment->translation()->description)
                    {!! custom_fields($assignment->id, $assignment->translation()->description) !!}
                @else
                    {!! custom_fields($assignment->id, $assessment->description) !!}
                @endif
            </div>

            <div style="text-align: center;">
                @if ($assignment->assessment()->id == get_global('sspan') && \Auth::user()->language_id == 1)
                    <h3>Please review this instructional video before beginning the assessment:</h3>
                    <div class="instruction-video">
                        <video width="900" height="506" controls controlsList="nodownload">
                            <source src="https://s3-us-west-2.amazonaws.com/aoe-uploads/videos/wmsspan_instructions.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <a style="line-height: 56px;margin-top:60px;" href="{{ $assignment->url }}" class="btn btn-primary btn-lg">{{ translate('Begin The Assessment') }}</a>
                    </div>
                @else
                    <a style="line-height: 56px;" href="{{ $assignment->url }}" class="btn btn-primary btn-lg">{{ translate('Begin The Assessment') }}</a>
                @endif
            </div>

            @include('assignment.partials._footer')

        </div>
    </div>

@stop