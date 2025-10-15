/**
 * Default Grunt control
 *
 * CRITICAL NOTE:
 * - `grunt watch` is expected to be used during development. It will not minify the output.
 * - `grunt` is expected to be used prior to deployment to production.
 *
 * For best results, include ""prefix": "../"," in package.json to make sure
 * the existing node_module folder is used without accidental nesting.
 *
 *
 */
const sass = require('sass');
const path = require('path');
const fs = require('fs');
const { globSync } = require('glob');

module.exports = function(grunt) {

    // Dynamically detect parent theme directory
    // Looks for the first directory in ../ that has a node_modules/bootstrap folder
    const themesDir = path.resolve(__dirname, '..');
    const childThemeName = path.basename(__dirname);
    let parentThemeDir = null;
    let parentThemeName = null;

    // Read all directories in themes folder
    const themeDirs = fs.readdirSync(themesDir).filter(file => {
        return fs.statSync(path.join(themesDir, file)).isDirectory() && file !== childThemeName;
    });

    // Find parent theme (has node_modules/bootstrap)
    for (const dir of themeDirs) {
        const bootstrapPath = path.join(themesDir, dir, 'node_modules/bootstrap');
        if (fs.existsSync(bootstrapPath)) {
            parentThemeDir = path.join('..', dir, 'node_modules');
            parentThemeName = dir;
            grunt.log.writeln('Found parent theme Bootstrap at: ' + dir);
            break;
        }
    }

    // Fallback: derive parent theme name from child theme name (remove '-child' suffix)
    if (!parentThemeDir) {
        parentThemeName = childThemeName.replace(/-child$/, '');
        parentThemeDir = path.join('..', parentThemeName, 'node_modules');
        grunt.log.warn('Could not auto-detect parent theme, using derived name: ' + parentThemeName);
    }

    // Dynamically find all child block SCSS files (auto-detection like webpack)
    const blockScssFiles = globSync('./src/templates/blocks/**/style.scss');

    // Filter out hidden directories (like .block template)
    const filteredBlockScssFiles = blockScssFiles.filter(filePath => !filePath.includes('/.'));

    grunt.log.writeln('[Block SCSS] Found ' + filteredBlockScssFiles.length + ' block SCSS files');

    // Build dynamic sass files object for blocks
    const blockSassFiles = {};
    const blockCopyFiles = [];
    const blockTempFiles = [];

    filteredBlockScssFiles.forEach(filePath => {
        // Extract block name from path
        const match = filePath.match(/blocks\/([^\/]+)\/style\.scss$/);
        if (match && match[1]) {
            const blockName = match[1];
            // Compile temp file with prepended imports to dist/blocks/[block-name]/style.css
            blockSassFiles['dist/blocks/' + blockName + '/style.css'] = '.tmp/blocks/' + blockName + '/style.scss';

            // Track temp file locations
            blockTempFiles.push({
                blockName: blockName,
                source: filePath,
                temp: '.tmp/blocks/' + blockName + '/style.scss'
            });

            // Prepare copy task: dist -> src
            blockCopyFiles.push({
                src: 'dist/blocks/' + blockName + '/style.css',
                dest: 'src/templates/blocks/' + blockName + '/style.css'
            });

            grunt.log.writeln('[Block SCSS] Added entry: ' + blockName);
        }
    });

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        copy: {
            main: {
                files: [
                    // NOTE: Bootstrap copy task no longer needed - child theme now imports from parent theme's node_modules
                    // The includePaths in sass options (above) resolves ~bootstrap to parent theme's node_modules
                    // Keeping this task definition for backwards compatibility, but it can be removed
                    // {
                    //     expand: true,
                    //     flatten: false, // flattens results to a single level
                    //     cwd: 'node_modules/bootstrap/scss/', // makes all src relative to cwd
                    //     src: ['vendor/**/*', 'mixins/**/*', 'helpers/**/*', 'utilities/**/*', '_functions.scss', '_variables*.scss', '_maps.scss', '_mixins.scss', '_utilities.scss', '_root.scss', '_reboot.scss'], // includes files within path
                    //     dest: 'src/libs/bootstrap/', //copy file to location
                    //     filter: 'isFile',
                    // },
                    // {
                    //     expand: true,
                    //     flatten: false, // flattens results to a single level
                    //     cwd: 'node_modules/waypoints/lib/', // makes all src relative to cwd
                    //     src: ['**/*'], // includes files within path
                    //     dest: 'src/libs/waypoints/', //copy file to location
                    //     filter: 'isFile',
                    // },
                    // {
                    //     expand: true,
                    //     flatten: false, // flattens results to a single level
                    //     cwd: 'node_modules/isotope-layout/dist/', // makes all src relative to cwd
                    //     src: ['**/*'], // includes files within path
                    //     dest: 'src/libs/isotope/', //copy file to location
                    //     filter: 'isFile',
                    // }
                ],
            },
            // Copy compiled block CSS files back to source directories
            blocks: {
                files: blockCopyFiles
            }
        },
        svg_sprite: {
            dist: {
                expand: false,
                cwd: 'src/icons/svg',
                src: ['*.svg'],
                dest: 'dist',
                options: {
                    shape: {
                       transform: [{
                           svgo: {
                               plugins: [
                                   { name: 'preset-default' },
                                   { name: 'removeAttrs', params: { attrs: '(fill|stroke)' } },
                                   { name: 'removeStyleElement' }
                               ]
                           }
                       }]
                   },
                    mode: {
                        view: {
                            dest: '',
                            sprite: './svg/sprites/sprite',
                            bust: false,
                            render: {
                                scss: {
                                    template: 'src/icons/svg_sprite.template.scss.handlebars',
                                    dest: '../src/sass/global/_sprites'
                                }
                            }
                        },
                        symbol: {
                            dest: './svg',
                            inline: true
                        }
                    }
                }
            }
        },
        uglify: {
            options: {
                sourceMap : true
            },
            public: {
                files : {
                    "dist/scripts.min.js" :
                        [
                            "src/js/core.js",
                            "src/js/parts/**/*.part.js"
                        ],
                    "dist/admin.min.js" :
                        [
                            "src/js/admin.js"
                        ]
                }
            }
        },
        sass : {
            options : {
                implementation: sass,
                sourceMap : true,
                outputStyle : "expanded",
                // Add parent theme's node_modules to resolve path (dynamically detected above)
                // This allows child theme to use parent's Bootstrap without hardcoding parent theme name
                includePaths: [
                    parentThemeDir,
                    // Add parent theme's pattern source path for variable imports
                    path.join('..', parentThemeName, 'src/patternlab/source/_patterns')
                ]
            },
            public : {
                files : {
                    "dist/styles.css" : "src/sass/styles.scss",
                    "dist/admin.css" : "src/sass/wordpress/admin/admin.scss",
                    "dist/editor.css" : "src/sass/wordpress/editor/editor.scss"
                }
            },
            // Compile block SCSS files with variable injection
            blocks: {
                options: {
                    implementation: sass,
                    sourceMap: true,
                    outputStyle: "expanded",
                    includePaths: [
                        parentThemeDir,
                        path.join('..', parentThemeName, 'src/patternlab/source/_patterns'),
                        './src' // Add child theme src for relative imports
                    ]
                },
                files: blockSassFiles
            }
        },
        watch: {
            primary: {
                options: {
                    reload: false,
                    spawn: false,
                    interrupt: false,
                    livereload: true
                },
                files: [
                    "src/js/parts/**/*.js",
                    "src/sass/**/*.scss"
                ],
                tasks: ["uglify","sass:public"]
            },
            blocks: {
                options: {
                    reload: false,
                    spawn: false,
                    interrupt: false,
                    livereload: true
                },
                files: [
                    "src/templates/blocks/**/style.scss"
                ],
                tasks: ["prepare-blocks", "sass:blocks", "postcss:blocks", "copy:blocks", "clean:blocks"]
            },
            fonts: {
                options: {
                    reload: false,
                    spawn: false,
                    interrupt: false,
                    livereload: true
                },
                files: [
                    'source/fonts/fontello'
                ],
                tasks: ['fontello']
            }
        },
        postcss: {
            options: {
                processors: [
                    require('pixrem')(), // add fallbacks for rem units
                    require('autoprefixer')(), // add vendor prefixes
                    require('cssnano')({
                        preset: ['default', {
                            discardComments: { removeAll: true }, // Remove all comments
                            discardDuplicates: true // Remove duplicate CSS rules
                        }]
                    }) // minify the result
                ]
            },
            public: {
                map: {
                    inline: false, // Save all sourcemaps as separate files...
                    annotation: "dist/" // ...to the specified directory
                },
                src: "dist/*.css"
            },
            // Minify block CSS files (runs on dist before copying to src)
            blocks: {
                map: {
                    inline: false,
                    annotation: function(dest) {
                        return dest.replace(/\.css$/, '');
                    }
                },
                src: "dist/blocks/**/style.css"
            }
        },
        compress: {
            main: {
                options: {
                    mode: 'gzip'
                },
                expand: true,
                cwd: 'dist/',
                src: ['**/*.js', '**/*.css'],
                dest: 'dist/',
                ext: '.gz'
            }
        },
        clean: {
            // Clean up dist/blocks and temp directories after copying to source
            blocks: ['dist/blocks', '.tmp/blocks']
        }
    });

    // Custom task to prepend variable imports to block SCSS files
    grunt.registerTask('prepare-blocks', 'Prepend variable imports to block SCSS files', function() {
        blockTempFiles.forEach(function(block) {
            const sourceContent = grunt.file.read(block.source);
            // Import path is relative to .tmp/blocks/[block-name]/ directory
            const tempContent = '@import "../../../src/sass/wordpress/blocks/block-variables";\n' + sourceContent;

            // Ensure temp directory exists and write file
            grunt.file.write(block.temp, tempContent);
            grunt.log.writeln('Prepared block SCSS: ' + block.blockName);
        });
    });

    grunt.loadNpmTasks("grunt-contrib-copy");
    grunt.loadNpmTasks("grunt-sass");
    grunt.loadNpmTasks("grunt-postcss");
    grunt.loadNpmTasks("grunt-contrib-uglify");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-svg-sprite");
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-clean');

    // Default task(s).
    // grunt.registerTask("default", ["svg_sprite","uglify","sass"]);
    grunt.registerTask("default", ["uglify","sass:public","prepare-blocks","sass:blocks","postcss:blocks","copy:blocks","clean:blocks"]);
    grunt.registerTask("sprites", ["svg_sprite","sass:public","prepare-blocks","sass:blocks","postcss:blocks","copy:blocks","clean:blocks"]);
    grunt.registerTask("init", ["copy", "svg_sprite", "uglify", "sass:public","prepare-blocks","sass:blocks","postcss:blocks","copy:blocks","clean:blocks"]);
    // grunt.registerTask("init", ["copy", "uglify", "sass"]);
    grunt.registerTask("prod", ["svg_sprite", "uglify","sass:public","prepare-blocks","sass:blocks","postcss:blocks","copy:blocks","clean:blocks","postcss:public", "compress"]);
};
