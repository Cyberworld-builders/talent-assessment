var ExplodeTask = React.createClass({
    propTypes: {
        task: React.PropTypes.object.isRequired
    },
    nop: function() {
    },
    render: function() {
        return (
            <div>
                <TaskObList task={this.props.task} mode='view' onAddProbClick={this.nop} onProbEdit={this.nop} onProbDel={this.nop} onBlockDel={this.nop} onInstEdit={this.nop} onInstDel={this.nop}/>
            </div>
        );
    }
});