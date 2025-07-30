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