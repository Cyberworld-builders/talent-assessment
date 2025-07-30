var SingleLetterSlide = React.createClass({
    propTypes: {
        letter: React.PropTypes.string.isRequired
    },
    render: function() {
        return <div style={{fontSize:75}}>{this.props.letter}</div>;
    }
});

/**
 * Letter recall screen.
 * @prop letters     array
 * @prop limitSelect boolean Limit the number of selections to the number alphabets shown.
 * @prop onSubmitResponse callback
 */ 
var LetterRecall = React.createClass({
    propTypes: {
        letters: React.PropTypes.array.isRequired,
        limitSelect: React.PropTypes.bool,
        onSubmitResponse: React.PropTypes.func.isRequired
    },
    getInitialState: function() {
        var selects =[];
        var limit = this.props.limitSelect ? this.props.letters.length: 12;
        for(var i = 0;  i < limit; i++)
            selects.push(null);

        return {
            options: LS.makeOptions(this.props.letters),
            //selects stores the order in which slots were selected.
            //For example, if the 3rd slot were select first, then 1st slot, and finally 2nd slot,
            //select would contain the values [3, 1, 2]
            selects: selects
        };
    },
    componentDidMount: function() {
        this.startTime = new Date().getTime();
    },
    render: function() {
        return (
            <div>
                <div className="row">
                    {
                        this.state.options.map(function(letter, index){
                            var s = this.state.selects.indexOf(index);
                            return (
                                <div key={index} className="col-xs-4" onClick={this.letterClicked.bind(this, index)} style={{paddingTop:20, paddingBottom:20}}>
                                    <div style={{fontSize:45, cursor:'pointer'}}>
                                        {letter}
                                    
                                        <div style={{display:'inline-block', textAlign:'center', width:50, position:'relative', top:-5}}>
                                            <span className="badge" style={{fontSize:30, backgroundColor:'#5bc0de'}}>
                                                {s == -1 ? '' : s + 1}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            );
                        }, this)
                    }  
                </div>
                <div className="row" style={{marginTop:25}}>
                    <div className="col-xs-6">
                        <button className="btn btn-default pull-right" onClick={this.clearSelects}>{translate('Clear')}</button>
                    </div>
                    <div className="col-xs-6">
                        <button className="btn btn-default pull-left" onClick={this.submitResponse}>{translate('Continue')}</button>
                    </div>
                </div>
            </div>
        );
    },
    letterClicked: function(index, event) {

        var i = this.state.selects.indexOf(index);

        //If this index is already selected, deselect it.
        if(i != -1) {
            this.state.selects[i] = null;
            this.setState({selects:this.state.selects});
        }
        //Else, select the index (put into this.state.selects).
        else {
            for(var j = 0; j < this.state.selects.length; j++) {
                if(this.state.selects[j] == null) {
                    this.state.selects[j] = index;
                    this.setState({selects:this.state.selects});
                    break;
                }
            }
        }
    },
    clearSelects: function() {
        for(var i = 0; i < this.state.selects.length; i++) {
            this.state.selects[i] = null;
        }
        this.setState({selects:this.state.selects});
    },
    submitResponse: function() {
        //Get the response time
        var endTime = new Date().getTime();
        var time = endTime - this.startTime;

        //Map select indexes to letters
        var options = this.state.options;
        var response = trimArray(this.state.selects).map(function(i){return options[i]});

        this.props.onSubmitResponse(this.state.options, response, time);
    }
});

var LetterSequenceReport = React.createClass({
    propTypes: {
        letters: React.PropTypes.array.isRequired,
        response: React.PropTypes.object.isRequired
    },
    complete: function() {
        this.props.onComplete();
    },
    render: function() {
        var letters = this.props.letters;
        var options = this.props.response.options;
        var response = this.props.response.response;
        var time = this.props.response.time;

        //Calculate correct count
        var correctCount = 0;
        for(var i = 0; i < letters.length; i++) {
            if(letters[i] == response[i])
                correctCount++;
        }

        return (
            <div>
                <div className="row">
                    <div className="col-xs-12" style={{fontSize:25, marginBottom:25}}>
                        {translate('You recalled')} {correctCount} {translate('out of')} {letters.length} {translate('letters correctly')}.
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

/**
 * @prop probId The problem id.
 * @prop questionId The id of the assessment question.
 * @prop letters The list of alphabets to present to user.
 * @prop report  Indicates if the result of this assessment should be displayed.
 * @prop onComplete The callback when this component is finished.
 */
var LetterSequence = React.createClass({
    propTypes: {
        probId: React.PropTypes.number.isRequired,
        questionId: React.PropTypes.number.isRequired,
        letters: React.PropTypes.array.isRequired,
        report: React.PropTypes.bool,
        onComplete: React.PropTypes.func.isRequired
    },
    /**
     * Gets the initial state of this component.
     * Stages: 'flash', 'recall', 'report'
     */
    getInitialState: function() {
        return {count:0, stage:'flash'};
    },
    componentDidMount: function() {
        this.timer = setInterval(this.timerTick, 1000);
    },
    timerTick: function() {
        var count = this.state.count + 1;

        if(count == this.props.letters.length) {
            clearInterval(this.timer);
            this.state.stage = 'recall';
        }

        this.setState({count:count});
    },
    /**
     * @params challenge array  The list of letters presented to the user on the recall screen.
     * @params selections array User's selections.
     */
    handleResponse: function(options, response, time) {
        if(this.props.probId != undefined && this.props.probId != null)
            this.response = {probId: this.props.probId, questionId: this.props.questionId, options: options, response: response, time: time};
        else
            this.response = {options: options, response: response, time: time};

        if(this.props.report)
            this.setState({stage: 'report'});
        else
            this.complete();
    },
    complete: function() {
        this.props.onComplete(this.response);
    },
    render: function() {
        //If there are still more letters, display the letters.
        if(this.state.stage == 'flash') {
            return (<SingleLetterSlide letter={this.props.letters[this.state.count]} />);
        }
        else if(this.state.stage == 'recall') {
            return (<LetterRecall letters={this.props.letters} onSubmitResponse={this.handleResponse}/>);
        }
        else {
            return <LetterSequenceReport letters={this.props.letters} response={this.response} onComplete={this.complete}/>
        }
    }
});