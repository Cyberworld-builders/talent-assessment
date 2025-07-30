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