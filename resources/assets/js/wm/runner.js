var Runner = React.createClass({
    propTypes: {
        taskId: React.PropTypes.number.isRequired,
        task: React.PropTypes.object.isRequired,
        assignmentId: React.PropTypes.number.isRequired,
        preview: React.PropTypes.bool.isRequired
    },
    getInitialState: function() {
        return {
            progress: 1,
            practiceComplete: false,
            practiceTimes: [],
            standardDeviation: 150,
            deviationMultiplier: 2.5,
            blockTimeLimit: 8000,
        };
    },
    addPracticeTime: function(time) {
        this.state.practiceTimes.push(time);
    },
    setBlockTimeLimit: function(averageResponseTime) {
        var newLimit = averageResponseTime + (this.state.standardDeviation * this.state.deviationMultiplier);
        if (newLimit < 3000)
            newLimit = 3000;
        this.setState({blockTimeLimit: newLimit});
        //console.log('new time limit: ', newLimit);
    },
    endPracticePhase: function() {
        this.state.practiceComplete = true;

        // Calculate time averages
        var total = 0;
        var length = this.state.practiceTimes.length;
        for (var i = 0; i < length; i++)
            total = total + this.state.practiceTimes[i];
        var averageTime = total / length;
        this.setBlockTimeLimit(averageTime);
    },
    checkIfPractice: function() {
        if (this.state.practiceComplete)
            return false;

        return true;
    },
    onPartInfoComplete: function(workerId, qualId) {
        this.state.workerId = workerId;
        this.state.qualId = qualId;
        this.advance();
    },
    onTaskComplete: function(res) {
        this.advance();

        //Short problem blocks
        for(var i = 0; i < this.props.task.blocks.length; i++)
            this.props.task.blocks[i].problems.sort(function(a, b){return (a.id - b.id)});

        if (this.props.preview == false)
        {
            // If we are taking a sample test, submit differently
            if (this.props.assignmentId == 123456789)
            {
                var url = window.location.href;
                var n1 = url.search("sample/") + 7;
                var n2 = url.search("/take");
                var name = url.substring(n1, n2);
                var path = '/assessment/sample/'+name+'/complete';
                var method = "post";
                var form = document.createElement("form");
                console.log(url, name, path, method);
                form.setAttribute("method", method);
                form.setAttribute("action", path);
                var token = jQuery('meta[name="csrf_token"]').attr('content');
                var params = {_token: token};

                for(var key in params) {
                    if(params.hasOwnProperty(key)) {
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", key);
                        hiddenField.setAttribute("value", params[key]);

                        form.appendChild(hiddenField);
                    }
                }

                document.body.appendChild(form);
                form.submit();
            }

            // This is an actual test
            else
            {
                $.ajax({
                    type: 'POST',
                    url: saveUrl,
                    data: {
                        // taskId: this.props.taskId,
                        // workerId: this.state.workerId,
                        // qualId: this.state.qualId,
                        assignmentId: this.props.assignmentId,
                        preview: this.props.preview,
                        json: JSON.stringify(res),
                        // score: TSK.getScore(this.props.task, res)
                    },
                    context: this,
                    success: function(data, textStatus, jqXHR) {

                        // Or an error occurred
                        if (data['success'] != true)
                        {
                            console.log('something is not right');
                            var output = document.createElement("pre");
                            output.innerHTML = data;
                            document.body.insertBefore(output, document.body.firstChild);
                            this.setState({
                                progress: 5,
                                message: "Something went wrong. Please contact the test administrator."
                            });
                        }

                        // Actual assessment, complete it
                        else
                        {
                            console.log('task completed');
                            var submitButton = document.getElementById('complete');
                            submitButton.click();
                            this.setState({
                                progress: 5,
                                message: "Assessment completed! Please wait while we submit your responses."
                            });
                        }
                    },
                    error: function(data, jqXHR, textStatus, errorThrown) {
                        console.log(data);
                        var output = document.createElement("pre");
                        output.innerHTML = data.responseText;
                        document.body.insertBefore(output, document.body.firstChild);
                        console.log('Ajax save error', textStatus, errorThrown);
                        this.setState({progress: 4});
                    }
                });
            }
        }

        else
        {
            this.setState({
                progress: 5,
                message: "Assessment completed! Because this is a preview, responses have not been saved."
            });
        }
    },
    advance: function() {
        if(this.state.progress < 3)
            this.setState({progress: this.state.progress + 1});
    },
    render:function(){
        switch(this.state.progress) {
            case 0: //Participant ID screen
                return <PartInfoForm onComplete={this.onPartInfoComplete}/>
            case 1: //The task
                return <Runner.Task task={this.props.task} onComplete={this.onTaskComplete} pushTime={this.addPracticeTime} onEndPractice={this.endPracticePhase} onCheckPractice={this.checkIfPractice} blockTimeLimit={this.state.blockTimeLimit} />
            case 2:
                return <Runner.SavingResult/>
            case 3:
                return <Runner.SaveResultSuccess workerId={this.state.workerId} qualId={this.state.qualId} respId={this.state.respId}/>
            case 4:
                return <Runner.SaveResultError/>
            case 5:
                return <Runner.ShowData message={this.state.message} data={this.state.data}/>
        }
    }
});

Runner.Task = React.createClass({
    propTypes: {
        task: React.PropTypes.object.isRequired,
        onComplete: React.PropTypes.func.isRequired,
        onEndPractice: React.PropTypes.func.isRequired,
        pushTime: React.PropTypes.func.isRequired,
        onCheckPractice: React.PropTypes.func.isRequired,
        blockTimeLimit: React.PropTypes.number.isRequired,
    },
    getInitialState: function() {
        this.tra = {correct: 0, total: 0};
        this.res = [];

        return {i: 0};
    },
    onBlockComplete: function(res, tra) {
        var entry = this.props.task.struct[this.state.i];
        var block = this.props.task.blocks[entry.id];
        var practice = block.practice;
        var questionType = block.problems[0].type;

        // If math/letters or symmetry/squares, record the time it takes to do the practice question
        // Use this time for all other math and symmetry questions
        if (practice)
        {
            if (questionType == 'eqls')
            {
                for (var j = 0; j < res[0].equations.length; j++)
                {
                    var equation = res[0].equations[j];
                    var time = equation.time;
                    this.props.pushTime(time);
                }
            }
            if (questionType == 'sysq')
            {
                for (var j = 0; j < res[0].symmetries.length; j++)
                {
                    var symmetry = res[0].symmetries[j];
                    var time = symmetry.time;
                    this.props.pushTime(time);
                }
            }
        }

        // If we're still practicing, check if we're about to end
        if (this.props.onCheckPractice())
        {
            var nextEntry = this.props.task.struct[this.state.i];
            for (var k = 0; k < this.props.task.struct.length; k++)
            {
                nextEntry = this.props.task.struct[this.state.i + 1 + k];
                if (nextEntry && nextEntry.type == "block")
                    break;
            }
            block = this.props.task.blocks[nextEntry.id];
            practice = block.practice;
            if (! practice)
                this.props.onEndPractice();
        }

        this.res.push(res);
        this.advance();
    },
    advance: function() {
        if(this.state.i == this.props.task.struct.length - 1) {
            this.props.onComplete(this.res);
        }
        else {
            this.setState({i: this.state.i + 1});
        }
    },
    getCompToRender: function() {
        if(this.props.task.struct[this.state.i].type == 'block')
            return this.getBlockToRender();
        else
            return this.getInstToRender();
    },
    /**
     * Returns a Block component to be rendered.
     */
    getBlockToRender: function() {
        var entry = this.props.task.struct[this.state.i];
        var block = this.props.task.blocks[entry.id];
        return  <Block key={this.state.i} block={block.problems} tra={block.practice ? {correct: 0, total: 0} : this.tra} randomize={true} practice={block.practice} timeLimit={this.props.blockTimeLimit} onComplete={this.onBlockComplete} />
    },
    /**
     * Returns an Instruction component to be rendered.
     */
    getInstToRender: function() {
        var entry = this.props.task.struct[this.state.i];
        var inst = this.props.task.instructs[entry.id];
        return <Instruction key={this.state.i} text={inst.text} nextBtnLabel={inst.next} onComplete={this.advance}/>
    },
    render: function() {
        return this.getCompToRender();
    }
});

Runner.SavingResult = React.createClass({
    render: function() {
        return <div style={{fontSize:25}}>You have completed the task. Please wait while we are submitting your responses.</div>
    }
});

Runner.SaveResultSuccess = React.createClass({
    propTypes: {
        workerId: React.PropTypes.string.isRequired,
        qualId: React.PropTypes.number.isRequired,
        respId: React.PropTypes.number.isRequired
    },
    render: function() {
        return (
            <div style={{fontSize:25}}>
                <p>Your responses have been submitted with confirmation code <b>{this.props.workerId + '-' + this.props.qualId + '-' + this.props.respId}</b></p>
                <p>You may now close this survey. Thank you.</p>
            </div>
        )
    }
});

Runner.SaveResultError = React.createClass({
    render: function() {
        return <div style={{fontSize:25}}>There was an error submitting your responses. Please contact the coordinator of this experiment.</div>
    }
});

Runner.ShowData = React.createClass({
    propTypes: {
        message: React.PropTypes.string.isRequired,
    },
    render: function() {
        return <div style={{fontSize:25}}>{this.props.message}</div>
    }
});