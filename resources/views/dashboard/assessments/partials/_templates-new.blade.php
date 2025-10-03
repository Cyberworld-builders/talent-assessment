<!-- Field Templates for New Editor -->
<div id="field-templates" style="display: none;">
	
	<!-- Multiple Choice Template -->
	<div id="field-template-1" class="field-item" data-type="1">
		<div class="field-header">
			<div style="display: flex; align-items: center;">
				<div class="drag-handle">
					<i class="fa-bars"></i>
				</div>
				<div class="field-number">1</div>
				<div class="field-type">Multiple Choice</div>
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
				<strong>Multiple Choice Question:</strong>
				<div style="margin-top: 8px;">
					<div style="margin-bottom: 5px;">What is your preferred working style?</div>
					<div style="margin-top: 10px;">
						<small style="color: #7f8c8d;">Options:</small>
						<div style="margin: 2px 0; padding: 2px 8px; background: #f8f9fa; border-radius: 3px; display: inline-block; margin-right: 5px;">Collaborative</div>
						<div style="margin: 2px 0; padding: 2px 8px; background: #f8f9fa; border-radius: 3px; display: inline-block; margin-right: 5px;">Independent</div>
						<div style="margin: 2px 0; padding: 2px 8px; background: #f8f9fa; border-radius: 3px; display: inline-block; margin-right: 5px;">Mixed</div>
					</div>
				</div>
			</div>
		</div>
		{!! Form::hidden('field_type[]', 1) !!}
		{!! Form::hidden('field_content[]', 'What is your preferred working style?') !!}
		{!! Form::hidden('field_number[]', 1) !!}
		{!! Form::hidden('field_anchors[]', json_encode([
			['tag' => 'Collaborative', 'value' => 1],
			['tag' => 'Independent', 'value' => 2],
			['tag' => 'Mixed', 'value' => 3]
		])) !!}
	</div>
	
	<!-- Description Template -->
	<div id="field-template-2" class="field-item" data-type="2">
		<div class="field-header">
			<div style="display: flex; align-items: center;">
				<div class="drag-handle">
					<i class="fa-bars"></i>
				</div>
				<div class="field-number">1</div>
				<div class="field-type">Description</div>
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
				<strong>Description:</strong>
				<div style="margin-top: 8px; padding: 10px; background: #e8f4fd; border-left: 3px solid #3498db; border-radius: 3px;">
					Please read the following instructions carefully before proceeding with the assessment.
				</div>
			</div>
		</div>
		{!! Form::hidden('field_type[]', 2) !!}
		{!! Form::hidden('field_content[]', 'Please read the following instructions carefully before proceeding with the assessment.') !!}
		{!! Form::hidden('field_number[]', 1) !!}
	</div>
	
	<!-- Text Input Template -->
	<div id="field-template-3" class="field-item" data-type="3">
		<div class="field-header">
			<div style="display: flex; align-items: center;">
				<div class="drag-handle">
					<i class="fa-bars"></i>
				</div>
				<div class="field-number">1</div>
				<div class="field-type">Text Input</div>
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
				<strong>Text Input:</strong>
				<div style="margin-top: 8px;">
					<div style="margin-bottom: 5px;">Please describe your experience with this topic.</div>
					<div style="margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 3px; background: #f8f9fa;">
						<input type="text" placeholder="User will type here..." style="border: none; background: none; width: 100%;" disabled>
					</div>
				</div>
			</div>
		</div>
		{!! Form::hidden('field_type[]', 3) !!}
		{!! Form::hidden('field_content[]', 'Please describe your experience with this topic.') !!}
		{!! Form::hidden('field_number[]', 1) !!}
	</div>
	
	<!-- Letters Template -->
	<div id="field-template-4" class="field-item" data-type="4">
		<div class="field-header">
			<div style="display: flex; align-items: center;">
				<div class="drag-handle">
					<i class="fa-bars"></i>
				</div>
				<div class="field-number">1</div>
				<div class="field-type">Letters</div>
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
				<strong>Letters:</strong>
				<div style="margin-top: 8px;">
					<div style="margin-bottom: 5px;">Enter the letters in the correct order.</div>
					<div style="margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 3px; background: #f8f9fa;">
						<input type="text" placeholder="A, B, C, D..." style="border: none; background: none; width: 100%;" disabled>
					</div>
				</div>
			</div>
		</div>
		{!! Form::hidden('field_type[]', 4) !!}
		{!! Form::hidden('field_content[]', 'Enter the letters in the correct order.') !!}
		{!! Form::hidden('field_number[]', 1) !!}
	</div>
	
	<!-- Equation Template -->
	<div id="field-template-5" class="field-item" data-type="5">
		<div class="field-header">
			<div style="display: flex; align-items: center;">
				<div class="drag-handle">
					<i class="fa-bars"></i>
				</div>
				<div class="field-number">1</div>
				<div class="field-type">Equation</div>
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
				<strong>Equation:</strong>
				<div style="margin-top: 8px;">
					<div style="margin-bottom: 5px;">Solve the following equation: 2x + 5 = 13</div>
					<div style="margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 3px; background: #f8f9fa;">
						<input type="text" placeholder="x = ?" style="border: none; background: none; width: 100%;" disabled>
					</div>
				</div>
			</div>
		</div>
		{!! Form::hidden('field_type[]', 5) !!}
		{!! Form::hidden('field_content[]', 'Solve the following equation: 2x + 5 = 13') !!}
		{!! Form::hidden('field_number[]', 1) !!}
	</div>
	
</div>
