const gulp = require('gulp');
const concat = require('gulp-concat');
const copy = require('gulp-copy');
const less = require('gulp-less');
const sass = require('gulp-sass')(require('sass'));

// Compile LESS
gulp.task('less', function() {
    return gulp.src('resources/assets/less/app.less')
        .pipe(less({
            javascriptEnabled: true
        }))
        .pipe(gulp.dest('public/assets/css'));
});

// Concatenate scripts
gulp.task('scripts', function() {
    return Promise.all([
        // React
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/js/react.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        // JSXTransformer
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/js/JSXTransformer.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        // MathJax
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/js/MathJax.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        // Create assessment form
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/js/create-assessment-form.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        // WM scripts
        new Promise((resolve, reject) => {
            gulp.src([
                'resources/assets/js/wm/util.js',
                'resources/assets/js/wm/models.js',
                'resources/assets/js/wm/generic.js',
                'resources/assets/js/wm/text.js',
                'resources/assets/js/wm/ls.js',
                'resources/assets/js/wm/eq.js',
                'resources/assets/js/wm/eqls.js',
                'resources/assets/js/wm/sq.js',
                'resources/assets/js/wm/sy.js',
                'resources/assets/js/wm/sysq.js',
                'resources/assets/js/wm/runner.js',
            ], { allowEmpty: true })
                .pipe(concat('wm.js'))
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        // Create WM scripts
        new Promise((resolve, reject) => {
            gulp.src([
                'resources/assets/js/wm/util.js',
                'resources/assets/js/wm/models.js',
                'resources/assets/js/wm/marked.js',
                'resources/assets/js/wm/sq.js',
                'resources/assets/js/wm/sy.js',
                'resources/assets/js/wm/task-template.js',
                'resources/assets/js/wm/create-task.js',
            ], { allowEmpty: true })
                .pipe(concat('create-wm.js'))
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        // Other scripts
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/js/marked.min.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/js/translate-assessment-form.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/js/assignment.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/js/timer.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/js/autosize.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        })
    ]);
});

// Copy vendor assets
gulp.task('copy-vendor', function() {
    return Promise.all([
        // Copy marked from vendor
        new Promise((resolve, reject) => {
            gulp.src('vendor/bower-asset/marked/marked.min.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        // Copy MathJax
        new Promise((resolve, reject) => {
            gulp.src('vendor/mathjax/mathjax/MathJax.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/mathjax'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('vendor/mathjax/mathjax/extensions/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/mathjax/extensions'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('vendor/mathjax/mathjax/config/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/mathjax/config'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('vendor/mathjax/mathjax/jax/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/mathjax/jax'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('vendor/mathjax/mathjax/localization/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/mathjax/localization'))
                .on('end', resolve)
                .on('error', reject);
        })
    ]);
});

// Copy xenon theme assets
gulp.task('copy-xenon', function() {
    return Promise.all([
        // Copy JS files
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/jquery-1.11.1.min.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/bootstrap.min.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/TweenMax.min.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/resizeable.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/xenon-api.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/xenon-toggles.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/xenon-custom.js', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        // Copy directories
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/toastr/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/toastr'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/jquery-validate/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/jquery-validate'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/uikit/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/uikit'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/ckeditor/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/ckeditor'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/tagsinput/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/tagsinput'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/icheck/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/icheck'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/select2/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/select2'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/jquery-ui/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/jquery-ui'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/selectboxit/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/selectboxit'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/datepicker/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/datepicker'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/js/daterangepicker/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/js/daterangepicker'))
                .on('end', resolve)
                .on('error', reject);
        }),
        
        // Copy fonts
        new Promise((resolve, reject) => {
            gulp.src('resources/assets/xenon/css/fonts/**/*', { allowEmpty: true })
                .pipe(gulp.dest('public/assets/css/fonts'))
                .on('end', resolve)
                .on('error', reject);
        })
    ]);
});

// Default task
gulp.task('default', gulp.series('scripts', 'copy-vendor', 'copy-xenon'));