<div class="panel panel-headerless dimension" style="margin-bottom:5px;">

    <input type="hidden" name="dimension[]" value="0" />

    <a class="remove-row-button" href="#null"><i class="fa-times"></i></a>
    <div class="panel-body">
        <div class="row">

            <!-- User -->
            <div class="col-sm-3">
                <div class="user-tab">
                    <i class="fa-cube" style="float: left; font-size: 36px; padding-right: 10px; color: rgb(207, 207, 207);"></i>
                    <h3 id="dimension-name" style="font-size: 17px; font-weight: bold; margin: 0;">Dimension</h3>
                    <span id="dimension-used-in">Used in 1 Assessment</span>
                </div>
            </div>

            <!-- Percentile -->
            <div class="col-sm-3">
                <div class="form-group" style="margin:0;">
                    {!! Form::text('percentile[]', null, ['class' => 'form-control input-lg', 'placeholder' => 'Enter weighting percentile']) !!}
                </div>
            </div>

        </div>

    </div>

</div>