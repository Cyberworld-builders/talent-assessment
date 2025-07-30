<html moznomarginboxes="" mozdisallowselectionprint="">
<head>
    <meta name="viewport" content="width=device-width">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="/wp/wp-content/themes/aoe/js/highcharts.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="/wp/wp-content/themes/aoe/reports/reports.css">
    <style>
        h5 {
            position: relative;
            z-index: 1;
        }
        body {
            /*background-image:none;*/
            /*background-color:#ddd;*/
            background:url('/wp/wp-content/themes/aoe/images/aoe-group_home-banner.jpg') fixed no-repeat;
            background-size: 100% 100%;
        }
        .page-container {
            background-color: white;
            height: 1100px;
            width: 850px;
            padding: 45px 45px;
            page-break-after: always;
            margin: 0 auto;
            margin-bottom: 15px;
        }
        .img-container-1, .img-container-2 {position: absolute; height:997px; width:760px;}
        .img-container-1 small, .img-container-2 small {top:0; right:15px; position:absolute;}
        .img-container-1 img {position:absolute; bottom:0px; right:15px; width:100px;}
        .img-container-2 img {position:absolute; bottom:0px; left:15px; width:100px;}
        h1 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:64px; color:#02244a; line-height:64px; margin-top:0px;}
        h2 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:32px; color:#02244a; line-height:45px; margin-top:0px;}
        h3 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:23px; color:#02244a; line-height:32px; margin-top:0px;}
        .cover-for h3 {font-family:'Avenir Next LT Pro'; font-size:16px; line-height:23px;}
        h4 {-webkit-print-color-adjust: exact; font-family:'Didot Italic'; color:#02244a; font-size:32px; line-height:32px;}
        h5 {font-family:'Avenir Next LT Pro Medium'; font-size:1.616em; line-height:1.616em; color:#02244a;}
        .yellow-block {
            display: block;
            float: left;
            height: 150px;
            width: 150px;
            background: #E7B428;
            line-height: 23px;
            border-radius: 100px;
            margin-top: -42px;
            margin-right: 18px;
        }
        h6 {font-family:'Avenir Next LT Pro'; font-size:13px; line-height:23px; margin-top:0px;}
        p, ul li {font-family:'Avenir Next LT Pro'; font-size:16px; line-height:26px; margin-bottom:16px; list-style-type:square;}
        .red {color:#E32731;} .yellow {color:#E7B428;} .green {color:#30BD21;}
        .text-justify {text-align:justify;}
        .border-bottom {border-top:1px solid rgba(0,0,0,0.1); padding-top:10px;}
        .underline {border-bottom:1px solid black;}
        small {font-family:'Avenir Next LT Pro'; font-size:13px; line-height:18px;}
        .leftside {left:15px!important; right:inherit; position:absolute;}
        .disclaimer {padding-top:30px;}
        .cover-logo {padding:90px 0px 0px; margin:0 auto;}
        .cover-for {padding-top:90px; padding-bottom:120px;}
        .cover-for {padding: 0;}
        .white {color:white;}
        #chart {width:600px; margin:0 auto;}
        .chart {margin-left:-45px;}

        @media screen and (max-width:867px){
            /*body {background-image:none; background-color:white;}*/
            .page-container {padding:32px; height:auto; min-height:800px; width:100%;}
            .img-container-1 img, .img-container-2 img, .report-logo {display:none;}
            .img-container-1, .img-container-2 {
                position: absolute;
                height:90%;
                width:90%;
            }
            #chart {width:100%; height:100%; margin-left:15px;}
            .highcharts-yaxis-title, .highcharts-xaxis-title {display:none;}
            .cover-for, .cover-logo {padding:0px;}
            h1 {line-height:1em; font-size:3.231em;}
            h3 {font-size: 1.616em;}
            h1, h2, h3, h4 {-webkit-print-color-adjust: exact;}
        }
    </style>
    <script>
        // Styles
        $(function () {

            // Employee Morale
            $('#chart1').highcharts({
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
                    categories: ['Average Score Across Company', 'Development', 'Design', 'Marketing', 'Management'],
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
                            y: -1,
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
                        {{ 3.47 }},
                        {{ 3.19 }},
                        {{ 4.02 }},
                        {{ 3.58 }},
                        {{ 3.10 }},
                    ]
                }]
            });
            $('#chart2').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 4.05 }},
                        {{ 3.85 }},
                        {{ 4.05 }},
                        {{ 3.91 }},
                        {{ 4.38 }},
                    ]
                }]
            });
            $('#chart3').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 3.20 }},
                        {{ 3.09 }},
                        {{ 4.22 }},
                        {{ 2.35 }},
                        {{ 3.14 }},
                    ]
                }]
            });
            $('#chart4').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 2.98 }},
                        {{ 3.19 }},
                        {{ 4.02 }},
                        {{ 2.58 }},
                        {{ 2.10 }},
                    ]
                }]
            });

            // Leader Involvement
            $('#chart5').highcharts({
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
                    categories: ['Average Score Across Company', 'Development', 'Design', 'Marketing', 'Management'],
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
                            y: -1,
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
                        {{ 3.47 }},
                        {{ 3.19 }},
                        {{ 4.02 }},
                        {{ 3.58 }},
                        {{ 3.10 }},
                    ]
                }]
            });
            $('#chart6').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 4.05 }},
                        {{ 3.85 }},
                        {{ 4.05 }},
                        {{ 3.91 }},
                        {{ 4.38 }},
                    ]
                }]
            });
            $('#chart7').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 3.20 }},
                        {{ 3.09 }},
                        {{ 4.22 }},
                        {{ 2.35 }},
                        {{ 3.14 }},
                    ]
                }]
            });
            $('#chart8').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 2.98 }},
                        {{ 3.19 }},
                        {{ 4.02 }},
                        {{ 2.58 }},
                        {{ 2.10 }},
                    ]
                }]
            });
            $('#chart9').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 2.98 }},
                        {{ 3.19 }},
                        {{ 4.02 }},
                        {{ 2.58 }},
                        {{ 2.10 }},
                    ]
                }]
            });

            // Organizational Mindset
            $('#chart10').highcharts({
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
                    categories: ['Average Score Across Company', 'Development', 'Design', 'Marketing', 'Management'],
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
                            y: -1,
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
                        {{ 3.47 }},
                        {{ 3.19 }},
                        {{ 4.02 }},
                        {{ 3.58 }},
                        {{ 3.10 }},
                    ]
                }]
            });
            $('#chart11').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 3.20 }},
                        {{ 3.09 }},
                        {{ 4.22 }},
                        {{ 2.35 }},
                        {{ 3.14 }},
                    ]
                }]
            });
            $('#chart12').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 2.98 }},
                        {{ 3.19 }},
                        {{ 4.02 }},
                        {{ 2.58 }},
                        {{ 2.10 }},
                    ]
                }]
            });
            $('#chart13').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 4.05 }},
                        {{ 3.85 }},
                        {{ 4.05 }},
                        {{ 3.91 }},
                        {{ 4.38 }},
                    ]
                }]
            });

            // Team Climate
            $('#chart14').highcharts({
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
                    categories: ['Average Score Across Company', 'Development', 'Design', 'Marketing', 'Management'],
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
                            y: -1,
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
                        {{ 3.47 }},
                        {{ 3.19 }},
                        {{ 4.02 }},
                        {{ 3.58 }},
                        {{ 3.10 }},
                    ]
                }]
            });
            $('#chart15').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 3.20 }},
                        {{ 3.09 }},
                        {{ 4.22 }},
                        {{ 2.35 }},
                        {{ 3.14 }},
                    ]
                }]
            });
            $('#chart16').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 2.98 }},
                        {{ 3.19 }},
                        {{ 4.02 }},
                        {{ 2.58 }},
                        {{ 2.10 }},
                    ]
                }]
            });
            $('#chart17').highcharts({
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
                    height: 180,
                    spacingLeft: 45
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: ['Average Score', 'Development', 'Design', 'Marketing', 'Management'],
                    labels: {
                        style: {
                            fontSize:'14px'
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
                            fontSize:'12px'
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
                            y: -1,
                            style: {
                                fontFamily: 'Avenir Next LT Pro',
                                textShadow: false,
                                color:'#ffffff',
                                fontSize:'14px',
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
                    borderWidth: 5,
                    borderColor: '#e77928',
                    data: [
                        {{ 2.98 }},
                        {{ 3.19 }},
                        {{ 4.02 }},
                        {{ 2.58 }},
                        {{ 2.10 }},
                    ]
                }]
            });
        });
    </script>
</head>
<body>

<?php
    $page = 1;
?>

{{-- Cover --}}
<div class="page-container" id="{{ $page }}">
    <div class="container">

        {{-- Logo --}}
        <div class="row">
            <div class="col-xs-2 visible-xs"></div>
            <div class="col-xs-8 col-sm-8 col-sm-offset-2 text-center">
                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                <img class="img-responsive text-center cover-logo" src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
            </div>
        </div>

        {{-- Candidate --}}
        <div class="row">
            <div class="col-sm-5 col-sm-offset-7 text-right cover-for">
                <br><br><br class="hidden-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                <h3>Organizational Profile for:</h3>
                <h4>AOE</h4>
                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
            </div>
        </div>

        {{-- Overview --}}
        <div class="row">
            <div class="col-sm-10">
                <h5>Overview</h5>
                <p>
                    This report measures the organizational well-being of your company and examines important dimensions such as employee
                    morale, leader involvement, organizational mindset, and team climate, all of which are key indicators of the overall
                    health of your organization.
                </p>
            </div>
        </div>
        <div class="row"><div class="col-sm-12"><hr></div></div>

        {{-- Disclaimer --}}
        <div class="row disclaimer">
            <div class="col-xs-10 col-sm-10">
                <h6 class="small">
                    AOE Science offers the most scientifically valid candidate assessments. AOE uses the latest Talent Evidence from the scientific literature,
                    their own research, and the needs of organizations to arrive at Evidence-Based Talent Solutions.
                </h6>
            </div>
            <div class="col-xs-2 col-sm-2 text-right report-logo">
                <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/report-logo-1.png">
            </div>
        </div>
    </div>
</div>

{{-- Employee Morale --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-O-01.png">
                    </div>
                </div>
                <h1>Employee Morale</h1>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                <p>
                    Commitment, satisfaction and well-being are the key indicators for employee morale. The present report provides an overall view of employee
                    morale across the entire organization and also for each supervisory team. Research demonstrates that high employee morale is a predictor of
                    employee performance and that employee morale is predicted by team climate. Although there are other indicators of morale, the present survey
                    assessed the three primary aspects:
                </p>
                <br/><br/>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Organizational Commitment</h3>
            </div>
            <div class="col-xs-8">
                <p>An employee's level of engagement and identification with the organization; a metric of loyalty.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Satisfaction</h3>
            </div>
            <div class="col-xs-8">
                <p>Extent to which employees enjoy working at the organization.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Well-Being</h3>
            </div>
            <div class="col-xs-8">
                <p>
                    The subjective, social and psychological health of employees which includes strong social bonds, self-acceptance, work autonomy,
                    growth and development as an employee, and goal achievement.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Employee Morale Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-O-01.png">
                    </div>
                </div>
                <h5>Employee Morale - Overall Score</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <p>
                    Below is the average score across the entire company. Scores for each functional area are below
                    that. These scores are averages of respondent ratings.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="chart1"></div>
            </div>
            <div class="col-sm-12 text-justify">
                <br/><br/>
                <h5>Summary</h5>
                <p>
                    Commitment, satisfaction and well-being are the key indicators for employee morale. If you see low
                    scores on this report, then organizational well-being is in jeopardy. Action is needed. Each question
                    in this report is an actionable item - you can use these data to help increase morale and achieve
                    higher levels of performance and well-being.
                </p>
                <p>
                    If you require anything more, please let us know. We would be happy to help you take action on these
                    results to increase your organizational well-being.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Employee Morale Sub-dimension Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Title --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <br/><br/>
                <h2>Employee Morale - Subdimension Scores</h2>
            </div>
        </div>

        {{-- Heading --}}
        <div class="row">
            <div class="col-xs-3">
                <p>Dimension</p>
            </div>
            <div class="col-xs-8">
                <p>Scores</p>
            </div>
        </div>

        {{-- Scores --}}
        <div class="row">
            <div class="col-xs-3">
                <h5>Well-Being</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart2"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Satisfaction</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart3"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Commitment</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart4"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Leader Involvement --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-O-01.png">
                    </div>
                </div>
                <h1>Leader Involvement</h1>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                <p>
                    This report measures specific leadership behaviors which are desirable for leading change
                    and inspiring employees. The purpose of this report is to provide actionable
                    feedback to achieve increased team effectiveness and improve management skills which
                    positively impacts business results.
                </p>
                <br/><br/>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Information</h3>
            </div>
            <div class="col-xs-8">
                <p>
                    Providing accurate and necessary information to employees which better enables them
                    to do their jobs effectively.
                </p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Reward</h3>
            </div>
            <div class="col-xs-8">
                <p>Recognition and rewards for strong performance.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Power</h3>
            </div>
            <div class="col-xs-8">
                <p>
                    Empowers employees to make decisions about how to carry out their work;
                    decision-making latitude and autonomy.
                </p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Knowledge</h3>
            </div>
            <div class="col-xs-8">
                <p>
                    Providing employees with the relevant performance feedback and training.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Leader Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-O-01.png">
                    </div>
                </div>
                <h5>Leader Involvement - Overall Score</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <p>
                    Below is the average score across the entire company. Scores for each functional area are below
                    that. These scores are averages of respondent ratings.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="chart5"></div>
            </div>
            <div class="col-sm-12 text-justify">
                <br/><br/>
                <h5>Summary</h5>
                <p>
                    Leader involvement behaviors are critical to help employees achieve their full potential
                    and leaders achieve their full leadership potential. As involvement increases, it will
                    bring improvements to team climate, employees' attitudes and performance, and organizational
                    well-being as a whole. Leaders have direct control over the behaviors that support an
                    engaged workforce. This is a highly actionable aspect of leadership - and the survey results
                    can identify strengths and opportunities for improvement. The next step is to create an
                    action plan to increase involvement.
                </p>
                <p>
                    If you require anything more, please let us know. We would be happy to help you take action on these
                    results to increase your organizational well-being.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Leader Sub-dimension Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Title --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <br/><br/>
                <h2>Leader Involvement - Subdimension Scores</h2>
            </div>
        </div>

        {{-- Heading --}}
        <div class="row">
            <div class="col-xs-3">
                <p>Dimension</p>
            </div>
            <div class="col-xs-8">
                <p>Scores</p>
            </div>
        </div>

        {{-- Scores --}}
        <div class="row">
            <div class="col-xs-3">
                <h5>Information</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart6"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Reward</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart7"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Power</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart8"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Knowledge</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart9"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Organizational Mindset --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-O-01.png">
                    </div>
                </div>
                <h1>Organizational Mindset</h1>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                <p>
                    The organizational mindset defines the extent to which your organization focuses on
                    well-being. The organizational mindset report is designed to provide a summary of the
                    key ingredients to setting organizational well-being in positive motion:
                </p>
                <br/><br/>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Organizational Support</h3>
            </div>
            <div class="col-xs-8">
                <p>
                    Extent to which the organization provides a supportive environment for employee well-being
                    and performance.
                </p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Organizational Engagement</h3>
            </div>
            <div class="col-xs-8">
                <p>
                    The organization's focus on employee engagement and development to drive performance.
                </p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Strategic Alignment</h3>
            </div>
            <div class="col-xs-8">
                <p>
                    Extent to which employee well-being is a part of the organization's core strategy.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Mindset Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-O-01.png">
                    </div>
                </div>
                <h5>Organizational Mindset - Overall Score</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <p>
                    Below is the average score across the entire company. Scores for each functional area are below
                    that. These scores are averages of respondent ratings.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="chart10"></div>
            </div>
            <div class="col-sm-12 text-justify">
                <br/><br/>
                <h5>Summary</h5>
                <p>
                    The mindset of your organization is the top-level driver to help AOE focus on organizational
                    well-being. By working to increase organizational support, engagement, and strategic
                    alignment, you will be well on your way to improving organizational well-being.
                </p>
                <p>
                    If you require anything more, please let us know. We would be happy to help you take action
                    on these results to increase your organizational well-being.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Mindset Sub-dimension Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Title --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <br/><br/>
                <h2>Organizational Mindset - Subdimension Scores</h2>
            </div>
        </div>

        {{-- Heading --}}
        <div class="row">
            <div class="col-xs-3">
                <p>Dimension</p>
            </div>
            <div class="col-xs-8">
                <p>Scores</p>
            </div>
        </div>

        {{-- Scores --}}
        <div class="row">
            <div class="col-xs-3">
                <h5>Organizational Support</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart11"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Organizational Engagement</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart12"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Strategic Alignment</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart13"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Team Climate --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-O-01.png">
                    </div>
                </div>
                <h1>Team Climate</h1>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                <p>
                    Team climate is the "psychological glue" that holds a team together - a team's confidence,
                    respect, and shared engagement that directly lead to morale. A climate is established by
                    a work unit's (team) leader. Research demonstrates that a highly involved leader produces
                    better team climates. Team climates consist of a shared understanding of engagement,
                    confidence, and feelings of respect. These are captured in the team climate report.
                </p>
                <br/><br/>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Shared Engagement</h3>
            </div>
            <div class="col-xs-8">
                <p>
                    Extent to which team members are equally enthusiastic & involved in their work.
                </p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Collective Confidence</h3>
            </div>
            <div class="col-xs-8">
                <p>
                    The degree of confidence team members have about their skills and their ability to do
                    complete and accurate work.
                </p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <h3>Mutual Respect & Trust</h3>
            </div>
            <div class="col-xs-8">
                <p>
                    The amount of respect and trust employees have for their fellow team members.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Team Climate Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-O-01.png">
                    </div>
                </div>
                <h5>Team Climate - Overall Score</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <p>
                    Below is the average score across the entire company. Scores for each functional area are below
                    that. These scores are averages of respondent ratings.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="chart14"></div>
            </div>
            <div class="col-sm-12 text-justify">
                <br/><br/>
                <h5>Summary</h5>
                <p>
                    Team climate is the "psychological glue" that holds a team together - a team's confidence,
                    respect, and shared engagement that directly lead to morale. The present report provides
                    a snapshot of how your team is doing. The higher the climate score, the better the
                    potential for high organizational well-being. A team leader and all team member can use
                    each question in this report as an actionable item to improve a team's confidence, trust,
                    and engagement.
                </p>
                <p>
                    If you require anything more, please let us know. We would be happy to help you take action
                    on these results to increase your organizational well-being.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Team Sub-dimension Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Title --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <br/><br/>
                <h2>Team Climate - Subdimension Scores</h2>
            </div>
        </div>

        {{-- Heading --}}
        <div class="row">
            <div class="col-xs-3">
                <p>Dimension</p>
            </div>
            <div class="col-xs-8">
                <p>Scores</p>
            </div>
        </div>

        {{-- Scores --}}
        <div class="row">
            <div class="col-xs-3">
                <h5>Team Engagement</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart15"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Collective Confidence</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart16"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Respect & Trust</h5>
            </div>
            <div class="col-xs-8">
                <div class="chart">
                    <div id="chart17"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<p class="text-center white">Powered by <a href="http://aoescience.com/">AOE Science</a></p>
</body>
</html>