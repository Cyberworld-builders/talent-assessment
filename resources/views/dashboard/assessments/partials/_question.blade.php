<?php
	$description = \App\Question::getTypeDescription($type);
	$isWMType = \App\Question::checkIfWMType($type);
?>

<li class="list-item">
	<div class="question">

		@if ($id)
			{!! Form::hidden('', $id, ['id' => 'id']) !!}
		@endif

		<div class="question-header">
			<div class="row">
				<div class="col-sm-8">

					<!-- Handle -->
					<div class="reorder uk-nestable-handle"></div>

					<!-- Question Number -->
					<div id="number"<?php if ($type == 2) echo ' style="display:none;"'; ?>>{{ $number }}</div>

					<!-- Body -->
					<div class="body">

						<!-- React Component -->
						@if ($type == 6 or $type == 7 or $type == 8 or $type == 9 or $type == 10)
							<div class="react-comp" data-type="{{ $type }}" data-json="{{ $content }}"><i class="fa-spinner fa-spin"></i></div>
						@endif

						<!-- Content -->
						<div id="content" class="content" <?php if ($type == 6 or $type == 7 or $type == 8 or $type == 9 or $type == 10) echo ' style="display:none;"'; ?>>
							{!! $content !!}
						</div>

						<!-- Instructions -->
						<p id="description" class="small text-muted">{!! $description !!}</p>

						<!-- Edit Content -->
						<span class="edit-field" id="content-edit-field">
							<div class="input-group input-group-lg input-group-minimal">
								{!! Form::text('', 'This is a sample question', [
									'class' => 'form-control input-lg no-right-border question-edit-input'
								]) !!}
								<span class="input-group-addon">
									<a class="advanced-edit-question" data-toggle="tooltip" title="Advanced Editor">
										<i class="fa-edit"></i>
									</a>
								</span>
							</div>
						</span>

					</div>

				</div>
				<div class="col-sm-4">

					<!-- Controls -->
					<div class="controls">

						<!-- WM Controls -->
						<div id="practice" data-practice="{{ $practice }}"<?php echo ($practice) ? ' class="active"' : null; ?><?php echo ($isWMType) ? null : ' style="display:none;"' ?>>
							@if ($practice)
								Practice Question
							@else
								Test Question
							@endif
						</div>

						<!-- Dimension -->
						<?php $current_dim = $dimensions->where('id', $dimension_id)->first(); ?>
						<div id="dimension" <?php echo ($dimension_id && $current_dim) ? 'class="active"' : '' ?> data-dimension="{{ $dimension_id }}" <?php if ($type == 2) echo ' style="display:none;"'; ?>>
							@if ($dimension_id && $current_dim)
								Dimension:
								<?php
									if ($current_dim)
									{
										if ($current_dim->parent) {
											$parent_dim = $dimensions->where('id', $current_dim->parent)->first();
											echo $parent_dim->code;
										}
										echo $current_dim->code;
										echo $number;
									}
								?>
							@else
								Dimension Not Set
							@endif
						</div>

						<!-- Weighting -->
						<a id="duplicate-question" class="control">
							<i class="fa-copy"></i>
						</a>

						<!-- Trash -->
						<a id="remove-question" class="control">
							<i class="fa-trash"></i>
						</a>
					</div>

				</div>
			</div>
		</div>

		<div class="question-body">

			<div class="body">
				<div class="row">
					<div class="question-type-column col-sm-2">
						<button id="question-type" type="button" class="btn" data-id="{{ $type }}">
							<?php
								$types = \App\Question::types();
								echo '<i class="'.$types[$type]['icon'].'"></i> '.$types[$type]['name'];
							?>
						</button>
					</div>

					<!-- Anchoring -->
					<div class="anchors-column col-sm-4" <?php if ($type != 1) echo ' style="display:none;"'; ?>>
						<div id="anchors">
							@if (is_array($anchors) && !empty($anchors))
								@foreach ($anchors as $i => $anchor)
									@if (array_key_exists('value', $anchor) && array_key_exists('tag', $anchor))
										<div class="anchor" data-value="{{ $anchor['value'] }}">{{ $anchor['tag'] }}</div>
									@else
										<div class="alert alert-danger">Non-existing key</div>
									@endif
								@endforeach
							@else
								<div class="anchor disabled">No Anchors Specified</div>
							@endif
						</div>
					</div>
				</div>
			</div>

		</div>

		<!-- Remove Question -->
		<span class="edit-field" id="remove-field">
			<h3>Are you sure you want to remove this question?</h3>
			<span id="confirm-remove"><i class="fa-check"></i> Yes, Remove</span>
			<span id="cancel-remove"><i class="fa-times"></i> Don't Remove</span>
		</span>

		<!-- Set Question Type -->
		<div class="edit-field" id="question-type-field">
			<h3>Choose the <strong>type</strong> of question this is:</h3>
			<span id="cancel-question-type"><i class="fa-times"></i> Cancel</span>
			<div class="question-types">
				@foreach (\App\Question::types() as $typeId => $type)
					<div class="question-type" data-id="{{ $typeId }}" data-name="{{ $type['name'] }}" data-icon="{{ $type['icon'] }}" data-description="{{ $type['description'] }}" data-default="{{ $type['default'] }}">
						<span><i class="{{ $type['icon'] }}"></i> {{ $type['name'] }}</span>
					</div>
				@endforeach
			</div>
		</div>

		<!-- Set Dimension -->
		<div class="edit-field" id="dimension-field">
			<h3>Choose the <strong>Dimension</strong> and <strong>Sub-dimension</strong> this question belongs to:</h3>
			<span id="cancel-dimension"><i class="fa-times"></i> Cancel</span>
			<div class="dimensions">
				@foreach ($dimensions as $dim)
					<?php
						$id = $dim->id;
						if ($dim->parent) continue;
					?>
					<div class="dimension" data-id="{{ $dim->id }}" data-code="{{ $dim->code }}">
						<span>{{ $dim->name }}</span>

						<?php
							$has_children = false;
							foreach ($dimensions as $subdim) {
								if ($subdim->parent == $id) {
									$has_children = true;
									break;
								}
							}
						?>

						@if ($has_children)
							<div class="sub-dimensions">
								<div class="sub-arrow"><i class="fa-long-arrow-right"></i></div>
								@foreach ($dimensions as $subdim)
									@if ($subdim->parent == $id)
										<div class="sub-dimension" data-id="{{ $subdim->id }}" data-code="{{ $subdim->code }}"><span>{{ $subdim->name }}</span></div>
									@endif
								@endforeach
							</div>
						@endif
					</div>
				@endforeach
			</div>
		</div>

	</div>
</li>