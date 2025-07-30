/**
 * Dependencies
 *   - ls.js
 *   - eq.js
 */

/**
 * A math-letter sequence problem.
 *
 * @prop problem object required
 * @prop tra     object optional
 *   Task Running accuracy which is a running accuracy of all blocks. This object has the format
 *   {correct: integer, total: integer}, which are correct count and total count.
 * @prop feedback boolean
 * @prop onComplete callback
 */
var MathLetter = React.createClass({
    getInitialState: function() {
        return {stage: 0, tra: this.props.tra};
    },
    advance: function() {
        if(this.state.stage < 1 || (this.state.stage == 1 && this.props.feedback))
            this.setState({stage: this.state.stage + 1});
        else {
            var response = {probId: this.props.problem.id, questionId: this.props.questionId, letters: this.recallRes, equations: this.mathRes};
            this.props.onComplete(response, this.state.tra);
        }
    },
    /**
     * When MathLetter.Sequence component is completed.
     * @param mathRes array<object> An array of object of user responses to math problems.
     */
    onSequenceComplete: function(mathRes, tra) {
        this.mathRes = mathRes;
        this.state.tra = tra;
        this.advance();
    },
    /**
     * @param options array<string>  All choices (letters) presented to the user on the recall screen.
     * @prarm indexes   array<Integer> user response as an array of indexes.
     * @param startTime integer        See getTime() of date object.
     * @param endTime   integer        See getTime() of date object.
     */
    onSubmitRecall: function(options, response, time) {
        //Save the recall result
        this.recallRes = {options: options, response: response, time: time};
        this.advance();
    },
    render: function() {
        switch(this.state.stage) {
            case 0:
                return <MathLetter.Sequence problem={this.props.problem} tra={this.props.tra} timeLimit={this.props.timeLimit} onComplete={this.onSequenceComplete}/>
            case 1:
                return <LetterRecall letters={this.props.problem.letters} onSubmitResponse={this.onSubmitRecall}/>
            case 2:
                return <MathLetter.Feedback problem={this.props.problem} mathRes={this.mathRes} recallRes={this.recallRes} onComplete={this.advance}/>
        }   
    }
});

/**
 * Displays a sequence of alternating math equations and letters.
 * @prop problem object An object with fields {type, letters, equations}.
 * @prop tra     object See MathLetter component.
 * @prop traLow  number Lower threshold for TRA. If the TRA is below this, the task is stopped. Default 90%
 * @prop onComplete callback
 */
MathLetter.Sequence = React.createClass({
    getDefaultProps: function() {
        return {
            traLow: 0.9
        };
    },
    getInitialState: function() {
        //An array of math responses
        this.mathRes = [];

        return {
            count:  0,
            tra:    this.props.tra,
            showLowTra: false,
            showedLowTra: false,
            showTimeout: false,
            showedTimeout: false,
        };
    },
    componentDidUpdate: function() {
        if (this.state.count % 2 != 0) {
            this.timer = setInterval(this.onLetterTimeUp, 1000);
        }
    },
    onLetterTimeUp: function() {
        clearInterval(this.timer);
        this.advance();
    },
    onMathSubmit: function(response, startTime, endTime) {
        this.mathRes.push({response: response, time: endTime - startTime});
        //console.log('response', response);

        if (response == null && this.state.showedTimeout == false)
            return this.setState({showTimeout:true, showedTimeout:true});

        this.adjustTra(response);

        if(this.props.tra && this.state.tra.total > 2 &&
            !this.state.showedLowTra &&
            response !== EQ.getAnswer(this.props.problem.equations[Math.floor(this.state.count /2)]) &&
            this.state.tra.correct / this.state.tra.total < this.props.traLow)
            return this.setState({showLowTra:true, showedLowTra:true});

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
    /**
     * @param response boolean Participant's response to equation problem.
     */
    adjustTra: function(response) {
        if(!this.state.tra)
            return;

        var i = Math.floor(this.state.count / 2);
        var a = EQ.getAnswer(this.props.problem.equations[i]);
        var tra = this.state.tra;
        if(response == a)
            tra.correct++;
        tra.total++;
        this.setState({tra: tra});
    },
    advance: function() {
        if(this.state.count < (this.props.problem.letters.length * 2 - 1)) {
            this.setState({count: this.state.count + 1});
        }
        else
            this.props.onComplete(this.mathRes, this.state.tra);
    },
    render: function() {
        if(this.state.showLowTra)
            return <LowTra type={'math'} traLow={this.props.traLow} onComplete={this.onLowTraComplete}/>

        if(this.state.showTimeout)
            return <Timeout type={'math'} onComplete={this.onTimeoutComplete}/>

        if(this.state.count % 2 == 0) {
            return (
                <MathEq.Equation key={this.state.count}
                    equation={this.props.problem.equations[Math.floor(this.state.count / 2)]}
                    tra={this.state.tra}
                    timeLimit={this.props.timeLimit}
                    onSubmit={this.onMathSubmit} />
            );
        }
        else {
            return (
                <SingleLetterSlide
                    key={this.state.count}
                    letter={this.props.problem.letters[Math.floor(this.state.count / 2)]} />
            );
        }
    }
});

/**
 * @prop problem    The original problem.
 * @prop mathRes    Math problem responses from the user.
 * @prop recallRes  Letter recall response from the user.
 * @prop onComplete callback
 */
MathLetter.Feedback = React.createClass({
    getMathCorrectCount: function() {
        var res = 0;
        for(var i = 0; i < this.props.problem.equations.length; i++) {
            if(this.props.mathRes[i].response == EQ.getAnswer(this.props.problem.equations[i]))
                res++;
        }
        return res;
    },
    getLetterCorrectCount: function() {
        var res = 0;

        for(var i = 0; i < this.props.problem.letters.length; i++)
            if(this.props.problem.letters[i] == this.props.recallRes.response[i])
                res++;

        return res;
    },
    complete: function() {
        this.props.onComplete();
    },
    render: function() {
        var mc = this.getMathCorrectCount();
        var lc = this.getLetterCorrectCount();
        var ml = this.props.problem.equations.length; //Math problem length
        var ll = this.props.problem.letters.length;  //Letters length

        return (
            <div style={{fontSize:22}}>
                <div className="row" style={{marginBottom:20}}>
                    <div className="col-xs-12">
                        {translate('You recalled')} {lc} {translate('out of')} {ll} {translate('letters correctly')}.
                    </div>
                </div>
                <div className="row" style={{marginBottom:25}}>
                    <div className="col-xs-12">
                        {translate('You answered')} {mc} {translate('out of')} {ml} {translate('math questions correctly')}.
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