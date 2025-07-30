/**
 * Dependencies
 *   - letter-comp.js
 *   - sentence-comp.js
 */

/**
 * A sentence-letter sequence problem.
 * @prop sentences object {id:integer, sentence:string, sol:boolean}
 * @prop letters   array  An array of alphabets.
 * @prop feedback boolean
 * @prop onComplete callback
 */
var SentenceLetter = React.createClass({
    getInitialState: function() {
        return {stage: 0}
    },
    advance: function() {
        switch(this.state.stage) {
            case 0:
                this.setState({stage: 1});
                break;
            case 1:
                if(this.props.feedback)
                    this.setState({stage: 2});
                else
                    this.complete();
                break;
            case 2:
                this.complete();
                break;
        }
    },
    complete: function(){
        if(this.props.onComplete)
            this.props.onComplete(this.sentenceRes, this.recallRes);
    },
    /**
     * When SentenceLetter.Sequence component is completed.
     * @param sentenceRes array<object> An array of object of user responses to math problems.
     */
    onSequenceComplete: function(sentenceRes) {
        this.sentenceRes = sentenceRes;
        this.advance();
    },
    /**
     * @param challenge array<string>  All choices (letters) presented to the user on the recall screen.
     * @prarm indexes   array<Integer> user response as an array of indexes.
     * @param startTime integer        See getTime() of date object.
     * @param endTime   integer        See getTime() of date object.
     */
    onSubmitRecall: function(challenge, indexes, startTime, endTime) {
        //Save the recall result
        this.recallRes = {challenge: challenge, indexes: indexes, startTime: startTime, endTime: endTime};
        this.advance();
    },
    render: function() {
        switch(this.state.stage) {
            case 0:
                return (
                    <SentenceLetter.Sequence sentences={this.props.sentences} letters={this.props.letters}
                    onComplete={this.onSequenceComplete} />
                );
            case 1:
                return <LetterRecall letters={this.props.letters} onSubmitResponse={this.onSubmitRecall} />;
            case 2:
                return (
                    <SentenceLetter.Feedback sentences={this.props.sentences} letters={this.props.letters}
                    sentenceRes={this.sentenceRes} recallRes={this.recallRes} onComplete={this.advance} />
                );
        }   
    }
});

/**
 * Displays a sequence of alternating math equations and letters.
 * @prop problem object An object with fields {type, letters, equations}.
 * @prop onComplete callback
 */
SentenceLetter.Sequence = React.createClass({
    getInitialState: function() {
        //An array of math responses, each of which has the format 
        //{res: boolean, startTime: integer, endTime: integer}
        this.sentenceRes = [];

        return {count: 0};
    },
    componentDidUpdate: function() {
        if(this.state.count % 2 != 0) {
            this.timer = setInterval(this.onLetterTimeUp, 1000);
        }
    },
    onLetterTimeUp: function() {
        clearInterval(this.timer);
        this.advance();
    },
    onReadingSubmit: function(res) {
        this.sentenceRes.push(res);
        this.advance();
    },
    advance: function() {
        if(this.state.count < (this.props.letters.length * 2 - 1)) {
            this.setState({count: this.state.count + 1});
        }
        else
            this.props.onComplete(this.sentenceRes);
    },
    render: function() {
        if(this.state.count % 2 == 0) {
            var s = this.props.sentences[Math.floor(this.state.count / 2)].sentence;
            return (
                <SentenceQuestion key={this.state.count} sentence={s} onComplete={this.onReadingSubmit} />
            );
        }
        else {
            var l = this.props.letters[Math.floor(this.state.count / 2)];
            return (
                <SingleLetterSlide key={this.state.count} letter={l} />
            );
        }
    }
});

/**
 * @prop sentences   array<object>  An array of object {id:integer, sentence:string, sol:boolean}
 * @prop letters     array<string   An array of alphabets.
 * @prop sentenceRes array<object>  Responses from the user. {res:boolean, startTime, endTime}
 * @prop recallRes   array<integer> Letter recall response from the user.
 * @prop onComplete  callback
 */
SentenceLetter.Feedback = React.createClass({
    getReadingCorrectCount: function() {
        var res = 0;
        for(var i = 0; i < this.props.sentences.length; i++) {
            if(this.props.sentenceRes[i].res == this.props.sentences[i].sol)
                res++;
        }
        return res;
    },
    getLetterCorrectCount: function() {
        var res = 0;

        var r = this.props.recallRes.indexes.map(
            function(i) {return this.props.recallRes.challenge[i];}, this
        );

        for(var i = 0; i < this.props.letters.length; i++) {
            if(r[i] == this.props.letters[i])
                res++;
        }
        return res;
    },
    complete: function() {
        this.props.onComplete();
    },
    render: function() {
        var rc = this.getReadingCorrectCount();
        var lc = this.getLetterCorrectCount();
        var rl = this.props.sentences.length; //Math problem length
        var ll = this.props.letters.length;  //Letters length

        return (
            <div style={{marginTop:100, fontSize:22}}>
                <div className="row" style={{marginBottom:20}}>
                    <div className="col-xs-12">
                        You recalled {lc} out of {ll} letters correctly.  
                    </div>
                </div>
                <div className="row" style={{marginBottom:25}}>
                    <div className="col-xs-12">
                        You answered {rc} out of {rl} math questions correctly.
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