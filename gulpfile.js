var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.less('app.less', 'public/assets/css')
        .scripts('react.js', 'public/assets/js/react.js')
        .scripts('JSXTransformer.js', 'public/assets/js/JSXTransformer.js')
        .scripts('MathJax.js', 'public/assets/js/MathJax.js')
        .scripts('create-assessment-form.js', 'public/assets/js/create-assessment-form.js')
        .scripts([
            'wm/util.js',
            'wm/models.js',
            'wm/generic.js',
            'wm/text.js',
            'wm/ls.js',
            'wm/eq.js',
            'wm/eqls.js',
            'wm/sq.js',
            'wm/sy.js',
            'wm/sysq.js',
            'wm/runner.js',
        ], 'public/assets/js/wm.js')
        .scripts([
            'wm/util.js',
            'wm/models.js',
            'wm/marked.js',
            'wm/sq.js',
            'wm/sy.js',
            'wm/task-template.js',
            'wm/create-task.js',
        ], 'public/assets/js/create-wm.js')
        .copy('vendor/bower-asset/marked/marked.min.js', 'public/assets/js/marked.min.js')
        .copy('vendor/mathjax/mathjax/MathJax.js', 'public/assets/js/mathjax/MathJax.js')
        .copy('vendor/mathjax/mathjax/extensions', 'public/assets/js/mathjax/extensions')
        .copy('vendor/mathjax/mathjax/config', 'public/assets/js/mathjax/config')
        .copy('vendor/mathjax/mathjax/jax', 'public/assets/js/mathjax/jax')
        .copy('vendor/mathjax/mathjax/localization', 'public/assets/js/mathjax/localization')
        .scripts('marked.min.js', 'public/assets/js/marked.min.js')
        .scripts('translate-assessment-form.js', 'public/assets/js/translate-assessment-form.js')
        .scripts('assignment.js', 'public/assets/js/assignment.js')
        .scripts('timer.js', 'public/assets/js/timer.js')
        .scripts('autosize.js', 'public/assets/js/autosize.js')
        // Copy xenon theme assets
        .copy('resources/assets/xenon/js/jquery-1.11.1.min.js', 'public/assets/js/jquery-1.11.1.min.js')
        .copy('resources/assets/xenon/js/bootstrap.min.js', 'public/assets/js/bootstrap.min.js')
        .copy('resources/assets/xenon/js/TweenMax.min.js', 'public/assets/js/TweenMax.min.js')
        .copy('resources/assets/xenon/js/resizeable.js', 'public/assets/js/resizeable.js')
        .copy('resources/assets/xenon/js/xenon-api.js', 'public/assets/js/xenon-api.js')
        .copy('resources/assets/xenon/js/xenon-toggles.js', 'public/assets/js/xenon-toggles.js')
        .copy('resources/assets/xenon/js/xenon-custom.js', 'public/assets/js/xenon-custom.js')
        .copy('resources/assets/xenon/js/toastr', 'public/assets/js/toastr')
        .copy('resources/assets/xenon/js/jquery-validate', 'public/assets/js/jquery-validate')
        .copy('resources/assets/xenon/css/fonts', 'public/assets/css/fonts');
});