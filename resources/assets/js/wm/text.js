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