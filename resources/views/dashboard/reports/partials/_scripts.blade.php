@if ($report->score_method == 2 || $report->show_fit == 0)
    <style>
        .highcharts-legend {
            display: none;
        }
    </style>
@endif
<script>
    $(function () {
        @foreach ($assessments as $assessment)
            @foreach ($templates as $global => $template)
                @if ($assessment->id == get_global($global) && View::exists('dashboard.reports.templates.'.$template))

                    {{-- Ability--}}
                    @if ($template == 'a')
                        <?php
                            // Defaults
                            $scoringController = new \App\Http\Controllers\ScoringController();
                            $defaults = $scoringController->getScoreDefaults($assessment->id);
                            $score = $defaults['score'];
                            $percentile = $defaults['percentile'];
                            $zones = $scoringController->getZones(null, $assessment->id, $report->score_method);
                            $text = 'Pursue';
						    $color = '#30BD21';
                            if (! $report->show_fit)
                            {
                                $text = '';
                                $color = '#000000';
								$zones = $scoringController->getZones(null, $assessment->id, 2);
                            }

                            // Actual scores
                            if (isset($scores))
                            {
                            	// Divisions
                                if ($scores[$assessment->id]['division'] == 1)
                                {
                                    $text = 'Not Recommended';
									$color = '#E32731';
								}
                                elseif ($scores[$assessment->id]['division'] == 2 || $scores[$assessment->id]['division'] == 3)
                                {
                                    $text = 'Caution';
                                    $color = '#E7B428';
                                }
                                elseif ($scores[$assessment->id]['division'] == 4 || $scores[$assessment->id]['division'] == 5)
                                {
                                    $text = 'Pursue';
                                    $color = '#30BD21';
                                }

                                // Scores
                                $score = $scores[$assessment->id]['score'];
                                $percentile = $scores[$assessment->id]['percentile'];
                                $zones = $scores[$assessment->id]['zones'];

                                // No fit recommendation
								if (! $report->show_fit)
								{
									$text = '';
									$color = '#000000';
									$zones = $scoringController->getZones(null, $assessment->id, 2);
								}
                            }
                        ?>
                        $('#ability-chart').highcharts({
                            chart: {
                                type: 'areaspline',
                                height: 430,
                                borderRadius: 0,
                                spacingRight: 0,
                                plotBackgroundColor: 'rgba(0,0,0,0)',
                                backgroundColor: 'rgba(0,0,0,0)',
                                spacingLeft: 0,
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize: '16px', align: 'center'}
                            },
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom',
                                x: 0,
                                y: 0,
                                floating: false,
                                borderWidth: 0,
                                reversed: false,
                                symbolRadius: 20,
                                symbolWidth: 10,
                                symbolHeight: 10,
                                itemMarginBottom: 7,
                                padding: 0,
                            },
                            title: {
                                text: 'Raw Score: {{ $score }}<br>Percentile Score: {{ $percentile }}%',
                                align: 'left',
                                verticalAlign: 'top',
                                style: {fontSize: '16px', lineHeight:'23px'},
                                floating: true,
                                y: 7
                            },
                            subtitle: {
                                text: "{{ $text }}",
                                floating: true,
                                align: 'left',
                                style: {
                                    fontSize: '16px',
                                    color: "{{ $color }}",
                                    fontWeight: 'bold',
                                    lineHeight: '45px'
                                },
                                y: 52
                            },
                            credits: false,
                            tooltip: {enabled: false},
                            plotOptions: {
                                allowPointSelect: false,
                                showInLegend: false,
                                areaspline: {
                                    marker: {
                                        enabled: false,
                                        hover: false,
                                        states: {hover: {enabled: false}}
                                    }
                                }
                            },
                            xAxis: {
                                title: {
                                    text: 'Raw Score',
                                    style: {fontSize: '16px', color: '#222222'}
                                },
                                allowDecimals: false,
                                crosshair: false,
                                lineColor: "#000000",
                                lineWidth: 1,
                                tickColor: '#000000',
                                tickWidth: 1,
                                // floor: 10,
                                // ceiling: 55,
                                tickInterval: 1,
                                plotLines: [{
                                    color: 'rgba(0,0,0,0.25)',
                                    dashStyle: 'longdash',
                                    value: {{ $score }},
                                    width: 1,
                                    zIndex: 5,
                                    label: {
                                        text: 'Your Score',
                                        align: 'center',
                                        verticalAlign: 'middle',
                                    }
                                }]
                            },
                            yAxis: {
                                title: {
                                    text: null,
                                    style: {fontSize: '16px'}
                                },
                                lineWidth: 0,
                                gridLineColor: 'transparent',
                                tickInterval: 10,
                                labels: {enabled:false},
                                categories: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100]
                            },
                            series: [{
                                color: '#E32731',
                                name: 'Not Recommended',
                                borderRadius:0,
                                marker: {enabled: false},
                                data: [
                                    [0,0],
                                    [10,20],
                                    [25,100],
                                    [40,20],
                                    [50,0]
                                ],
                                zoneAxis: 'x',
                                fillOpacity: 1,
                                zones: [
                                    @foreach($zones as $zone)
                                        <?php echo '{value: '.$zone['value'].', color:"'.$zone['color'].'"},'; ?>
                                    @endforeach
                                ],
                            },{
                                name: 'Caution',
                                color: '#E7B428',
                            },{
                                name: 'Pursue',
                                color: '#30BD21',
                            }]
                        });
                    @endif

                    {{-- Personality --}}
                    @if ($template == 'p')
                        <?php
                            // Defaults
                            $scoringController = new \App\Http\Controllers\ScoringController();
                            $defaults = $scoringController->getScoreDefaults($assessment->id);

                            // Actual Scores
                            if (isset($scores))
                                $defaults = $scores[$assessment->id];
                        ?>
                        $('#personality-chart').highcharts({
                            chart: {
                                type: 'bar',
                                style: {
                                    fontFamily: 'Avenir Next LT Pro',
                                    fontSize:'16px',
                                    align: 'center'
                                },
                                showInLegend: false,
                                title: {
                                    text: null
                                },
                                height: 250,
                                spacingLeft: 15
                            },
                            title: {
                                text: null
                            },
                            xAxis: {
                                categories: ['Honesty-Humility', 'Emotional Control', 'Extraversion', 'Agreeableness', 'Conscientiousness', 'Openness'],
                                labels: {
                                    style: {
                                        fontSize:'16px'
                                    }
                                },
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text: null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {
                                        fontSize:'16px'
                                    }
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {
                                enabled: false
                            },
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -50,
                                        style: {
                                            fontFamily: 'Avenir Next LT Pro',
                                            textShadow: false,
                                            color:'#ffffff',
                                            fontSize:'16px',
                                            align: 'center'
                                        }
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                showInLegend: false,
                                enableMouseTracking: false,
                                name: 'HEXACO',
                                color: '#e77928',
                                data: [
                                    {{ $defaults['humility'] }},
                                    {{ $defaults['emotion'] }},
                                    {{ $defaults['extraversion'] }},
                                    {{ $defaults['agreeableness'] }},
                                    {{ $defaults['consc'] }},
                                    {{ $defaults['openness'] }}
                                ]
                            }]
                        });
                    @endif

                    {{-- Safety --}}
                    @if ($template == 's')
                        <?php
                            // Defaults
                            $scoringController = new \App\Http\Controllers\ScoringController();
                            $defaults = $scoringController->getScoreDefaults($assessment->id);

                            // Actual Scores
                            if (isset($scores))
                                $defaults = $scores[$assessment->id];
                        ?>
                        $('#safety-chart').highcharts({
                            chart: {
                                type: 'bar',
                                style: {
                                    fontFamily: 'Avenir Next LT Pro',
                                    fontSize:'16px',
                                    align: 'center'
                                },
                                showInLegend: false,
                                title: {
                                    text: null
                                },
                                height: 250,
                                spacingLeft: 15
                            },
                            title: {
                                text: null
                            },
                            xAxis: {
                                categories: ['Confidence', 'Focus', 'Control', 'Safety Knowledge', 'Safety Motivation', 'Risk Avoidance'],
                                labels: {
                                    style: {
                                        fontSize:'16px'
                                    }
                                },
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text: null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {
                                        fontSize:'16px'
                                    }
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {
                                enabled: false
                            },
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -50,
                                        style: {
                                            fontFamily: 'Avenir Next LT Pro',
                                            textShadow: false,
                                            color:'#ffffff',
                                            fontSize:'16px',
                                            align: 'center'
                                        }
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                showInLegend: false,
                                enableMouseTracking: false,
                                name: 'HEXACO',
                                color: '#e77928',
                                data: [
                                    {{ $defaults['self_confidence'] }},
                                    {{ $defaults['focus'] }},
                                    {{ $defaults['control'] }},
                                    {{ $defaults['knowledge'] }},
                                    {{ $defaults['motivation'] }},
                                    {{ $defaults['risk'] }}
                                ]
                            }]
                        });
                    @endif

                    {{-- Leadership --}}
                    @if ($template == 'l')
                        <?php
                            // Defaults
                            $scoringController = new \App\Http\Controllers\ScoringController();
                            $defaults = $scoringController->getScoreDefaults($assessment->id);
                            $minNeededScorers = 3;

                            // Actual Scores
                            if (isset($scores))
                                $defaults = $scores[$assessment->id];
                        ?>
                        $('#leader-chart').highcharts({
                        chart: {
                            type: 'bar',
                            style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                            showInLegend: false,
                            title: {text:null},
                            height:450,
                            spacingLeft: 15
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            categories: ['Power', 'Information', 'Rewards', 'Knowledge', 'Relationships'],
                            labels: {style: {fontSize:'16px'}},
                            title: {
                                text: null
                            }
                        },
                        yAxis: {
                            min: 1,
                            max: 5,
                            text:null,
                            tickInterval: 0.5,
                            labels: {
                                overflow: 'justify',
                                style: {fontSize:'16px'}
                            },
                            title: {
                                text: null
                            }
                        },
                        tooltip: {enabled: false},
                        plotOptions: {
                            bar: {
                                dataLabels: {
                                    enabled: true,
                                    x: -35,
                                    style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            enableMouseTracking: false,
                            pointPadding: 0,
                            groupPadding: 0.1,
                            name: 'Top Score',
                            color: '#02244a',
                            data: [
                                {{ $defaults['top']['power'] }},
                                {{ $defaults['top']['information'] }},
                                {{ $defaults['top']['rewards'] }},
                                {{ $defaults['top']['knowledge'] }},
                                {{ $defaults['top']['relationships'] }},
                            ]
                        }, {
                            enableMouseTracking: false,
                            pointPadding: 0,
                            groupPadding: 0.1,
                            color: '#9FAAC5',
                            name: 'Average Leader Score',
                            data: [
                                {{ $defaults['average']['power'] }},
                                {{ $defaults['average']['information'] }},
                                {{ $defaults['average']['rewards'] }},
                                {{ $defaults['average']['knowledge'] }},
                                {{ $defaults['average']['relationships'] }},
                            ]
                        },
                                @if ($defaults['scorers'] >= $minNeededScorers)
                            {
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                name: 'Your Score',
                                color: '#e77928',
                                data: [
                                    {{ $defaults['main']['power'] }},
                                    {{ $defaults['main']['information'] }},
                                    {{ $defaults['main']['rewards'] }},
                                    {{ $defaults['main']['knowledge'] }},
                                    {{ $defaults['main']['relationships'] }},
                                ]
                            }
                            @endif
                        ]
                    });
                        $('#leader-chart-power').highcharts({
                            chart: {
                                type: 'bar',
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                                showInLegend: false,
                                title: {text:null},
                                height:190,
                                spacingLeft: 15
                            },
                            title: {
                                text: 'Dimension Score',
                                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
                            },
                            xAxis: {
                                categories: ['Overall Score'],
                                labels: {style: {fontSize:'16px'}},
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text:null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {fontSize:'16px'}
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {enabled: false},
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -35,
                                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                name: 'Top Power Score',
                                color: '#02244a',
                                data: [{{ $defaults['top']['power'] }}]
                            }, {
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#9FAAC5',
                                name: 'Average Power Score',
                                data: [{{ $defaults['average']['power'] }}]
                            },
                                    @if ($defaults['scorers'] >= $minNeededScorers)
                                {
                                    enableMouseTracking: false,
                                    pointPadding: 0,
                                    groupPadding: 0.1,
                                    name: 'Your Power Score',
                                    color: '#e77928',
                                    data: [{{ $defaults['main']['power'] }}]
                                }
                                @endif
                            ]
                        });
                        $('#leader-chart-power-subdimensions').highcharts({
                            chart: {
                                type: 'bar',
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                                showInLegend: false,
                                title: {text:null},
                                height:260,
                                spacingLeft: 35
                            },
                            title: {
                                text: 'Sub-Dimension Score',
                                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
                            },
                            xAxis: {
                                categories: ['Communication Empowerment', 'Autonomy' ],
                                labels: {style: {fontSize:'16px'}},
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text:null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {fontSize:'16px'}
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {enabled: false},
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -50,
                                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#02244a',
                                name: 'Top Score',
                                data: [
                                    {{ $defaults['top']['commem'] }},
                                    {{ $defaults['top']['autonomy'] }}
                                ]
                            }, {
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#9FAAC5',
                                name: 'Average Score',
                                data: [
                                    {{ $defaults['average']['commem'] }},
                                    {{ $defaults['average']['autonomy'] }}
                                ]
                            },
                                    @if ($defaults['scorers'] >= $minNeededScorers)
                                {
                                    enableMouseTracking: false,
                                    pointPadding: 0,
                                    groupPadding: 0.1,
                                    color: '#e77928',
                                    name: 'Your Score',
                                    data: [
                                        {{ $defaults['main']['commem'] }},
                                        {{ $defaults['main']['autonomy'] }}
                                    ]
                                }
                                @endif
                            ]
                        });
                        $('#leader-chart-information').highcharts({
                            chart: {
                                type: 'bar',
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                                showInLegend: false,
                                title: {text:null},
                                height:190,
                                spacingLeft: 15
                            },
                            title: {
                                text: 'Dimension Score',
                                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
                            },
                            xAxis: {
                                categories: ['Overall Score'],
                                labels: {style: {fontSize:'16px'}},
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text:null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {fontSize:'16px'}
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {enabled: false},
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -35,
                                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                name: 'Top Information Score',
                                color: '#02244a',
                                data: [{{ $defaults['top']['information'] }}]
                            }, {
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#9FAAC5',
                                name: 'Average Information Score',
                                data: [{{ $defaults['average']['information'] }}]
                            },
                                    @if ($defaults['scorers'] >= $minNeededScorers)
                                {
                                    enableMouseTracking: false,
                                    pointPadding: 0,
                                    groupPadding: 0.1,
                                    name: 'Your Information Score',
                                    color: '#e77928',
                                    data: [{{ $defaults['main']['information'] }}]
                                }
                                @endif
                            ]
                        });
                        $('#leader-chart-information-subdimensions').highcharts({
                            chart: {
                                type: 'bar',
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                                showInLegend: false,
                                title: {text:null},
                                height:350,
                                spacingLeft: 35
                            },
                            title: {
                                text: 'Sub-Dimension Score',
                                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
                            },
                            xAxis: {
                                categories: ['Information - General', 'Communication with Upper Mgmt.', 'Feedback'],
                                labels: {style: {fontSize:'16px'}},
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text:null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {fontSize:'16px'}
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {enabled: false},
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -35,
                                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#02244a',
                                name: 'Top Score',
                                data: [
                                    {{ $defaults['top']['general'] }},
                                    {{ $defaults['top']['management'] }},
                                    {{ $defaults['top']['feedback'] }}
                                ]
                            }, {
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#9FAAC5',
                                name: 'Average Score',
                                data: [
                                    {{ $defaults['average']['general'] }},
                                    {{ $defaults['average']['management'] }},
                                    {{ $defaults['average']['feedback'] }}
                                ]
                            },
                                    @if ($defaults['scorers'] >= $minNeededScorers)
                                {
                                    enableMouseTracking: false,
                                    pointPadding: 0,
                                    groupPadding: 0.1,
                                    color: '#e77928',
                                    name: 'Your Score',
                                    data: [
                                        {{ $defaults['main']['general'] }},
                                        {{ $defaults['main']['management'] }},
                                        {{ $defaults['main']['feedback'] }}
                                    ]
                                }
                                @endif
                            ]
                        });
                        $('#leader-chart-rewards').highcharts({
                            chart: {
                                type: 'bar',
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                                showInLegend: false,
                                title: {text:null},
                                height:160,
                                spacingLeft: 35
                            },
                            title: {
                                text: null,
                                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
                            },
                            xAxis: {
                                categories: ['Rewards'],
                                labels: {style: {fontSize:'16px'}},
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text:null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {fontSize:'16px'}
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {enabled: false},
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -35,
                                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#02244a',
                                name: 'Top Reward Score',
                                data: [{{ $defaults['top']['rewards'] }}]
                            }, {
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#9FAAC5',
                                name: 'Average Reward Score',
                                data: [{{ $defaults['average']['rewards'] }}]
                            },
                                @if ($defaults['scorers'] >= $minNeededScorers)
                                {
                                    enableMouseTracking: false,
                                    pointPadding: 0,
                                    groupPadding: 0.1,
                                    color: '#e77928',
                                    name: 'Your Reward Score',
                                    data: [{{ $defaults['main']['rewards'] }}]
                                }
                                @endif
                            ]
                        });
                        $('#leader-chart-knowledge').highcharts({
                            chart: {
                                type: 'bar',
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                                showInLegend: false,
                                title: {text:null},
                                height:190,
                                spacingLeft: 15
                            },
                            title: {
                                text: 'Dimension Score',
                                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
                            },
                            xAxis: {
                                categories: ['Overall Score'],
                                labels: {style: {fontSize:'16px'}},
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text:null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {fontSize:'16px'}
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {enabled: false},
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -35,
                                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                name: 'Top Knowledge Score',
                                color: '#02244a',
                                data: [{{ $defaults['top']['knowledge'] }}]
                            }, {
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#9FAAC5',
                                name: 'Average Knowledge Score',
                                data: [{{ $defaults['average']['knowledge'] }}]
                            },
                                @if ($defaults['scorers'] >= $minNeededScorers)
                                {
                                    enableMouseTracking: false,
                                    pointPadding: 0,
                                    groupPadding: 0.1,
                                    name: 'Your Knowledge Score',
                                    color: '#e77928',
                                    data: [{{ $defaults['main']['knowledge'] }}]
                                }
                                @endif
                            ]
                        });
                        $('#leader-chart-knowledge-subdimensions').highcharts({
                            chart: {
                                type: 'bar',
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                                showInLegend: false,
                                title: {text:null},
                                height:340,
                                spacingLeft:25
                            },
                            title: {
                                text: 'Sub-Dimension Score',
                                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
                            },
                            xAxis: {
                                categories: ['Knowledge Empowerment', 'Mentoring', 'Knowledge Acquisition'],
                                labels: {style: {fontSize:'16px'}},
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text:null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {fontSize:'16px'}
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {enabled: false},
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -35,
                                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#02244a',
                                name: 'Top Score',
                                data: [
                                    {{ $defaults['top']['empowerment'] }},
                                    {{ $defaults['top']['mentoring'] }},
                                    {{ $defaults['top']['acquisition'] }}
                                ]
                            }, {
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#9FAAC5',
                                name: 'Average Score',
                                data: [
                                    {{ $defaults['average']['empowerment'] }},
                                    {{ $defaults['average']['mentoring'] }},
                                    {{ $defaults['average']['acquisition'] }}
                                ]
                            },
                                @if ($defaults['scorers'] >= $minNeededScorers)
                                {
                                    enableMouseTracking: false,
                                    pointPadding: 0,
                                    groupPadding: 0.1,
                                    color: '#e77928',
                                    name: 'Your Score',
                                    data: [
                                        {{ $defaults['main']['empowerment'] }},
                                        {{ $defaults['main']['mentoring'] }},
                                        {{ $defaults['main']['acquisition'] }}
                                    ]
                                }
                                @endif
                            ]
                        });
                        $('#leader-chart-relationships').highcharts({
                            chart: {
                                type: 'bar',
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                                showInLegend: false,
                                title: {text:null},
                                height:190,
                                spacingLeft: 15
                            },
                            title: {
                                text: 'Dimension Score',
                                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
                            },
                            xAxis: {
                                categories: ['Overall Score'],
                                labels: {style: {fontSize:'16px'}},
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text:null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {fontSize:'16px'}
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {enabled: false},
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -35,
                                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                name: 'Top Relationships Score',
                                color: '#02244a',
                                data: [{{ $defaults['top']['relationships'] }}]
                            }, {
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#9FAAC5',
                                name: 'Average Relationships Score',
                                data: [{{ $defaults['average']['relationships'] }}]
                            },
                                    @if ($defaults['scorers'] >= $minNeededScorers)
                                {
                                    enableMouseTracking: false,
                                    pointPadding: 0,
                                    groupPadding: 0.1,
                                    name: 'Your Relationships Score',
                                    color: '#e77928',
                                    data: [{{ $defaults['main']['relationships'] }}]
                                }
                                @endif
                            ]
                        });
                        $('#leader-chart-relationships-subdimensions').highcharts({
                            chart: {
                                type: 'bar',
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                                showInLegend: false,
                                title: {text:null},
                                height:400,
                                spacingLeft: 35
                            },
                            title: {
                                text: 'Sub-Dimension Score',
                                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
                            },
                            xAxis: {
                                categories: ['Conflict Management', 'Teamwork', 'Communication', 'Respect'],
                                labels: {style: {fontSize:'16px'}},
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text:null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {fontSize:'16px'}
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {enabled: false},
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -35,
                                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#02244a',
                                name: 'Top Score',
                                data: [
                                    {{ $defaults['top']['conflict'] }},
                                    {{ $defaults['top']['teamwork'] }},
                                    {{ $defaults['top']['communication'] }},
                                    {{ $defaults['top']['respect'] }},
                                ]
                            }, {
                                enableMouseTracking: false,
                                pointPadding: 0,
                                groupPadding: 0.1,
                                color: '#9FAAC5',
                                name: 'Average Score',
                                data: [
                                    {{ $defaults['average']['conflict'] }},
                                    {{ $defaults['average']['teamwork'] }},
                                    {{ $defaults['average']['communication'] }},
                                    {{ $defaults['average']['respect'] }},
                                ]
                            },
                                    @if ($defaults['scorers'] >= $minNeededScorers)
                                {
                                    enableMouseTracking: false,
                                    pointPadding: 0,
                                    groupPadding: 0.1,
                                    color: '#e77928',
                                    name: 'Your Score',
                                    data: [
                                        {{ $defaults['main']['conflict'] }},
                                        {{ $defaults['main']['teamwork'] }},
                                        {{ $defaults['main']['communication'] }},
                                        {{ $defaults['main']['respect'] }},
                                    ]
                                }
                                @endif
                            ]
                        });
                    @endif

                    {{-- WM OSpan --}}
                    @if ($template == 'wmo')
                        <?php
                            // Defaults
                            $scoringController = new \App\Http\Controllers\ScoringController();
                            $defaults = $scoringController->getScoreDefaults($assessment->id);
                            $score = $defaults['score'];
                            $percentile = $defaults['percentile'];
                            $zones = $scoringController->getZones(null, $assessment->id, $report->score_method);
                            $text = 'Pursue';
                            $color = '#30BD21';
                            if (! $report->show_fit)
                            {
                                $text = '';
                                $color = '#000000';
                                $zones = $scoringController->getZones(null, $assessment->id, 2);
                            }

                            // Actual scores
                            if (isset($scores))
                            {
                                // Divisions
                                if ($scores[$assessment->id]['division'] == 1)
                                {
                                    $text = 'Not Recommended';
                                    $color = '#E32731';
                                }
                                elseif ($scores[$assessment->id]['division'] == 2 || $scores[$assessment->id]['division'] == 3)
                                {
                                    $text = 'Caution';
                                    $color = '#E7B428';
                                }
                                elseif ($scores[$assessment->id]['division'] == 4 || $scores[$assessment->id]['division'] == 5)
                                {
                                    $text = 'Pursue';
                                    $color = '#30BD21';
                                }

                                // Scores
                                $score = $scores[$assessment->id]['score'];
                                $percentile = $scores[$assessment->id]['percentile'];
                                $zones = $scores[$assessment->id]['zones'];

                                // No fit recommendation
                                if (! $report->show_fit)
                                {
                                    $text = '';
                                    $color = '#000000';
                                    $zones = $scoringController->getZones(null, $assessment->id, 2);
                                }
                            }
                        ?>
                        $('#ospan-chart').highcharts({
                            chart: {
                                type: 'areaspline',
                                height:430,
                                borderRadius: 0,
                                spacingRight:0,
                                plotBackgroundColor:'rgba(0,0,0,0)',
                                backgroundColor:'rgba(0,0,0,0)',
                                spacingLeft:0,
                                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'}
                            },
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom',
                                x: 0,
                                y: 0,
                                floating: false,
                                borderWidth: 0,
                                reversed: false,
                                symbolRadius:20,
                                symbolWidth:10,
                                symbolHeight:10,
                                itemMarginBottom:7,
                                padding:0,
                            },
                            title: {
                                text: 'Raw Score: {{ $score }}<br>Percentile Score: {{ $percentile }}%',
                                align: 'left',
                                verticalAlign: 'top',
                                style: {fontSize: '16px', lineHeight:'23px'},
                                floating: true,
                                y:7
                            },
                        subtitle: {
                            text: "{{ $text }}",
                            floating: true,
                            align: 'left',
                            style: {
                                fontSize: '16px',
                                color: "{{ $color }}",
                                fontWeight: 'bold',
                                lineHeight: '45px'
                            },
                            y: 52
                        },
                            credits:false,
                            tooltip: {enabled: false},
                            plotOptions: {
                                allowPointSelect:false,
                                showInLegend: false,
                                areaspline: {
                                    marker: {
                                        enabled: false,
                                        hover:false,
                                        states: {hover: {enabled: false}}
                                    }
                                }
                            },
                            xAxis: {
                                title: {
                                    text: 'Raw Score',
                                    style: {fontSize: '16px', color:'#222222'}
                                },
                                allowDecimals:false,
                                crosshair:false,
                                lineColor:"#000000",
                                lineWidth:1,
                                tickColor: '#000000',
                                tickWidth: 1,
                                // floor: 10,
                                // ceiling: 55,
                                tickInterval:1,
                                plotLines: [{
                                    color: 'rgba(0,0,0,0.25)',
                                    dashStyle: 'longdash',
                                    value: {{ $score }},
                                    width: 1,
                                    zIndex: 5,
                                    label: {
                                        text: 'Your Score',
                                        align: 'center',
                                        verticalAlign: 'middle',
                                    }
                                }]
                            },
                            yAxis: {
                                title: {
                                    text:null,
                                    style: {fontSize: '16px'}
                                },
                                lineWidth:0,
                                gridLineColor: 'transparent',
                                tickInterval: 10,
                                labels: {enabled:false},
                                categories: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100]
                            },
                            series: [{
                                color: '#E32731',
                                name: 'Not Recommended',
                                borderRadius:0,
                                marker: {enabled: false},
                                data: [
                                    [0,0],
                                    [5,20],
                                    [11,100],
                                    [18,20],
                                    [23,0]
                                ],
                                zoneAxis: 'x',
                                fillOpacity:1,
                                zones: [
                                    @foreach($zones as $zone)
									    <?php echo '{value: '.$zone['value'].', color:"'.$zone['color'].'"},'; ?>
                                    @endforeach
                                ],
                            },{
                                name: 'Caution',
                                color: '#E7B428',
                            },{
                                name: 'Pursue',
                                color: '#30BD21',
                            }]
                        });
                    @endif
                @endif
            @endforeach
        @endforeach
    });
</script>