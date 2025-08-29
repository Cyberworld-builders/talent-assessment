@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Static Test Form</h1>
    <p>This is a static test form to isolate the anchors issue.</p>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>Test Assessment Form</h3>
                </div>
                <div class="panel-body">
                    <p>If you can see this, the controller is working fine.</p>
                    <p>The issue was likely in the view template.</p>
                    
                    <div class="alert alert-info">
                        <strong>Debug Info:</strong><br>
                        Assessment ID: {{ $assessment->id ?? 'N/A' }}<br>
                        Assessment Name: {{ $assessment->name ?? 'N/A' }}<br>
                        Questions Count: {{ count($questions ?? []) }}
                    </div>
                    
                    <div class="form-group">
                        <label>Test Question 1</label>
                        <div class="radio">
                            <label><input type="radio" name="test" value="1"> Option 1</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="test" value="2"> Option 2</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="test" value="3"> Option 3</label>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-primary">Submit Test</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection