/**
 * Dependencies
 *   - sq.js
 *   - sy.js
 */

/**
 * A symmetry square sequence recall problem.
 *
 * @prop problem    object   The problem with format {type:string, sequence:array<point>, syms:array<figure>}
 * @prop tra        object   Task Running Accuracy.
 * @prop traLow     number   Lower threshold for TRA. If the TRA is below this, the task is stopped. Default 90%
 * @prop feedback   boolean
 * @prop onComplete callback
 */
var SymmetryBoxSequence = React.createClass({
    propTypes: {
        problem: React.PropTypes.object.isRequired,
        questionId: React.PropTypes.number.isRequired,
        tra: React.PropTypes.object,
        traLow: React.PropTypes.number,
        feedback: React.PropTypes.bool,
        timeLimit: React.PropTypes.number.isRequired,
        onComplete: React.PropTypes.func.isRequired
    },
    getDefaultProps: function() {
        return {
            traLow: 0.9
        };
    },
    getInitialState: function() {
        return {stage: 0};
    },
    advance: function() {
        if(this.state.stage < 1 || (this.state.stage == 1 && this.props.feedback))
            this.setState({stage: this.state.stage + 1});
        else if(this.props.onComplete)
            this.props.onComplete({probId: this.props.problem.id, questionId: this.props.questionId, squares: {response: this.recallRes.selects, time: this.recallRes.endTime - this.recallRes.startTime}, symmetries: this.symRes}, this.tra);
    },
    /**
     * When sequence component is completed.
     * @param symRes array<object> An array of object of user responses to symmetry problems.
     */
    onSequenceComplete: function(symRes, tra) {
        this.symRes = symRes;
        this.tra = tra;
        this.advance();
    },
    /**
     * Handles the event after recall has finished.
     * @prarm selects   array<point>  User selection sequences, each of which is a location [x, y]
     * @param startTime integer       See getTime() of date object.
     * @param endTime   integer       See getTime() of date object.
     */
    onSubmitRecall: function(selects, startTime, endTime) {
        //Save the recall result
        this.recallRes = {selects: selects, startTime: startTime, endTime: endTime};
        this.advance();
    },
    render: function() {
        switch(this.state.stage) {
            case 0:
                return (
                    <SymmetryBoxSequence.Sequence problem={this.props.problem} tra={this.props.tra} traLow={this.props.traLow} timeLimit={this.props.timeLimit} onComplete={this.onSequenceComplete} />
                );
            case 1:
                return <BoxSequence.Recall sequence={this.props.problem.squares} onComplete={this.onSubmitRecall} />
            case 2:
                return (
                    <SymmetryBoxSequence.Feedback problem={this.props.problem} symRes={this.symRes}
                        recallRes={this.recallRes} onComplete={this.advance} />
                );
        }   
    }
});

/**
 * Displays a sequence of alternating symmetry and square locations.
 *
 * @prop problem    object   An object with fields {type, squares, symmetries}.
 * @prop tra        object
 * @prop traLow     number   Lower threshold for TRA. If the TRA is below this, the task is stopped. Default 90%
 * @prop onComplete callback
 */
SymmetryBoxSequence.Sequence = React.createClass({
    getInitialState: function() {
        this.symRes = [];
        return {
            count: 0,
            showLowTra: false,
            showedLowTra: false,
            showTimeout: false,
            showedTimeout: false,
        };
    },
    onBoxSlideComplete: function() {
        //clearInterval(this.timer);
        this.advance();
    },
    /**
     * @param res object Response {res: boolean, startTime: integer, endTime: integer}
     */
    onSymmetrySubmit: function(res, tra) {
        this.symRes.push(res);
        this.tra = tra;
        //console.log('response', res.response);

        if (res.response == null && this.state.showedTimeout == false)
            return this.setState({showTimeout:true, showedTimeout:true});

        if(this.props.tra && tra.total != 0 && tra.total > 2 &&
            !this.state.showedLowTra &&
            res.response !== SY.isSymmetric(this.props.problem.symmetries[Math.floor(this.state.count / 2)]) &&
            tra.correct / tra.total < this.props.traLow)
            return this.setState({showLowTra:true, showedLowTra: true});

        this.advance();
    },
    onLowTraComplete: function() {
        this.state.showLowTra = false;
        this.advance();
    },
    onTimeoutComplete: function() {
        this.state.showTimeout = false;
        this.state.showedTimeout = false;
        this.advance();
    },
    advance: function() {
        if(this.state.count < (this.props.problem.squares.length * 2 - 1)) {
            this.setState({count: this.state.count + 1});
        }
        else
            this.props.onComplete(this.symRes, this.tra);
    },
    render: function() {
        if(this.state.showLowTra)
            return <LowTra type={'symmetry'} traLow={this.props.traLow} onComplete={this.onLowTraComplete} />

        if(this.state.showTimeout)
            return <Timeout type={'symmetry'} onComplete={this.onTimeoutComplete}/>

        if(this.state.count % 2 == 0) {
            return (
                <SymmetryTest key={this.state.count}
                    colored={this.props.problem.symmetries[Math.floor(this.state.count / 2)]}
                    tra={this.props.tra}
                    timeLimit={this.props.timeLimit}
                    onComplete={this.onSymmetrySubmit} />
            );
        }
        else {
            return (
                <BoxSequence.Slide key={this.state.count}
                colored={[this.props.problem.squares[Math.floor(this.state.count / 2)]]}
                onComplete={this.onBoxSlideComplete} />
            );
        }
    }
});

/**
 * @prop problem    The original problem.
 * @prop symRes     Symmetry problem responses from the user.
 * @prop recallRes  Sequence recall response from the user.
 * @prop onComplete callback
 */
SymmetryBoxSequence.Feedback = React.createClass({
    getSymmetryCorrectCount: function() {
        var res = 0;
        for(var i = 0; i < this.props.problem.symmetries.length; i++) {
            if(this.props.symRes[i].response == SY.isSymmetric(this.props.problem.symmetries[i]))
                res++;
        }
        return res;
    },
    getBoxesCorrectCount: function() {
        var res = 0;

        for(var i = 0; i < this.props.problem.squares.length; i++) {
            if(this.props.recallRes.selects[i] && 
                this.props.recallRes.selects[i][0] == this.props.problem.squares[i][0] &&
                this.props.recallRes.selects[i][1] == this.props.problem.squares[i][1])
                res++;
        }
        return res;
    },
    complete: function() {
        this.props.onComplete();
    },
    render: function() {
        var mc = this.getSymmetryCorrectCount();
        var lc = this.getBoxesCorrectCount();
        var ml = this.props.problem.symmetries.length; //Math problem length
        var ll = this.props.problem.squares.length;  //Letters length

        return (
            <div style={{fontSize:22}}>
                <div className="row" style={{marginBottom:20}}>
                    <div className="col-xs-12">
                        {translate('You recalled')} {lc} {translate('out of')} {ll} {translate('squares correctly')}.
                    </div>
                </div>
                <div className="row" style={{marginBottom:25}}>
                    <div className="col-xs-12">
                        {translate('You answered')} {mc} {translate('out of')} {ml} {translate('symmetry figure questions correctly')}.
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-12">
                        <button className="btn btn-default" onClick={this.complete}>{translate('Continue')}</button>
                    </div>
                </div>
            </div>
        );
    }
});