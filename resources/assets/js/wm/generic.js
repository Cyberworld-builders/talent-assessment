var Instruction = React.createClass({
    getDefaultProps: function() {
        return {
            nextBtnLabel: 'Continue'
        };
    },
    render: function() {
        var style = this.props.style ? this.props.style : {};
        if(style) {
            style.marginBottom = 20;
        }

        return (
            <div>
                <div className="row" style={style}>
                    <div className="col-xs-10 col-xs-offset-1 col-lg-8 col-lg-offset-2" 
                    style={{
                        lineHeight: '160%',
                        textAlign:'justify', fontSize:20,
                        padding: '20px 25px',
                        backgroundColor: '#fcfcfc',
                        border: '1px solid #e1e1e8',
                        borderRadius: 4
                    }}>
                        <div dangerouslySetInnerHTML={{__html: marked(this.props.text)}}/>
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-2 col-xs-offset-5">
                        <button className="btn btn-default" onClick={this.props.onComplete}>{this.props.nextBtnLabel}</button>
                    </div>
                </div>
            </div>
        )
    }
});

var Demographics = React.createClass({
    complete: function() {
        this.props.onComplete();
    },
    render: function() {
        var years=[];
        for(var i = 0; i < 100; i++)
            years.push(new Date().getFullYear() - 2 - i);

        return (
            <div>
                <div className="row" style={{marginBottom:25, fontSize:20}}>
                    <div className="col-xs-12">Before we start, tell us a little about you</div>
                </div>
                <div className="row" style={{marginBottom:25}}>
                    <div className="col-sm-6 col-sm-offset-3">
                        <form>
                            <div className="form-group">
                                <label className="control-label">Birthday</label>
                                
                                <div className="row">
                                    <div className="col-xs-4">
                                        <select className="form-control">
                                            <option value="1">Jan</option><option value="2">Feb</option><option value="3">Mar</option>
                                            <option value="4">Apr</option><option value="5">May</option><option value="6">Jun</option>
                                            <option value="7">Jul</option><option value="8">Aug</option><option value="9">Sep</option>
                                            <option value="10">Oct</option><option value="11">Nov</option><option value="12">Dec</option>
                                        </select>
                                    </div>
                                    <div className="col-xs-4">
                                        <select className="form-control">
                                            {[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31]
                                                .map(function(val){
                                                    return <option value={val}>{val}</option>
                                                })
                                            }
                                        </select>
                                    </div>
                                    <div className="col-xs-4">
                                        <select className="form-control">
                                            {years.map(function(val){return <option value={val}>{val}</option>})}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div className="form-group">
                                <label className="control-label">Gender</label>
                                <div className="row">
                                    <div className="col-xs-4 col-xs-offset-4">
                                        <select className="form-control">
                                            <option value="female">Female</option>
                                            <option value="male">Male</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-12">
                        <button className="btn btn-default" onClick={this.complete}>Continue</button>
                    </div>
                </div>
            </div>
        );
    }
});

/**
 * A set of problems.
 * @prop block      array   The set of problems to render
 * @prop tra        object  Task Running Accuracy {correct: integer, total: integer}
 * @prop randomize  boolean If the problems should be shuffled.
 * @prop practice   boolean True if this block is a practice; false if this this block is to be recorded.
 * @prop onComplete callback 
 */
var Block = React.createClass({
    getInitialState: function() {
        this.tra = this.props.tra ? this.props.tra : this.props.keepTra ? {correct:0, total:0} : null;
        this.res = [];
        
        return {
            block: this.props.randomize ? shuffle(this.props.block) : this.props.block,
            progress: 0
        };
    },
    advance: function(res, tra) {
        this.res.push(res);
        this.tra = tra;
        
        if(this.state.progress == this.state.block.length - 1) {
            this.onComplete(this.res, this.tra);
        }
        else {
            this.setState({progress: this.state.progress + 1});
        }
    },
    onComplete: function(res, tra) {
        res.sort(function(a, b){return a.probId - b.probId});
        this.props.onComplete(this.res, this.tra);
    },
    render: function() {
        var block = this.state.block;
        var progress = this.state.progress;

        switch(block[progress].type) {
            case TEXT.typeId:
                return (
                    <TextInput key={progress} probId={block[progress].id} questionId={block[progress].questionId} text={block[progress].text}
                               onComplete={this.advance} />
                );
            case LS.typeId:
                return (
                    <LetterSequence key={progress} probId={block[progress].id} questionId={block[progress].questionId} letters={block[progress].letters}
                        onComplete={this.advance} report={this.props.practice} />
                );
            case EQ.typeId:
                return (
                    <MathEq key={progress} probId={block[progress].id} questionId={block[progress].questionId} equation={block[progress].equation}
                        feedback={this.props.practice} onComplete={this.advance} />
                );
            case EQLS.typeId:
                return (
                    <MathLetter key={progress} problem={block[progress]} questionId={block[progress].questionId}
                        tra={this.tra}
                        feedback={this.props.practice}
                        timeLimit={this.props.timeLimit}
                        onComplete={this.advance} />
                );
            case SQ.typeId:
                return <BoxSequence key={progress} probId={block[progress].id} questionId={block[progress].questionId} sequence={block[progress].squares} feedback={this.props.practice} onComplete={this.advance}/>
            case SY.typeId:
                return <SymmetryTest key={progress} probId={block[progress].id} questionId={block[progress].questionId} colored={block[progress].symmetry} feedback={this.props.practice} onComplete={this.advance} />
            case SYSQ.typeId:
                return (
                    <SymmetryBoxSequence key={progress}
                        problem={block[progress]} questionId={block[progress].questionId}
                        tra={this.tra}
                        feedback={this.props.practice}
                        timeLimit={this.props.timeLimit}
                        onComplete={this.advance} />
                );
            case RS.typeId:
                return (
                    <SentenceQuestion key={progress}
                        sentence={block[progress].sentence}
                        sol={block[progress].sol}
                        feedback={this.props.practice}
                        onComplete={this.advance} />
                );
            case RSLS.typeId:
                return (
                    <SentenceLetter key={progress}
                        sentences={block[progress].sentences}
                        letters={block[progress].letters}
                        feedback={this.props.practice}
                        onComplete={this.advance} />
                );
        }
    }
});

/**
 * A set of blocks to be presented to the user.
 *
 * @prop blocks     array    An array of blocks to render.
 * @prop keepTra    boolean  If task running accuracy should be kept and displayed.
 * @prop randomize  boolean 
 * @prop onComplete callback
 */
var Assessment = React.createClass({
    propTypes: {
        blocks: React.PropTypes.array.isRequired,
        keepTra: React.PropTypes.bool,
        randomize: React.PropTypes.bool,
        onComplete: React.PropTypes.func
    },
    getInitialState: function() {
        if(this.props.keepTra)
            this.tra = {correct: 0, total: 0};
        
        this.res = [];

        return {progress: 0};
    },
    advance: function(res, tra) {
        if(this.state.progress == this.props.blocks.length - 1) {
            this.props.onComplete(this.res);
        }
        else {
            this.res.push(res);
            this.tra = tra;
            this.setState({progress: this.state.progress + 1});
        }
    },
    render: function() {
        return (
            <Block key={this.state.progress} block={this.props.blocks[this.state.progress]} tra={this.tra} onComplete={this.advance}
                randomize={this.props.randomize} practice={false} />
        );
    }
});

/**
 * Message when problem is answered too slowly.
 *
 * @prop type   string The problem type.
 * @prop traLow number The lower threshold.
 * @onComplete callback
 */
var Timeout = React.createClass({
    onComplete: function() {
        if(this.props.onComplete)
            this.props.onComplete();
    },
    render: function() {
        return (
            <div>
                <div className="row">
                    <div className="col-xs-12" style={{fontSize:25, marginTop:100}}>
                        {
                            this.props.type === 'math' ?
                                translate('Please try to solve each math problem as quickly as you can.') :
                                translate('Please try to identify each symmetric and asymmetric figure as quickly as you can.')
                        }
                    </div>
                </div>
                <div className="row" style={{marginTop:25}}>
                    <div className="col-xs-12" style={{textAlign:'center'}}>
                        <button className="btn btn-default" onClick={this.onComplete}>{translate('Got it')}</button>
                    </div>
                </div>
            </div>
        );
    }
});

/**
 * Message when the TRA is too low.
 *
 * @prop type   string The problem type.
 * @prop traLow number The lower threshold.
 * @onComplete callback
 */
var LowTra = React.createClass({
    onComplete: function() {
        if(this.props.onComplete)
            this.props.onComplete();
    },
    render: function() {
        return (
            <div>
                <div className="row">
                    <div className="col-xs-12" style={{fontSize:25, marginTop:100}}>
                        {
                            this.props.type === 'math' ? 
                                translate('Please try to solve each math problem correctly, as quickly as you can.') :
                                translate('Please try to identify each symmetric and asymmetric figure correctly, as quickly as you can.')
                        }
                    </div>
                </div>
                <div className="row" style={{marginTop:25}}>
                    <div className="col-xs-12" style={{textAlign:'center'}}>
                        <button className="btn btn-default" onClick={this.onComplete}>{translate('Got it')}</button>
                    </div>
                </div>
            </div>
        );
    }
});

var PartInfoForm = React.createClass({
    propTypes: {
        onComplete: React.PropTypes.func.isRequired
    },
    onComplete: function() {
        var workerId = this.refs.workerId.getDOMNode().value.trim();
        var qualId = this.refs.qualId.getDOMNode().value.trim();

        if(workerId && workerId != '' && qualId && qualId != '' && /^\d+$/.test(qualId))
            this.props.onComplete(workerId, qualId);
    },
    render: function() {
        return (
            <div>
                <div className="row" style={{marginBottom:25}}>
                    <div className="col-xs-4 col-xs-offset-4 col-sm-2 col-sm-offset-5 form-Group">
                        <label className="form-label">Worker Identifier</label>
                        <input type="text" ref="workerId" className="form-control" style={{textAlign:'center'}}/>
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-4 col-xs-offset-4 col-sm-2 col-sm-offset-5 form-group">
                        <label className="form-label">Validation Code</label>
                        <input type="text" ref="qualId" className="form-control" style={{textAlign:'center'}}/>
                    </div>
                </div>
                <div className="row" style={{marginTop:25}}>
                    <div className="col-xs-12" style={{textAlign:'center'}}>
                        <button type="button" className="btn btn-default" onClick={this.onComplete}>Continue</button>
                    </div>
                </div>
            </div>
        );
    }
});