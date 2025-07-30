<tr data-index="{{ $i }}">
    <td>
        <div class="row no-border">
            <div class="col-sm-3">
                @if ($ksa)
                    {!! Form::text('ksas['.$i.'][name]', $ksa['name'], ['class' => 'form-control input-lg array-input']) !!}
                @else
                    {!! Form::text('ksas['.$i.'][name]', null, ['class' => 'form-control input-lg array-input']) !!}
                @endif
            </div>
            <div class="col-sm-9">
                {!! Form::textarea('ksas['.$i.'][description]', $ksa['description'], ['class' => 'form-control input-lg autosize array-input', 'rows' => 1]) !!}
            </div>
        </div>
    </td>
    <td>{!! Form::checkbox('ksas['.$i.'][relevant]', true, $ksa['relevant'], ['class' => 'iswitch iswitch-secondary array-input']) !!}</td>
</tr>