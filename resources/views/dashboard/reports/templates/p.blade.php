<?php
    global $page;
    global $i;
?>

{{-- Report --}}
<?php $page++ ?>
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
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-P-01.png">
                    </div>
                </div>
                <h1>Personality Report</h1>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                @include('dashboard.reports.partials._field', [
                    'field' => 'The AOE-P is based on the HEXACO model of personality, which is the latest scientifically valid model of individual personality. The HEXACO model is valid across many different cultures and incorporates the most up-to-date and accurate framework for assessing personality.

Personality is a very good indicator of what people will do on a typical day of work. The AOE-P is the first HEXACO-based assessment for application to organizations. The theoretical and empirical evidence shows that there are six major dimensions to personality. These are described as:'
                ])
				<?php $i++; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h3>Personality Dimension</h3>
            </div>
            <div class="col-xs-8">
                <h3>Personality Dimension Description</h3>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Honesty-Humility</p>
            </div>
            <div class="col-xs-8">
                <p class="small">Tendency to focus on fairness, sincerity, modesty, and greed avoidance.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Emotional Control</p>
            </div>
            <div class="col-xs-8">
                <p class="small">Tendency to focus on controlling your emotions, withstanding failures, setbacks and stresses, and maintaining and/or quickly regaining your composure.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Extraversion</p>
            </div>
            <div class="col-xs-8">
                <p class="small">Tendency to focus on social boldness, sociability, liveliness, and social self-esteem.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Agreeableness</p>
            </div>
            <div class="col-xs-8">
                <p class="small">Tendency to focus on flexibility, gentleness, forgiveness, and patience.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Conscientiousness</p>
            </div>
            <div class="col-xs-8">
                <p class="small">Tendency to focus on achievement, organization, perfectionism, and prudence.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Openness</p>
            </div>
            <div class="col-xs-8">
                <p class="small">Tendency to focus on inquisitiveness, aesthetic appreciation, unconventionality, and creativity.</p>
            </div>
        </div>
    </div>
</div>

{{-- Scoring and Importance --}}
<?php $page++ ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
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
                        <img class="img-responsive " src="/wp/wp-content/themes/aoe/images/AOE-P-01.png">
                    </div>
                </div>
                <h1>Scoring and Importance</h1>
                <h5>
                    of the AOE-P
                    @if (isset($edit) && isset($jobId) && $jobId)
                        for @include('dashboard.reports.partials._job')
                    @endif
                </h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                @include('dashboard.reports.partials._field', [
                    'field' => 'AOE\'s vast experience in research, predictive analytics, and content validation reveals that for [job]s all six dimensions of AOE-P are desired. Of these, Conscientiousness is a strongly desired characteristic that connects to performance. Conscientiousness encompasses prudence, organization, detail orientation, and achievement. Honesty and Humility is also strongly desired - encompassing fairness, sincerity, modesty, and greed avoidance. Emotional Control is also strongly desired for [job] positions. Although still important, some characteristics, such as Openness and Agreeableness, may not factor as heavily for [job] positions.'
                ])
				<?php $i++; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="personality-chart"></div>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Personality Factor Scores for @include('dashboard.reports.partials._name')</h5>
            </div>
        </div>
    </div>
</div>

{{-- Dimensions 1 and 2 Breakdown --}}
<?php $page++ ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small class="leftside">Scale: 1=Low 5=High</small>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Honesty-Humility</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-2 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Sincerity</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['sincerity'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to be genuine in interpersonal relations. Low scorers use flattery and are often seen as 'fake', whereas high scorers are viewed as being sincere and do not manipulate others.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Fairness</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['fairness'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to avoid unfair action, fraud, and corruption. Low scorers might cheat or steal; high scorers are unlikely to take advantage of others.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Greed Avoidance</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['greed'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to be uninterested in material goods or social status. Low scorers want to enjoy and to display wealth and privilege; high scorers are uninterested in material goods or social status.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Modesty</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['modesty'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to be modest and unassuming. Low scorers consider themselves as superior and entitled; high scorers see themselves as ordinary people.</h6>
            </div>
        </div>

        {{-- Heading --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Emotional Control</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-2 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Fearlessness</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['fearlessness'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to be brave/fearless. Low scorers are extremely fearful of physical harm; high scorers are relatively tough, brave, and not overly sensitive to physical injury.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Composure</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['composure'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to remain calm and collected at work. Low scorers worry excessively, even with minor issues; high scorers remain calm, even with major issues.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Independence</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['independence'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to work independently without much emotional support from others. Low scorers want encouragement and/or comfort from others; high scorers are self-assured and able to effectively deal with problems.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Stoical</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['stoical'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to accept what happens without showing strong emotion. Low scorers show strong emotions and have strong emotional attachments; high scorers show little emotion and have weak emotional attachments.</h6>
            </div>
        </div>
    </div>
</div>

{{-- Dimensions 3 and 4 Breakdown--}}
<?php $page++ ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small class="leftside">Scale: 1=Low 5=High</small>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Extraversion</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-2 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Self-Esteem</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['esteem'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to have positive self-regard, particularly at work. High scorers have self-respect and see themselves as likeable; low scorers tend to feel worthless and unpopular.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Social Boldness</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['boldness'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to be comfortable and confident in work situations. Low scorers are typically shy or awkward, particularly in leadership positions or large settings; high scorers are comfortable leading groups and communicating with people.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Sociability</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['sociability'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to enjoy social situations and interacting with a variety of individuals. Low scorers prefer solitary activities and work tasks; high scorers enjoy talking, visiting, and interacting with others at work.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Liveliness</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['liveliness'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to be optimistic, enthusiastic, and full of energy. Low scorers are generally not overly cheerful or dynamic; high scorers are generally enthusiastic and in high spirits.</h6>
            </div>
        </div>

        {{-- Heading --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Agreeableness</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-2 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Forgiveness</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['forgiveness'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to forgive and even trust those who may have caused harm. Low scorers might "hold a grudge" against those who have done one wrong; high scorers can forgive and are willing to work towards re-establishing friendly relations.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Gentleness</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['gentleness'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to be mild mannered and gentle in dealings with others. Low scorers are generally critical of others; high scorers tend not to be judgemental of others.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Flexibility</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['flexibility'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to compromise and cooperate with others. Low scorers are viewed as stubborn and likely are argumentative; high scorers are accommodating to suggestions and generally flexible.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Patience</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['patience'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to be patient and remain calm. Low scorers tend to get angry or upset easily; high scorers generally are more tolerant before possibly getting angry or upset.</h6>
            </div>
        </div>
    </div>
</div>

{{-- Dimensions 5 and 6 Breakdown --}}
<?php $page++ ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small class="leftside">Scale: 1=Low 5=High</small>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Conscientiousness</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-2 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Organization</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['organization'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to seek order and organize one's surroundings. Low scorers are generally sloppy and haphhazard; high scorers are generally well-organized and prefer a structured approach to tasks.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Achievement</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['achievement'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to work hard. Low scorers lack self-discipline and are not strongly motivated to achieve; high scorers are strongly motivated to achieve due to a strong "work ethic."</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Prudence</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['prudence'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to be detailed oriented. Low scorers are tolerant of errors in their work; high scorers check carefully for mistakes and potential improvements.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Detailed</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['detailed'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to carefully think things through and avoid impulses. Low scorers follow impulses and do not consider consequences; high scorers consider multiple options and are generally careful and self-controlled.</h6>
            </div>
        </div>

        {{-- Heading --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Openness</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-2 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Aesthetic Appreciation</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['appreciation'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to enjoy and see beauty in art, physical surroundings, and in nature. Low scorers don't care for art, aesthetics, or natural wonders; high scorers have a deep appreciation for a variety of art forms (e.g., nature, physical space).</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Inquisitiveness</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['inquisitiveness'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendecy to be inquisitive. Low scorers are generally not curious; high scorers are curious and prefer to know how things work or came to be.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Creativity</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['creativity'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to be innovative and experimental. Low scorers have little inclination for original thought, whereas high scorers actively seek new solutions to problems and express themselves.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Unconventionality</h5>
            </div>
            <div class="col-xs-2 underline text-center">
                <h5><?php echo (! isset($edit)) ? $scores[$assessment->id]['unconventionality'] : '3.55' ?></h5>
            </div>
            <div class="col-xs-7">
                <h6>Tendency to accept the unusual and different. Low scorers avoid things that are out of the ordinary; high scorers are open to out-of-the-ordinary ideas.</h6>
            </div>
        </div>
    </div>
</div>

{{-- Fit Evaluation --}}
@if ($report->show_fit)
<?php $page++ ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <br><br>
                <h1>Personality Fit Evaluation</h1>
                <h4>for @include('dashboard.reports.partials._name')</h4>
                <br><br>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @include('dashboard.reports.partials._field', [
                    'field' => 'Given these AOE-P results, in conjunction with job and industry profiles for [job]s, the potential fit for name is:'
                ])
				<?php $i++; ?>
                <br><br>
                <div class="col-xs-2"></div>
                <div class="col-xs-8">
                    @if (! isset($edit))
                        @if ($report->score_method == 1 || ($report->score_method == 2 && $report->model_configured))
                            @if ($scores[$assessment->id]['division'] == 1)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/low.png">
                            @elseif ($scores[$assessment->id]['division'] == 2)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/mid-to-low.png">
                            @elseif ($scores[$assessment->id]['division'] == 3)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/mid.png">
                            @elseif ($scores[$assessment->id]['division'] == 4)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/mid-to-high.png">
                            @elseif ($scores[$assessment->id]['division'] == 5)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/high.png">
                            @endif

                            @if ($report->score_method == 2 && $report->model_configured)
                                {{-- Model Confidence --}}
                                <div style="clear:both;"></div>
                                <div class="model-confidence" style="text-align:center;">
                                    <h2 style="display:inline-block;font-size:23px;">Model Confidence:</h2>
                                    <h4 style="display:inline-block;font-size:23px;">{{ $scores[$assessment->id]['confidence'] }}</h4>
                                </div>
                            @elseif ($report->score_method == 2 && ! $report->model_configured)
                                <div class="alert alert-danger" role="alert">
                                    Predictive model not configured
                                </div>
                            @endif
                        @endif
                    @else
                        <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/mid.png">

                        @if ($report->score_method == 2)
                            {{-- Model Confidence --}}
                            <div style="clear:both;"></div>
                            <div class="model-confidence" style="text-align:center;">
                                <h2 style="display:inline-block;font-size:23px;">Model Confidence:</h2>
                                <h4 style="display:inline-block;font-size:23px;">100</h4>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@else
@include('dashboard.reports.partials._field', [
    'field' => 'Given these AOE-P results, in conjunction with job and industry profiles for [job]s, the potential fit for name is:',
    'hidden' => true,
])
<?php $i++; ?>
@endif