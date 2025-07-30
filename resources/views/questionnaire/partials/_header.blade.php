<div class="heading">

    {{-- Background --}}
    <img class="background" src="{{ asset('assets/images/aoe-background.jpg') }}" />

    {{-- Logo --}}
    <div class="logo">
        <img src="{{ asset('assets/images/aoe-jaq.png') }}" />
    </div>

    {{-- Title --}}
    {{--<div class="title">Job Questionnaire</div>--}}

</div>

@if ($jaq->completed)
    <div class="alert alert-success" style="text-align:center;">
        <h3 style="color:white;">This Questionnaire Has Been Completed</h3>
    </div><br>
@endif
