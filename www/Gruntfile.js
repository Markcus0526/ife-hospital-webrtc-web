module.exports = function (grunt) {

    // Project configuration
    grunt.initConfig({

        // configure uglify
        uglify: {
            options: {
                mangle: false,
                compress:{
                    drop_console: true
                }
            },
            vendor: {
                files: {'resource/js/vendor.min.js': [
                    'resource/js/jquery.min.js',
                    'resource/js/jquery-migrate.min.js',
                    'resource/js/jquery-ui/jquery-ui-1.10.3.custom.min.js',
                    'resource/js/bootstrap/js/bootstrap.min.js',
                    'resource/js/bootstrap-datepicker/js/bootstrap-datepicker.js',
                    'resource/js/bootstrap-timepicker/js/bootstrap-timepicker.min.js',
                    'resource/js/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js',
                    'resource/js/bootstrap-switch/js/bootstrap-switch.min.js',
                    'resource/js/jquery-slimscroll/jquery.slimscroll.min.js',

                    'resource/js/jquery-form/jquery-form.min.js',
                    'resource/js/jquery.appear/jquery.appear.js',
                    'resource/js/jquery-validation/js/jquery.validate.js',
                    'resource/js/jquery-validation/js/additional-methods.min.js',

                    'resource/js/masked-input/jquery.maskedinput.min.js',
                    'resource/js/notification/SmartNotification.js',

                    'resource/js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js',
                    'resource/js/fancybox/source/jquery.fancybox.pack.js',

                    'resource/js/bootstrap-select/bootstrap-select.js',
                    'resource/js/select2/select2.js',

                    'resource/js/intl-tel-input/intlTelInput.js',
                    'resource/js/intl-tel-input/utils.js',

                    'resource/js/jquery-cycle/jquery.cycle.all.min.js',

                    'resource/js/webrtc/adapter.js',
                ],
                'resource/js/app.min.js': ['resource/js/app.js'],
                'resource/js/utility.min.js': ['resource/js/utility.js'],
                'resource/js/room.min.js': ['resource/js/room.js'],}
            }
        },

        // configure JSHint
        jshint: {
            options:{
                jshintrc: ".jshintrc"
            },
            vendor: [
                'resource/js/jquery.min.js',
                'resource/js/jquery-migrate.min.js',
                'resource/js/bootstrap/js/bootstrap.min.js',
                'resource/js/bootstrap-datepicker/js/bootstrap-datepicker.js',
                'resource/js/bootstrap-timepicker/js/bootstrap-timepicker.min.js',
                'resource/js/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js',
                'resource/js/bootstrap-switch/js/bootstrap-switch.min.js',
                'resource/js/jquery-slimscroll/jquery.slimscroll.min.js',

                'resource/js/jquery-form/jquery-form.min.js',
                'resource/js/jquery.appear/jquery.appear.js',
                'resource/js/jquery-validation/js/jquery.validate.js',
                'resource/js/jquery-validation/js/additional-methods.min.js',

                'resource/js/masked-input/jquery.maskedinput.min.js',
                'resource/js/notification/SmartNotification.js',

                'resource/js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js',
                'resource/js/fancybox/source/jquery.fancybox.pack.js',

                'resource/js/select2/select2.min.js',

                'resource/js/intl-tel-input/intlTelInput.js',
                'resource/js/intl-tel-input/utils.js',

                'resource/js/webrtc/adapter.js',
            ]
        }

    });

    // load pluginsng
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');


    // default
    grunt.registerTask('default', ['jshint', 'uglify']);
    grunt.registerTask('compress', ['uglify']);
};