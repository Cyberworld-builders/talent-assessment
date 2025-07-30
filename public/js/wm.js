/**
 * Checks if the array of points contains a specific point.
 * @param array array<array>   An array of array which is a point.
 * @param point array<integer> An array containing two elements [x, y].
 * @return true if array contains the point.
 *         false if:
 *             array is null or undefined
 *             point is null or undefined
 *             array does not contain the point
 */
function arrayHasPoint(array, point) {
    if(!array || !point)
        return false;

    for(var i = 0; i < array.length; i++)
        if(array[i] && array[i][0] == point[0] && array[i][1] == point[1])
            return true;
    return false;
}

/**
 * Shuffles an array in place.
 *
 * @param array The array to be shuffled.
 * @returns the array itself.
 */
function shuffle(array) {
    var currentIndex = array.length, temporaryValue, randomIndex;

    // While there remain elements to shuffle...
    while (0 !== currentIndex) {

      // Pick a remaining element...
      randomIndex = Math.floor(Math.random() * currentIndex);
      currentIndex -= 1;

      // And swap it with the current element.
      temporaryValue = array[currentIndex];
      array[currentIndex] = array[randomIndex];
      array[randomIndex] = temporaryValue;
    }

    return array;
}

/**
 * Trim null from the end of array, in place.
 *
 * @param array The array to be trimmed.
 * @returns the array itself.
 */
function trimArray(array) {
    var p = 0;
    for(var i = 0; i < array.length; i++) {
        if(array[i] == null) {
            p = i;
            break;
        }
    }
    var res = array.splice(0, p);
    return res;
}

/**
 * No operation
 */
function nop() {}

var PointCollection = {
    indexOf: function(pointArray, point) {
        for(var i = 0; i < pointArray.length; i++)
            if(pointArray[i] && pointArray[i][0] == point[0] && pointArray[i][0] == point[0] && pointArray[i][1] == point[1])
                return i;
        return -1;
    }
};

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}
var EQ = {
    typeId: 'eq',
    typeLabel: 'Equation',
    getAnswer: function(equation) {
        var p = equation.indexOf('=');
        return eval(equation.substring(0, p)) == equation.substring(p + 1);
    },
    isValid: function(equation) {
        return /^[()0-9+\-*\/]+=\d+$/.test(equation);
    },
    getScore: function(equation, response) {
        if(this.getAnswer(equation) === response)
            return 1;
        return 0;
    }
};

var TEXT = {
    typeId: 'input',
    typeLabel: 'Text Input',
};

var LS = {
    typeId: 'ls',
    typeLabel: 'Letter Sequence',
    /**
     * Make a random array of letters (options), including the specified letter array, to be used for recall.
     *
     * @param letters array A list of letters to include. This is usually the original list of letters shown to participant.
     */
    makeOptions: function(letters) {
        var res = letters.slice(), a = 'A'.charCodeAt(0), z = 'Z'.charCodeAt(0);

        while(res.length < 12) {
            //Get a random letter
            var c = Math.floor(Math.random() * (z - a)) + a;
            var l = String.fromCharCode(c);
            
            if(l != 'A' && l != 'E' && l != 'I' && l != 'O' && l != 'U' && res.indexOf(l) == -1)
                res.push(l);
        }

        res = shuffle(res);
        return res;
    },
    makeRandomLetterArray: function(length) {
        var res = [], a = 'A'.charCodeAt(0), z = 'Z'.charCodeAt(0);

        while(res.length < length) {
            //Get a random letter
            var c = Math.floor(Math.random() * (z - a)) + a;
            var l = String.fromCharCode(c);
            if(l != 'A' && l != 'E' && l != 'I' && l != 'O' && l != 'U' && res.indexOf(l) == -1)
                res.push(l);
        }
        
        return res;
    },
    getScore: function(letters, response) {
        var sum = 0;
        for(var i = 0; i < letters.length; i++)
            if(letters[i] === response[i])
                sum++;
        return sum;
    }
};

var EQLS = {
    typeId: 'eqls',
    typeLabel: 'Equation Letters'
};

var SQ = {
    typeId: 'sq',
    typeLabel: 'Square Sequence',
    /**
     * @param length How many squares are to be in the generated sequence.
     * @returns An array of arrays, where each array represent a point [x, y].
     */
    makeRandomFigure: function(length) {
        var res = [];
        while(res.length < length) {
            var x = Math.floor(Math.random() * 4);
            var y = Math.floor(Math.random() * 4);
            var p = [x, y];
            if(!arrayHasPoint(res, p))
                res.push(p);
        }
        return res;
    },
    getScore: function(squares, response) {
        var sum = 0;
        for(var i = 0; i < squares.length; i++)
            if(response[i] && squares[i][0] === response[i][0] && squares[i][1] === response[i][1])
                sum++;
        return sum;
    }
};

var SY = {
    typeId: 'sy',
    typeLabel: 'Symmetry',
    getRandomPoint: function(array) {
        var p = [0, 0];
        do {
            p[0] = Math.floor(Math.random() * 8);
            p[1] = Math.floor(Math.random() * 8);
        } while(arrayHasPoint(array, p));
        return p;
    },
    /**
     * Generate a random symmetric figure.
     * @param density integer how many cells are colored. This should be a 0 >= density <= 30.
     */
    makeSymmetricFigure: function(density) {
        if(!density || density < 0 || density > 30)
            density = Math.floor(Math.random() * 18) + 12;

        var points = [];

        while(points.length < density * 2) {
            var x = Math.floor(Math.random() * 4);
            var y = Math.floor(Math.random() * 8);
            var p = [x, y];

            if(!arrayHasPoint(points, p)) {
                points.push(p);
                points.push(this.getMirror(p));
            }
        }

        return points;
    },
    /**
     * Generate a asymmetric figure by mutating a random symmetric figure.
     * @param density integer how many cells are colored. This should be a 0 >= density <= 30.
     */
    makeAsymmetricFigure: function(density) {
        var points = this.makeSymmetricFigure(density);

        for(var i = 0; i < 3; i++) {
            var op = Math.floor(Math.random() * (i == 0 ? 2 : 3));
            switch(op) {
                case 0:
                    points.push(this.getRandomPoint(points));
                    break;
                case 1:
                    var index = Math.floor(Math.random() * points.length);
                    points.splice(index, 1);
                    break;
            }
        }

        return points;
    },
    /**
     * Generate a totally random figure.
     * @param density integer how many cells are colored. This should be a 0 >= density <= 30.
     */
    makeRandomFigure: function(density) {
        if(!density || density < 0 || density > 30)
            density = Math.floor(Math.random() * 18) + 12;

        var points = [];
        while(points.length < density * 2) {
            points.push(this.getRandomPoint(points));
        }
        return points;
    },
    makeFigure: function() {
        var density = Math.floor(Math.random() * 18) + 12; 
        switch(Math.floor(Math.random() * 5)) {
            case 0:
            case 1:
                return this.makeSymmetricFigure(density);
            case 2:
            case 3:
                return this.makeAsymmetricFigure(density);
            case 4:
                return this.makeRandomFigure(density);
        }

        return this.makeSymmetricFigure(density);
    },
    getMirror: function(p) {
        return [7 - p[0], p[1]];
    },
    /**
     * Checks a figure, represented by array, is symmetric.
     * Throws if array is null or undefined.
     */
    isSymmetric: function(array) {
        if(!array)
            throw 'Figure array is undefined';

        for(var i = 0; i < array.length; i++)
            if(!arrayHasPoint(array, this.getMirror(array[i])))
                return false;
        return true;
    },
    getScore: function(symmetry, response) {
        if(this.isSymmetric(symmetry) === response)
            return 1;
        return 0;
    }
};

var SYSQ = {
    typeId: 'sysq',
    typeLabel: 'Symmetry Squares',
    /**
     * Make a random computer generated problem.
     * @param length integer The length of the sequence.
     * @returns An object {type, squares, symmetries}
     */
    makeProblem: function(length) {
        var squares = SQ.makeRandomFigure(length);
        var symmetries = this.makeSymmetryFigures(length);
        
        return {type:this.typeId, squares:squares, symmetries:symmetries};
    },
    makeSymmetryFigures: function(length) {
        var symmetries = [];

        for(var i = 0; i < length; i++)
            symmetries.push(SY.makeFigure());

        return symmetries;
    }
};

var RS = {
    typeId: 'rs',
    typeLabel: 'Sentence'
};

var RSLS = {
    typeId: 'rsls',
    typeLabel: 'Sentence Letters'
};

BLK = {
    getScore: function(probBlock, respBlock) {
        var sum = 0;
        for(var i = 0; i < probBlock.problems.length; i++) {
            var prob = probBlock.problems[i];
            switch(prob.type) {
                case LS.typeId: sum += LS.getScore(probBlock.problems[i].letters, respBlock[i].response); break;
                case EQ.typeId: sum += EQ.getScore(probBlock.problems[i].equation, respBlock[i].response); break;
                case SQ.typeId: sum += SQ.getScore(probBlock.problems[i].squares, respBlock[i].response); break;
                case SY.typeId: sum += SY.getScore(probBlock.problems[i].symmetry, respBlock[i].response); break;
                case EQLS.typeId: sum += LS.getScore(probBlock.problems[i].letters, respBlock[i].letters.response); break;
                case SYSQ.typeId: sum += SQ.getScore(probBlock.problems[i].squares, respBlock[i].squares.response); break;
            }
        }
        return sum;        
    },
    getMaxScore: function(block) {
        var sum = 0;

        for(var i = 0; i < block.problems.length; i++) {
            var prob = block.problems[i];
            switch(prob.type) {
                case LS.typeId: sum += prob.letters.length; break;
                case EQ.typeId: sum++; break;
                case SQ.typeId: sum += prob.squares.length; break;
                case SY.typeId: sum++; break;
                case EQLS.typeId: sum += prob.letters.length; break;
                case SYSQ.typeId: sum += prob.squares.length; break;
            }
        }

        return sum;
    }
};

TSK = {
    getScore: function(task, respBlocks) {
        var sum = 0;
        for(var i = 0; i < task.blocks.length; i++)
            if(!task.blocks[i].practice)
                sum += BLK.getScore(task.blocks[i], respBlocks[i]);
        return sum;
    },
    getMaxScore: function(task) {
        var sum = 0;
        for(var i = 0; i < task.blocks.length; i++)
            if(!task.blocks[i].practice)
                sum += BLK.getMaxScore(task.blocks[i]);
        return sum;
    }
};
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
var TextInput = React.createClass({
    propTypes: {
        probId: React.PropTypes.number.isRequired,
        questionId: React.PropTypes.number.isRequired,
        text: React.PropTypes.string.isRequired,
        onComplete: React.PropTypes.func.isRequired
    },

    getInitialState: function() {
        return {
            input: "",
        }
    },

    handleChange: function(event) {
        this.setState({input: event.target.value});
    },

    complete: function() {
        var response = {
            probId: this.props.probId,
            questionId: this.props.questionId,
            response: this.state.input,
        };
        this.props.onComplete(response);
    },

    render: function() {
        return (
            <div>
                <div className="row">
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
                    <div className="col-xs-10 col-xs-offset-1 col-lg-8 col-lg-offset-2"
                         style={{
                             lineHeight: '160%',
                             textAlign:'justify', fontSize: 20,
                             padding: '20px 0',
                         }}>
                        <input type="text" value={this.state.input} onChange={this.handleChange}
                            style={{
                                width: '100%',
                                border: '2px solid #e1e1e8',
                                borderRadius: 4,
                                padding: '10px',
                                fontSize: 24
                            }} />
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-2 col-xs-offset-5">
                        <button className="btn btn-default" onClick={this.complete}>Continue</button>
                    </div>
                </div>
            </div>
        )
    }
});
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
/**
 * @prop equation string     The equation of this problem.
 * @prop feedback boolean    Whether to display feedback to the user.
 * @prop onComplete callback The callback when this component is finished.
 */
var MathEq = React.createClass({
    propTypes: {
        probId: React.PropTypes.number.isRequired,
        questionId: React.PropTypes.number.isRequired,
        equation: React.PropTypes.string.isRequired,
        feedback: React.PropTypes.bool,
        onComplete: React.PropTypes.func.isRequired,
        timeLimit: React.PropTypes.number.isRequired,
    },
    getInitialState: function() {
        return {stage: 0};
    },
    handleSubmit: function(response, startTime, endTime) {
        this.response = {
            probId: this.props.probId,
            questionId: this.props.questionId,
            response: response,
            time: endTime - startTime
        }

        if(this.props.feedback) {
            this.setState({stage:1});
        }
        else {
            this.complete();
        }
    },
    complete: function() {
        this.props.onComplete(this.response);
    },
    render: function() {
        switch(this.state.stage) {
            case 0:
                return <MathEq.Equation equation={this.props.equation} onSubmit={this.handleSubmit} />
            case 1:
                return (
                    <MathEq.Feedback equation={this.props.equation} response={this.response}
                        showTime={true} onComplete={this.complete} />
                );
        }
    }
});

/**
 * @prop equation string
 * @prop tra      object
 * @prop onSubmit callback
 */
MathEq.Equation = React.createClass({
    componentDidMount: function() {
        MathJax.Hub.Queue(['Typeset', MathJax.Hub, 'equation']);
        MathJax.Hub.Queue(['afterTypeSet', this]);
    },
    afterTypeSet: function() {
        this.pause = setInterval(this.afterRenderPause, 500);
    },
    /**
     * Show the content after giving Mathjax a short pause to render the equation.
     */
    afterRenderPause: function() {
        clearInterval(this.pause);
        $('#component').css('visibility', 'visible');
        this.startTime = new Date().getTime();
        this.timeLimit = setInterval(this.onEquationTimeUp, (this.props.timeLimit ? this.props.timeLimit : 8000));
    },
    onEquationTimeUp: function() {
        clearInterval(this.timeLimit);
        var endTime = new Date().getTime();
        //console.log('time is up: ', endTime - this.startTime);
        this.props.onSubmit(null, this.startTime, endTime);
    },
    submitTrue: function() {
        this.handleSubmit(true);
    },
    submitFalse: function() {
        this.handleSubmit(false);
    },
    handleSubmit: function(res) {
        clearInterval(this.timeLimit);
        var endTime = new Date().getTime();
        //console.log('submitted early: ', endTime - this.startTime);
        this.props.onSubmit(res, this.startTime, endTime);
    },
    render: function() {
        var equation = this.props.equation.replace(/\*/g, '\\times');

        return (
            <div style={{visibility:'hidden'}} id="component">
                <div className="row" style={{marginBottom:60}}>
                    <div className="col-xs-12" id="equation" style={{fontSize:35}}
                        dangerouslySetInnerHTML={{__html: '`' + equation + '`'}}>
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-6">
                        <button className="btn btn-default pull-right" onClick={this.submitTrue}>{translate('True')}</button>
                    </div>
                    <div className="col-xs-6">
                        <button className="btn btn-default pull-left" onClick={this.submitFalse}>{translate('False')}</button>
                    </div>
                </div>
                {
                    this.props.tra ? <MathEq.Tra tra={this.props.tra} /> : null
                }
            </div>
        );
    }
});

/*
 * @prop tra object Task Running Accuracy
 */
MathEq.Tra = React.createClass({
    render: function() {
        return null;
        // return (
        //     <div style={{position:'fixed', bottom:-100, left:0, width:'100%', textAlign:'center'}}>
        //         <b>Math Accuracy</b> <br/> Correct: {this.props.tra.correct} | Incorrect: {this.props.tra.total - this.props.tra.correct} | Total: {this.props.tra.total}
        //     </div>
        // );
    }
});

/**
 * @prop equation   string  The problem.
 * @prop response   boolean User's response.
 * @prop startTime  integer See return value of getTime() of JavaScript's Date object.
 * @prop endTime    integer
 * @prop showTime   boolean If this feedback should show user's response time.
 * @prop onComplete callback
 */
MathEq.Feedback = React.createClass({
    complete: function() {
        this.props.onComplete();
    },
    render: function() {
        return (
            <div>
                <div className="row" style={{marginBottom:25}}>
                    <div className="col-xs-12" style={{fontSize:25}}>
                        {EQ.getAnswer(this.props.equation) === this.props.response.response ? translate('Correct') : translate('Incorrect')}!
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
/**
 * The root component of the box sequence problem.
 * This component does the following
 *   1. Displays the sequence component.
 *   2. Displays the recall component.
 *   3. Optionally displays the feedback component.
 * @prop sequence   array<point> An array of colored cells.
 * @prop feedback   boolean      If feedback should be displayed.
 * @prop onComplete callback
 */
var BoxSequence = React.createClass({
    propTypes: {
        probId: React.PropTypes.number.isRequired,
        questionId: React.PropTypes.number.isRequired,
        sequence: React.PropTypes.array.isRequired,
        feedback: React.PropTypes.bool,
        onComplete: React.PropTypes.func.isRequired
    },
    getInitialState: function() {
        return {stage: 0};
    },
    advance: function() {
        if(this.state.stage < 1 || (this.state.stage == 1 && this.props.feedback))
            this.setState({stage: this.state.stage + 1});
        else
            this.props.onComplete({probId: this.props.probId, questionId: this.props.questionId, response: this.res.res, time: (this.res.endTime - this.res.startTime)});
    },
    /**
     * Handles recall response from the user.
     * @param res       array<point> A list of cells where user clicked, in the order they were clicked.
     * @param startTime integer
     * @param endTime   integer
     */
    onRecallComplete: function(res, startTime, endTime) {
        this.res = {res: res, startTime: startTime, endTime: endTime};
        this.advance();
    },
    render: function() {
        switch(this.state.stage) {
            case 0:
                return (
                    <BoxSequence.SlideSet sequence={this.props.sequence} onComplete={this.advance} />
                );
            case 1:
                return (
                    <BoxSequence.Recall sequence={this.props.sequence} onComplete={this.onRecallComplete} />
                );
            case 2:
                return (
                    <BoxSequence.Feedback sequence={this.props.sequence} response={this.res} onComplete={this.advance} />
                );
        }
    },
    statics: {
        generateRandomProblem: function(length) {
            var res = {type: 'squares', problem: []};

            while(res.problem.length < length) {
                var x = Math.floor(Math.random() * 4);
                var y = Math.floor(Math.random() * 4);
                var p = [x, y];
                if(!arrayHasPoint(res.problem, p))
                    res.problem.push(p);
            }

            return res;
        }
    }
});

/**
 * Displays a sequence colored boxes.
 * @prop sequence array<point> An array of locations.
 * @prop onComplete callback
 */
BoxSequence.SlideSet = React.createClass({
    getInitialState: function() {
        return {count: 0};
    },
    advance: function() {
        if(this.state.count < this.props.sequence.length - 1)
            this.setState({count: this.state.count + 1});
        else
            this.props.onComplete();
    },
    render: function() {
        return (
            <BoxSequence.Slide 
                key={this.state.count}
                colored={[this.props.sequence[this.state.count]]}
                onComplete={this.advance} />
        );
    }
});

/**
 * A single slide of the sequence.
 * @prop colored array<point> An array specified which box should be color-filled.
 * @prop onComplete callback
 */
BoxSequence.Slide = React.createClass({
    componentDidMount: function() {
        this.timer = setInterval(this.timeup, 1000);
    },
    timeup: function() {
        clearInterval(this.timer);
        this.props.onComplete();
    },
    render: function() {
        return (
            <div>
                <div className="row" style={{marginBottom:20}}>
                    <div className="col-xs-12" style={{fontSize:20, textAlign:'center'}}>
                        &nbsp;
                    </div>
                </div>
                <div className="row" style={{marginBottom:25}}>
                    <div className="col-md-6 col-md-offset-3 col-xs-8 col-xs-offset-2">
                        <BoxSequence.Slide.Figure rows={4} cols={4} colored={this.props.colored} />
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-12">
                        <button className="btn btn-default" style={{visibility:'hidden'}}>Spacer</button>
                    </div>
                </div>
            </div>
        );
    }
});

/**
 * @prop rows        integer       Number of rows.
 * @prop cols        integer       Number of columns.
 * @prop colored     array<point>  An array specified which box should be color-filled.
 * @prop cellText    array<object> Cell text with format {loc: [x, y], text:'text'}.
 * @prop borderColor string        Color code of border. 
 * @prop loColor     string        Color code of non-highlighted cell.
 * @prop hiColor     string        Color code of highlighted cell.
 * @prop onCellClick callback
 */
BoxSequence.Slide.Figure = React.createClass({
    propTypes: {
        rows: React.PropTypes.number.isRequired,
        cols: React.PropTypes.number.isRequired,
        colored: React.PropTypes.array,
        cellText: React.PropTypes.array,
        borderColor: React.PropTypes.string,
        loColor: React.PropTypes.string,
        hiColor: React.PropTypes.string,
        class: React.PropTypes.string,
        onCellClick: React.PropTypes.func
    },
    getDefaultProps: function() {
        return {
            borderColor: '#555',
            loColor: '#ffffff',
            hiColor: '#005997',
        };
    },
    componentDidMount: function() {
        var svg = this.props.class ? $('') : $('svg.' + this.props.class);
        var width = svg.width();
        svg.height(width);
    },
    onCellClick: function(cell) {
        if(this.props.onCellClick)
            this.props.onCellClick(cell);
    },
    /**
     * Checks if a cell is colored against props 'colored'.
     * @param cell array<integer> An array consisting two elements [x, y].
     */
    cellIsColored: function(cell) {
        if(!this.props.colored)
            return false;

        for(var i = 0; i < this.props.colored.length; i++)
            if(cell[0] == this.props.colored[i][0] && cell[1] == this.props.colored[i][1])
                return true;
        return false;
    },
    /**
     * Returns the index of this.props.cellText for this cell, or -1 if this cell
     * does not have text.
     */
    cellTextIndex: function(cell) {
        if(!this.props.cellText)
            return -1;
        for(var i = 0; i < this.props.cellText.length; i++)
            if(this.props.cellText[i].loc &&
                cell[0] == this.props.cellText[i].loc[0] && 
                cell[1] == this.props.cellText[i].loc[1])
                return i;
        return -1;
    },
    render: function() {
        var x0 = 25, y0 = 25;
        var width = 100;
        var cells = [];

        //Make the cells to draw
        for(var x = 0; x < this.props.cols; x++)
            for(var y = 0; y < this.props.rows; y++)
                cells.push([x, y]);

        var viewBoxW = this.props.cols * width + x0;
        var viewBoxH = this.props.rows * width + y0;

        return (
            <svg className={this.props.class} style={{width:'100%'}} viewBox={'0 0 ' + viewBoxH + ' ' + viewBoxH}>
                {
                    cells.map(function(cell, index) {
                        if(this.cellTextIndex(cell) != -1)
                            return (
                                <g key={index} onClick={this.onCellClick.bind(this, cell)} style={{cursor:this.props.onCellClick ? 'pointer' : 'auto'}}>
                                    <rect x={x0 + width * cell[0]} y={y0 + width * cell[1]} width={width} height={width}
                                        stroke={this.props.borderColor}
                                        fill={this.cellIsColored(cell) ? this.props.hiColor : this.props.loColor}>
                                    </rect>
                                    {/*<circle
                                        cx={x0 + (width * cell[0] + width * (cell[0] + 1)) / 2}
                                        cy={y0 + width * cell[1] + 65} r="3" fill="red" />*/}
                                    <text textAnchor='middle'
                                        x={x0 + (width * cell[0] + width * (cell[0] + 1)) / 2}
                                        y={y0 + width * cell[1] + 65}
                                        fontSize='50' 
                                        fill='black'>
                                        {this.props.cellText[this.cellTextIndex(cell)].text}
                                    </text>
                                </g>
                            )
                        else
                            return (
                                <g key={index} onClick={this.onCellClick.bind(this, cell)} style={{cursor:this.props.onCellClick ? 'pointer' : 'auto'}}>
                                    <rect key={index} x={x0 + width * cell[0]} y={y0 + width * cell[1]}
                                        width={width} height={width} stroke={this.props.borderColor}
                                        fill={this.cellIsColored(cell) ? this.props.hiColor : this.props.loColor}>
                                    </rect>
                                </g>
                            );
                    }, this)
                }
            </svg>
        );
    }
});

/**
 * The recall screen.
 * @prop sequence   array<point>
 * @prop onComplete callback
 */
BoxSequence.Recall = React.createClass({
    getInitialState: function() {
        return {selects: this.props.sequence.map(function(){
            return null;
        })};
    },
    componentDidMount: function() {
        this.startTime = new Date().getTime();
    },
    /**
     * If the cell has been selected, return its index in this.state.selects;
     * return -1 otherwise.
     * @param cell array<integer>
     */
    getCellSelectIndex: function(cell) {
        var s = this.state.selects;
        for(var i = 0; i < s.length; i++)
            if(s[i] && s[i][0] == cell[0] && s[i][0] == cell[0] && s[i][1] == cell[1])
                return i;
        return -1;
    },
    onCellClick: function(cell) {
        var index = this.getCellSelectIndex(cell);
        var selects = this.state.selects;

        if(index == -1) {
            for(var i = 0; i < selects.length; i++) {
                if(selects[i] == null) {
                    selects[i] = cell;
                    this.setState({selects: selects});
                    break;
                }
            }
        }
        else {
            selects[index] = null;
            this.setState({selects: selects});
        }
    },
    onClear: function() {
        for(var i = 0; i < this.state.selects.length; i++)
            this.state.selects[i] = null;
        this.setState({selects: this.state.selects});
    },
    onComplete: function() {
        var endTime = new Date().getTime();
        this.props.onComplete(this.state.selects, this.startTime, endTime);
    },
    render: function() {
        //Make cell text
        var cellText = this.state.selects.map(function(cell, index){
            return {loc:cell, text:index + 1};
        });

        return (
            <div>
                <div className="row" style={{marginBottom:20}}>
                    <div className="col-xs-12" style={{fontSize:20, textAlign:'center'}}>
                        {translate('Please recall the order of the blue boxes')}
                    </div>
                </div>
                <div className="row" style={{marginBottom:25}}>
                    <div className="col-md-6 col-md-offset-3 col-xs-8 col-xs-offset-2">
                        <BoxSequence.Slide.Figure rows={4} cols={4} cellText={cellText} onCellClick={this.onCellClick} />
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-6">
                        <button className="btn btn-default pull-right" onClick={this.onClear}>{translate('Clear')}</button>
                    </div>
                    <div className="col-xs-6">
                        <button className="btn btn-default pull-left" onClick={this.onComplete}>{translate('Continue')}</button>
                    </div>
                </div>
            </div>
        );
    }
});

/**
 * The feedback screen.
 * @prop sequence array<point> The original problem sequence
 * @prop response object       The user's response with format
 *                             {res: array<point>, startTime: integer, endTime: integer}
 * @prop onComplete callback
 */
BoxSequence.Feedback = React.createClass({
    getCorrectCount: function() {
        var res = 0;
        var sequence = this.props.sequence;
        var response = this.props.response.res;

        for(var i = 0; i < sequence.length; i++)
            if(response[i] && response[i][0] == sequence[i][0] && response[i][1] == sequence[i][1])
                res++;
        return res;
    },
    render: function() {
        return (
            <div>
                <div className="row">
                    <div className="col-xs-12" style={{fontSize:25, marginBottom:25}}>
                        {translate('You recalled')} {this.getCorrectCount()} {translate('out of')} {this.props.sequence.length} {translate('squares correctly')}.
                    </div>
                </div>
                <div className="row">
                    <button className="btn btn-default" onClick={this.onComplete}>{translate('Continue')}</button>
                </div>
            </div>
        )
    },
    onComplete: function() {
        if(this.props.onComplete)
            this.props.onComplete();
    }
});
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
//# sourceMappingURL=wm.js.map
