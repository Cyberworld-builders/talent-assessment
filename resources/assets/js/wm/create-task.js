var CreateTask = React.createClass({
    statics: {
        editMode: {add: 0, edit: 1}
    },
    getInitialState: function() {
        // var task;
        // if(taskType == 'ospan')
        //     task = taskTemplate.ospan;
        // else if(taskType == 'sspan')
        //     task = taskTemplate.sspan;
        // else
        //     task = taskTemplate.combined;
        
        return {
            // task: task,
            //Edit problem context {mode, prob, blockId, probId, subId, ssubId}
            editContext: {mode: CreateTask.editMode.add}
        };
    },
    onBlockAdd: function(type) {
        this.state.task.blocks.push({practice: (type === 'prac'), problems:[]});
        this.state.task.struct.push({type: 'block', id: this.state.task.blocks.length - 1});
        this.setState({task: this.state.task});
    },
    onBlockDel: function(blockId) {
        console.log('onBlockDel', blockId);
        this.state.task.blocks.splice(blockId, 1);

        //Find the entry in task.struct to remove
        var i = 0;
        for(; i < this.state.task.struct.length; i++)
            if(this.state.task.struct[i].id == blockId && this.state.task.struct[i].type == 'block')
                break;

        //Remove an entry from task.struct
        this.state.task.struct.splice(i, 1);

        //Adjust inst id of remaining instruct in struct
        for(var i = 0; i < this.state.task.struct.length; i++)
            if(this.state.task.struct[i].type == 'block' && this.state.task.struct[i].id > blockId)
                this.state.task.struct[i].id--;

        this.setState({task: this.state.task});
    },
    /**
     * @param mode integer One of values of editMode.
     * @param blockId integer Position of the block.
     * @param probId integer Position of the problem in the block.
     * @param subid integer  Sub id
     * @param ssubid integer Sub-sub id
     */
    showProbForm: function(mode, blockId, probId, subId, ssubId) {
        console.log(blockId,probId,subId,ssubId);
        if(mode == CreateTask.editMode.add) {
            this.setState({editContext:{mode: CreateTask.editMode.add, blockId: blockId}});
            $(ProbForm.domIdSel).modal('show');
        }
        else {
            var block = this.state.task.blocks[blockId];
            var prob = block.problems[probId];
            var ec = {
                mode: CreateTask.editMode.edit,
                blockId: blockId,
                probId: probId,
                subId: subId,
                ssubId: ssubId
            };

            switch(prob.type) {
                case EQLS.typeId:
                    prob = subId == 0 ? {type: LS.typeId, letters: prob.letters} : {type: EQ.typeId, equation: prob.equations[ssubId]};
                    break;
                case SYSQ.typeId:
                    prob = subId == 0 ? {type: SQ.typeId, squares: prob.squares} : {type: SY.typeId, symmetry: prob.symmetries[ssubId]};
                    break;
            }

            ec.prob = prob;

            this.setState({editContext: ec});
            $(ProbForm.domIdSel).modal('show');
        }
    },
    onAddProbClick: function(blockId) {
        this.showProbForm(CreateTask.editMode.add, blockId);
    },
    onProbEdit: function(blockId, probId, subId, ssubId) {
        this.showProbForm(CreateTask.editMode.edit, blockId, probId, subId, ssubId);
    },
    onProbDel: function(blockId, probId) {
        var block = this.state.task.blocks[blockId];
        block.problems.splice(probId, 1);
        for(var i = 0; i < block.problems.length; i++)
            block.problems[i].id = i;
        this.setState({task:this.state.task});
    },
    onProbFormSave: function() {
        if(this.state.editContext.mode === CreateTask.editMode.add)
            this.probFormSaveNew();
        else
            this.probFormSaveEdit();  
    },
    probFormSaveNew: function() {
        var type = $(ProbForm.domIdSel + ' #probType').val();
        switch(type) {
            case LS.typeId:
                return this.probFormSaveNewLS();
            case EQ.typeId:
                return this.probFormSaveNewEQ();
            case EQLS.typeId:
                return this.probFormSaveNewEQLS();
            case SQ.typeId:
                return this.probFormSaveNewSQ();
            case SY.typeId:
                return this.probFormSaveNewSY();
            case SYSQ.typeId:
                return this.probFormSaveNewSYSQ();
        }
    },
    probFormSaveNewLS: function() {
        var str = $(ProbForm.domIdSel + ' #letters').val().trim();

        if(str && str.length > 0) {
            var a = str.split(',').map(function(str){return str.trim()}),
                i = this.state.editContext.blockId;

            this.state.task.blocks[i].problems.push({
                id: this.state.task.blocks[i].problems.length,
                type: LS.typeId,
                letters: a
            });

            this.setState({task: this.state.task});
            $(ProbForm.domIdSel).modal('hide');
        }
    },
    probFormSaveNewEQ: function() {
        var eq = $(ProbForm.domIdSel + ' #equation').val().trim();

        if(EQ.isValid(eq)) {
            var i = this.state.editContext.blockId;

            this.state.task.blocks[i].problems.push({
                id: this.state.task.blocks[i].problems.length,
                type: EQ.typeId,
                equation: eq
            });

            this.setState({task: this.state.task});
            $(ProbForm.domIdSel).modal('hide');
        }
    },
    probFormSaveNewEQLS: function() {
        var eqStr = $(ProbForm.domIdSel + ' #equations').val().trim();
        var leStr = $(ProbForm.domIdSel + ' #letters').val().trim();

        if(eqStr && leStr) {
            var eq = eqStr.split(',').map(function(str){return str.trim()});
            var le = leStr.split(',').map(function(str){return str.trim()});

            if(eq && eq.length > 0 && le && le.length > 0 && eq.length == le.length) {
                var i = this.state.editContext.blockId;
                this.state.task.blocks[i].problems.push({
                    id: this.state.task.blocks[i].problems.length,
                    type: EQLS.typeId,
                    letters: le,
                    equations: eq
                });
                this.setState({blocks: this.state.task.blocks});
                $(ProbForm.domIdSel).modal('hide');
            }
        }
    },
    probFormSaveNewSQ: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var json = $(ProbForm.domIdSel + ' #squares').val().trim();
        this.state.task.blocks[blockId].problems.push({
            id: this.state.task.blocks[blockId].problems.length,
            type: SQ.typeId,
            squares: JSON.parse(json)
        });
        this.setState({task: this.state.task});
        $(ProbForm.domIdSel).modal('hide');
    },
    probFormSaveNewSY: function(){
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var json = $(ProbForm.domIdSel + ' #symmetry').val().trim();
        this.state.task.blocks[blockId].problems.push({
            id: this.state.task.blocks[blockId].problems.length,
            type: SY.typeId,
            symmetry: JSON.parse(json)
        });
        this.setState({task: this.state.task});
        $(ProbForm.domIdSel).modal('hide');
    },
    probFormSaveNewSYSQ: function() {
        var length = $(ProbForm.domIdSel + ' #length').val().trim();
        var blockId = this.state.editContext.blockId;

        if(length && /^\d+$/.test(length)) {
            length = parseInt(length);
            
            this.state.task.blocks[blockId].problems.push({
                id: this.state.task.blocks[blockId].problems.length,
                type: SYSQ.typeId,
                squares: SQ.makeRandomFigure(length),
                symmetries: SYSQ.makeSymmetryFigures(length)
            });

            this.setState({task: this.state.task});
            $(ProbForm.domIdSel).modal('hide');
        }
    },
    probFormSaveEdit: function() {
        var editContext = this.state.editContext;
        var block = this.state.task.blocks[editContext.blockId];
        var prob = block.problems[editContext.probId];

        switch(prob.type) {
            case LS.typeId:
                return this.probFormSaveEditLS();
            case EQ.typeId:
                return this.probFormSaveEditEQ();
            case EQLS.typeId:
                return this.probFormSaveEditEQLS();
            case SQ.typeId:
                return this.probFormSaveEditSQ();
            case SY.typeId:
                return this.probFormSaveEditSY();
            case SYSQ.typeId:
                return this.probFormSaveEditSYSQ();
        }
    },
    probFormSaveEditLS: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;

        var str = $(ProbForm.domIdSel + ' #letters').val().trim();
        
        if(str && str.length > 0) {
            var a = str.split(',').map(function(str){return str.trim()});
            if(a.length > 0) {
                this.state.task.blocks[blockId].problems[probId].letters = a;
                this.setState({task: this.state.task});
                $(ProbForm.domIdSel).modal('hide');
            }
        }
    },
    probFormSaveEditEQ: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;
        var equation = $(ProbForm.domIdSel + ' #equation').val().trim();

        if(EQ.isValid(equation)) {
            this.state.task.blocks[blockId].problems[probId].equation = equation;
            this.setState({task: this.state.task});
            $(ProbForm.domIdSel).modal('hide');
        }
    },
    probFormSaveEditEQLS: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;
        var subId = editContext.subId;
        var ssubId = editContext.ssubId;

        //This feature is currently not supported, so we just return.
        if(subId == null || subId == undefined) return;

        //Edit letters
        if(subId == 0) {
            this.probFormSaveEditLS();
        }
        //Edit one of the equations
        else if(subId == 1) {
            var equation = $(ProbForm.domIdSel + ' #equation').val().trim();

            if(EQ.isValid(equation)) {
                this.state.task.blocks[blockId].problems[probId].equations[ssubId] = equation;
                this.setState({task: this.state.task});
                $(ProbForm.domIdSel).modal('hide');
            }
        }
    },
    probFormSaveEditSQ: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;
        var json = $(ProbForm.domIdSel + ' #squares').val().trim();
        this.state.task.blocks[blockId].problems[probId].squares = JSON.parse(json);
        this.setState({task: this.state.task});
        $(ProbForm.domIdSel).modal('hide');
    },
    probFormSaveEditSY: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;
        var json = $(ProbForm.domIdSel + ' #symmetry').val().trim();
        this.state.task.blocks[blockId].problems[probId].symmetry = JSON.parse(json);
        this.setState({task: this.state.task});
        $(ProbForm.domIdSel).modal('hide');
    },
    probFormSaveEditSYSQ: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;
        var subId = editContext.subId;
        var ssubId = editContext.ssubId;

        //This feature is currently not supported, so we just return.
        if(subId == null || subId == undefined) return;

        //Edit squares
        if(subId == 0) {
            this.probFormSaveEditSQ();
        }
        //Edit one of the equations
        else if(subId == 1) {
            var symmetry = $(ProbForm.domIdSel + ' #symmetry').val().trim();
            this.state.task.blocks[blockId].problems[probId].symmetries[ssubId] = JSON.parse(symmetry);
            this.setState({task: this.state.task});
            $(ProbForm.domIdSel).modal('hide');
            
        }
    },
    onTaskSave: function() {
        if(this.validateTask()) {
            $.ajax({
                type: 'POST',
                url: taskSaveUrl,
                contentType: 'application/json',
                data: {
                    name: this.refs.taskName.refs.input.getDOMNode().value.trim(),
                    task: JSON.stringify(this.state.task),
                    maxScore: TSK.getMaxScore(this.state.task)
                },
                success: function(data, textStatus, jqXHR) {
                    // console.log('Ajax save success', textStatus, data);
                    console.log(data);
                    // window.location.href = taskIndexUrl;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Ajax save error', textStatus, errorThrown);
                }
            });
        }
    },
    validateTask: function() {
        //Reset error message
        this.setState({error: null});

        //Validate that name is not empty
        var name = this.refs.taskName.refs.input.getDOMNode().value.trim();
        if(!name || name == '') {
            this.setState({error: 'Task name is empty'});
            return false;
        }

        //Validate that every block has problem
        for(var i = 0; i < this.state.task.blocks.length; i++) {
            if(this.state.task.blocks[i].problems.length == 0) {
                this.setState({error: 'Block ' + (i + 1) + ' is empty'});
                return false;
            }
        }

        return true;
    },
    onInstAdd: function() {
        console.log('onInstAdd');
        this.setState({editContext:{}});
        $(InstForm.domIdSel).modal('show');
    },
    onInstEdit: function(instId) {
        console.log('onInstEdit', instId);
        this.setState({editContext:{inst: this.state.task.instructs[instId]}});
        $(InstForm.domIdSel).modal('show');
    },
    onInstDel: function(instId) {
        //Remove the inst entry
        this.state.task.instructs.splice(instId, 1);
        
        //Find the entry in task.struct to remove
        var i = 0;
        for(; i < this.state.task.struct.length; i++)
            if(this.state.task.struct[i].id == instId && this.state.task.struct[i].type == 'inst')
                break;

        //Remove an entry from task.struct
        this.state.task.struct.splice(i, 1);

        //Adjust inst id of remaining instruct in struct
        for(var i = 0; i < this.state.task.struct.length; i++)
            if(this.state.task.struct[i].type == 'inst' && this.state.task.struct[i].id > instId)
                this.state.task.struct[i].id--;

        this.setState({task: this.state.task});
    },
    onInstFormSave: function() {
        var text = this.refs.instForm.refs.body.refs.text.getDOMNode().value.trim();
        var next = this.refs.instForm.refs.body.refs.next.getDOMNode().value.trim();

        if(text == '' || next == '') return;

        //Editing existing instruction
        if(this.state.editContext.inst) {
            this.state.editContext.inst.text = text;
            this.state.editContext.inst.next = next;
            this.setState({task: this.state.task});
        }
        //Adding new instruction
        else {
            var inst = {text: text, next: next}
            var instId = this.state.task.instructs.length;

            this.state.task.instructs.push(inst);
            this.state.task.struct.push({type: 'inst', id: instId});
            this.setState({task: this.state.task});
        }

        $(InstForm.domIdSel).modal('hide');
    },
    render: function() {
        return (
            <div>
                {/*<CreateTask.TaskNameInput ref="taskName"/>*/}
                {/*<TaskObList task={this.state.task} onAddProbClick={this.onAddProbClick} onProbEdit={this.onProbEdit} onProbDel={this.onProbDel} onBlockDel={this.onBlockDel} onInstEdit={this.onInstEdit} onInstDel={this.onInstDel}/>*/}
                {/*<CreateTask.Buttons onBlockAdd={this.onBlockAdd} onInstAdd={this.onInstAdd}/>*/}
                {/*<CreateTask.Error error={this.state.error}/>*/}
                {/*<CreateTask.SaveRow onTaskSave={this.onTaskSave}/>*/}
                <ProbForm editContext={this.state.editContext} onSaveClick={this.onProbFormSave}/>
                {/*<InstForm ref="instForm" editContext={this.state.editContext} onSaveClick={this.onInstFormSave}/>*/}
            </div>
        );
    }
    // render: function() {
    //     return (
    //         <div>
    //             <ProbForm editContext={this.state.editContext} onSaveClick={this.onProbFormSave}/>
    //         </div>
    //     );
    // }
});

CreateTask.TaskNameInput = React.createClass({
    render: function() {
        return (
            <form>
                <div className="form-group">
                    <label htmlFor="taskName">Task Name</label>
                    <input ref="input" id="taskName" className="form-control"/>
                </div>
            </form>
        )
    }
});

CreateTask.Error = React.createClass({
    propTypes: {
        error: React.PropTypes.string
    },
    render: function() {
        if(!this.props.error || this.props.error == '')
            return null;
        return <div className="alert alert-danger" role="alert">{this.props.error}</div>
    }
});

CreateTask.Buttons = React.createClass({
    propTypes: {
        onBlockAdd: React.PropTypes.func.isRequired,
        onInstAdd: React.PropTypes.func.isRequired
    },
    onAddTaskBlock: function() {
        this.props.onBlockAdd('task');
    },
    onAddPracBlock: function() {
        this.props.onBlockAdd('prac');
    },
    render: function() {
        return (
            <div style={{marginBottom:10}}>
                <button className="btn btn-default" onClick={this.onAddTaskBlock}>New Task Block</button> <button className="btn btn-default" onClick={this.onAddPracBlock}>New Practice Block</button> <button className="btn btn-default" onClick={this.props.onInstAdd}>New Instruction</button>
            </div>
        );
    }
});

CreateTask.SaveRow = React.createClass({
    propTypes: {
        onTaskSave: React.PropTypes.func.isRequired
    },
    render: function() {
        return (
            <div style={{marginTop:10}}>
                <button className="btn btn-default" onClick={this.props.onTaskSave}>Finish</button>
            </div>
        )
    }
});

var TaskObList = React.createClass({
    propTypes: {
        task: React.PropTypes.object.isRequired,
        onAddProbClick: React.PropTypes.func.isRequired,
        onProbEdit: React.PropTypes.func.isRequired,
        onProbDel: React.PropTypes.func.isRequired,
        onBlockDel: React.PropTypes.func.isRequired,
        onInstEdit: React.PropTypes.func.isRequired,
        onInstDel: React.PropTypes.func.isRequired,
        mode: React.PropTypes.string
    },
    getDefaultProps: function() {
        return {
            mode: 'edit'
        };
    },
    render: function() {
        var comps = this.props.task.struct.map(function(desc, i) {
            if(desc.type === 'inst')
                return <TaskObList.Inst key={i} instId={desc.id} inst={this.props.task.instructs[desc.id]} mode={this.props.mode} onInstEdit={this.props.onInstEdit} onInstDel={this.props.onInstDel}/>
            else if(desc.type === 'block')
                return <Block key={i} blockId={desc.id} block={this.props.task.blocks[desc.id]} mode={this.props.mode} onAddProbClick={this.props.onAddProbClick} onProbEdit={this.props.onProbEdit} onProbDel={this.props.onProbDel} onBlockDel={this.props.onBlockDel}/>
        }.bind(this));

        return <div>{comps}</div>
    }
});

TaskObList.Inst = React.createClass({
    propTypes: {
        instId: React.PropTypes.number.isRequired,
        inst: React.PropTypes.object.isRequired,
        onInstEdit: React.PropTypes.func.isRequired,
        onInstDel: React.PropTypes.func.isRequired,
        mode: React.PropTypes.string.isRequired
    },
    onInstEdit: function() {
        this.props.onInstEdit(this.props.instId);
    },
    onInstDel: function() {
        this.props.onInstDel(this.props.instId);
    },
    render: function() {
        return (
            <div className="panel panel-default">
                <div className="panel-heading">
                    <table border="0" cellSpacing="0" cellPadding="0" width="100%">
                        <tr>
                            <td>
                                <h2 className="panel-title">Instruction</h2>
                            </td>
                            {
                                this.props.mode === 'edit' ?
                                    <td style={{width:35}}>
                                        <button type="button" className="close" aria-label="Close" onClick={this.onInstDel}>
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </td>
                                :
                                null
                            }
                        </tr>
                    </table>
                </div>
                <div className="panel-body" onClick={this.onInstEdit}>
                    <span dangerouslySetInnerHTML={{__html:marked(this.props.inst.text)}}/> <kbd>{this.props.inst.next}</kbd>
                </div>
            </div>
        )
    }
});

var Block = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        block: React.PropTypes.object.isRequired,
        onAddProbClick: React.PropTypes.func.isRequired,
        onProbEdit: React.PropTypes.func.isRequired,
        onProbDel: React.PropTypes.func.isRequired,
        mode: React.PropTypes.string.isRequired
    },
    onProbEdit: function(blockId, probId, subId, ssubId) {
        this.props.onProbEdit(this.props.blockId, probId, subId, ssubId);
    },
    onBlockDel: function() {
        this.props.onBlockDel(this.props.blockId);

    },
    render: function() {
        return (
            <div className="panel panel-default">
                <Block.Heading blockId={this.props.blockId} block={this.props.block} onBlockDel={this.onBlockDel} mode={this.props.mode}/>
                <Block.Body blockId={this.props.blockId} block={this.props.block} onProbEdit={this.onProbEdit} onProbDel={this.props.onProbDel}/>
                {this.props.mode === 'edit' ? <Block.Footer blockId={this.props.blockId} onAddProbClick={this.props.onAddProbClick}/> : null }
            </div>
        )
    }
});


Block.Heading = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        block: React.PropTypes.object.isRequired,
        mode: React.PropTypes.string.isRequired,
        onBlockDel: React.PropTypes.func
    },
    render: function() {
        return (
            <div className="panel-heading">
                <table border="0" cellPadding="0" cellSpacing="0" width="100%">
                    <tr>
                        <td>
                            <h2 className="panel-title">Block {this.props.blockId + 1} {this.props.block.practice ? '(Practice)' : '(Non-Practice)'}</h2>
                        </td>
                        <td>
                        {
                            this.props.mode === 'edit' ?
                                <button type="button" className="close" aria-label="Close" onClick={this.props.onBlockDel}><span aria-hidden="true">&times;</span></button> : null
                        }
                        </td>
                    </tr>
                </table>
            </div>
        )
    }
});

Block.Body = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        block: React.PropTypes.object.isRequired,
        onProbEdit: React.PropTypes.func.isRequired,
        onProbDel: React.PropTypes.func.isRequired
    },
    render: function() {
        var res = this.props.block.problems.length > 0 ?
            <Block.Table blockId={this.props.blockId} block={this.props.block} onProbEdit={this.props.onProbEdit} onProbDel={this.props.onProbDel} /> :
            <div className="panel-body">There is currently no problem in this block.</div>
        return res;
    }
});

Block.Table = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        block: React.PropTypes.object.isRequired,
        onProbEdit: React.PropTypes.func.isRequired,
        onProbDel: React.PropTypes.func.isRequired
    },
    render: function() {
        return (
            <table className="table">
                <tbody>
                    {this.props.block.problems.map(function(p, i){
                        return <Block.Table.Row key={i} blockId={this.props.blockId} problem={p} onProbEdit={this.props.onProbEdit} onProbDel={this.props.onProbDel}/>
                    }.bind(this))}
                </tbody>
            </table>
        )
    }
});

Block.Table.Row = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        problem: React.PropTypes.object.isRequired,
        onProbEdit: React.PropTypes.func.isRequired,
        onProbDel: React.PropTypes.func.isRequired
    },
    onProbEdit: function(blockId, probId, subId, ssubId) {
        this.props.onProbEdit(null, this.props.problem.id, subId, ssubId);
    },
    onProbDel: function() {
        this.props.onProbDel(this.props.blockId, this.props.problem.id);
    },
    render: function() {
        return (
            <tr>
                <td style={{width:50}}>{this.props.problem.id + 1}</td>
                <td style={{width:140}}>{this.getProblemTypeName(this.props.problem)}</td>
                <td>{this.getProblemWidget()}</td>
                <td>
                    <button type="button" className="close pull-right" aria-label="Close" onClick={this.onProbDel}><span aria-hidden="true">&times;</span></button>
                </td>
            </tr>
        )
    },
    getProblemTypeName: function(problem) {
        switch(this.props.problem.type) {
            case EQ.typeId: return EQ.typeLabel
            case LS.typeId: return LS.typeLabel
            case EQLS.typeId: return EQLS.typeLabel
            case SQ.typeId: return SQ.typeLabel
            case SY.typeId: return SY.typeLabel
            case SYSQ.typeId: return SYSQ.typeLabel
        }
    },
    getProblemWidget: function() {
        switch(this.props.problem.type) {
            case EQ.typeId:
                return <Block.Table.Row.MathWidget onProbEdit={this.onProbEdit} equation={this.props.problem.equation} />
            case LS.typeId:
                return <Block.Table.Row.LettersWidget onProbEdit={this.onProbEdit} letters={this.props.problem.letters} />
            case EQLS.typeId:
                return <Block.Table.Row.MathLetterWidget onProbEdit={this.onProbEdit} equations={this.props.problem.equations} letters={this.props.problem.letters}/>
            case SQ.typeId:
                return <Block.Table.Row.SquaresWidget squares={this.props.problem.squares} onProbEdit={this.onProbEdit}/>
            case SY.typeId:
                return <Block.Table.Row.SymmetryWidget symmetry={this.props.problem.symmetry} onProbEdit={this.onProbEdit}/>
            case SYSQ.typeId:
                return <Block.Table.Row.SymmetrySquaresWidget squares={this.props.problem.squares} symmetries={this.props.problem.symmetries} onProbEdit={this.onProbEdit}/>
        }
    }
});

Block.Table.Row.MathWidget = React.createClass({
    propTypes: {
        equation: React.PropTypes.string.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        return <span className="inline-item" style={{cursor:'pointer'}} onClick={this.props.onProbEdit.bind(null, null, null, null, null)}>{this.props.equation}, {EQ.getAnswer(this.props.equation).toString()}</span>
    }
});

Block.Table.Row.LettersWidget = React.createClass({
    propTypes: {
        letters: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        var str = this.props.letters.toString().replace(/,/g, ', ');
        return <span className="inline-item" style={{cursor:'pointer'}} onClick={this.props.onProbEdit.bind(null, null, null, null, null)}>{str}</span>
    }
});

Block.Table.Row.MathLetterWidget = React.createClass({
    propTypes: {
        equations: React.PropTypes.array.isRequired,
        letters: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        var lc = <Block.Table.Row.LettersWidget letters={this.props.letters} onProbEdit={this.props.onProbEdit.bind(null, null, null, 0, null)}/>

        var ec = this.props.equations.map(function(equation, i) {
            return <Block.Table.Row.MathWidget key={i} equation={equation} onProbEdit={this.props.onProbEdit.bind(null, null, null, 1, i)}/>
        }.bind(this));

        return (
            <div>
                {lc}{ec}
            </div>
        )
    }
});

Block.Table.Row.SquaresWidget = React.createClass({
    propTypes: {
        squares: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        return <Block.Table.Row.SquaresWidget.Figure squares={this.props.squares} onProbEdit={this.props.onProbEdit}/>
    }
});

Block.Table.Row.SquaresWidget.Figure = React.createClass({
    propTypes: {
        squares: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    onClick: function() {
        this.props.onProbEdit(null, null, null, null);
    },
    render: function() {
        var text = this.props.squares.map(function(cell, i){
            return {loc: cell, text: i + 1};
        });

        return (
            <div style={{display:'inline-block', width:100, marginRight:15}}>
                <BoxSequence.Slide.Figure class="row-figure" rows={4} cols={4} cellText={text} onCellClick={this.onClick}/>
            </div>
        );
    }
});

Block.Table.Row.SymmetryWidget = React.createClass({
    propTypes: {
        symmetry: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        return <Block.Table.Row.SymmetryWidget.Figure symmetry={this.props.symmetry} onProbEdit={this.props.onProbEdit}/>
    }
});

Block.Table.Row.SymmetryWidget.Figure = React.createClass({
    propTypes: {
        symmetry: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    onClick: function() {
        this.props.onProbEdit(null, null, null, null);
    },
    render: function() {
        return (
            <div style={{display:'inline-block', width:100, marginRight:15, marginBottom:8, marginTop:8}}>
                <BoxSequence.Slide.Figure class="row-figure" rows={8} cols={8} colored={this.props.symmetry} borderColor={'#000'} hiColor={'#000'} onCellClick={this.onClick}/>
                <div style={{textAlign:'center', width:'100%', fontSize:12}}>{SY.isSymmetric(this.props.symmetry) ? 'Symmetric' : 'Asymmetric'}</div>
            </div>
        );
    }
});

Block.Table.Row.SymmetrySquaresWidget = React.createClass({
    propTypes: {
        squares: React.PropTypes.array.isRequired,
        symmetries: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        var squares = <Block.Table.Row.SquaresWidget.Figure squares={this.props.squares} onProbEdit={this.props.onProbEdit.bind(null, null, null, 0, null)}/>

        var symmetries = this.props.symmetries.map(function(symmetry, i) {
            return <Block.Table.Row.SymmetryWidget.Figure key={i} symmetry={symmetry} onProbEdit={this.props.onProbEdit.bind(null, null, null, 1, i)}/>
        }.bind(this));

        return (
            <div>
                <div style={{display:'inline-block'}}>{squares}<div dangerouslySetInnerHTML={{__html: '&nbsp;'}}/></div>
                {symmetries}
            </div>
        );
    }
});

Block.Footer = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        onAddProbClick: React.PropTypes.func.isRequired
    },
    onAddProbClick: function() {
        this.props.onAddProbClick(this.props.blockId);
    },
    render: function() {
        return (
            <div className="panel-footer">
                <button className="btn btn-default" onClick={this.onAddProbClick}>Add Problem</button>
            </div>
        )
    }
});


var ProbForm = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired,
        onSaveClick: React.PropTypes.func.isRequired
    },
    statics: {
        domId: 'probForm',
        domIdSel: '#probForm'
    },
    render: function() {
        return (
            <div className="modal fade" id={ProbForm.domId} tabIndex="-1" role="dialog" labelledby="myModalLabel">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <ProbForm.Header editContext={this.props.editContext}/>
                        <ProbForm.Body editContext={this.props.editContext}/>
                        <ProbForm.Footer onSaveClick={this.props.onSaveClick} />
                    </div>
                </div>
            </div>
        )
    }
});

ProbForm.Header = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    render: function() {
        return (
            <div className="modal-header">
                <button type="button" className="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {
                    this.props.editContext.mode === CreateTask.editMode.add ? 
                        <h4 className="modal-title" id="myModalLabel">New Problem</h4> :
                        <h4 className="modal-title" id="myModalLabel">Edit Problem</h4>
                }
            </div>
        )
    }
});

ProbForm.Body = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        return {
            type: LS.typeId
        }
    },
    onTypeChange: function(event) {
        this.setState({type: event.target.value});
    },
    render: function() {
        return (
            <div className="modal-body">
                <form>
                    {
                        this.props.editContext.mode === CreateTask.editMode.edit ? null :
                        <div className="form-group">
                            <label htmlFor="probType">Type</label>
                            <select className="form-control" id="probType" value={this.state.type} onChange={this.onTypeChange}>
                                <option value={LS.typeId}>Letter Sequence</option>
                                <option value={EQ.typeId}>Math Equation</option>
                                <option value={EQLS.typeId}>Math and Letters</option>
                                <option value={SQ.typeId}>Square Sequence</option>
                                <option value={SY.typeId}>Symmetry</option>
                                <option value={SYSQ.typeId}>Symmetry and Squares</option>
                            </select>
                        </div>
                    }

                    <ProbForm.SpecialPane editContext={this.props.editContext} type={this.props.editContext.prob ? this.props.editContext.prob.type : this.state.type}/>
                    {/*<ProbForm.SpecialPane editContext={this.props.editContext} type={SQ.typeId}/>*/}
                </form>
            </div>
        )
    }
});

ProbForm.SpecialPane = React.createClass({
    propTypes: {
        type: React.PropTypes.string.isRequired,
        editContext: React.PropTypes.object.isRequired
    },
    render: function() {
        switch(this.props.type) {
            case LS.typeId:
                return <ProbForm.LSPane editContext={this.props.editContext}/>
            case EQ.typeId:
                return <ProbForm.EQPane editContext={this.props.editContext}/>
            case EQLS.typeId:
                return <ProbForm.EQLSPane/>
            case SQ.typeId:
                return <ProbForm.SQPane editContext={this.props.editContext}/>
            case SY.typeId:
                return <ProbForm.SYPane editContext={this.props.editContext}/>
            case SYSQ.typeId:
                return <ProbForm.SYSQPane/>
            default:
                return null;
        }
    }
});

ProbForm.LSPane = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        return {val: this.props.editContext.prob ? this.props.editContext.prob.letters : ''};
    },
    componentWillReceiveProps: function(nextProps) {
        this.setState({val: nextProps.editContext.prob ? nextProps.editContext.prob.letters : ''});
    },
    onChange: function(event) {
        this.setState({val: event.target.value});
    },
    render: function() {
        return (
            <div>
                <div className="form-group">
                    <label htmlFor="letters">Letters</label>
                    <input type="text" className="form-control" id="letters" value={this.state.val} onChange={this.onChange}/>
                    <div>A sequence of letters. For example: <code>X,Y,Z</code></div>
                </div>
            </div>
        )
    }
});

ProbForm.EQPane = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        return {val: this.props.editContext.prob ? this.props.editContext.prob.equation : ''};
    },
    componentWillReceiveProps: function(nextProps) {
        this.setState({val: nextProps.editContext.prob ? nextProps.editContext.prob.equation : ''});
    },
    onChange: function(event) {
        this.setState({val: event.target.value});
    },
    render: function() {
        return (
            <div>
                <div className="form-group">
                    <label htmlFor="equation">Equation</label>
                    <input className="form-control" id="equation" value={this.state.val} onChange={this.onChange}/>
                    <div>An equation, such as <code>(2*2)+2=2</code></div>
                </div>
            </div>
        )
    }
});

ProbForm.EQLSPane = React.createClass({
    render: function() {
        return (
            <div>
                <div className="form-group">
                    <label htmlFor="equations">Equations</label>
                    <textarea className="form-control" id="equations"></textarea>
                    <div>Equations separated by commas <code>,</code>. For example: <code>(2*2)+2=2, (4/2)-1=1</code></div>
                </div>
                <div className="form-group">
                    <label htmlFor="letters">Letters</label>
                    <input type="text" className="form-control" id="letters" />
                    <div>A sequence of letters. For example: <code>X,Y,Z</code></div>
                </div>
            </div>
        )
    }
});

ProbForm.SQPane = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        var slots = new Array(4 * 4);
        
        if(this.props.editContext.prob)
            for(var i = 0; i < this.props.editContext.prob.squares.length; i++)
                slots[i] = this.props.editContext.prob.squares[i];

        return {slots: slots};
    },
    componentWillReceiveProps: function(nextProps) {
        var slots = new Array(4 * 4);

        if(nextProps.editContext.prob)
            for(var i = 0; i < nextProps.editContext.prob.squares.length; i++)
                slots[i] = nextProps.editContext.prob.squares[i];

        this.setState({slots: slots});
    },
    onCellClick: function(cell) {
        var index = PointCollection.indexOf(this.state.slots, cell);
        var slots = this.state.slots;

        if(index == -1) {
            for(var i = 0; i < slots.length; i++) {
                if(!slots[i]) {
                    slots[i] = cell;
                    this.setState({slots: slots});
                    break;
                }
            }
        }
        else {
            slots[index] = null;
            this.setState({slots: slots});
        }
    },
    render: function() {
        var slots = this.state.slots,
            cellText = [],
            trunc = [];

        for(var i = 0; i < slots.length; i++)
            cellText.push({loc: slots[i], text: i + 1});

        for(var i = 0; i < slots.length; i++)
            if(slots[i])
                trunc.push(slots[i]);

        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-xs-6 col-xs-offset-3">
                        <input type="hidden" id="squares" value={JSON.stringify(trunc)}/>
                        <BoxSequence.Slide.Figure rows={4} cols={4} cellText={cellText} onCellClick={this.onCellClick}/>
                    </div>
                </div>
            </div>
        )
    }
});

ProbForm.SYPane = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        if (this.props.editContext.subType == null)
            var colored = this.props.editContext.prob ? this.props.editContext.prob.symmetry : [];
        else if (this.props.editContext.subType == 1)
            var colored = this.props.editContext.prob ? this.props.editContext.prob.symmetries[this.props.editContext.key] : [];
        return {colored: colored};
    },
    componentWillReceiveProps: function(nextProps) {
        if (nextProps.editContext.subType == null)
            var colored = nextProps.editContext.prob ? nextProps.editContext.prob.symmetry : [];
        else if (nextProps.editContext.subType == 1)
            var colored = nextProps.editContext.prob ? nextProps.editContext.prob.symmetries[nextProps.editContext.key] : [];

        this.setState({colored: colored});
    },
    onCellClick: function(cell) {
        var i = PointCollection.indexOf(this.state.colored, cell);
        if(i == -1)
            this.state.colored.push(cell);
        else
            this.state.colored.splice(i, 1);
        this.setState({colored: this.state.colored});
    },
    makeSymmetric: function() {
        this.setState({colored: SY.makeSymmetricFigure()});
    },
    makeAsymetric: function() {
        switch(Math.floor(Math.random() * 2)) {
            case 0:
                this.setState({colored: SY.makeAsymmetricFigure()});
            case 1:
                this.setState({colored: SY.makeRandomFigure()});
        }
    },
    render: function() {
        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-xs-6 col-xs-offset-3">
                        <input type="hidden" id="symmetry" value={JSON.stringify(this.state.colored)}/>
                        <BoxSequence.Slide.Figure rows={8} cols={8} colored={this.state.colored} borderColor={'#000'} hiColor={'#000'} onCellClick={this.onCellClick}/>
                    </div>
                </div>
                <div className="row"><div className="col-xs-12"><hr/></div></div>
                <div className="row">
                    <div className="col-xs-12" style={{textAlign:'center'}}>
                        <button type="button" className="btn btn-default" onClick={this.makeSymmetric}>Generate Symmetric</button> <button type="button" className="btn btn-default" onClick={this.makeAsymetric}>Generate Asymmetric</button>
                    </div>
                </div>
            </div>
        )
    }
});

ProbForm.SYSQPane = React.createClass({
    render: function() {
        return (
            <div className="form-group">
                <label htmlFor="length">Number of Subproblems</label>
                <input className="form-control" id="length" defaultValue={3}/>
                <div>Length of square sequence and must be an integer</div>
            </div>
        )
    }
});

ProbForm.Footer = React.createClass({
    propTypes: {
        onSaveClick: React.PropTypes.func.isRequired
    },
    render: function() {
        return (
            <div className="modal-footer">
                <button type="button" className="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" className="btn btn-primary" onClick={this.props.onSaveClick}>Ok</button>
            </div>
        )
    }
});

var InstForm = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired,
        onSaveClick: React.PropTypes.func.isRequired
    },
    statics: {
        domId: 'instForm',
        domIdSel: '#instForm'
    },
    render: function() {
        return (
            <div className="modal fade" id={InstForm.domId} tabIndex="-1" role="dialog">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <InstForm.Header editContext={this.props.editContext}/>
                        <InstForm.Body editContext={this.props.editContext} ref="body"/>
                        <InstForm.Footer onSaveClick={this.props.onSaveClick} />
                    </div>
                </div>
            </div>
        )
    }
});

InstForm.Header = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    render: function() {
        return (
            <div className="modal-header">
                <button type="button" className="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {
                    this.props.editContext.mode === CreateTask.editMode.add ? 
                        <h4 className="modal-title" id="myModalLabel">New Problem</h4> :
                        <h4 className="modal-title" id="myModalLabel">Edit Problem</h4>
                }
            </div>
        )
    }
});

InstForm.Body = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        return {
            text: this.props.editContext.inst ? this.props.editContext.inst.text : '',
            next: this.props.editContext.inst ? this.props.editContext.inst.next : ''
        }
    },
    componentWillReceiveProps: function(nextProps) {
        if(nextProps.editContext) {
            this.setState({
                text: nextProps.editContext.inst ? nextProps.editContext.inst.text : '',
                next: nextProps.editContext.inst ? nextProps.editContext.inst.next : ''
            });
        }
    },
    onChange: function(event) {
        if(event.target.id == 'instText')
            this.setState({text: event.target.value});
        else if(event.target.id == 'instNext')
            this.setState({next: event.target.value});
    },
    render: function() {
        return (
            <div className="modal-body">
                <form>
                    <div className="form-group">
                        <label htmlFor="instText">Text</label>
                        <textarea className="form-control" id="instText" ref="text" rows="6" value={this.state.text} onChange={this.onChange}/>
                    </div>
                    <div className="form-group">
                        <label htmlFor="instNext">Next Label</label>
                        <input className="form-control" id="instNext" ref="next" maxLength="30" value={this.state.next} onChange={this.onChange}/>
                    </div>
                </form>
            </div>
        )
    }
});

InstForm.Footer = React.createClass({
    propTypes: {
        onSaveClick: React.PropTypes.func.isRequired
    },
    render: function() {
        return (
            <div className="modal-footer">
                <button type="button" className="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" className="btn btn-primary" onClick={this.props.onSaveClick}>Ok</button>
            </div>
        )
    }
});
