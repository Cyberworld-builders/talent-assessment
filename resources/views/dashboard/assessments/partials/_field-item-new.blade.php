<?php
	$description = \App\Question::getTypeDescription($type);
	$isWMType = \App\Question::checkIfWMType($type);
?>

<div class="field-item" data-type="{{ $type }}" data-id="{{ $id ?? '' }}">
	<div class="field-header">
		<div style="display: flex; align-items: center;">
			<div class="drag-handle">
				<i class="fa-bars"></i>
			</div>
			<div class="field-number">{{ $number }}</div>
			<div class="field-type">{{ $description }}</div>
		</div>
		<div class="field-actions">
			<button type="button" class="field-action-btn edit-field" title="Edit Field">
				<i class="fa-edit"></i> Edit
			</button>
			<button type="button" class="field-action-btn duplicate-field" title="Duplicate Field">
				<i class="fa-copy"></i> Duplicate
			</button>
			<button type="button" class="field-action-btn remove-field" title="Remove Field">
				<i class="fa-trash"></i> Remove
			</button>
		</div>
	</div>
	
	<div class="field-content">
		<div class="field-preview">
			@if ($type == 2)
				<!-- Description Field -->
				<div class="description-preview">
					<strong>Description:</strong>
					<div style="margin-top: 8px; padding: 10px; background: #e8f4fd; border-left: 3px solid #3498db; border-radius: 3px;">
						{!! $content !!}
					</div>
				</div>
			@elseif ($type == 1)
				<!-- Multiple Choice Field -->
				<div class="multiple-choice-preview">
					<strong>Multiple Choice Question:</strong>
					<div style="margin-top: 8px;">
						<div style="margin-bottom: 5px; font-weight: 500;">{!! $content !!}</div>
						@if (isset($anchors) && !empty($anchors))
							<div style="margin-top: 10px;">
								<small style="color: #7f8c8d; font-weight: 600;">Anchors:</small>
								<div style="margin-top: 5px;">
									@foreach ($anchors as $anchor)
										<div style="margin: 3px 0; padding: 6px 12px; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; display: inline-block; margin-right: 8px; font-size: 13px;">
											<i class="fa-circle-o" style="margin-right: 5px; color: #6c757d;"></i>{{ $anchor['tag'] }}
										</div>
									@endforeach
								</div>
							</div>
						@endif
					</div>
				</div>
			@elseif ($type == 3)
				<!-- Text Input Field -->
				<div class="text-input-preview">
					<strong>Text Input:</strong>
					<div style="margin-top: 8px;">
						<div style="margin-bottom: 5px; font-weight: 500;">{!! $content !!}</div>
						<div style="margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa;">
							<input type="text" placeholder="User will type here..." style="border: none; background: none; width: 100%; color: #6c757d; font-style: italic;" disabled>
						</div>
					</div>
				</div>
			@else
				<!-- Other Field Types -->
				<div class="other-field-preview">
					<strong>{{ $description }}:</strong>
					<div style="margin-top: 8px;">
						{!! $content !!}
					</div>
				</div>
			@endif
		</div>
	</div>
	
	<!-- Hidden form fields for submission -->
	@if ($id)
		{!! Form::hidden('field_id[]', $id) !!}
	@endif
	{!! Form::hidden('field_type[]', $type) !!}
	{!! Form::hidden('field_content[]', $content) !!}
	{!! Form::hidden('field_number[]', $number) !!}
	@if (isset($anchors))
		{!! Form::hidden('field_anchors[]', json_encode($anchors)) !!}
	@endif
	@if (isset($dimension_id))
		{!! Form::hidden('field_dimension[]', $dimension_id) !!}
	@endif
</div>
