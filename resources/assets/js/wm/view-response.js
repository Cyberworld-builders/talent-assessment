var RespView = React.createClass({
    propTypes: {
        task: React.PropTypes.object.isRequired,
        resp: React.PropTypes.array.isRequired
    },
    render: function() {
        return <RespView.BlockList task={this.props.task} resp={this.props.resp}/>
    }
});

RespView.BlockList = React.createClass({
    propTypes: {
        task: React.PropTypes.object.isRequired,
        resp: React.PropTypes.array.isRequired
    },
    render: function() {
        var blocks = this.props.task.blocks.map(function(block, i) {
            return <RespView.Block key={i} blockId={i} probBlock={block} respBlock={this.props.resp[i]}/>
        }.bind(this));

        return <table className="table table-bordered"><RespView.ColHeaders/>{blocks}<RespView.Footer task={this.props.task} respBlocks={this.props.resp}/></table>
    }
});

RespView.Block = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        respBlock: React.PropTypes.array.isRequired
    },
    render: function() {
        return (
            <tbody>
                {/*<RespView.Block.Header blockId={this.props.blockId}/>*/}
                <RespView.Block.Body blockId={this.props.blockId} probBlock={this.props.probBlock} respBlock={this.props.respBlock}/>
            </tbody>
        )
    }
});

RespView.Block.Header = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired
    },
    render: function() {
        return <tr><td colSpan="4" style={{textAlign:'center'}}><b>Block {this.props.blockId + 1}</b></td></tr>
    }
});

RespView.Block.Body = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        respBlock: React.PropTypes.array.isRequired
    },
    render: function() {
        var r = this.props.probBlock.problems.map(function(prob, i){
            switch(prob.type) {
                case LS.typeId: return <RespView.LSProblem key={i} blockId={this.props.blockId} probBlock={this.props.probBlock} prob={prob} resp={this.props.respBlock[i]}/>
                case EQ.typeId: return <RespView.EQProblem key={i} blockId={this.props.blockId} probBlock={this.props.probBlock} prob={prob} resp={this.props.respBlock[i]}/>
                case EQLS.typeId: return <RespView.EQLSProblem key={i} blockId={this.props.blockId} probBlock={this.props.probBlock} prob={prob} resp={this.props.respBlock[i]}/>
                case SQ.typeId: return <RespView.SQProblem key={i} blockId={this.props.blockId} probBlock={this.props.probBlock} prob={prob} resp={this.props.respBlock[i]}/>
                case SY.typeId: return <RespView.SYProblem key={i} blockId={this.props.blockId} probBlock={this.props.probBlock} prob={prob} resp={this.props.respBlock[i]}/>
                case SYSQ.typeId: return <RespView.SYSQProblem key={i} blockId={this.props.blockId} probBlock={this.props.probBlock} prob={prob} resp={this.props.respBlock[i]}/>
            }
        }.bind(this));

        return <tbody>{r}</tbody>
    }
});

RespView.LSProblem = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        prob: React.PropTypes.object.isRequired,
        resp: React.PropTypes.object.isRequired
    },
    render: function() {
        return (
            <tr>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.blockId + 1}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.prob.id + 1}</td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.LettersWidget letters={this.props.prob.letters} onProbEdit={nop}/> <Block.Table.Row.LettersWidget letters={this.props.resp.options} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.LettersWidget letters={this.props.resp.response} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.time / 1000}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>
                    {this.props.probBlock.practice ? 'Practice' : LS.getScore(this.props.prob.letters, this.props.resp.response)}
                </td>
            </tr>
        )
    }
});

RespView.EQProblem = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        prob: React.PropTypes.object.isRequired,
        resp: React.PropTypes.object.isRequired
    },
    render: function() {
        return (
            <tr>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.blockId + 1}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.prob.id + 1}</td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.MathWidget equation={this.props.prob.equation} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.response.toString().capitalize()}</td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.time / 1000}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>
                    {this.props.probBlock.practice ? 'Practice' : EQ.getScore(this.props.prob.equation, this.props.resp.response)}
                </td>
            </tr>
        )
    }
});

RespView.EQLSProblem = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        prob: React.PropTypes.object.isRequired,
        resp: React.PropTypes.object.isRequired
    },
    render: function() {
        var eq = this.props.prob.equations.map(function(equation, i) {
            return <RespView.EQLSProblem.EQ key={i} blockId={this.props.blockId} probBlock={this.props.probBlock} prob={this.props.prob} resp={this.props.resp} eqid={i}/>
        }.bind(this));

        return (
            <tbody>
                <RespView.EQLSProblem.LS blockId={this.props.blockId} probBlock={this.props.probBlock} prob={this.props.prob} resp={this.props.resp}/>
                {eq}
            </tbody>
        )
    }
});

RespView.EQLSProblem.LS = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        prob: React.PropTypes.object.isRequired,
        resp: React.PropTypes.object.isRequired
    },
    render: function () {
        return (
            <tr>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.blockId + 1}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.prob.id + 1}</td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.LettersWidget letters={this.props.prob.letters} onProbEdit={nop}/> <Block.Table.Row.LettersWidget letters={this.props.resp.letters.options} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.LettersWidget letters={this.props.resp.letters.response} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.letters.time / 1000}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>
                    {this.props.probBlock.practice ? 'Practice' : LS.getScore(this.props.prob.letters, this.props.resp.letters.response)}
                </td>
            </tr>
        )
    }
});

RespView.EQLSProblem.EQ = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        prob: React.PropTypes.object.isRequired,
        resp: React.PropTypes.object.isRequired,
        eqid: React.PropTypes.number.isRequired
    },
    render: function() {
        return (
            <tr>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.blockId + 1}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.prob.id + 1}</td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.MathWidget equation={this.props.prob.equations[this.props.eqid]} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.equations[this.props.eqid].response.toString().capitalize()}</td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.equations[this.props.eqid].time / 1000}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>
                    {this.props.probBlock.practice ? 'Practice' : ' - '}</td>
            </tr>
        )
    }
});

RespView.SQProblem = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        prob: React.PropTypes.object.isRequired,
        resp: React.PropTypes.object.isRequired
    },
    render: function() {
        return (
            <tr>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.blockId + 1}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.prob.id + 1}</td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.SquaresWidget squares={this.props.prob.squares} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.SquaresWidget squares={this.props.resp.response} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.time / 1000}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>
                    {this.props.probBlock.practice ? 'Practice' : SQ.getScore(this.props.prob.squares, this.props.resp.response)}
                </td>
            </tr>
        )
    }
});

RespView.SYProblem = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        prob: React.PropTypes.object.isRequired,
        resp: React.PropTypes.object.isRequired
    },
    render: function() {
        return (
            <tr>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.blockId + 1}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.prob.id + 1}</td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.SymmetryWidget symmetry={this.props.prob.symmetry} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.response ? 'Symmetric' : 'Asymmetric'}</td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.time / 1000}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>
                    {this.props.probBlock.practice ? 'Practice' : SY.getScore(this.props.prob.symmetry, this.props.resp.response)}
                </td>
            </tr>
        )
    }
});

RespView.SYSQProblem = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        prob: React.PropTypes.object.isRequired,
        resp: React.PropTypes.object.isRequired
    },
    render: function() {
        var sy = this.props.prob.symmetries.map(function(symmetry, i) {
            return <RespView.SYSQProblem.SY key={i} blockId={this.props.blockId} probBlock={this.props.probBlock} prob={this.props.prob} resp={this.props.resp} syid={i}/>
        }.bind(this));

        return (
            <tbody>
                <RespView.SYSQProblem.SQ blockId={this.props.blockId} probBlock={this.props.probBlock} prob={this.props.prob} resp={this.props.resp}/>
                {sy}
            </tbody>
        )
    }
});

RespView.SYSQProblem.SQ = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        prob: React.PropTypes.object.isRequired,
        resp: React.PropTypes.object.isRequired
    },
    render: function () {
        return (
            <tr>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.blockId + 1}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.prob.id + 1}</td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.SquaresWidget squares={this.props.prob.squares} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.SquaresWidget squares={this.props.resp.squares.response} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.squares.time / 1000}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>
                    {this.props.probBlock.practice ? 'Practice' : SQ.getScore(this.props.prob.squares, this.props.resp.squares.response)}
                </td>
            </tr>
        )
    }
});

RespView.SYSQProblem.SY = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        probBlock: React.PropTypes.object.isRequired,
        prob: React.PropTypes.object.isRequired,
        resp: React.PropTypes.object.isRequired,
        syid: React.PropTypes.number.isRequired
    },
    render: function() {
        return (
            <tr>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.blockId + 1}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>{this.props.prob.id + 1}</td>
                <td style={{verticalAlign:'middle'}}><Block.Table.Row.SymmetryWidget symmetry={this.props.prob.symmetries[this.props.syid]} onProbEdit={nop}/></td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.symmetries[this.props.syid].response ? 'Symmetric' : 'Asymmetric'}</td>
                <td style={{verticalAlign:'middle'}}>{this.props.resp.symmetries[this.props.syid].time / 1000}</td>
                <td style={{verticalAlign:'middle', textAlign:'center'}}>
                    {this.props.probBlock.practice ? 'Practice' : ' - '}</td>
            </tr>
        )
    }
});

RespView.ColHeaders = React.createClass({
    render: function() {
        return (
            <tr>
                <th style={{textAlign:'center'}}>Block #</th>
                <th style={{textAlign:'center'}}>Prob #</th>
                <th>Problem</th>
                <th>Response</th>
                <th>Time (s)</th>
                <th style={{textAlign:'center'}}>Score</th>
            </tr>
        )
    }
});

RespView.Footer = React.createClass({
    propTypes: {
        task: React.PropTypes.object.isRequired, 
        respBlocks: React.PropTypes.array.isRequired
    },
    render: function() {
        return (
            <tbody>
                <tr>
                    <th colSpan={5} style={{textAlign:'right'}}>Total</th>
                    <td style={{textAlign:'center'}}>
                        {TSK.getScore(this.props.task, this.props.respBlocks)}
                    </td>
                </tr>
                <tr>
                    <th colSpan={5} style={{textAlign:'right'}}>Maximum</th>
                    <td style={{textAlign:'center'}}>
                        {TSK.getMaxScore(this.props.task)}
                    </td>
                </tr>
                <tr>
                <th colSpan={5} style={{textAlign:'right'}}>Percentage</th>
                    <td style={{textAlign:'center'}}>
                        {Math.round(TSK.getScore(this.props.task, this.props.respBlocks) / TSK.getMaxScore(this.props.task) * 100) / 100}
                    </td>
                </tr>
            </tbody>
        )
    }
})