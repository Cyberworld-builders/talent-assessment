<div class="panel panel-headerless assignment-user" style="margin-bottom:5px;">

    <input type="hidden" name="user[]" value="0" />

    <a class="remove-row-button" href="#null"><i class="fa-times"></i></a>
    <div class="panel-body">
        <div class="row">

            <!-- User -->
            <div class="col-sm-3">
                <div class="user-tab">
                    <i class="fa-user" style="float: left; font-size: 36px; padding-right: 10px; color: rgb(207, 207, 207);"></i>
                    <h3 id="user-name" style="font-size: 17px; font-weight: bold; margin: 0;">Bob Dylan</h3>
                    <span id="user-username-email">bob123 <i>(bob@bob.com)</i></span>
                    <div class="right-arrow" style="float: right; position: relative; top: -10px;"><i class="fa-long-arrow-right"></i></div>
                </div>
            </div>

            <!-- Assessments -->
            <div class="col-sm-9">

                <div class="assessment-tabs">

                    {{--@include('dashboard.assignments.partials._assessment')--}}
                    {{--<hr />--}}
{{--                    @include('dashboard.assignments.partials._assessment')--}}

                </div>

            </div>

        </div>

    </div>

</div>