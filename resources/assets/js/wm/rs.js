/** sentences[0] are logically true, and sentence[1] are logically false. */
var sentences = [[
    'The computer was running slowly, but we can take it into the store to get it fixed.',
    'Lizzie\'s parents are celebrating their twentieth wedding anniversary at a nice restaurant.',
    'I took my dog to the park and we spent the day playing fetch with sticks that fell off of trees.',
    'Joe loves breakfast food so he decided to buy a waffle maker when he went to the store.',
    'The rainforest is home to numerous species of plants and animals, so it is important to take care of it.',
    'Every Wednesday Susie and Adam meet at a local restaurant to talk about their week because they are good friends.',
    'After every family vacation the smith\'s like to print out one photo and mail it to all of their friends.',
    'Since Kelly is majoring in Spanish, she hopes she will be able to take a trip to Spain before she graduates.',
    'Jim wants to be a Doctor so he spends hours in the library so that he can maintain a perfect GPA.',
    'Every university student is excited to take their last exams so that they can rest after a long semester.'
],
[
    'The pen was being a very good girl when she did what she was told.',
    'I am going to go to the coffee shop to change my tire because my car broke down.',
    'The camel in front of me at the grocery store was buying five cans of noodle soup.',
    'The panda is always laughing at her friends crazy stories about their vacations across Asia.',
    'After a long day at the office, Sarah loves going home and having conversations with her cheesecake about how her day has been.',
    'Rebeca has met the lamp of her dreams and hopes to get married next may at her family church.',
    'High school English class was really hard for the computer so he had to have a tutor help him study for his final.',
    'After a long week of school, Molly loves to spend Friday nights curled up with a good book of pasta.',
    'The starfish wished she had put on sunscreen because she got a terrible sunburn after her last trip to the beach.',
    'The skateboard always feels jealous when the dog gets to take a trip to the park.'
]];

/*
 * @prop sentence   string   The problem sentence, which is either logically true or false.
 * @prop sol        boolean  The correct answer (solution), which is either true or false.
 * @prop feedback   boolean  If feedback should be displayed.
 * @prop onComplete callback
 */
var SentenceQuestion = React.createClass({
    getInitialState: function() {
        return {stage: 0};
    },
    componentDidMount: function() {
        this.startTime = new Date().getTime();
    },
    /**
     * Handles the event when user click on true or false.
     * @params res boolean The user's response
     */
    onRespond: function(res) {
        var endTime = new Date().getTime();
        this.res = {res: res, startTime: this.startTime, endTime: endTime}
        this.advance();
    },
    advance: function() {
        if(this.state.stage == 0 && this.props.feedback)
            this.setState({stage: 1});
        else {
            this.onComplete(this.res);
        }
    },
    onComplete:function() {
        if(this.props.onComplete)
            this.props.onComplete(this.res);
    },
    render: function(){
        switch(this.state.stage) {
            case 0:
                return (
                    <div>
                        <div className="row" style={{marginTop:100, marginBottom:35}}>
                            <div className="col-md-8 col-md-offset-2 col-xs-12">
                                <div style={{
                                    backgroundColor:'#f9f9f9',
                                    textAlign:'justify',
                                    padding:'0.5em 0.8em',
                                    fontSize:25,
                                    borderLeft: '10px solid #ccc'
                                }}>
                                    {this.props.sentence}
                                </div>
                            </div>
                        </div>
                        <div className="row" style={{marginBottom:25}}>
                            <div className="col-xs-12" style={{fontSize:20}}>
                                The sentence above makes sense.
                            </div>
                        </div>
                        <div className="row">
                            <div className="col-xs-6">
                                <button className="btn btn-default pull-right" onClick={this.onRespond.bind(this, true)}>True</button>
                            </div>
                            <div className="col-xs=6">
                                <button className="btn btn-default pull-left" onClick={this.onRespond.bind(this, false)}>False</button>
                            </div>
                        </div>
                    </div>
                )
            case 1:
                return (
                    <SentenceQuestion.Feedback sentence={this.props.sentence} sol={this.props.sol}
                    res={this.res} onComplete={this.onComplete} />
                );
        }
    },
    statics: {
        pickRandomQuestion: function() {
            var r = Math.floor(Math.random() * 2);
            var n = Math.floor(Math.random() * sentences[r].length);
            var sol = (r == 0 ? true : false);
            var sentence = sentences[r][n];
            return {type:'sentence', id: r * 100 + n, sentence: sentence, sol: sol};
        },
        pickRandomQuestions: function(length) {
            var res = [];
            var used = [];
            while(res.length < length) {
                var s = SentenceQuestion.pickRandomQuestion();
                if(used.indexOf(s.id) == -1) {
                    res.push(s);
                    used.push(s.id);
                }
            }
            return res;
        }
    }
});

/**
 * @prop sentence   string   The problem sentence
 * @prop sol        boolean  The correct answer (solution)
 * @prop res        object   User response with the format {res: boolean, startTime:integer, endTime:integer}
 * @prop onComplete callback
 */
SentenceQuestion.Feedback = React.createClass({
    onComplete: function() {
        if(this.props.onComplete)
            this.props.onComplete();
    },
    render: function() {
        return (
            <div>
                <div className="row">
                    <div className="col-xs-12" style={{fontSize:25, marginTop:200, marginBottom:25}}>
                        Your answer is {this.props.res.res == this.props.sol ? 'correct' : 'incorrect'}.
                    </div>
                </div>
                <div className="row">
                    <button className="btn btn-default" onClick={this.onComplete}>Continue</button>
                </div>
            </div>
        )
    }
});