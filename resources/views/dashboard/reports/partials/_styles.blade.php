<style>
    /* Fonts */
    @font-face {
        font-family: 'Bebas Neue';
        src: url('{{ $fontsUrl }}/bebasneue_regular-webfont.eot');
        src: url('{{ $fontsUrl }}/bebasneue_regular-webfont.eot?#iefix') format('embedded-opentype'),
        url('{{ $fontsUrl }}/bebasneue_regular-webfont.woff') format('woff'),
        url('{{ $fontsUrl }}fonts/bebasneue_regular-webfont.ttf') format('truetype'),
        url('{{ $fontsUrl }}/bebasneue_regular-webfont.svg#Bebas Neue') format('svg');
        font-weight: normal;
        font-style: normal;
    }
    @font-face {
        font-family: 'Didot';
        src: url('{{ $fontsUrl }}/DidotLTStd-Roman.eot');
        src: url('{{ $fontsUrl }}/DidotLTStd-Roman.eot?#iefix') format('embedded-opentype'),
        url('{{ $fontsUrl }}/DidotLTStd-Roman.woff') format('woff'),
        url('{{ $fontsUrl }}/DidotLTStd-Roman.ttf') format('truetype'),
        url('{{ $fontsUrl }}/DidotLTStd-Roman.svg#Didot') format('svg');
        font-weight: normal;
        font-style: normal;
    }
    @font-face {
        font-family: 'Didot Italic';
        src: url('{{ $fontsUrl }}/DidotLTStd-Italic.eot');
        src: url('{{ $fontsUrl }}/DidotLTStd-Italic.eot?#iefix') format('embedded-opentype'),
        url('{{ $fontsUrl }}/DidotLTStd-Italic.woff') format('woff'),
        url('{{ $fontsUrl }}/DidotLTStd-Italic.ttf') format('truetype'),
        url('{{ $fontsUrl }}/DidotLTStd-Italic.svg#Didot Italic') format('svg');
        font-weight: normal;
        font-style: normal;
    }
    @font-face {
        font-family: 'Avenir Next LT Pro';
        src: url('{{ $fontsUrl }}/AvenirNextLTPro-Regular.eot');
        src: url('{{ $fontsUrl }}/AvenirNextLTPro-Regular.eot?#iefix') format('embedded-opentype'),
        url('{{ $fontsUrl }}/AvenirNextLTPro-Regular.woff') format('woff'),
        url('{{ $fontsUrl }}/AvenirNextLTPro-Regular.ttf') format('truetype'),
        url('{{ $fontsUrl }}/AvenirNextLTPro-Regular.svg#Avenir Next LT Pro') format('svg');
        font-weight: normal;
        font-style: normal;
    }
    @font-face {
        font-family: 'Avenir Next LT Pro Medium';
        src: url('{{ $fontsUrl }}/AvenirNextLTPro-Medium.eot');
        src: url('{{ $fontsUrl }}/AvenirNextLTPro-Medium.eot?#iefix') format('embedded-opentype'),
        url('{{ $fontsUrl }}/AvenirNextLTPro-Medium.woff') format('woff'),
        url('{{ $fontsUrl }}/AvenirNextLTPro-Medium.ttf') format('truetype'),
        url('{{ $fontsUrl }}/AvenirNextLTPro-Medium.svg#Avenir Next LT Pro Medium') format('svg');
        font-weight: normal;
        font-style: normal;
    }
    @font-face {
        font-family: 'Avenir Next LT Pro Bold';
        src: url('{{ $fontsUrl }}/AvenirNextLTPro-Bold.eot');
        src: url('{{ $fontsUrl }}/AvenirNextLTPro-Bold.eot?#iefix') format('embedded-opentype'),
        url('{{ $fontsUrl }}/AvenirNextLTPro-Bold.woff') format('woff'),
        url('{{ $fontsUrl }}/AvenirNextLTPro-Bold.ttf') format('truetype'),
        url('{{ $fontsUrl }}/AvenirNextLTPro-Bold.svg#Avenir Next LT Pro Bold') format('svg');
        font-weight: normal;
        font-style: normal;
    }

    /* Navbar fixes */
    @media screen and (min-width: 768px) {
        .navbar.horizontal-menu.navbar-minimal {
            height: 58px;
        }
    }
    .navbar-brand {
        line-height: 16px;
    }

    /* Main report styles */
    body {
        background:url('/wp/wp-content/themes/aoe/images/aoe-group_home-banner.jpg') fixed no-repeat;
        background-size: 100% 100%;
    }
    .page-container.assignment.details {
        /*background:url('/wp/wp-content/themes/aoe/images/aoe-group_home-banner.jpg') fixed no-repeat;*/
        background: transparent;
        background-size: 100% 100%;
    }
    .assignment.details .main-content {
        background: transparent;
        padding: 0;
        margin-top: 20px;
    }
    .report-template .container {
        max-width: 100%;
    }
    .report-template .page-container {
        background-color: white;
        height: 1100px;
        width: 850px;
        padding: 45px 45px;
        page-break-after: always;
        margin: 0 auto;
        margin-bottom: 15px;
        display: block;
    }
    .report-template textarea {
        width: 100%;
        padding: 1em;
        height: 60px;
    }
    .report-template p.small {
        font-size: 14px;
        line-height: 23px;
    }
    .report-template .img-container-1, .report-template .img-container-2 {position: absolute; height:997px; width:760px;}
    .report-template .img-container-1 small, .report-template .img-container-2 small {top:0; right:15px; position:absolute;}
    .report-template .img-container-1 img {position:absolute; bottom:0px; right:15px; width:100px;}
    .report-template .img-container-2 img {position:absolute; bottom:0px; left:15px; width:100px;}
    .report-template h1 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:64px; color:#02244a; line-height:64px; margin-top:0px; font-weight: 300;}
    .report-template h2 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:32px; color:#02244a; line-height:45px; margin-top:0px;}
    .report-template h3 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:23px; color:#02244a; line-height:32px; margin-top:0px;}
    .report-template .cover-for h3 {font-family:'Avenir Next LT Pro'; font-size:16px; line-height:23px; text-transform: none;}
    .report-template h4 {-webkit-print-color-adjust: exact; font-family:'Didot Italic'; color:#02244a; font-size:32px; line-height:32px;}
    .report-template h5 {font-family:'Avenir Next LT Pro Medium'; font-size:1.616em; line-height:1.616em; color:#02244a;}
    .report-template .yellow-block {
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
    .report-template h6 {font-family:'Avenir Next LT Pro'; font-size:13px; line-height:23px; margin-top:0px;}
    .report-template p, .report-template ul li {font-family:'Avenir Next LT Pro'; font-size:16px; line-height:26px; margin-bottom:16px; list-style-type:square;}
    .report-template ol li {font-family:'Avenir Next LT Pro'; font-size:16px; line-height:26px; margin-bottom:16px;}
    .report-template .red {color:#E32731;} .yellow {color:#E7B428;} .green {color:#30BD21;}
    .report-template .text-justify {text-align:justify;}
    .report-template .border-bottom {border-top:1px solid rgba(0,0,0,0.1); padding-top:10px;}
    .report-template .underline {border-bottom:1px solid black;}
    .report-template small {font-family:'Avenir Next LT Pro'; font-size:13px; line-height:18px;}
    .report-template .leftside {left:15px!important; right:inherit; position:absolute;}
    .report-template .disclaimer {padding-top:30px;}
    .report-template .cover-logo {padding:90px 0px 0px; margin:0 auto;}
    .report-template .cover-for {padding-top:90px; padding-bottom:120px;}
    .report-template .white {color:white;}
    .report-template .chart div {width:600px; margin:0 auto;}
    .report-template .chart {margin-left:-45px;}

    @media screen and (max-width:867px) {
        .report-template .page-container {padding:32px; height:auto; min-height:800px; width:100%;}
        .report-template .img-container-1 img, .img-container-2 img, .report-logo {display:none;}
        .report-template .img-container-1, .img-container-2 {
            position: absolute;
            height:90%;
            width:90%;
        }
        .report-template .chart div {width:100%; height:100%; margin-left:15px;}
        .report-template .highcharts-yaxis-title, .highcharts-xaxis-title {display:none;}
        .report-template .cover-for, .cover-logo {padding:0px;}
        .report-template h1 {line-height:1em; font-size:3.231em;}
        .report-template h3 {font-size: 1.616em;}
        .report-template h1, h2, h3, h4 {-webkit-print-color-adjust: exact;}
    }

    /* Adjust styling for small screens, as the PDF export will use these styles */
    /* Adjust all the font sizes and page margins, also removing the background */
    @media screen and (max-width:480px) {
        body {
            background: none;
        }
        .cell-center {
            text-align:center;
        }
        .underline {
            border-bottom:0px;
        }
        #invisible-12 {
            width:100%;
        }
        #invisible-4 {
            width:16.666%;
        }
        #invisible-0 {
            display:none;
        }
        .col-xs-8 {
            width:66.66666666666666%;
        }
        .col-xs-4 {
            width:33.33333333333333%;
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
        .cover-for {padding-top:90px; padding-bottom:60px;}
        .white {color:white;}
        .chart div {width:600px; margin:0 auto;}
        .chart {margin-left:-45px;}
        .page-container{
            padding:32px;
            width: 100%;
        }
        h1 {line-height:1em; font-size:4.231em;}
        h3 {font-size: 1.616em;}
        h1, h2, h3, h4 {-webkit-print-color-adjust: exact;}
        p {
            font-size: 18px;
            line-height: 28px;
        }
        h6 {
            font-size: 15px;
            line-height: 26px;
        }
        h2 {
            margin-top: 20px;
        }
    }
</style>