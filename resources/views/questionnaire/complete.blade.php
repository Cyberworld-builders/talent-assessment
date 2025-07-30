@extends('app')

@section('title')
    Questionnaire Complete
@stop

@section('body')

    <div class="page-container assignment"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
        <div class="main-content">

            <h3>This questionnaire has been completed!</h3>

            <p>Thank you for participating, your answers have been recorded and a confirmation email has been sent to you.</p>

            <br/><br/><a href="/assignments" class="btn btn-white">Back To Assignments</a>

        </div>
    </div>

@stop