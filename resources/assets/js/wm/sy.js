/**
 * Dependencies
 *   - sq.js
 */

var SymmetryTest = React.createClass({
    propTypes: {
        probId: React.PropTypes.number,             //Problem id
        questionId: React.PropTypes.number.isRequired, //Assessment question id
        colored: React.PropTypes.array.isRequired,  //Array of points that specifies which box should be color-filled.
        tra: React.PropTypes.object,                //Task running accuracy
        feedback: React.PropTypes.bool,             //If feedback should be displayed
        onComplete: React.PropTypes.func.isRequired, //Callback when this component is finished.
        timeLimit: React.PropTypes.number.isRequired,
    },
    getInitialState: function() {
        return {stage: 0};
    },
    componentDidMount: function() {
        this.startTime = new Date().getTime();
        this.timeLimit = setInterval(this.onSymmetryTimeUp, (this.props.timeLimit ? this.props.timeLimit : 8000));
    },
    onSymmetryTimeUp: function() {
        clearInterval(this.timeLimit);
        var endTime = new Date().getTime();
        // console.log('time is up: ', endTime - this.startTime);
        this.res = null;
        this.time = endTime - this.startTime;

        this.advance();
    },
    /**
     * Handles the event when user click on true or false.
     * @params res boolean The user's response
     */
    onRespond: function(res) {
        clearInterval(this.timeLimit);
        var endTime = new Date().getTime();
        //console.log('submitted early: ', endTime - this.startTime);
        this.res = res;
        this.time = endTime - this.startTime;

        this.adjustTra(res);
        this.advance();
    },
    adjustTra: function(res) {
        if(!this.props.tra)
            return;
        
        this.tra = this.props.tra;

        if(res == SY.isSymmetric(this.props.colored))
            this.tra.correct++;
        this.tra.total++;
    },
    advance: function() {
        if(this.state.stage == 0 && this.props.feedback)
            this.setState({stage: 1});
        else {
            this.onComplete();
        }
    },
    onComplete:function() {
        if (!this.tra)
            this.tra = this.props.tra;
        if(this.props.probId == undefined)
            this.props.onComplete({response: this.res, time: this.time}, this.tra);
        else
            this.props.onComplete({probId: this.props.probId, questionId:this.props.questionId, response: this.res, time: this.time}, this.tra);


    },
    render: function(){
        switch(this.state.stage) {
            case 0:
                return (
                    <div>
                        <div className="row" style={{marginBottom:25}}>
                            <div className="col-md-6 col-md-offset-3 col-xs-8 col-xs-offset-2">
                                <BoxSequence.Slide.Figure rows={8} cols={8} colored={this.props.colored} borderColor={'#000'} hiColor={'#000'} timeLimit={this.props.timeLimit} />
                            </div>
                        </div>
                        <div className="row">
                            <div className="col-xs-6">
                                <button className="btn btn-default pull-right" onClick={this.onRespond.bind(this, true)}>{translate('True')}</button>
                            </div>
                            <div className="col-xs=6">
                                <button className="btn btn-default pull-left" onClick={this.onRespond.bind(this, false)}>{translate('False')}</button>
                            </div>
                        </div>
                        {
                            this.props.tra ? <SymmetryTest.Tra tra={this.props.tra} /> : null
                        }
                    </div>
                )
            case 1:
                return (<SymmetryTest.Feedback colored={this.props.colored} res={this.res} onComplete={this.onComplete} />)
        }
    }
});

/*
 * @prop tra object Task Running Accuracy
 */
SymmetryTest.Tra = React.createClass({
    render: function() {
        return null;
        // return (
        //     <div style={{position:'fixed', bottom:20, left:0, width:'100%', textAlign:'center'}}>
        //         <b>Symmetry Accuracy</b> <br/> Correct: {this.props.tra.correct} | Incorrect: {this.props.tra.total - this.props.tra.correct} | Total: {this.props.tra.total}
        //     </div>
        // );
    }
});

/**
 * @prop colored array<point>
 * @prop res     object with the format {res: boolean, startTime: integer, endTime: integer}
 * @prop onComplete callback
 */
SymmetryTest.Feedback = React.createClass({
    propTypes: {
        colored: React.PropTypes.array.isRequired,
        res: React.PropTypes.bool.isRequired,
        onComplete: React.PropTypes.func.isRequired
    },
    onComplete: function() {
        if(this.props.onComplete)
            this.props.onComplete();
    },
    render: function() {
        return (
            <div>
                <div className="row">
                    <div className="col-xs-12" style={{fontSize:25, marginBottom:25}}>
                        {translate('Your answer is')} {SY.isSymmetric(this.props.colored) == this.props.res ? translate('correct') : translate('incorrect')}.
                    </div>
                </div>
                <div className="row">
                    <button className="btn btn-default" onClick={this.onComplete}>{translate('Continue')}</button>
                </div>
            </div>
        )
    }
});