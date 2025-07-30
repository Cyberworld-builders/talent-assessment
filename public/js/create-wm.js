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
/**
 * marked - a markdown parser
 * Copyright (c) 2011-2014, Christopher Jeffrey. (MIT Licensed)
 * https://github.com/chjj/marked
 */

;(function() {

/**
 * Block-Level Grammar
 */

var block = {
  newline: /^\n+/,
  code: /^( {4}[^\n]+\n*)+/,
  fences: noop,
  hr: /^( *[-*_]){3,} *(?:\n+|$)/,
  heading: /^ *(#{1,6}) *([^\n]+?) *#* *(?:\n+|$)/,
  nptable: noop,
  lheading: /^([^\n]+)\n *(=|-){2,} *(?:\n+|$)/,
  blockquote: /^( *>[^\n]+(\n(?!def)[^\n]+)*\n*)+/,
  list: /^( *)(bull) [\s\S]+?(?:hr|def|\n{2,}(?! )(?!\1bull )\n*|\s*$)/,
  html: /^ *(?:comment *(?:\n|\s*$)|closed *(?:\n{2,}|\s*$)|closing *(?:\n{2,}|\s*$))/,
  def: /^ *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +["(]([^\n]+)[")])? *(?:\n+|$)/,
  table: noop,
  paragraph: /^((?:[^\n]+\n?(?!hr|heading|lheading|blockquote|tag|def))+)\n*/,
  text: /^[^\n]+/
};

block.bullet = /(?:[*+-]|\d+\.)/;
block.item = /^( *)(bull) [^\n]*(?:\n(?!\1bull )[^\n]*)*/;
block.item = replace(block.item, 'gm')
  (/bull/g, block.bullet)
  ();

block.list = replace(block.list)
  (/bull/g, block.bullet)
  ('hr', '\\n+(?=\\1?(?:[-*_] *){3,}(?:\\n+|$))')
  ('def', '\\n+(?=' + block.def.source + ')')
  ();

block.blockquote = replace(block.blockquote)
  ('def', block.def)
  ();

block._tag = '(?!(?:'
  + 'a|em|strong|small|s|cite|q|dfn|abbr|data|time|code'
  + '|var|samp|kbd|sub|sup|i|b|u|mark|ruby|rt|rp|bdi|bdo'
  + '|span|br|wbr|ins|del|img)\\b)\\w+(?!:/|[^\\w\\s@]*@)\\b';

block.html = replace(block.html)
  ('comment', /<!--[\s\S]*?-->/)
  ('closed', /<(tag)[\s\S]+?<\/\1>/)
  ('closing', /<tag(?:"[^"]*"|'[^']*'|[^'">])*?>/)
  (/tag/g, block._tag)
  ();

block.paragraph = replace(block.paragraph)
  ('hr', block.hr)
  ('heading', block.heading)
  ('lheading', block.lheading)
  ('blockquote', block.blockquote)
  ('tag', '<' + block._tag)
  ('def', block.def)
  ();

/**
 * Normal Block Grammar
 */

block.normal = merge({}, block);

/**
 * GFM Block Grammar
 */

block.gfm = merge({}, block.normal, {
  fences: /^ *(`{3,}|~{3,})[ \.]*(\S+)? *\n([\s\S]*?)\s*\1 *(?:\n+|$)/,
  paragraph: /^/,
  heading: /^ *(#{1,6}) +([^\n]+?) *#* *(?:\n+|$)/
});

block.gfm.paragraph = replace(block.paragraph)
  ('(?!', '(?!'
    + block.gfm.fences.source.replace('\\1', '\\2') + '|'
    + block.list.source.replace('\\1', '\\3') + '|')
  ();

/**
 * GFM + Tables Block Grammar
 */

block.tables = merge({}, block.gfm, {
  nptable: /^ *(\S.*\|.*)\n *([-:]+ *\|[-| :]*)\n((?:.*\|.*(?:\n|$))*)\n*/,
  table: /^ *\|(.+)\n *\|( *[-:]+[-| :]*)\n((?: *\|.*(?:\n|$))*)\n*/
});

/**
 * Block Lexer
 */

function Lexer(options) {
  this.tokens = [];
  this.tokens.links = {};
  this.options = options || marked.defaults;
  this.rules = block.normal;

  if (this.options.gfm) {
    if (this.options.tables) {
      this.rules = block.tables;
    } else {
      this.rules = block.gfm;
    }
  }
}

/**
 * Expose Block Rules
 */

Lexer.rules = block;

/**
 * Static Lex Method
 */

Lexer.lex = function(src, options) {
  var lexer = new Lexer(options);
  return lexer.lex(src);
};

/**
 * Preprocessing
 */

Lexer.prototype.lex = function(src) {
  src = src
    .replace(/\r\n|\r/g, '\n')
    .replace(/\t/g, '    ')
    .replace(/\u00a0/g, ' ')
    .replace(/\u2424/g, '\n');

  return this.token(src, true);
};

/**
 * Lexing
 */

Lexer.prototype.token = function(src, top, bq) {
  var src = src.replace(/^ +$/gm, '')
    , next
    , loose
    , cap
    , bull
    , b
    , item
    , space
    , i
    , l;

  while (src) {
    // newline
    if (cap = this.rules.newline.exec(src)) {
      src = src.substring(cap[0].length);
      if (cap[0].length > 1) {
        this.tokens.push({
          type: 'space'
        });
      }
    }

    // code
    if (cap = this.rules.code.exec(src)) {
      src = src.substring(cap[0].length);
      cap = cap[0].replace(/^ {4}/gm, '');
      this.tokens.push({
        type: 'code',
        text: !this.options.pedantic
          ? cap.replace(/\n+$/, '')
          : cap
      });
      continue;
    }

    // fences (gfm)
    if (cap = this.rules.fences.exec(src)) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'code',
        lang: cap[2],
        text: cap[3] || ''
      });
      continue;
    }

    // heading
    if (cap = this.rules.heading.exec(src)) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'heading',
        depth: cap[1].length,
        text: cap[2]
      });
      continue;
    }

    // table no leading pipe (gfm)
    if (top && (cap = this.rules.nptable.exec(src))) {
      src = src.substring(cap[0].length);

      item = {
        type: 'table',
        header: cap[1].replace(/^ *| *\| *$/g, '').split(/ *\| */),
        align: cap[2].replace(/^ *|\| *$/g, '').split(/ *\| */),
        cells: cap[3].replace(/\n$/, '').split('\n')
      };

      for (i = 0; i < item.align.length; i++) {
        if (/^ *-+: *$/.test(item.align[i])) {
          item.align[i] = 'right';
        } else if (/^ *:-+: *$/.test(item.align[i])) {
          item.align[i] = 'center';
        } else if (/^ *:-+ *$/.test(item.align[i])) {
          item.align[i] = 'left';
        } else {
          item.align[i] = null;
        }
      }

      for (i = 0; i < item.cells.length; i++) {
        item.cells[i] = item.cells[i].split(/ *\| */);
      }

      this.tokens.push(item);

      continue;
    }

    // lheading
    if (cap = this.rules.lheading.exec(src)) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'heading',
        depth: cap[2] === '=' ? 1 : 2,
        text: cap[1]
      });
      continue;
    }

    // hr
    if (cap = this.rules.hr.exec(src)) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'hr'
      });
      continue;
    }

    // blockquote
    if (cap = this.rules.blockquote.exec(src)) {
      src = src.substring(cap[0].length);

      this.tokens.push({
        type: 'blockquote_start'
      });

      cap = cap[0].replace(/^ *> ?/gm, '');

      // Pass `top` to keep the current
      // "toplevel" state. This is exactly
      // how markdown.pl works.
      this.token(cap, top, true);

      this.tokens.push({
        type: 'blockquote_end'
      });

      continue;
    }

    // list
    if (cap = this.rules.list.exec(src)) {
      src = src.substring(cap[0].length);
      bull = cap[2];

      this.tokens.push({
        type: 'list_start',
        ordered: bull.length > 1
      });

      // Get each top-level item.
      cap = cap[0].match(this.rules.item);

      next = false;
      l = cap.length;
      i = 0;

      for (; i < l; i++) {
        item = cap[i];

        // Remove the list item's bullet
        // so it is seen as the next token.
        space = item.length;
        item = item.replace(/^ *([*+-]|\d+\.) +/, '');

        // Outdent whatever the
        // list item contains. Hacky.
        if (~item.indexOf('\n ')) {
          space -= item.length;
          item = !this.options.pedantic
            ? item.replace(new RegExp('^ {1,' + space + '}', 'gm'), '')
            : item.replace(/^ {1,4}/gm, '');
        }

        // Determine whether the next list item belongs here.
        // Backpedal if it does not belong in this list.
        if (this.options.smartLists && i !== l - 1) {
          b = block.bullet.exec(cap[i + 1])[0];
          if (bull !== b && !(bull.length > 1 && b.length > 1)) {
            src = cap.slice(i + 1).join('\n') + src;
            i = l - 1;
          }
        }

        // Determine whether item is loose or not.
        // Use: /(^|\n)(?! )[^\n]+\n\n(?!\s*$)/
        // for discount behavior.
        loose = next || /\n\n(?!\s*$)/.test(item);
        if (i !== l - 1) {
          next = item.charAt(item.length - 1) === '\n';
          if (!loose) loose = next;
        }

        this.tokens.push({
          type: loose
            ? 'loose_item_start'
            : 'list_item_start'
        });

        // Recurse.
        this.token(item, false, bq);

        this.tokens.push({
          type: 'list_item_end'
        });
      }

      this.tokens.push({
        type: 'list_end'
      });

      continue;
    }

    // html
    if (cap = this.rules.html.exec(src)) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: this.options.sanitize
          ? 'paragraph'
          : 'html',
        pre: !this.options.sanitizer
          && (cap[1] === 'pre' || cap[1] === 'script' || cap[1] === 'style'),
        text: cap[0]
      });
      continue;
    }

    // def
    if ((!bq && top) && (cap = this.rules.def.exec(src))) {
      src = src.substring(cap[0].length);
      this.tokens.links[cap[1].toLowerCase()] = {
        href: cap[2],
        title: cap[3]
      };
      continue;
    }

    // table (gfm)
    if (top && (cap = this.rules.table.exec(src))) {
      src = src.substring(cap[0].length);

      item = {
        type: 'table',
        header: cap[1].replace(/^ *| *\| *$/g, '').split(/ *\| */),
        align: cap[2].replace(/^ *|\| *$/g, '').split(/ *\| */),
        cells: cap[3].replace(/(?: *\| *)?\n$/, '').split('\n')
      };

      for (i = 0; i < item.align.length; i++) {
        if (/^ *-+: *$/.test(item.align[i])) {
          item.align[i] = 'right';
        } else if (/^ *:-+: *$/.test(item.align[i])) {
          item.align[i] = 'center';
        } else if (/^ *:-+ *$/.test(item.align[i])) {
          item.align[i] = 'left';
        } else {
          item.align[i] = null;
        }
      }

      for (i = 0; i < item.cells.length; i++) {
        item.cells[i] = item.cells[i]
          .replace(/^ *\| *| *\| *$/g, '')
          .split(/ *\| */);
      }

      this.tokens.push(item);

      continue;
    }

    // top-level paragraph
    if (top && (cap = this.rules.paragraph.exec(src))) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'paragraph',
        text: cap[1].charAt(cap[1].length - 1) === '\n'
          ? cap[1].slice(0, -1)
          : cap[1]
      });
      continue;
    }

    // text
    if (cap = this.rules.text.exec(src)) {
      // Top-level should never reach here.
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'text',
        text: cap[0]
      });
      continue;
    }

    if (src) {
      throw new
        Error('Infinite loop on byte: ' + src.charCodeAt(0));
    }
  }

  return this.tokens;
};

/**
 * Inline-Level Grammar
 */

var inline = {
  escape: /^\\([\\`*{}\[\]()#+\-.!_>])/,
  autolink: /^<([^ >]+(@|:\/)[^ >]+)>/,
  url: noop,
  tag: /^<!--[\s\S]*?-->|^<\/?\w+(?:"[^"]*"|'[^']*'|[^'">])*?>/,
  link: /^!?\[(inside)\]\(href\)/,
  reflink: /^!?\[(inside)\]\s*\[([^\]]*)\]/,
  nolink: /^!?\[((?:\[[^\]]*\]|[^\[\]])*)\]/,
  strong: /^__([\s\S]+?)__(?!_)|^\*\*([\s\S]+?)\*\*(?!\*)/,
  em: /^\b_((?:[^_]|__)+?)_\b|^\*((?:\*\*|[\s\S])+?)\*(?!\*)/,
  code: /^(`+)\s*([\s\S]*?[^`])\s*\1(?!`)/,
  br: /^ {2,}\n(?!\s*$)/,
  del: noop,
  text: /^[\s\S]+?(?=[\\<!\[_*`]| {2,}\n|$)/
};

inline._inside = /(?:\[[^\]]*\]|[^\[\]]|\](?=[^\[]*\]))*/;
inline._href = /\s*<?([\s\S]*?)>?(?:\s+['"]([\s\S]*?)['"])?\s*/;

inline.link = replace(inline.link)
  ('inside', inline._inside)
  ('href', inline._href)
  ();

inline.reflink = replace(inline.reflink)
  ('inside', inline._inside)
  ();

/**
 * Normal Inline Grammar
 */

inline.normal = merge({}, inline);

/**
 * Pedantic Inline Grammar
 */

inline.pedantic = merge({}, inline.normal, {
  strong: /^__(?=\S)([\s\S]*?\S)__(?!_)|^\*\*(?=\S)([\s\S]*?\S)\*\*(?!\*)/,
  em: /^_(?=\S)([\s\S]*?\S)_(?!_)|^\*(?=\S)([\s\S]*?\S)\*(?!\*)/
});

/**
 * GFM Inline Grammar
 */

inline.gfm = merge({}, inline.normal, {
  escape: replace(inline.escape)('])', '~|])')(),
  url: /^(https?:\/\/[^\s<]+[^<.,:;"')\]\s])/,
  del: /^~~(?=\S)([\s\S]*?\S)~~/,
  text: replace(inline.text)
    (']|', '~]|')
    ('|', '|https?://|')
    ()
});

/**
 * GFM + Line Breaks Inline Grammar
 */

inline.breaks = merge({}, inline.gfm, {
  br: replace(inline.br)('{2,}', '*')(),
  text: replace(inline.gfm.text)('{2,}', '*')()
});

/**
 * Inline Lexer & Compiler
 */

function InlineLexer(links, options) {
  this.options = options || marked.defaults;
  this.links = links;
  this.rules = inline.normal;
  this.renderer = this.options.renderer || new Renderer;
  this.renderer.options = this.options;

  if (!this.links) {
    throw new
      Error('Tokens array requires a `links` property.');
  }

  if (this.options.gfm) {
    if (this.options.breaks) {
      this.rules = inline.breaks;
    } else {
      this.rules = inline.gfm;
    }
  } else if (this.options.pedantic) {
    this.rules = inline.pedantic;
  }
}

/**
 * Expose Inline Rules
 */

InlineLexer.rules = inline;

/**
 * Static Lexing/Compiling Method
 */

InlineLexer.output = function(src, links, options) {
  var inline = new InlineLexer(links, options);
  return inline.output(src);
};

/**
 * Lexing/Compiling
 */

InlineLexer.prototype.output = function(src) {
  var out = ''
    , link
    , text
    , href
    , cap;

  while (src) {
    // escape
    if (cap = this.rules.escape.exec(src)) {
      src = src.substring(cap[0].length);
      out += cap[1];
      continue;
    }

    // autolink
    if (cap = this.rules.autolink.exec(src)) {
      src = src.substring(cap[0].length);
      if (cap[2] === '@') {
        text = cap[1].charAt(6) === ':'
          ? this.mangle(cap[1].substring(7))
          : this.mangle(cap[1]);
        href = this.mangle('mailto:') + text;
      } else {
        text = escape(cap[1]);
        href = text;
      }
      out += this.renderer.link(href, null, text);
      continue;
    }

    // url (gfm)
    if (!this.inLink && (cap = this.rules.url.exec(src))) {
      src = src.substring(cap[0].length);
      text = escape(cap[1]);
      href = text;
      out += this.renderer.link(href, null, text);
      continue;
    }

    // tag
    if (cap = this.rules.tag.exec(src)) {
      if (!this.inLink && /^<a /i.test(cap[0])) {
        this.inLink = true;
      } else if (this.inLink && /^<\/a>/i.test(cap[0])) {
        this.inLink = false;
      }
      src = src.substring(cap[0].length);
      out += this.options.sanitize
        ? this.options.sanitizer
          ? this.options.sanitizer(cap[0])
          : escape(cap[0])
        : cap[0]
      continue;
    }

    // link
    if (cap = this.rules.link.exec(src)) {
      src = src.substring(cap[0].length);
      this.inLink = true;
      out += this.outputLink(cap, {
        href: cap[2],
        title: cap[3]
      });
      this.inLink = false;
      continue;
    }

    // reflink, nolink
    if ((cap = this.rules.reflink.exec(src))
        || (cap = this.rules.nolink.exec(src))) {
      src = src.substring(cap[0].length);
      link = (cap[2] || cap[1]).replace(/\s+/g, ' ');
      link = this.links[link.toLowerCase()];
      if (!link || !link.href) {
        out += cap[0].charAt(0);
        src = cap[0].substring(1) + src;
        continue;
      }
      this.inLink = true;
      out += this.outputLink(cap, link);
      this.inLink = false;
      continue;
    }

    // strong
    if (cap = this.rules.strong.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.strong(this.output(cap[2] || cap[1]));
      continue;
    }

    // em
    if (cap = this.rules.em.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.em(this.output(cap[2] || cap[1]));
      continue;
    }

    // code
    if (cap = this.rules.code.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.codespan(escape(cap[2], true));
      continue;
    }

    // br
    if (cap = this.rules.br.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.br();
      continue;
    }

    // del (gfm)
    if (cap = this.rules.del.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.del(this.output(cap[1]));
      continue;
    }

    // text
    if (cap = this.rules.text.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.text(escape(this.smartypants(cap[0])));
      continue;
    }

    if (src) {
      throw new
        Error('Infinite loop on byte: ' + src.charCodeAt(0));
    }
  }

  return out;
};

/**
 * Compile Link
 */

InlineLexer.prototype.outputLink = function(cap, link) {
  var href = escape(link.href)
    , title = link.title ? escape(link.title) : null;

  return cap[0].charAt(0) !== '!'
    ? this.renderer.link(href, title, this.output(cap[1]))
    : this.renderer.image(href, title, escape(cap[1]));
};

/**
 * Smartypants Transformations
 */

InlineLexer.prototype.smartypants = function(text) {
  if (!this.options.smartypants) return text;
  return text
    // em-dashes
    .replace(/---/g, '\u2014')
    // en-dashes
    .replace(/--/g, '\u2013')
    // opening singles
    .replace(/(^|[-\u2014/(\[{"\s])'/g, '$1\u2018')
    // closing singles & apostrophes
    .replace(/'/g, '\u2019')
    // opening doubles
    .replace(/(^|[-\u2014/(\[{\u2018\s])"/g, '$1\u201c')
    // closing doubles
    .replace(/"/g, '\u201d')
    // ellipses
    .replace(/\.{3}/g, '\u2026');
};

/**
 * Mangle Links
 */

InlineLexer.prototype.mangle = function(text) {
  if (!this.options.mangle) return text;
  var out = ''
    , l = text.length
    , i = 0
    , ch;

  for (; i < l; i++) {
    ch = text.charCodeAt(i);
    if (Math.random() > 0.5) {
      ch = 'x' + ch.toString(16);
    }
    out += '&#' + ch + ';';
  }

  return out;
};

/**
 * Renderer
 */

function Renderer(options) {
  this.options = options || {};
}

Renderer.prototype.code = function(code, lang, escaped) {
  if (this.options.highlight) {
    var out = this.options.highlight(code, lang);
    if (out != null && out !== code) {
      escaped = true;
      code = out;
    }
  }

  if (!lang) {
    return '<pre><code>'
      + (escaped ? code : escape(code, true))
      + '\n</code></pre>';
  }

  return '<pre><code class="'
    + this.options.langPrefix
    + escape(lang, true)
    + '">'
    + (escaped ? code : escape(code, true))
    + '\n</code></pre>\n';
};

Renderer.prototype.blockquote = function(quote) {
  return '<blockquote>\n' + quote + '</blockquote>\n';
};

Renderer.prototype.html = function(html) {
  return html;
};

Renderer.prototype.heading = function(text, level, raw) {
  return '<h'
    + level
    + ' id="'
    + this.options.headerPrefix
    + raw.toLowerCase().replace(/[^\w]+/g, '-')
    + '">'
    + text
    + '</h'
    + level
    + '>\n';
};

Renderer.prototype.hr = function() {
  return this.options.xhtml ? '<hr/>\n' : '<hr>\n';
};

Renderer.prototype.list = function(body, ordered) {
  var type = ordered ? 'ol' : 'ul';
  return '<' + type + '>\n' + body + '</' + type + '>\n';
};

Renderer.prototype.listitem = function(text) {
  return '<li>' + text + '</li>\n';
};

Renderer.prototype.paragraph = function(text) {
  return '<p>' + text + '</p>\n';
};

Renderer.prototype.table = function(header, body) {
  return '<table>\n'
    + '<thead>\n'
    + header
    + '</thead>\n'
    + '<tbody>\n'
    + body
    + '</tbody>\n'
    + '</table>\n';
};

Renderer.prototype.tablerow = function(content) {
  return '<tr>\n' + content + '</tr>\n';
};

Renderer.prototype.tablecell = function(content, flags) {
  var type = flags.header ? 'th' : 'td';
  var tag = flags.align
    ? '<' + type + ' style="text-align:' + flags.align + '">'
    : '<' + type + '>';
  return tag + content + '</' + type + '>\n';
};

// span level renderer
Renderer.prototype.strong = function(text) {
  return '<strong>' + text + '</strong>';
};

Renderer.prototype.em = function(text) {
  return '<em>' + text + '</em>';
};

Renderer.prototype.codespan = function(text) {
  return '<code>' + text + '</code>';
};

Renderer.prototype.br = function() {
  return this.options.xhtml ? '<br/>' : '<br>';
};

Renderer.prototype.del = function(text) {
  return '<del>' + text + '</del>';
};

Renderer.prototype.link = function(href, title, text) {
  if (this.options.sanitize) {
    try {
      var prot = decodeURIComponent(unescape(href))
        .replace(/[^\w:]/g, '')
        .toLowerCase();
    } catch (e) {
      return '';
    }
    if (prot.indexOf('javascript:') === 0 || prot.indexOf('vbscript:') === 0) {
      return '';
    }
  }
  var out = '<a href="' + href + '"';
  if (title) {
    out += ' title="' + title + '"';
  }
  out += '>' + text + '</a>';
  return out;
};

Renderer.prototype.image = function(href, title, text) {
  var out = '<img src="' + href + '" alt="' + text + '"';
  if (title) {
    out += ' title="' + title + '"';
  }
  out += this.options.xhtml ? '/>' : '>';
  return out;
};

Renderer.prototype.text = function(text) {
  return text;
};

/**
 * Parsing & Compiling
 */

function Parser(options) {
  this.tokens = [];
  this.token = null;
  this.options = options || marked.defaults;
  this.options.renderer = this.options.renderer || new Renderer;
  this.renderer = this.options.renderer;
  this.renderer.options = this.options;
}

/**
 * Static Parse Method
 */

Parser.parse = function(src, options, renderer) {
  var parser = new Parser(options, renderer);
  return parser.parse(src);
};

/**
 * Parse Loop
 */

Parser.prototype.parse = function(src) {
  this.inline = new InlineLexer(src.links, this.options, this.renderer);
  this.tokens = src.reverse();

  var out = '';
  while (this.next()) {
    out += this.tok();
  }

  return out;
};

/**
 * Next Token
 */

Parser.prototype.next = function() {
  return this.token = this.tokens.pop();
};

/**
 * Preview Next Token
 */

Parser.prototype.peek = function() {
  return this.tokens[this.tokens.length - 1] || 0;
};

/**
 * Parse Text Tokens
 */

Parser.prototype.parseText = function() {
  var body = this.token.text;

  while (this.peek().type === 'text') {
    body += '\n' + this.next().text;
  }

  return this.inline.output(body);
};

/**
 * Parse Current Token
 */

Parser.prototype.tok = function() {
  switch (this.token.type) {
    case 'space': {
      return '';
    }
    case 'hr': {
      return this.renderer.hr();
    }
    case 'heading': {
      return this.renderer.heading(
        this.inline.output(this.token.text),
        this.token.depth,
        this.token.text);
    }
    case 'code': {
      return this.renderer.code(this.token.text,
        this.token.lang,
        this.token.escaped);
    }
    case 'table': {
      var header = ''
        , body = ''
        , i
        , row
        , cell
        , flags
        , j;

      // header
      cell = '';
      for (i = 0; i < this.token.header.length; i++) {
        flags = { header: true, align: this.token.align[i] };
        cell += this.renderer.tablecell(
          this.inline.output(this.token.header[i]),
          { header: true, align: this.token.align[i] }
        );
      }
      header += this.renderer.tablerow(cell);

      for (i = 0; i < this.token.cells.length; i++) {
        row = this.token.cells[i];

        cell = '';
        for (j = 0; j < row.length; j++) {
          cell += this.renderer.tablecell(
            this.inline.output(row[j]),
            { header: false, align: this.token.align[j] }
          );
        }

        body += this.renderer.tablerow(cell);
      }
      return this.renderer.table(header, body);
    }
    case 'blockquote_start': {
      var body = '';

      while (this.next().type !== 'blockquote_end') {
        body += this.tok();
      }

      return this.renderer.blockquote(body);
    }
    case 'list_start': {
      var body = ''
        , ordered = this.token.ordered;

      while (this.next().type !== 'list_end') {
        body += this.tok();
      }

      return this.renderer.list(body, ordered);
    }
    case 'list_item_start': {
      var body = '';

      while (this.next().type !== 'list_item_end') {
        body += this.token.type === 'text'
          ? this.parseText()
          : this.tok();
      }

      return this.renderer.listitem(body);
    }
    case 'loose_item_start': {
      var body = '';

      while (this.next().type !== 'list_item_end') {
        body += this.tok();
      }

      return this.renderer.listitem(body);
    }
    case 'html': {
      var html = !this.token.pre && !this.options.pedantic
        ? this.inline.output(this.token.text)
        : this.token.text;
      return this.renderer.html(html);
    }
    case 'paragraph': {
      return this.renderer.paragraph(this.inline.output(this.token.text));
    }
    case 'text': {
      return this.renderer.paragraph(this.parseText());
    }
  }
};

/**
 * Helpers
 */

function escape(html, encode) {
  return html
    .replace(!encode ? /&(?!#?\w+;)/g : /&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function unescape(html) {
  return html.replace(/&([#\w]+);/g, function(_, n) {
    n = n.toLowerCase();
    if (n === 'colon') return ':';
    if (n.charAt(0) === '#') {
      return n.charAt(1) === 'x'
        ? String.fromCharCode(parseInt(n.substring(2), 16))
        : String.fromCharCode(+n.substring(1));
    }
    return '';
  });
}

function replace(regex, opt) {
  regex = regex.source;
  opt = opt || '';
  return function self(name, val) {
    if (!name) return new RegExp(regex, opt);
    val = val.source || val;
    val = val.replace(/(^|[^\[])\^/g, '$1');
    regex = regex.replace(name, val);
    return self;
  };
}

function noop() {}
noop.exec = noop;

function merge(obj) {
  var i = 1
    , target
    , key;

  for (; i < arguments.length; i++) {
    target = arguments[i];
    for (key in target) {
      if (Object.prototype.hasOwnProperty.call(target, key)) {
        obj[key] = target[key];
      }
    }
  }

  return obj;
}


/**
 * Marked
 */

function marked(src, opt, callback) {
  if (callback || typeof opt === 'function') {
    if (!callback) {
      callback = opt;
      opt = null;
    }

    opt = merge({}, marked.defaults, opt || {});

    var highlight = opt.highlight
      , tokens
      , pending
      , i = 0;

    try {
      tokens = Lexer.lex(src, opt)
    } catch (e) {
      return callback(e);
    }

    pending = tokens.length;

    var done = function(err) {
      if (err) {
        opt.highlight = highlight;
        return callback(err);
      }

      var out;

      try {
        out = Parser.parse(tokens, opt);
      } catch (e) {
        err = e;
      }

      opt.highlight = highlight;

      return err
        ? callback(err)
        : callback(null, out);
    };

    if (!highlight || highlight.length < 3) {
      return done();
    }

    delete opt.highlight;

    if (!pending) return done();

    for (; i < tokens.length; i++) {
      (function(token) {
        if (token.type !== 'code') {
          return --pending || done();
        }
        return highlight(token.text, token.lang, function(err, code) {
          if (err) return done(err);
          if (code == null || code === token.text) {
            return --pending || done();
          }
          token.text = code;
          token.escaped = true;
          --pending || done();
        });
      })(tokens[i]);
    }

    return;
  }
  try {
    if (opt) opt = merge({}, marked.defaults, opt);
    return Parser.parse(Lexer.lex(src, opt), opt);
  } catch (e) {
    e.message += '\nPlease report this to https://github.com/chjj/marked.';
    if ((opt || marked.defaults).silent) {
      return '<p>An error occured:</p><pre>'
        + escape(e.message + '', true)
        + '</pre>';
    }
    throw e;
  }
}

/**
 * Options
 */

marked.options =
marked.setOptions = function(opt) {
  merge(marked.defaults, opt);
  return marked;
};

marked.defaults = {
  gfm: true,
  tables: true,
  breaks: false,
  pedantic: false,
  sanitize: false,
  sanitizer: null,
  mangle: true,
  smartLists: false,
  silent: false,
  highlight: null,
  langPrefix: 'lang-',
  smartypants: false,
  headerPrefix: '',
  renderer: new Renderer,
  xhtml: false
};

/**
 * Expose
 */

marked.Parser = Parser;
marked.parser = Parser.parse;

marked.Renderer = Renderer;

marked.Lexer = Lexer;
marked.lexer = Lexer.lex;

marked.InlineLexer = InlineLexer;
marked.inlineLexer = InlineLexer.output;

marked.parse = marked;

if (typeof module !== 'undefined' && typeof exports === 'object') {
  module.exports = marked;
} else if (typeof define === 'function' && define.amd) {
  define(function() { return marked; });
} else {
  this.marked = marked;
}

}).call(function() {
  return this || (typeof window !== 'undefined' ? window : global);
}());

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
var taskTemplate = {

};

taskTemplate.ospan = {
    blocks: [
        {
            practice: true,
            problems: [
                {id: 0, type: LS.typeId, letters: ['G', 'H']},
                {id: 1, type: LS.typeId, letters: ['P', 'F', 'D']},
                {id: 2, type: LS.typeId, letters: ['V', 'R', 'S', 'N']}
            ]
        },
        {
            practice: true,
            problems: [
                {id: 0, type: EQ.typeId, equation: '(2*4)+1=5'},
                {id: 1, type: EQ.typeId, equation: '(24/2)-6=1'},
                {id: 2, type: EQ.typeId, equation: '(10/2)+2=6'},
                {id: 3, type: EQ.typeId, equation: '(2*3)-3=3'},
                {id: 4, type: EQ.typeId, equation: '(2*2)+2=6'},
                {id: 5, type: EQ.typeId, equation: '(7/7)+7=8'}
            ]
        },
        {
            practice: true,
            problems: [
                {
                    id: 0,
                    type: EQLS.typeId,
                    letters: ['G', 'H'],
                    equations: [
                        '(10*2)-10=10',
                        '(1*2)+1=2'
                    ]
                },
                {
                    id: 1,
                    type: EQLS.typeId,
                    letters: ['P', 'F', 'D'],
                    equations: [
                        '(5*2)-10=10',
                        '(10*3)+1=15',
                        '(10/5)+5=7'
                    ]
                },
                {
                    id: 2,
                    type: EQLS.typeId,
                    letters: ['V', 'R', 'S', 'N'],
                    equations: [
                        '(6*2)-10=3',
                        '(6/3)+3=5',
                        '(7*2)-7=7',
                        '(8*2)-1=16'
                    ]
                }
            ]
        },
        {
            practice: false,
            problems: [
                {
                    id: 0,
                    type: EQLS.typeId,
                    letters: ['D', 'L', 'P'],
                    equations: [
                        '(9*2)-10=8',
                        '(3*4)+5=30',
                        '(5*4)-19=1'
                    ]
                },
                {
                    id: 1,
                    type: EQLS.typeId,
                    letters: ['P', 'L', 'D', 'F'],
                    equations: [
                        '(25*2)-10=20',
                        '(10*10)-10=90',
                        '(4*5)+5=15',
                        '(10/5)+5=7'
                    ]
                },
                {
                    id: 2,
                    type: EQLS.typeId,
                    letters: ['P', 'R', 'S', 'Y', 'N'],
                    equations: [
                        '(10/2)+6=4',
                        '(8*3)-8=16',
                        '(6/2)-1=2',
                        '(3*12)+3=12',
                        '(6*8)-2=20'
                    ]
                },
                {
                    id: 3,
                    type: EQLS.typeId,
                    letters: ['Q', 'X', 'Z', 'D', 'C', 'V'],
                    equations: [
                        '(3*5)-10=8',
                        '(15*2)-15=0',
                        '(5*2)-3=5',
                        '(10/2)+6=11',
                        '(4/2)-1=10',
                        '(8*6)+5=53'
                    ]
                },
                {
                    id: 4,
                    type: EQLS.typeId,
                    letters: ['S', 'F', 'G', 'H', 'J', 'K', 'L'],
                    equations: [
                        '(10/5)-2=1',
                        '(12/3)-4=0',
                        '(4*4)+4=20',
                        '(12/4)-3=4',
                        '(25*2)-10=20',
                        '(8*6)-8=30',
                        '(5/5)+2=1'
                    ]
                }
            ]
        },
        {
            practice: false,
            problems: [
                {
                    id: 0,
                    type: EQLS.typeId,
                    letters: ['G', 'W', 'J'],
                    equations: [
                        '(9*2)-10=8',
                        '(3*4)+5=30',
                        '(5*4)-3=17'
                    ]
                },
                {
                    id: 1,
                    type: EQLS.typeId,
                    letters: ['D', 'L', 'T', 'Q'],
                    equations: [
                        '(25*2)-10=20',
                        '(4*1)+20=24',
                        '(4*5)+5=15',
                        '(10/5)+5=7'
                    ]
                },
                {
                    id: 2,
                    type: EQLS.typeId,
                    letters: ['R', 'F', 'V', 'S', 'W'],
                    equations: [
                        '(10/2)+6=4',
                        '(8*3)-8=16',
                        '(6/2)-1=2',
                        '(3*12)+3=12',
                        '(6*8)-2=20'
                    ]
                },
                {
                    id: 3,
                    type: EQLS.typeId,
                    letters: ['T', 'X', 'G', 'D', 'C', 'V'],
                    equations: [
                        '(13*2)-10=14',
                        '(15*2)-20=0',
                        '(5*2)-3=5',
                        '(10*8)+2=82',
                        '(4/2)-1=10',
                        '(8*6)+5=53'
                    ]
                },
                {
                    id: 4,
                    type: EQLS.typeId,
                    letters: ['R', 'W', 'V', 'H', 'Q', 'K', 'P'],
                    equations: [
                        '(10/5)-2=1',
                        '(3*3)-5=4',
                        '(4/4)+4=5',
                        '(12/4)-3=4',
                        '(25*2)-10=20',
                        '(8/2)+4=6',
                        '(5/5)+2=1'
                    ]
                }
            ]
        }
    ],
    instructs: [
        {
            text: 'Welcome. The task you will be completing today involves two things: remembering letters and solving simple math problems. First, you will practice each part before you begin the task. Please read the instructions carefully so you know how to do the task.\n\nPlease click the CONTINUE button to begin',
            next: 'Continue'
        },
        {
            text: 'You will now practice remembering letters.  You will see one letter at a time presented and your goal is to remember the letters in the exact order that they appeared on the screen. After a set of letters is presented, you will see a screen with 12 possible letters.  You will click on each letter in the order you think they were presented to you. A number will appear next to the letter to indicate its position. For example, 1 will appear for the first letter, a 2 for the second letter and so on. If you need to change or adjust the order of the letters, please use the CLEAR button.\n\nOnce you think you have all the letters in the correct order, click CONTINUE to see the next set of letters.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        {
        
            text: 'You have completed the practice.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        { //5
            text: 'You will now practice solving the simple math problems. You will see a math problem such as (2x2) + 3 = 5? Presented on the screen. Your goal is solve the problem and indicate whether the number after the = sign is true or false. In this example, (2x2) + 3 does not equal 5, so you would click the FALSE button.\n\nAfter you click on the TRUE or FALSE button, you will see whether your answer was correct or incorrect. Your goal is to solve each problem correctly as quickly as you can.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        { //7
        
            text: 'You have completed the practice.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        { //8
            text: 'You will now practice remembering letters and solving math problems together. This practice will prepare you to complete the task and it will be more challenging than doing each part alone.\n\nFirst you will see a math problem to solve it by clicking on TRUE or FALSE. Next, you will see a letter. You will need to remember the letter. You will see several math problem and letter combinations in a set. After a set, you will be presented with 12 letters on the screen. As before, you will click on each letter in the order you believe they appeared on the screen.\n\nWe will keep track of your responses. If you take too long to respond to the math problem, the task will move on to the next letter and your math will be marked as incorrect. If you answer several incorrect math problems, you will receive a message that you have too many math errors.\n\nYour goal is to solve the math problem correctly as quickly as possible AND remember each of the letters in the exact order they appeared on the screen.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        { //10
            text: 'You have completed the practice session. You are now ready to complete the task.\n\nYour goal is to solve the math problem correctly as quickly as possible AND remember each of the letters in the exact order they appeared on the screen.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        { //11
            text: 'You are now ready to begin the task.\n\nYour goal is to solve the math problem correctly as quickly as possible AND remember each of the letters in the exact order they appeared on the screen.\n\nThe task can be challenging, and we ask you to try to do your best.\n\nPlease click START to move to next part of the task.',
            next: 'Start'
        }
    ],
    struct: [
        {type: 'inst',  id: 0},
        {type: 'inst',  id: 1},
        {type: 'block', id: 0},
        {type: 'inst',  id: 2},
        {type: 'inst',  id: 3},
        {type: 'block', id: 1},
        {type: 'inst',  id: 4},
        {type: 'inst',  id: 5},
        {type: 'block', id: 2},
        {type: 'inst',  id: 6},
        {type: 'inst',  id: 7},
        {type: 'block', id: 3},
        {type: 'block', id: 4}
    ]
};

taskTemplate.sspan = {
    blocks: [
        {
            practice: true,
            problems: [
                {id: 0, type: SQ.typeId, squares: SQ.makeRandomFigure(2)},
                {id: 1, type: SQ.typeId, squares: SQ.makeRandomFigure(3)},
                {id: 2, type: SQ.typeId, squares: SQ.makeRandomFigure(4)}
            ]
        },
        {
            practice: true,
            problems: [
                {id: 0, type: SY.typeId, symmetry: SY.makeFigure()},
                {id: 1, type: SY.typeId, symmetry: SY.makeFigure()},
                {id: 2, type: SY.typeId, symmetry: SY.makeFigure()}
            ]
        },
        {
            practice: true,
            problems: [
                {id: 0, type: SYSQ.typeId, squares: SQ.makeRandomFigure(2), symmetries: SYSQ.makeSymmetryFigures(2)},
                {id: 1, type: SYSQ.typeId, squares: SQ.makeRandomFigure(3), symmetries: SYSQ.makeSymmetryFigures(3)},
                {id: 2, type: SYSQ.typeId, squares: SQ.makeRandomFigure(4), symmetries: SYSQ.makeSymmetryFigures(4)}
            ]
        },
        {
            practice: false,
            problems: [
                {id: 0, type: SYSQ.typeId, squares: SQ.makeRandomFigure(3), symmetries: SYSQ.makeSymmetryFigures(3)},
                {id: 1, type: SYSQ.typeId, squares: SQ.makeRandomFigure(4), symmetries: SYSQ.makeSymmetryFigures(4)},
                {id: 2, type: SYSQ.typeId, squares: SQ.makeRandomFigure(5), symmetries: SYSQ.makeSymmetryFigures(5)},
                {id: 3, type: SYSQ.typeId, squares: SQ.makeRandomFigure(6), symmetries: SYSQ.makeSymmetryFigures(6)},
                {id: 4, type: SYSQ.typeId, squares: SQ.makeRandomFigure(7), symmetries: SYSQ.makeSymmetryFigures(7)}
            ]
        },
        {
            practice: false,
            problems: [
                {id: 0, type: SYSQ.typeId, squares: SQ.makeRandomFigure(3), symmetries: SYSQ.makeSymmetryFigures(3)},
                {id: 1, type: SYSQ.typeId, squares: SQ.makeRandomFigure(4), symmetries: SYSQ.makeSymmetryFigures(4)},
                {id: 2, type: SYSQ.typeId, squares: SQ.makeRandomFigure(5), symmetries: SYSQ.makeSymmetryFigures(5)},
                {id: 3, type: SYSQ.typeId, squares: SQ.makeRandomFigure(6), symmetries: SYSQ.makeSymmetryFigures(6)},
                {id: 4, type: SYSQ.typeId, squares: SQ.makeRandomFigure(7), symmetries: SYSQ.makeSymmetryFigures(7)}
            ]
        }
    ],
    instructs: [
        {
            text: 'Welcome. The task you will be completing today involves two things: remembering the location of blue squares on the screen and solving simple symmetry problems. First, you will practice each part before you begin the task. Please read the instructions carefully so you know how to do the task.\n\nPlease click the CONTINUE button to begin.',
            next: 'Continue'
        },
        {
            text: 'You will now practice remembering the position of blue squares.  You will see one blue square appear at a time in a 4x4 grid and your goal is to remember the location of the squares in the exact order that they appeared on the screen. After a set of blue squares is presented, you will see a screen with the 4x4 grid with 16 possible square positions.  You will click on each position on the grid in the order you think the blue squares were presented to you. A number will appear in the grid to mark the position. For example, 1 will appear for the first grid position you click, a 2 for the second grid position and so on. If you need to change or adjust the order of the blue squares, please use the CLEAR button.\n\nOnce you think you have all the blue squares in the correct order, click to the CONTINUE screen to see the next set of blue squares.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        {
            text: 'You have completed the practice.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        {
            text: 'You will now practice solving the simple symmetry problems. You will see a grid with black squares filled in on the screen. Imagine that there is a vertical line (top to bottom) in the center of the grid. Your task is to indicate whether the black blocks on the left and right side of this imaginary line are identical in position. If the two sides are the same, click on TRUE and if the two sides are different, click on FALSE.\n\nAfter you click on the TRUE or FALSE button, you will see whether your answer was correct or incorrect. Your goal is to solve each problem correctly as quickly as you can.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        {
            text: 'You have completed the practice.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        {
            text: 'You will now practice remembering the position blue squares and solving symmetry problems together. This practice will prepare you to complete the task and it will be more challenging than doing each part alone.\n\nFirst you will see a symmetry problem to solve whether the two sides are identical by clicking on TRUE or FALSE. Next, you will see a blue square on the grid. You will need to remember the position of the blue square. You will see several symmetry problem and blue square combinations in a set. After a set, you will see a blank 4x4 grid with 16 possible square positions.  As before, you will click on each position on the grid in the order you think the blue squares were presented to you.\n\nWe will keep track of your responses. If you take too long to respond to the symmetry problem, the task will move on to the next blue square and your response will be marked as incorrect. If you answer several incorrect symmetry problems, you will receive a message that you have too many errors.\n\nYour goal is to solve the symmetry problem correctly as quickly as possible AND remember each of the blue squares in the exact order they appeared on the screen.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        {
            text: 'You have completed the practice of symmetry and blue squares. You are now ready to complete the task.\n\nYour goal is to solve the symmetry problem correctly as quickly as possible AND remember each of the blue squares in the exact order they appeared on the screen.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        {
            text: 'You are now ready to begin the task.\n\nYour goal is to solve the symmetry problem correctly as quickly as possible AND remember each of the blue squares in the exact order they appeared on the screen.\n\nThe task can be challenging, and we ask you to try to do your best.\n\nPlease click START to move to next part of the task.',
            next: 'Start'
        }
    ],
    struct: [
        {type: 'inst',  id: 0},
        {type: 'inst',  id: 1},
        {type: 'block', id: 0},
        {type: 'inst',  id: 2},
        {type: 'inst',  id: 3},
        {type: 'block', id: 1},
        {type: 'inst',  id: 4},
        {type: 'inst',  id: 5},
        {type: 'block', id: 2},
        {type: 'inst',  id: 6},
        {type: 'inst',  id: 7},
        {type: 'block', id: 3},
        {type: 'block', id: 4}
    ]
}

taskTemplate.combined = {
	blocks: [
        {
            practice: true,
            problems: [
                {id: 0, type: LS.typeId, letters: ['G', 'H']},
                {id: 1, type: LS.typeId, letters: ['P', 'F', 'D']},
                {id: 2, type: LS.typeId, letters: ['V', 'R', 'S', 'N']}
            ]
        },
        {
            practice: true,
            problems: [
                {id: 0, type: EQ.typeId, equation: '(2*4)+1=5'},
                {id: 1, type: EQ.typeId, equation: '(24/2)-6=1'},
                {id: 2, type: EQ.typeId, equation: '(10/2)+2=6'},
                {id: 3, type: EQ.typeId, equation: '(2*3)-3=3'},
                {id: 4, type: EQ.typeId, equation: '(2*2)+2=6'},
                {id: 5, type: EQ.typeId, equation: '(7/7)+7=8'}
            ]
        },
        {
            practice: true,
            problems: [
                {
                    id: 0,
                    type: EQLS.typeId,
                    letters: ['G', 'H'],
                    equations: [
                        '(10*2)-10=10',
                        '(1*2)+1=2'
                    ]
                },
                {
                    id: 1,
                    type: EQLS.typeId,
                    letters: ['P', 'F', 'D'],
                    equations: [
                        '(5*2)-10=10',
                        '(10*3)+1=15',
                        '(10/5)+5=7'
                    ]
                },
                {
                    id: 2,
                    type: EQLS.typeId,
                    letters: ['V', 'R', 'S', 'N'],
                    equations: [
                        '(6*2)-10=3',
                        '(6/3)+3=5',
                        '(7*2)-7=7',
                        '(8*2)-1=16'
                    ]
                }
            ]
        },
        {
            practice: false,
            problems: [
                {
                    id: 0,
                    type: EQLS.typeId,
                    letters: ['D', 'L', 'P'],
                    equations: [
                        '(9*2)-10=8',
                        '(3*4)+5=30',
                        '(5*4)-19=1'
                    ]
                },
                {
                    id: 1,
                    type: EQLS.typeId,
                    letters: ['P', 'L', 'D', 'F'],
                    equations: [
                        '(25*2)-10=20',
                        '(10*10)-10=90',
                        '(4*5)+5=15',
                        '(10/5)+5=7'
                    ]
                },
                {
                    id: 2,
                    type: EQLS.typeId,
                    letters: ['P', 'R', 'S', 'Y', 'N'],
                    equations: [
                        '(10/2)+6=4',
                        '(8*3)-8=16',
                        '(6/2)-1=2',
                        '(3*12)+3=12',
                        '(6*8)-2=20'
                    ]
                },
                {
                    id: 3,
                    type: EQLS.typeId,
                    letters: ['Q', 'X', 'Z', 'D', 'C', 'V'],
                    equations: [
                        '(3*5)-10=8',
                        '(15*2)-15=0',
                        '(5*2)-3=5',
                        '(10/2)+6=11',
                        '(4/2)-1=10',
                        '(8*6)+5=53'
                    ]
                },
                {
                    id: 4,
                    type: EQLS.typeId,
                    letters: ['S', 'F', 'G', 'H', 'J', 'K', 'L'],
                    equations: [
                        '(10/5)-2=1',
                        '(12/3)-4=0',
                        '(4*4)+4=20',
                        '(12/4)-3=4',
                        '(25*2)-10=20',
                        '(8*6)-8=30',
                        '(5/5)+2=1'
                    ]
                }
            ]
        },
        {
            practice: false,
            problems: [
                {
                    id: 0,
                    type: EQLS.typeId,
                    letters: ['G', 'W', 'J'],
                    equations: [
                        '(9*2)-10=8',
                        '(3*4)+5=30',
                        '(5*4)-3=17'
                    ]
                },
                {
                    id: 1,
                    type: EQLS.typeId,
                    letters: ['D', 'L', 'T', 'Q'],
                    equations: [
                        '(25*2)-10=20',
                        '(4*1)+20=24',
                        '(4*5)+5=15',
                        '(10/5)+5=7'
                    ]
                },
                {
                    id: 2,
                    type: EQLS.typeId,
                    letters: ['R', 'F', 'V', 'S', 'W'],
                    equations: [
                        '(10/2)+6=4',
                        '(8*3)-8=16',
                        '(6/2)-1=2',
                        '(3*12)+3=12',
                        '(6*8)-2=20'
                    ]
                },
                {
                    id: 3,
                    type: EQLS.typeId,
                    letters: ['T', 'X', 'G', 'D', 'C', 'V'],
                    equations: [
                        '(13*2)-10=14',
                        '(15*2)-20=0',
                        '(5*2)-3=5',
                        '(10*8)+2=82',
                        '(4/2)-1=10',
                        '(8*6)+5=53'
                    ]
                },
                {
                    id: 4,
                    type: EQLS.typeId,
                    letters: ['R', 'W', 'V', 'H', 'Q', 'K', 'P'],
                    equations: [
                        '(10/5)-2=1',
                        '(3*3)-5=4',
                        '(4/4)+4=5',
                        '(12/4)-3=4',
                        '(25*2)-10=20',
                        '(8/2)+4=6',
                        '(5/5)+2=1'
                    ]
                }
            ]
        },
        {
            practice: true,
            problems: [
                {id: 0, type: SQ.typeId, squares: SQ.makeRandomFigure(2)},
                {id: 1, type: SQ.typeId, squares: SQ.makeRandomFigure(3)},
                {id: 2, type: SQ.typeId, squares: SQ.makeRandomFigure(4)}
            ]
        },
        {
            practice: true,
            problems: [
                {id: 0, type: SY.typeId, symmetry: SY.makeFigure()},
                {id: 1, type: SY.typeId, symmetry: SY.makeFigure()},
                {id: 2, type: SY.typeId, symmetry: SY.makeFigure()}
            ]
        },
        {
            practice: true,
            problems: [
                {id: 0, type: SYSQ.typeId, squares: SQ.makeRandomFigure(2), symmetries: SYSQ.makeSymmetryFigures(2)},
                {id: 1, type: SYSQ.typeId, squares: SQ.makeRandomFigure(3), symmetries: SYSQ.makeSymmetryFigures(3)},
                {id: 2, type: SYSQ.typeId, squares: SQ.makeRandomFigure(4), symmetries: SYSQ.makeSymmetryFigures(4)}
            ]
        },
        {
            practice: false,
            problems: [
                {id: 0, type: SYSQ.typeId, squares: SQ.makeRandomFigure(3), symmetries: SYSQ.makeSymmetryFigures(3)},
                {id: 1, type: SYSQ.typeId, squares: SQ.makeRandomFigure(4), symmetries: SYSQ.makeSymmetryFigures(4)},
                {id: 2, type: SYSQ.typeId, squares: SQ.makeRandomFigure(5), symmetries: SYSQ.makeSymmetryFigures(5)},
                {id: 3, type: SYSQ.typeId, squares: SQ.makeRandomFigure(6), symmetries: SYSQ.makeSymmetryFigures(6)},
                {id: 4, type: SYSQ.typeId, squares: SQ.makeRandomFigure(7), symmetries: SYSQ.makeSymmetryFigures(7)}
            ]
        },
        {
            practice: false,
            problems: [
                {id: 0, type: SYSQ.typeId, squares: SQ.makeRandomFigure(3), symmetries: SYSQ.makeSymmetryFigures(3)},
                {id: 1, type: SYSQ.typeId, squares: SQ.makeRandomFigure(4), symmetries: SYSQ.makeSymmetryFigures(4)},
                {id: 2, type: SYSQ.typeId, squares: SQ.makeRandomFigure(5), symmetries: SYSQ.makeSymmetryFigures(5)},
                {id: 3, type: SYSQ.typeId, squares: SQ.makeRandomFigure(6), symmetries: SYSQ.makeSymmetryFigures(6)},
                {id: 4, type: SYSQ.typeId, squares: SQ.makeRandomFigure(7), symmetries: SYSQ.makeSymmetryFigures(7)}
            ]
        }
    ],
    instructs: [
        {
            text: 'Welcome. The task you will be completing today involves two things: remembering letters and solving simple math problems. First, you will practice each part before you begin the task. Please read the instructions carefully so you know how to do the task.\n\nPlease click the CONTINUE button to begin',
            next: 'Continue'
        },
        {
            text: 'You will now practice remembering letters.  You will see one letter at a time presented and your goal is to remember the letters in the exact order that they appeared on the screen. After a set of letters is presented, you will see a screen with 12 possible letters.  You will click on each letter in the order you think they were presented to you. A number will appear next to the letter to indicate its position. For example, 1 will appear for the first letter, a 2 for the second letter and so on. If you need to change or adjust the order of the letters, please use the CLEAR button.\n\nOnce you think you have all the letters in the correct order, click CONTINUE to see the next set of letters.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        {
        
            text: 'You have completed the practice.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        { //5
            text: 'You will now practice solving the simple math problems. You will see a math problem such as (2x2) + 3 = 5? Presented on the screen. Your goal is solve the problem and indicate whether the number after the = sign is true or false. In this example, (2x2) + 3 does not equal 5, so you would click the FALSE button.\n\nAfter you click on the TRUE or FALSE button, you will see whether your answer was correct or incorrect. Your goal is to solve each problem correctly as quickly as you can.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        { //7
        
            text: 'You have completed the practice.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        { //8
            text: 'You will now practice remembering letters and solving math problems together. This practice will prepare you to complete the task and it will be more challenging than doing each part alone.\n\nFirst you will see a math problem to solve it by clicking on TRUE or FALSE. Next, you will see a letter. You will need to remember the letter. You will see several math problem and letter combinations in a set. After a set, you will be presented with 12 letters on the screen. As before, you will click on each letter in the order you believe they appeared on the screen.\n\nWe will keep track of your responses. If you take too long to respond to the math problem, the task will move on to the next letter and your math will be marked as incorrect. If you answer several incorrect math problems, you will receive a message that you have too many math errors.\n\nYour goal is to solve the math problem correctly as quickly as possible AND remember each of the letters in the exact order they appeared on the screen.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        { //10
            text: 'You have completed the practice session. You are now ready to complete the task.\n\nYour goal is to solve the math problem correctly as quickly as possible AND remember each of the letters in the exact order they appeared on the screen.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        { //11
            text: 'You are now ready to begin the task.\n\nYour goal is to solve the math problem correctly as quickly as possible AND remember each of the letters in the exact order they appeared on the screen.\n\nThe task can be challenging, and we ask you to try to do your best.\n\nPlease click START to move to next part of the task.',
            next: 'Start'
        },
        {
            text: 'Welcome. The task you will be completing today involves two things: remembering the location of blue squares on the screen and solving simple symmetry problems. First, you will practice each part before you begin the task. Please read the instructions carefully so you know how to do the task.\n\nPlease click the CONTINUE button to begin.',
            next: 'Continue'
        },
        {
            text: 'You will now practice remembering the position of blue squares.  You will see one blue square appear at a time in a 4x4 grid and your goal is to remember the location of the squares in the exact order that they appeared on the screen. After a set of blue squares is presented, you will see a screen with the 4x4 grid with 16 possible square positions.  You will click on each position on the grid in the order you think the blue squares were presented to you. A number will appear in the grid to mark the position. For example, 1 will appear for the first grid position you click, a 2 for the second grid position and so on. If you need to change or adjust the order of the blue squares, please use the CLEAR button.\n\nOnce you think you have all the blue squares in the correct order, click to the CONTINUE screen to see the next set of blue squares.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        {
            text: 'You have completed the practice.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        {
            text: 'You will now practice solving the simple symmetry problems. You will see a grid with black squares filled in on the screen. Imagine that there is a vertical line (top to bottom) in the center of the grid. Your task is to indicate whether the black blocks on the left and right side of this imaginary line are identical in position. If the two sides are the same, click on TRUE and if the two sides are different, click on FALSE.\n\nAfter you click on the TRUE or FALSE button, you will see whether your answer was correct or incorrect. Your goal is to solve each problem correctly as quickly as you can.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        {
            text: 'You have completed the practice.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        {
            text: 'You will now practice remembering the position of blue squares and solving symmetry problems together. This practice will prepare you to complete the task and it will be more challenging than doing each part alone.\n\nFirst you will see a symmetry problem to solve whether the two sides are identical by clicking on TRUE or FALSE. Next, you will see a blue square on the grid. You will need to remember the position of the blue square. You will see several symmetry problem and blue square combinations in a set. After a set, you will see a blank 4x4 grid with 16 possible square positions.  As before, you will click on each position on the grid in the order you think the blue squares were presented to you.\n\nWe will keep track of your responses. If you take too long to respond to the symmetry problem, the task will move on to the next blue square and your response will be marked as incorrect. If you answer several incorrect symmetry problems, you will receive a message that you have too many errors.\n\nYour goal is to solve the symmetry problem correctly as quickly as possible AND remember each of the blue squares in the exact order they appeared on the screen.\n\nPlease click the START button to begin.',
            next: 'Start'
        },
        {
            text: 'You have completed the practice of symmetry and blue squares. You are now ready to complete the task.\n\nYour goal is to solve the symmetry problem correctly as quickly as possible AND remember each of the blue squares in the exact order they appeared on the screen.\n\nPlease click CONTINUE to move to next part of the task.',
            next: 'Continue'
        },
        {
            text: 'You are now ready to begin the task.\n\nYour goal is to solve the symmetry problem correctly as quickly as possible AND remember each of the blue squares in the exact order they appeared on the screen.\n\nThe task can be challenging, and we ask you to try to do your best.\n\nPlease click START to move to next part of the task.',
            next: 'Start'
        }
    ],
    struct: [
        {type: 'inst',  id: 0},//ospan
        {type: 'inst',  id: 1},
        {type: 'block', id: 0},
        {type: 'inst',  id: 2},
        {type: 'inst',  id: 3},
        {type: 'block', id: 1},
        {type: 'inst',  id: 4},
        {type: 'inst',  id: 5},
        {type: 'block', id: 2},
        {type: 'inst',  id: 6},
        {type: 'inst',  id: 7},
        {type: 'block', id: 3},
        {type: 'block', id: 4},
        {type: 'inst',  id: 8},//sspan
        {type: 'inst',  id: 9},
        {type: 'block', id: 5},
        {type: 'inst',  id: 10},
        {type: 'inst',  id: 11},
        {type: 'block', id: 6},
        {type: 'inst',  id: 12},
        {type: 'inst',  id: 13},
        {type: 'block', id: 7},
        {type: 'inst',  id: 14},
        {type: 'inst',  id: 15},
        {type: 'block', id: 8},
        {type: 'block', id: 9}
    ]
};
var CreateTask = React.createClass({
    statics: {
        editMode: {add: 0, edit: 1}
    },
    getInitialState: function() {
        // var task;
        // if(taskType == 'ospan')
        //     task = taskTemplate.ospan;
        // else if(taskType == 'sspan')
        //     task = taskTemplate.sspan;
        // else
        //     task = taskTemplate.combined;
        
        return {
            // task: task,
            //Edit problem context {mode, prob, blockId, probId, subId, ssubId}
            editContext: {mode: CreateTask.editMode.add}
        };
    },
    onBlockAdd: function(type) {
        this.state.task.blocks.push({practice: (type === 'prac'), problems:[]});
        this.state.task.struct.push({type: 'block', id: this.state.task.blocks.length - 1});
        this.setState({task: this.state.task});
    },
    onBlockDel: function(blockId) {
        console.log('onBlockDel', blockId);
        this.state.task.blocks.splice(blockId, 1);

        //Find the entry in task.struct to remove
        var i = 0;
        for(; i < this.state.task.struct.length; i++)
            if(this.state.task.struct[i].id == blockId && this.state.task.struct[i].type == 'block')
                break;

        //Remove an entry from task.struct
        this.state.task.struct.splice(i, 1);

        //Adjust inst id of remaining instruct in struct
        for(var i = 0; i < this.state.task.struct.length; i++)
            if(this.state.task.struct[i].type == 'block' && this.state.task.struct[i].id > blockId)
                this.state.task.struct[i].id--;

        this.setState({task: this.state.task});
    },
    /**
     * @param mode integer One of values of editMode.
     * @param blockId integer Position of the block.
     * @param probId integer Position of the problem in the block.
     * @param subid integer  Sub id
     * @param ssubid integer Sub-sub id
     */
    showProbForm: function(mode, blockId, probId, subId, ssubId) {
        console.log(blockId,probId,subId,ssubId);
        if(mode == CreateTask.editMode.add) {
            this.setState({editContext:{mode: CreateTask.editMode.add, blockId: blockId}});
            $(ProbForm.domIdSel).modal('show');
        }
        else {
            var block = this.state.task.blocks[blockId];
            var prob = block.problems[probId];
            var ec = {
                mode: CreateTask.editMode.edit,
                blockId: blockId,
                probId: probId,
                subId: subId,
                ssubId: ssubId
            };

            switch(prob.type) {
                case EQLS.typeId:
                    prob = subId == 0 ? {type: LS.typeId, letters: prob.letters} : {type: EQ.typeId, equation: prob.equations[ssubId]};
                    break;
                case SYSQ.typeId:
                    prob = subId == 0 ? {type: SQ.typeId, squares: prob.squares} : {type: SY.typeId, symmetry: prob.symmetries[ssubId]};
                    break;
            }

            ec.prob = prob;

            this.setState({editContext: ec});
            $(ProbForm.domIdSel).modal('show');
        }
    },
    onAddProbClick: function(blockId) {
        this.showProbForm(CreateTask.editMode.add, blockId);
    },
    onProbEdit: function(blockId, probId, subId, ssubId) {
        this.showProbForm(CreateTask.editMode.edit, blockId, probId, subId, ssubId);
    },
    onProbDel: function(blockId, probId) {
        var block = this.state.task.blocks[blockId];
        block.problems.splice(probId, 1);
        for(var i = 0; i < block.problems.length; i++)
            block.problems[i].id = i;
        this.setState({task:this.state.task});
    },
    onProbFormSave: function() {
        if(this.state.editContext.mode === CreateTask.editMode.add)
            this.probFormSaveNew();
        else
            this.probFormSaveEdit();  
    },
    probFormSaveNew: function() {
        var type = $(ProbForm.domIdSel + ' #probType').val();
        switch(type) {
            case LS.typeId:
                return this.probFormSaveNewLS();
            case EQ.typeId:
                return this.probFormSaveNewEQ();
            case EQLS.typeId:
                return this.probFormSaveNewEQLS();
            case SQ.typeId:
                return this.probFormSaveNewSQ();
            case SY.typeId:
                return this.probFormSaveNewSY();
            case SYSQ.typeId:
                return this.probFormSaveNewSYSQ();
        }
    },
    probFormSaveNewLS: function() {
        var str = $(ProbForm.domIdSel + ' #letters').val().trim();

        if(str && str.length > 0) {
            var a = str.split(',').map(function(str){return str.trim()}),
                i = this.state.editContext.blockId;

            this.state.task.blocks[i].problems.push({
                id: this.state.task.blocks[i].problems.length,
                type: LS.typeId,
                letters: a
            });

            this.setState({task: this.state.task});
            $(ProbForm.domIdSel).modal('hide');
        }
    },
    probFormSaveNewEQ: function() {
        var eq = $(ProbForm.domIdSel + ' #equation').val().trim();

        if(EQ.isValid(eq)) {
            var i = this.state.editContext.blockId;

            this.state.task.blocks[i].problems.push({
                id: this.state.task.blocks[i].problems.length,
                type: EQ.typeId,
                equation: eq
            });

            this.setState({task: this.state.task});
            $(ProbForm.domIdSel).modal('hide');
        }
    },
    probFormSaveNewEQLS: function() {
        var eqStr = $(ProbForm.domIdSel + ' #equations').val().trim();
        var leStr = $(ProbForm.domIdSel + ' #letters').val().trim();

        if(eqStr && leStr) {
            var eq = eqStr.split(',').map(function(str){return str.trim()});
            var le = leStr.split(',').map(function(str){return str.trim()});

            if(eq && eq.length > 0 && le && le.length > 0 && eq.length == le.length) {
                var i = this.state.editContext.blockId;
                this.state.task.blocks[i].problems.push({
                    id: this.state.task.blocks[i].problems.length,
                    type: EQLS.typeId,
                    letters: le,
                    equations: eq
                });
                this.setState({blocks: this.state.task.blocks});
                $(ProbForm.domIdSel).modal('hide');
            }
        }
    },
    probFormSaveNewSQ: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var json = $(ProbForm.domIdSel + ' #squares').val().trim();
        this.state.task.blocks[blockId].problems.push({
            id: this.state.task.blocks[blockId].problems.length,
            type: SQ.typeId,
            squares: JSON.parse(json)
        });
        this.setState({task: this.state.task});
        $(ProbForm.domIdSel).modal('hide');
    },
    probFormSaveNewSY: function(){
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var json = $(ProbForm.domIdSel + ' #symmetry').val().trim();
        this.state.task.blocks[blockId].problems.push({
            id: this.state.task.blocks[blockId].problems.length,
            type: SY.typeId,
            symmetry: JSON.parse(json)
        });
        this.setState({task: this.state.task});
        $(ProbForm.domIdSel).modal('hide');
    },
    probFormSaveNewSYSQ: function() {
        var length = $(ProbForm.domIdSel + ' #length').val().trim();
        var blockId = this.state.editContext.blockId;

        if(length && /^\d+$/.test(length)) {
            length = parseInt(length);
            
            this.state.task.blocks[blockId].problems.push({
                id: this.state.task.blocks[blockId].problems.length,
                type: SYSQ.typeId,
                squares: SQ.makeRandomFigure(length),
                symmetries: SYSQ.makeSymmetryFigures(length)
            });

            this.setState({task: this.state.task});
            $(ProbForm.domIdSel).modal('hide');
        }
    },
    probFormSaveEdit: function() {
        var editContext = this.state.editContext;
        var block = this.state.task.blocks[editContext.blockId];
        var prob = block.problems[editContext.probId];

        switch(prob.type) {
            case LS.typeId:
                return this.probFormSaveEditLS();
            case EQ.typeId:
                return this.probFormSaveEditEQ();
            case EQLS.typeId:
                return this.probFormSaveEditEQLS();
            case SQ.typeId:
                return this.probFormSaveEditSQ();
            case SY.typeId:
                return this.probFormSaveEditSY();
            case SYSQ.typeId:
                return this.probFormSaveEditSYSQ();
        }
    },
    probFormSaveEditLS: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;

        var str = $(ProbForm.domIdSel + ' #letters').val().trim();
        
        if(str && str.length > 0) {
            var a = str.split(',').map(function(str){return str.trim()});
            if(a.length > 0) {
                this.state.task.blocks[blockId].problems[probId].letters = a;
                this.setState({task: this.state.task});
                $(ProbForm.domIdSel).modal('hide');
            }
        }
    },
    probFormSaveEditEQ: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;
        var equation = $(ProbForm.domIdSel + ' #equation').val().trim();

        if(EQ.isValid(equation)) {
            this.state.task.blocks[blockId].problems[probId].equation = equation;
            this.setState({task: this.state.task});
            $(ProbForm.domIdSel).modal('hide');
        }
    },
    probFormSaveEditEQLS: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;
        var subId = editContext.subId;
        var ssubId = editContext.ssubId;

        //This feature is currently not supported, so we just return.
        if(subId == null || subId == undefined) return;

        //Edit letters
        if(subId == 0) {
            this.probFormSaveEditLS();
        }
        //Edit one of the equations
        else if(subId == 1) {
            var equation = $(ProbForm.domIdSel + ' #equation').val().trim();

            if(EQ.isValid(equation)) {
                this.state.task.blocks[blockId].problems[probId].equations[ssubId] = equation;
                this.setState({task: this.state.task});
                $(ProbForm.domIdSel).modal('hide');
            }
        }
    },
    probFormSaveEditSQ: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;
        var json = $(ProbForm.domIdSel + ' #squares').val().trim();
        this.state.task.blocks[blockId].problems[probId].squares = JSON.parse(json);
        this.setState({task: this.state.task});
        $(ProbForm.domIdSel).modal('hide');
    },
    probFormSaveEditSY: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;
        var json = $(ProbForm.domIdSel + ' #symmetry').val().trim();
        this.state.task.blocks[blockId].problems[probId].symmetry = JSON.parse(json);
        this.setState({task: this.state.task});
        $(ProbForm.domIdSel).modal('hide');
    },
    probFormSaveEditSYSQ: function() {
        var editContext = this.state.editContext;
        var blockId = editContext.blockId;
        var probId = editContext.probId;
        var subId = editContext.subId;
        var ssubId = editContext.ssubId;

        //This feature is currently not supported, so we just return.
        if(subId == null || subId == undefined) return;

        //Edit squares
        if(subId == 0) {
            this.probFormSaveEditSQ();
        }
        //Edit one of the equations
        else if(subId == 1) {
            var symmetry = $(ProbForm.domIdSel + ' #symmetry').val().trim();
            this.state.task.blocks[blockId].problems[probId].symmetries[ssubId] = JSON.parse(symmetry);
            this.setState({task: this.state.task});
            $(ProbForm.domIdSel).modal('hide');
            
        }
    },
    onTaskSave: function() {
        if(this.validateTask()) {
            $.ajax({
                type: 'POST',
                url: taskSaveUrl,
                contentType: 'application/json',
                data: {
                    name: this.refs.taskName.refs.input.getDOMNode().value.trim(),
                    task: JSON.stringify(this.state.task),
                    maxScore: TSK.getMaxScore(this.state.task)
                },
                success: function(data, textStatus, jqXHR) {
                    // console.log('Ajax save success', textStatus, data);
                    console.log(data);
                    // window.location.href = taskIndexUrl;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Ajax save error', textStatus, errorThrown);
                }
            });
        }
    },
    validateTask: function() {
        //Reset error message
        this.setState({error: null});

        //Validate that name is not empty
        var name = this.refs.taskName.refs.input.getDOMNode().value.trim();
        if(!name || name == '') {
            this.setState({error: 'Task name is empty'});
            return false;
        }

        //Validate that every block has problem
        for(var i = 0; i < this.state.task.blocks.length; i++) {
            if(this.state.task.blocks[i].problems.length == 0) {
                this.setState({error: 'Block ' + (i + 1) + ' is empty'});
                return false;
            }
        }

        return true;
    },
    onInstAdd: function() {
        console.log('onInstAdd');
        this.setState({editContext:{}});
        $(InstForm.domIdSel).modal('show');
    },
    onInstEdit: function(instId) {
        console.log('onInstEdit', instId);
        this.setState({editContext:{inst: this.state.task.instructs[instId]}});
        $(InstForm.domIdSel).modal('show');
    },
    onInstDel: function(instId) {
        //Remove the inst entry
        this.state.task.instructs.splice(instId, 1);
        
        //Find the entry in task.struct to remove
        var i = 0;
        for(; i < this.state.task.struct.length; i++)
            if(this.state.task.struct[i].id == instId && this.state.task.struct[i].type == 'inst')
                break;

        //Remove an entry from task.struct
        this.state.task.struct.splice(i, 1);

        //Adjust inst id of remaining instruct in struct
        for(var i = 0; i < this.state.task.struct.length; i++)
            if(this.state.task.struct[i].type == 'inst' && this.state.task.struct[i].id > instId)
                this.state.task.struct[i].id--;

        this.setState({task: this.state.task});
    },
    onInstFormSave: function() {
        var text = this.refs.instForm.refs.body.refs.text.getDOMNode().value.trim();
        var next = this.refs.instForm.refs.body.refs.next.getDOMNode().value.trim();

        if(text == '' || next == '') return;

        //Editing existing instruction
        if(this.state.editContext.inst) {
            this.state.editContext.inst.text = text;
            this.state.editContext.inst.next = next;
            this.setState({task: this.state.task});
        }
        //Adding new instruction
        else {
            var inst = {text: text, next: next}
            var instId = this.state.task.instructs.length;

            this.state.task.instructs.push(inst);
            this.state.task.struct.push({type: 'inst', id: instId});
            this.setState({task: this.state.task});
        }

        $(InstForm.domIdSel).modal('hide');
    },
    render: function() {
        return (
            <div>
                {/*<CreateTask.TaskNameInput ref="taskName"/>*/}
                {/*<TaskObList task={this.state.task} onAddProbClick={this.onAddProbClick} onProbEdit={this.onProbEdit} onProbDel={this.onProbDel} onBlockDel={this.onBlockDel} onInstEdit={this.onInstEdit} onInstDel={this.onInstDel}/>*/}
                {/*<CreateTask.Buttons onBlockAdd={this.onBlockAdd} onInstAdd={this.onInstAdd}/>*/}
                {/*<CreateTask.Error error={this.state.error}/>*/}
                {/*<CreateTask.SaveRow onTaskSave={this.onTaskSave}/>*/}
                <ProbForm editContext={this.state.editContext} onSaveClick={this.onProbFormSave}/>
                {/*<InstForm ref="instForm" editContext={this.state.editContext} onSaveClick={this.onInstFormSave}/>*/}
            </div>
        );
    }
    // render: function() {
    //     return (
    //         <div>
    //             <ProbForm editContext={this.state.editContext} onSaveClick={this.onProbFormSave}/>
    //         </div>
    //     );
    // }
});

CreateTask.TaskNameInput = React.createClass({
    render: function() {
        return (
            <form>
                <div className="form-group">
                    <label htmlFor="taskName">Task Name</label>
                    <input ref="input" id="taskName" className="form-control"/>
                </div>
            </form>
        )
    }
});

CreateTask.Error = React.createClass({
    propTypes: {
        error: React.PropTypes.string
    },
    render: function() {
        if(!this.props.error || this.props.error == '')
            return null;
        return <div className="alert alert-danger" role="alert">{this.props.error}</div>
    }
});

CreateTask.Buttons = React.createClass({
    propTypes: {
        onBlockAdd: React.PropTypes.func.isRequired,
        onInstAdd: React.PropTypes.func.isRequired
    },
    onAddTaskBlock: function() {
        this.props.onBlockAdd('task');
    },
    onAddPracBlock: function() {
        this.props.onBlockAdd('prac');
    },
    render: function() {
        return (
            <div style={{marginBottom:10}}>
                <button className="btn btn-default" onClick={this.onAddTaskBlock}>New Task Block</button> <button className="btn btn-default" onClick={this.onAddPracBlock}>New Practice Block</button> <button className="btn btn-default" onClick={this.props.onInstAdd}>New Instruction</button>
            </div>
        );
    }
});

CreateTask.SaveRow = React.createClass({
    propTypes: {
        onTaskSave: React.PropTypes.func.isRequired
    },
    render: function() {
        return (
            <div style={{marginTop:10}}>
                <button className="btn btn-default" onClick={this.props.onTaskSave}>Finish</button>
            </div>
        )
    }
});

var TaskObList = React.createClass({
    propTypes: {
        task: React.PropTypes.object.isRequired,
        onAddProbClick: React.PropTypes.func.isRequired,
        onProbEdit: React.PropTypes.func.isRequired,
        onProbDel: React.PropTypes.func.isRequired,
        onBlockDel: React.PropTypes.func.isRequired,
        onInstEdit: React.PropTypes.func.isRequired,
        onInstDel: React.PropTypes.func.isRequired,
        mode: React.PropTypes.string
    },
    getDefaultProps: function() {
        return {
            mode: 'edit'
        };
    },
    render: function() {
        var comps = this.props.task.struct.map(function(desc, i) {
            if(desc.type === 'inst')
                return <TaskObList.Inst key={i} instId={desc.id} inst={this.props.task.instructs[desc.id]} mode={this.props.mode} onInstEdit={this.props.onInstEdit} onInstDel={this.props.onInstDel}/>
            else if(desc.type === 'block')
                return <Block key={i} blockId={desc.id} block={this.props.task.blocks[desc.id]} mode={this.props.mode} onAddProbClick={this.props.onAddProbClick} onProbEdit={this.props.onProbEdit} onProbDel={this.props.onProbDel} onBlockDel={this.props.onBlockDel}/>
        }.bind(this));

        return <div>{comps}</div>
    }
});

TaskObList.Inst = React.createClass({
    propTypes: {
        instId: React.PropTypes.number.isRequired,
        inst: React.PropTypes.object.isRequired,
        onInstEdit: React.PropTypes.func.isRequired,
        onInstDel: React.PropTypes.func.isRequired,
        mode: React.PropTypes.string.isRequired
    },
    onInstEdit: function() {
        this.props.onInstEdit(this.props.instId);
    },
    onInstDel: function() {
        this.props.onInstDel(this.props.instId);
    },
    render: function() {
        return (
            <div className="panel panel-default">
                <div className="panel-heading">
                    <table border="0" cellSpacing="0" cellPadding="0" width="100%">
                        <tr>
                            <td>
                                <h2 className="panel-title">Instruction</h2>
                            </td>
                            {
                                this.props.mode === 'edit' ?
                                    <td style={{width:35}}>
                                        <button type="button" className="close" aria-label="Close" onClick={this.onInstDel}>
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </td>
                                :
                                null
                            }
                        </tr>
                    </table>
                </div>
                <div className="panel-body" onClick={this.onInstEdit}>
                    <span dangerouslySetInnerHTML={{__html:marked(this.props.inst.text)}}/> <kbd>{this.props.inst.next}</kbd>
                </div>
            </div>
        )
    }
});

var Block = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        block: React.PropTypes.object.isRequired,
        onAddProbClick: React.PropTypes.func.isRequired,
        onProbEdit: React.PropTypes.func.isRequired,
        onProbDel: React.PropTypes.func.isRequired,
        mode: React.PropTypes.string.isRequired
    },
    onProbEdit: function(blockId, probId, subId, ssubId) {
        this.props.onProbEdit(this.props.blockId, probId, subId, ssubId);
    },
    onBlockDel: function() {
        this.props.onBlockDel(this.props.blockId);

    },
    render: function() {
        return (
            <div className="panel panel-default">
                <Block.Heading blockId={this.props.blockId} block={this.props.block} onBlockDel={this.onBlockDel} mode={this.props.mode}/>
                <Block.Body blockId={this.props.blockId} block={this.props.block} onProbEdit={this.onProbEdit} onProbDel={this.props.onProbDel}/>
                {this.props.mode === 'edit' ? <Block.Footer blockId={this.props.blockId} onAddProbClick={this.props.onAddProbClick}/> : null }
            </div>
        )
    }
});


Block.Heading = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        block: React.PropTypes.object.isRequired,
        mode: React.PropTypes.string.isRequired,
        onBlockDel: React.PropTypes.func
    },
    render: function() {
        return (
            <div className="panel-heading">
                <table border="0" cellPadding="0" cellSpacing="0" width="100%">
                    <tr>
                        <td>
                            <h2 className="panel-title">Block {this.props.blockId + 1} {this.props.block.practice ? '(Practice)' : '(Non-Practice)'}</h2>
                        </td>
                        <td>
                        {
                            this.props.mode === 'edit' ?
                                <button type="button" className="close" aria-label="Close" onClick={this.props.onBlockDel}><span aria-hidden="true">&times;</span></button> : null
                        }
                        </td>
                    </tr>
                </table>
            </div>
        )
    }
});

Block.Body = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        block: React.PropTypes.object.isRequired,
        onProbEdit: React.PropTypes.func.isRequired,
        onProbDel: React.PropTypes.func.isRequired
    },
    render: function() {
        var res = this.props.block.problems.length > 0 ?
            <Block.Table blockId={this.props.blockId} block={this.props.block} onProbEdit={this.props.onProbEdit} onProbDel={this.props.onProbDel} /> :
            <div className="panel-body">There is currently no problem in this block.</div>
        return res;
    }
});

Block.Table = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        block: React.PropTypes.object.isRequired,
        onProbEdit: React.PropTypes.func.isRequired,
        onProbDel: React.PropTypes.func.isRequired
    },
    render: function() {
        return (
            <table className="table">
                <tbody>
                    {this.props.block.problems.map(function(p, i){
                        return <Block.Table.Row key={i} blockId={this.props.blockId} problem={p} onProbEdit={this.props.onProbEdit} onProbDel={this.props.onProbDel}/>
                    }.bind(this))}
                </tbody>
            </table>
        )
    }
});

Block.Table.Row = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        problem: React.PropTypes.object.isRequired,
        onProbEdit: React.PropTypes.func.isRequired,
        onProbDel: React.PropTypes.func.isRequired
    },
    onProbEdit: function(blockId, probId, subId, ssubId) {
        this.props.onProbEdit(null, this.props.problem.id, subId, ssubId);
    },
    onProbDel: function() {
        this.props.onProbDel(this.props.blockId, this.props.problem.id);
    },
    render: function() {
        return (
            <tr>
                <td style={{width:50}}>{this.props.problem.id + 1}</td>
                <td style={{width:140}}>{this.getProblemTypeName(this.props.problem)}</td>
                <td>{this.getProblemWidget()}</td>
                <td>
                    <button type="button" className="close pull-right" aria-label="Close" onClick={this.onProbDel}><span aria-hidden="true">&times;</span></button>
                </td>
            </tr>
        )
    },
    getProblemTypeName: function(problem) {
        switch(this.props.problem.type) {
            case EQ.typeId: return EQ.typeLabel
            case LS.typeId: return LS.typeLabel
            case EQLS.typeId: return EQLS.typeLabel
            case SQ.typeId: return SQ.typeLabel
            case SY.typeId: return SY.typeLabel
            case SYSQ.typeId: return SYSQ.typeLabel
        }
    },
    getProblemWidget: function() {
        switch(this.props.problem.type) {
            case EQ.typeId:
                return <Block.Table.Row.MathWidget onProbEdit={this.onProbEdit} equation={this.props.problem.equation} />
            case LS.typeId:
                return <Block.Table.Row.LettersWidget onProbEdit={this.onProbEdit} letters={this.props.problem.letters} />
            case EQLS.typeId:
                return <Block.Table.Row.MathLetterWidget onProbEdit={this.onProbEdit} equations={this.props.problem.equations} letters={this.props.problem.letters}/>
            case SQ.typeId:
                return <Block.Table.Row.SquaresWidget squares={this.props.problem.squares} onProbEdit={this.onProbEdit}/>
            case SY.typeId:
                return <Block.Table.Row.SymmetryWidget symmetry={this.props.problem.symmetry} onProbEdit={this.onProbEdit}/>
            case SYSQ.typeId:
                return <Block.Table.Row.SymmetrySquaresWidget squares={this.props.problem.squares} symmetries={this.props.problem.symmetries} onProbEdit={this.onProbEdit}/>
        }
    }
});

Block.Table.Row.MathWidget = React.createClass({
    propTypes: {
        equation: React.PropTypes.string.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        return <span className="inline-item" style={{cursor:'pointer'}} onClick={this.props.onProbEdit.bind(null, null, null, null, null)}>{this.props.equation}, {EQ.getAnswer(this.props.equation).toString()}</span>
    }
});

Block.Table.Row.LettersWidget = React.createClass({
    propTypes: {
        letters: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        var str = this.props.letters.toString().replace(/,/g, ', ');
        return <span className="inline-item" style={{cursor:'pointer'}} onClick={this.props.onProbEdit.bind(null, null, null, null, null)}>{str}</span>
    }
});

Block.Table.Row.MathLetterWidget = React.createClass({
    propTypes: {
        equations: React.PropTypes.array.isRequired,
        letters: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        var lc = <Block.Table.Row.LettersWidget letters={this.props.letters} onProbEdit={this.props.onProbEdit.bind(null, null, null, 0, null)}/>

        var ec = this.props.equations.map(function(equation, i) {
            return <Block.Table.Row.MathWidget key={i} equation={equation} onProbEdit={this.props.onProbEdit.bind(null, null, null, 1, i)}/>
        }.bind(this));

        return (
            <div>
                {lc}{ec}
            </div>
        )
    }
});

Block.Table.Row.SquaresWidget = React.createClass({
    propTypes: {
        squares: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        return <Block.Table.Row.SquaresWidget.Figure squares={this.props.squares} onProbEdit={this.props.onProbEdit}/>
    }
});

Block.Table.Row.SquaresWidget.Figure = React.createClass({
    propTypes: {
        squares: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    onClick: function() {
        this.props.onProbEdit(null, null, null, null);
    },
    render: function() {
        var text = this.props.squares.map(function(cell, i){
            return {loc: cell, text: i + 1};
        });

        return (
            <div style={{display:'inline-block', width:100, marginRight:15}}>
                <BoxSequence.Slide.Figure class="row-figure" rows={4} cols={4} cellText={text} onCellClick={this.onClick}/>
            </div>
        );
    }
});

Block.Table.Row.SymmetryWidget = React.createClass({
    propTypes: {
        symmetry: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        return <Block.Table.Row.SymmetryWidget.Figure symmetry={this.props.symmetry} onProbEdit={this.props.onProbEdit}/>
    }
});

Block.Table.Row.SymmetryWidget.Figure = React.createClass({
    propTypes: {
        symmetry: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    onClick: function() {
        this.props.onProbEdit(null, null, null, null);
    },
    render: function() {
        return (
            <div style={{display:'inline-block', width:100, marginRight:15, marginBottom:8, marginTop:8}}>
                <BoxSequence.Slide.Figure class="row-figure" rows={8} cols={8} colored={this.props.symmetry} borderColor={'#000'} hiColor={'#000'} onCellClick={this.onClick}/>
                <div style={{textAlign:'center', width:'100%', fontSize:12}}>{SY.isSymmetric(this.props.symmetry) ? 'Symmetric' : 'Asymmetric'}</div>
            </div>
        );
    }
});

Block.Table.Row.SymmetrySquaresWidget = React.createClass({
    propTypes: {
        squares: React.PropTypes.array.isRequired,
        symmetries: React.PropTypes.array.isRequired,
        onProbEdit: React.PropTypes.func.isRequired
    },
    render: function() {
        var squares = <Block.Table.Row.SquaresWidget.Figure squares={this.props.squares} onProbEdit={this.props.onProbEdit.bind(null, null, null, 0, null)}/>

        var symmetries = this.props.symmetries.map(function(symmetry, i) {
            return <Block.Table.Row.SymmetryWidget.Figure key={i} symmetry={symmetry} onProbEdit={this.props.onProbEdit.bind(null, null, null, 1, i)}/>
        }.bind(this));

        return (
            <div>
                <div style={{display:'inline-block'}}>{squares}<div dangerouslySetInnerHTML={{__html: '&nbsp;'}}/></div>
                {symmetries}
            </div>
        );
    }
});

Block.Footer = React.createClass({
    propTypes: {
        blockId: React.PropTypes.number.isRequired,
        onAddProbClick: React.PropTypes.func.isRequired
    },
    onAddProbClick: function() {
        this.props.onAddProbClick(this.props.blockId);
    },
    render: function() {
        return (
            <div className="panel-footer">
                <button className="btn btn-default" onClick={this.onAddProbClick}>Add Problem</button>
            </div>
        )
    }
});


var ProbForm = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired,
        onSaveClick: React.PropTypes.func.isRequired
    },
    statics: {
        domId: 'probForm',
        domIdSel: '#probForm'
    },
    render: function() {
        return (
            <div className="modal fade" id={ProbForm.domId} tabIndex="-1" role="dialog" labelledby="myModalLabel">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <ProbForm.Header editContext={this.props.editContext}/>
                        <ProbForm.Body editContext={this.props.editContext}/>
                        <ProbForm.Footer onSaveClick={this.props.onSaveClick} />
                    </div>
                </div>
            </div>
        )
    }
});

ProbForm.Header = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    render: function() {
        return (
            <div className="modal-header">
                <button type="button" className="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {
                    this.props.editContext.mode === CreateTask.editMode.add ? 
                        <h4 className="modal-title" id="myModalLabel">New Problem</h4> :
                        <h4 className="modal-title" id="myModalLabel">Edit Problem</h4>
                }
            </div>
        )
    }
});

ProbForm.Body = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        return {
            type: LS.typeId
        }
    },
    onTypeChange: function(event) {
        this.setState({type: event.target.value});
    },
    render: function() {
        return (
            <div className="modal-body">
                <form>
                    {
                        this.props.editContext.mode === CreateTask.editMode.edit ? null :
                        <div className="form-group">
                            <label htmlFor="probType">Type</label>
                            <select className="form-control" id="probType" value={this.state.type} onChange={this.onTypeChange}>
                                <option value={LS.typeId}>Letter Sequence</option>
                                <option value={EQ.typeId}>Math Equation</option>
                                <option value={EQLS.typeId}>Math and Letters</option>
                                <option value={SQ.typeId}>Square Sequence</option>
                                <option value={SY.typeId}>Symmetry</option>
                                <option value={SYSQ.typeId}>Symmetry and Squares</option>
                            </select>
                        </div>
                    }

                    <ProbForm.SpecialPane editContext={this.props.editContext} type={this.props.editContext.prob ? this.props.editContext.prob.type : this.state.type}/>
                    {/*<ProbForm.SpecialPane editContext={this.props.editContext} type={SQ.typeId}/>*/}
                </form>
            </div>
        )
    }
});

ProbForm.SpecialPane = React.createClass({
    propTypes: {
        type: React.PropTypes.string.isRequired,
        editContext: React.PropTypes.object.isRequired
    },
    render: function() {
        switch(this.props.type) {
            case LS.typeId:
                return <ProbForm.LSPane editContext={this.props.editContext}/>
            case EQ.typeId:
                return <ProbForm.EQPane editContext={this.props.editContext}/>
            case EQLS.typeId:
                return <ProbForm.EQLSPane/>
            case SQ.typeId:
                return <ProbForm.SQPane editContext={this.props.editContext}/>
            case SY.typeId:
                return <ProbForm.SYPane editContext={this.props.editContext}/>
            case SYSQ.typeId:
                return <ProbForm.SYSQPane/>
            default:
                return null;
        }
    }
});

ProbForm.LSPane = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        return {val: this.props.editContext.prob ? this.props.editContext.prob.letters : ''};
    },
    componentWillReceiveProps: function(nextProps) {
        this.setState({val: nextProps.editContext.prob ? nextProps.editContext.prob.letters : ''});
    },
    onChange: function(event) {
        this.setState({val: event.target.value});
    },
    render: function() {
        return (
            <div>
                <div className="form-group">
                    <label htmlFor="letters">Letters</label>
                    <input type="text" className="form-control" id="letters" value={this.state.val} onChange={this.onChange}/>
                    <div>A sequence of letters. For example: <code>X,Y,Z</code></div>
                </div>
            </div>
        )
    }
});

ProbForm.EQPane = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        return {val: this.props.editContext.prob ? this.props.editContext.prob.equation : ''};
    },
    componentWillReceiveProps: function(nextProps) {
        this.setState({val: nextProps.editContext.prob ? nextProps.editContext.prob.equation : ''});
    },
    onChange: function(event) {
        this.setState({val: event.target.value});
    },
    render: function() {
        return (
            <div>
                <div className="form-group">
                    <label htmlFor="equation">Equation</label>
                    <input className="form-control" id="equation" value={this.state.val} onChange={this.onChange}/>
                    <div>An equation, such as <code>(2*2)+2=2</code></div>
                </div>
            </div>
        )
    }
});

ProbForm.EQLSPane = React.createClass({
    render: function() {
        return (
            <div>
                <div className="form-group">
                    <label htmlFor="equations">Equations</label>
                    <textarea className="form-control" id="equations"></textarea>
                    <div>Equations separated by commas <code>,</code>. For example: <code>(2*2)+2=2, (4/2)-1=1</code></div>
                </div>
                <div className="form-group">
                    <label htmlFor="letters">Letters</label>
                    <input type="text" className="form-control" id="letters" />
                    <div>A sequence of letters. For example: <code>X,Y,Z</code></div>
                </div>
            </div>
        )
    }
});

ProbForm.SQPane = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        var slots = new Array(4 * 4);
        
        if(this.props.editContext.prob)
            for(var i = 0; i < this.props.editContext.prob.squares.length; i++)
                slots[i] = this.props.editContext.prob.squares[i];

        return {slots: slots};
    },
    componentWillReceiveProps: function(nextProps) {
        var slots = new Array(4 * 4);

        if(nextProps.editContext.prob)
            for(var i = 0; i < nextProps.editContext.prob.squares.length; i++)
                slots[i] = nextProps.editContext.prob.squares[i];

        this.setState({slots: slots});
    },
    onCellClick: function(cell) {
        var index = PointCollection.indexOf(this.state.slots, cell);
        var slots = this.state.slots;

        if(index == -1) {
            for(var i = 0; i < slots.length; i++) {
                if(!slots[i]) {
                    slots[i] = cell;
                    this.setState({slots: slots});
                    break;
                }
            }
        }
        else {
            slots[index] = null;
            this.setState({slots: slots});
        }
    },
    render: function() {
        var slots = this.state.slots,
            cellText = [],
            trunc = [];

        for(var i = 0; i < slots.length; i++)
            cellText.push({loc: slots[i], text: i + 1});

        for(var i = 0; i < slots.length; i++)
            if(slots[i])
                trunc.push(slots[i]);

        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-xs-6 col-xs-offset-3">
                        <input type="hidden" id="squares" value={JSON.stringify(trunc)}/>
                        <BoxSequence.Slide.Figure rows={4} cols={4} cellText={cellText} onCellClick={this.onCellClick}/>
                    </div>
                </div>
            </div>
        )
    }
});

ProbForm.SYPane = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        if (this.props.editContext.subType == null)
            var colored = this.props.editContext.prob ? this.props.editContext.prob.symmetry : [];
        else if (this.props.editContext.subType == 1)
            var colored = this.props.editContext.prob ? this.props.editContext.prob.symmetries[this.props.editContext.key] : [];
        return {colored: colored};
    },
    componentWillReceiveProps: function(nextProps) {
        if (nextProps.editContext.subType == null)
            var colored = nextProps.editContext.prob ? nextProps.editContext.prob.symmetry : [];
        else if (nextProps.editContext.subType == 1)
            var colored = nextProps.editContext.prob ? nextProps.editContext.prob.symmetries[nextProps.editContext.key] : [];

        this.setState({colored: colored});
    },
    onCellClick: function(cell) {
        var i = PointCollection.indexOf(this.state.colored, cell);
        if(i == -1)
            this.state.colored.push(cell);
        else
            this.state.colored.splice(i, 1);
        this.setState({colored: this.state.colored});
    },
    makeSymmetric: function() {
        this.setState({colored: SY.makeSymmetricFigure()});
    },
    makeAsymetric: function() {
        switch(Math.floor(Math.random() * 2)) {
            case 0:
                this.setState({colored: SY.makeAsymmetricFigure()});
            case 1:
                this.setState({colored: SY.makeRandomFigure()});
        }
    },
    render: function() {
        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-xs-6 col-xs-offset-3">
                        <input type="hidden" id="symmetry" value={JSON.stringify(this.state.colored)}/>
                        <BoxSequence.Slide.Figure rows={8} cols={8} colored={this.state.colored} borderColor={'#000'} hiColor={'#000'} onCellClick={this.onCellClick}/>
                    </div>
                </div>
                <div className="row"><div className="col-xs-12"><hr/></div></div>
                <div className="row">
                    <div className="col-xs-12" style={{textAlign:'center'}}>
                        <button type="button" className="btn btn-default" onClick={this.makeSymmetric}>Generate Symmetric</button> <button type="button" className="btn btn-default" onClick={this.makeAsymetric}>Generate Asymmetric</button>
                    </div>
                </div>
            </div>
        )
    }
});

ProbForm.SYSQPane = React.createClass({
    render: function() {
        return (
            <div className="form-group">
                <label htmlFor="length">Number of Subproblems</label>
                <input className="form-control" id="length" defaultValue={3}/>
                <div>Length of square sequence and must be an integer</div>
            </div>
        )
    }
});

ProbForm.Footer = React.createClass({
    propTypes: {
        onSaveClick: React.PropTypes.func.isRequired
    },
    render: function() {
        return (
            <div className="modal-footer">
                <button type="button" className="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" className="btn btn-primary" onClick={this.props.onSaveClick}>Ok</button>
            </div>
        )
    }
});

var InstForm = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired,
        onSaveClick: React.PropTypes.func.isRequired
    },
    statics: {
        domId: 'instForm',
        domIdSel: '#instForm'
    },
    render: function() {
        return (
            <div className="modal fade" id={InstForm.domId} tabIndex="-1" role="dialog">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <InstForm.Header editContext={this.props.editContext}/>
                        <InstForm.Body editContext={this.props.editContext} ref="body"/>
                        <InstForm.Footer onSaveClick={this.props.onSaveClick} />
                    </div>
                </div>
            </div>
        )
    }
});

InstForm.Header = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    render: function() {
        return (
            <div className="modal-header">
                <button type="button" className="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {
                    this.props.editContext.mode === CreateTask.editMode.add ? 
                        <h4 className="modal-title" id="myModalLabel">New Problem</h4> :
                        <h4 className="modal-title" id="myModalLabel">Edit Problem</h4>
                }
            </div>
        )
    }
});

InstForm.Body = React.createClass({
    propTypes: {
        editContext: React.PropTypes.object.isRequired
    },
    getInitialState: function() {
        return {
            text: this.props.editContext.inst ? this.props.editContext.inst.text : '',
            next: this.props.editContext.inst ? this.props.editContext.inst.next : ''
        }
    },
    componentWillReceiveProps: function(nextProps) {
        if(nextProps.editContext) {
            this.setState({
                text: nextProps.editContext.inst ? nextProps.editContext.inst.text : '',
                next: nextProps.editContext.inst ? nextProps.editContext.inst.next : ''
            });
        }
    },
    onChange: function(event) {
        if(event.target.id == 'instText')
            this.setState({text: event.target.value});
        else if(event.target.id == 'instNext')
            this.setState({next: event.target.value});
    },
    render: function() {
        return (
            <div className="modal-body">
                <form>
                    <div className="form-group">
                        <label htmlFor="instText">Text</label>
                        <textarea className="form-control" id="instText" ref="text" rows="6" value={this.state.text} onChange={this.onChange}/>
                    </div>
                    <div className="form-group">
                        <label htmlFor="instNext">Next Label</label>
                        <input className="form-control" id="instNext" ref="next" maxLength="30" value={this.state.next} onChange={this.onChange}/>
                    </div>
                </form>
            </div>
        )
    }
});

InstForm.Footer = React.createClass({
    propTypes: {
        onSaveClick: React.PropTypes.func.isRequired
    },
    render: function() {
        return (
            <div className="modal-footer">
                <button type="button" className="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" className="btn btn-primary" onClick={this.props.onSaveClick}>Ok</button>
            </div>
        )
    }
});

//# sourceMappingURL=create-wm.js.map
