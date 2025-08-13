@extends('dashboard.dashboard')

@section('title')
    Edit Feedback Library
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Edit Feedback Library: {{ $library->name }}</div>
                </div>
                <div class="panel-body">
                    <form id="feedback-form" method="POST" action="{{ url('dashboard/feedback/' . $library->id) }}">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        
                        <div class="form-group">
                            <label for="name">Library Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ $library->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="feedback">Feedback Content (JSON)</label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="15" required 
                                      placeholder='{
  "dimensions": {
    "leadership": {
      "high": "Excellent leadership skills demonstrated. You show strong ability to guide and motivate others.",
      "medium": "Good leadership potential. Continue developing your ability to influence and guide others.",
      "low": "Leadership development needed. Focus on building confidence and communication skills."
    },
    "communication": {
      "high": "Outstanding communication skills. You effectively convey ideas and build rapport.",
      "medium": "Good communication abilities. Continue practicing clear and concise expression.",
      "low": "Communication skills need improvement. Focus on clarity and active listening."
    }
  }
}'>{{ json_encode($library->feedback, JSON_PRETTY_PRINT) }}</textarea>
                            <small class="help-block">Enter feedback content in JSON format. Structure should include dimensions and performance levels.</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Library</button>
                            <a href="{{ url('dashboard/feedback') }}" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#feedback-form').on('submit', function(e) {
                e.preventDefault();
                
                var formData = $(this).serialize();
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            alert(response.success);
                            window.location.href = '{{ url("dashboard/feedback") }}';
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        if (errors) {
                            var errorMessage = '';
                            for (var key in errors) {
                                errorMessage += errors[key].join('\n');
                            }
                            alert('Error: ' + errorMessage);
                        } else {
                            alert('An error occurred while updating the feedback library.');
                        }
                    }
                });
            });
        });
    </script>
@endsection
