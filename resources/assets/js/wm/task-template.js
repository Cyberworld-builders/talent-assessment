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