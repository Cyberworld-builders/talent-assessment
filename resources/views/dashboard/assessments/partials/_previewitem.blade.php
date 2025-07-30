<div class="assignment-user">

    @if (isset($tempUser))
        <input type="hidden" name="user[]" value="{{ $tempUser->id }}" />
    @else
        <input type="hidden" name="user[]" value="0" />
    @endif

    @if (isset($tempTarget))
        <input type="hidden" name="target[]" value="{{ $tempTarget->id }}" />
    @else
        <input type="hidden" name="target[]" value="0" />
    @endif

    @if (isset($tempRole))
        <input type="hidden" name="role[]" value="{{ $tempRole }}" />
    @else
        <input type="hidden" name="role[]" value="0" />
    @endif

    <a class="remove-row-button" href="#null"><i class="fa-times"></i></a>
    <div class="panel-body">
        <div class="row">

            {{-- User --}}
            <div class="col-sm-4">
                <div class="user-tab">
                    <i class="fa-user"></i>
                    @if (isset($tempUser))
                        <h3 id="user-name">{{ $tempUser->name }}</h3>
                        <span id="user-username-email">{{ $tempUser->username }} <i>{{ $tempUser->email }}</i></span>
                    @else
                        <h3 id="user-name">Bob Dylan</h3>
                        <span id="user-username-email">bob123 <i>(bob@bob.com)</i></span>
                    @endif
                    @if ($target > 0)
                        <div class="right-arrow">
                            <i class="fa-long-arrow-right"></i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Target--}}
            <div class="col-sm-8">
                @if ($target > 0)
                    <div class="row">
                        <div class="col-sm-8">
                            <span class="target-span">Target: </span>
                            @if (isset($tempTarget))
                                <div class="label label-white target-label">{{ $tempTarget->name }} ({{ $tempTarget->email }})</div>
                            @else
                                @if (isset($tempUser))
                                    <div class="label label-white target-label" style="box-shadow: 0px 0px 0px 1px red;">Not Set</div>
                                @else
                                    <div class="label label-white target-label">Not Set</div>
                                @endif
                            @endif
                        </div>
                        <div class="col-sm-4">
                            <div class="pull-right target-button">
                                <a id="add-target-modal" class="btn btn-black"><i class="fa-user"></i> Set Target</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>