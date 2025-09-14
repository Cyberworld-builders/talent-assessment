@extends('dashboard.dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <i class="fa fa-edit"></i>
                    Edit Feedback Library: {{ $library->name }}
                </div>
            </div>
            <div class="panel-body">
                <form id="feedback-form" method="POST" action="{{ url('dashboard/feedback/' . $library->id) }}">
                    {{ csrf_field() }}
                    {{ method_field('PATCH') }}
                    
                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Library Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $library->name) }}" required>
                                @if($errors->has('name'))
                                    <span class="help-block text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_id">Client (Optional)</label>
                                <select class="form-control" id="client_id" name="client_id">
                                    <option value="">Global Library</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" 
                                                {{ old('client_id', $library->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('client_id'))
                                    <span class="help-block text-danger">{{ $errors->first('client_id') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Feedback Content -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="feedback">Feedback Content (JSON) *</label>
                                <textarea class="form-control" id="feedback" name="feedback" rows="15" 
                                          placeholder="Enter JSON feedback structure..." required>{{ old('feedback', json_encode($library->feedback, JSON_PRETTY_PRINT)) }}</textarea>
                                @if($errors->has('feedback'))
                                    <span class="help-block text-danger">{{ $errors->first('feedback') }}</span>
                                @endif
                                <div class="help-block">
                                    <p><strong>JSON Structure:</strong></p>
                                    <pre class="text-muted" style="font-size: 12px;">{
  "dimensions": {
    "dimension_name": {
      "high": "Feedback for high performers",
      "medium": "Feedback for medium performers", 
      "low": "Feedback for low performers"
    }
  }
}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Library
                            </button>
                            <a href="{{ url('dashboard/feedback') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // JSON validation
    $('#feedback').on('blur', function() {
        var jsonText = $(this).val();
        if (jsonText.trim()) {
            try {
                JSON.parse(jsonText);
                $(this).removeClass('error');
                $(this).next('.help-block').find('.json-error').remove();
            } catch (e) {
                $(this).addClass('error');
                if (!$(this).next('.help-block').find('.json-error').length) {
                    $(this).next('.help-block').append('<span class="text-danger json-error">Invalid JSON format</span>');
                }
            }
        }
    });
    
    // Form submission
    $('#feedback-form').on('submit', function(e) {
        var jsonText = $('#feedback').val();
        if (jsonText.trim()) {
            try {
                JSON.parse(jsonText);
            } catch (e) {
                e.preventDefault();
                alert('Please fix the JSON format before submitting.');
                return false;
            }
        }
    });
});
</script>

<style>
.error {
    border-color: #a94442;
}

.json-error {
    display: block;
    margin-top: 5px;
}
</style>
@endsection