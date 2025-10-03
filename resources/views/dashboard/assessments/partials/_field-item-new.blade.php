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
						<div style="margin-bottom: 5px;">{!! $content !!}</div>
						@if (isset($anchors) && !empty($anchors))
							@php
								$anchorsArray = is_string($anchors) ? json_decode($anchors, true) : $anchors;
							@endphp
							@if (!empty($anchorsArray))
								<div style="margin-top: 10px;">
									<small style="color: #7f8c8d;">Options:</small>
									@foreach ($anchorsArray as $anchor)
										<div style="margin: 2px 0; padding: 2px 8px; background: #f8f9fa; border-radius: 3px; display: inline-block; margin-right: 5px;">
											{{ $anchor['tag'] }}
										</div>
									@endforeach
								</div>
							@endif
						@endif
					</div>
				</div>
			@elseif ($type == 3)
				<!-- Text Input Field -->
				<div class="text-input-preview">
					<strong>Text Input:</strong>
					<div style="margin-top: 8px;">
						<div style="margin-bottom: 5px;">{!! $content !!}</div>
						<div style="margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 3px; background: #f8f9fa;">
							<input type="text" placeholder="User will type here..." style="border: none; background: none; width: 100%;" disabled>
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
