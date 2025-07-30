<div class="position" data-index="{{ $index }}">
    <div class="row">
        <div class="col-sm-3">
            {!! Form::label('position', 'Position', ['class' => 'control-label']) !!}
            <p class="small text-muted">Pre-fill the position field.</p>
        </div>
        <div class="col-sm-9 position-col">
            <div class="remove-position"><i class="fa-times"></i></div>
            @if ($index === '')
                {!! Form::text(null, null, ['class' => 'form-control input-lg']) !!}
            @else
                {!! Form::text('position['.$index.']', $position, ['class' => 'form-control input-lg']) !!}
            @endif

            {{-- Tasks --}}
            <div class="row task-row">
                <div class="col-sm-3">
                    {!! Form::label('tasks', 'Tasks', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Pre-fill the tasks for this position.</p>
                </div>
                <div class="col-sm-9 task-col">
                    @if ($analysis and $analysis->tasks)
                        @foreach ($analysis->tasks[$index] as $j => $task)
                            @include('dashboard.analysis.partials._task', [
                                'position' => $index,
                                'task' => $task,
                                'index' => $j
                            ])
                        @endforeach
                    @else
                        @include('dashboard.analysis.partials._task', [
                            'position' => $index,
                            'task' => '',
                            'index' => 0
                        ])
                    @endif
                </div>
                <div class="col-sm-12">
                    <br>
                    <div class="pull-right">
                        <button class="btn btn-small add-task"><i class="fa-plus"></i> Add Task</button>
                    </div>
                </div>
            </div>

            {{-- KSAs --}}
            <div class="row ksa-row">
                <div class="col-sm-3">
                    {!! Form::label('ksas', 'KSAs', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Pre-fill the KSAs for this position.</p>
                </div>
                <div class="col-sm-9 ksa-col">
                    @if ($analysis and $analysis->ksas)
                        @foreach ($analysis->ksas[$index] as $k => $ksa)
                            @include('dashboard.analysis.partials._ksa', [
                                'position' => $index,
                                'ksa' => $ksa['name'],
                                'description' => $ksa['description'],
                                'index' => $k
                            ])
                        @endforeach
                    @else
                        @include('dashboard.analysis.partials._ksa', [
                            'position' => $index,
                            'ksa' => '',
                            'description' => '',
                            'index' => 0
                        ])
                    @endif
                </div>
                <div class="col-sm-12">
                    <br>
                    <div class="pull-right">
                        <button class="btn btn-small add-ksa"><i class="fa-plus"></i> Add KSA</button>
                    </div>
                </div>
            </div>

            {{-- Ratings --}}
            <div class="row rating-row">
                <div class="col-sm-3">
                    {!! Form::label('ratings', 'Importance Ratings', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Choose what dimensions or assessments should be given an importance rating.</p>
                </div>
                <div class="col-sm-9 rating-col">
                    @if ($analysis and $analysis->ratings)
                        @foreach ($analysis->ratings[$index] as $n => $rating)
                            @include('dashboard.analysis.partials._rating', [
                                'index' => $n,
                                'position' => $index,
                                'type' => $rating['type'],
                                'id' => $rating['id'],
                                'description' => $rating['description'],
                            ])
                        @endforeach
                    @else
                        @include('dashboard.analysis.partials._rating', [
                            'position' => $index,
                            'index' => '',
                            'type' => '',
                            'id' => '',
                            'description' => '',
                        ])
                    @endif
                </div>
                <div class="col-sm-12">
                    <br>
                    <div class="pull-right">
                        <button class="btn btn-small add-rating"><i class="fa-plus"></i> Add Rating</button>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>
</div>