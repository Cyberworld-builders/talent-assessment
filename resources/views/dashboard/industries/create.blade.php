@extends('app')

@section('title')
    Create Industry
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Create New Industry</h3>
                </div>
                <div class="panel-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('dashboard/industries') }}">
                        {{ csrf_field() }}
                        
                        <div class="form-group">
                            <label for="name">Industry Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" required>
                            <p class="help-block">Enter a unique industry name (e.g., Technology, Healthcare, Finance)</p>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create Industry
                            </button>
                            <a href="{{ url('dashboard/industries') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Industries
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
