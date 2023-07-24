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
                files: {'server.min.js': [
                    'server.js']	
				}
            }
        },

    });

    // load pluginsng
    grunt.loadNpmTasks('grunt-contrib-uglify');


    // default
    grunt.registerTask('default', ['uglify']);
    grunt.registerTask('compress', ['uglify']);
};