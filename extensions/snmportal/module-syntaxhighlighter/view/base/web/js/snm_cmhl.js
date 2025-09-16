define([
    'jquery',
    'Snmportal_SyntaxHighlighter/hl/highlight'
], function ($,  hljs) {
    function addCss(url)
    {
        var tmp;
        if(document.createStyleSheet) {
            try { tmp = document.createStyleSheet(url); } catch (e) { }
        }
        else {
            tmp			= document.createElement('link');
            tmp.rel		= 'stylesheet';
            tmp.type	= 'text/css';
            tmp.media	= "all";
            tmp.href	= url;
            document.getElementsByTagName("head")[0].appendChild(tmp);
            tmp.styleSheet;
        }
    }
/*
    hljs.configure({
        useBR: true,
        tabReplace: '    ',
        classPrefix: ''
    })
*/
    hljs.configure({
        tabReplace: '    '
    })
    intLngs();
    return {
        initElement:function(element,mode,css1,css2)
        {
            addCss(css1);
            var code = element.innerHTML;
            if (mode == 'phtml') {
                mode = 'php';
            }
            element.className += ' '+mode;
            hljs.highlightBlock(element);//$(element).find('pre code')[0]);
        }
    }
    function intLngs()
    {
      //  hljs.initHighlighting();
      //  var lngs = hljs.listLanguages();
        hljs.registerLanguage("php",
            function(hljs) {
                var VARIABLE = {
                    begin: '\\$+[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*'
                };
                var PREPROCESSOR = {
                    className: 'meta', begin: /<\?(php)?|\?>/
                };
                var STRING = {
                    className: 'string',
                    contains: [hljs.BACKSLASH_ESCAPE, PREPROCESSOR],
                    variants: [
                        {
                            begin: 'b"', end: '"'
                        },
                        {
                            begin: 'b\'', end: '\''
                        },
                        hljs.inherit(hljs.APOS_STRING_MODE, {illegal: null}),
                        hljs.inherit(hljs.QUOTE_STRING_MODE, {illegal: null})
                    ]
                };
                var NUMBER = {variants: [hljs.BINARY_NUMBER_MODE, hljs.C_NUMBER_MODE]};
                return {
                    aliases: ['php', 'php3', 'php4', 'php5', 'php6', 'php7'],
                    case_insensitive: true,
                    keywords:
                        'and include_once list abstract global private echo interface as static endswitch ' +
                        'array null if endwhile or const for endforeach self var while isset public ' +
                        'protected exit foreach throw elseif include __FILE__ empty require_once do xor ' +
                        'return parent clone use __CLASS__ __LINE__ else break print eval new ' +
                        'catch __METHOD__ case exception default die require __FUNCTION__ ' +
                        'enddeclare final try switch continue endfor endif declare unset true false ' +
                        'trait goto instanceof insteadof __DIR__ __NAMESPACE__ ' +
                        'yield finally',
                    contains: [
                        hljs.HASH_COMMENT_MODE,
                        hljs.COMMENT('//', '$', {contains: [PREPROCESSOR]}),
                        hljs.COMMENT(
                            '/\\*',
                            '\\*/',
                            {
                                contains: [
                                    {
                                        className: 'doctag',
                                        begin: '@[A-Za-z]+'
                                    }
                                ]
                            }
                        ),
                        hljs.COMMENT(
                            '__halt_compiler.+?;',
                            false,
                            {
                                endsWithParent: true,
                                keywords: '__halt_compiler',
                                lexemes: hljs.UNDERSCORE_IDENT_RE
                            }
                        ),
                        {
                            className: 'string',
                            begin: /<<<['"]?\w+['"]?$/, end: /^\w+;?$/,
                            contains: [
                                hljs.BACKSLASH_ESCAPE,
                                {
                                    className: 'subst',
                                    variants: [
                                        {begin: /\$\w+/},
                                        {begin: /\{\$/, end: /\}/}
                                    ]
                                }
                            ]
                        },
                        PREPROCESSOR,
                        {
                            className: 'keyword', begin: /\$this\b/
                        },
                        VARIABLE,
                        {
                            // swallow composed identifiers to avoid parsing them as keywords
                            begin: /(::|->)+[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/
                        },
                        {
                            className: 'function',
                            beginKeywords: 'function', end: /[;{]/, excludeEnd: true,
                            illegal: '\\$|\\[|%',
                            contains: [
                                hljs.UNDERSCORE_TITLE_MODE,
                                {
                                    className: 'params',
                                    begin: '\\(', end: '\\)',
                                    contains: [
                                        'self',
                                        VARIABLE,
                                        hljs.C_BLOCK_COMMENT_MODE,
                                        STRING,
                                        NUMBER
                                    ]
                                }
                            ]
                        },
                        {
                            className: 'class',
                            beginKeywords: 'class interface', end: '{', excludeEnd: true,
                            illegal: /[:\(\$"]/,
                            contains: [
                                {beginKeywords: 'extends implements'},
                                hljs.UNDERSCORE_TITLE_MODE
                            ]
                        },
                        {
                            beginKeywords: 'namespace', end: ';',
                            illegal: /[\.']/,
                            contains: [hljs.UNDERSCORE_TITLE_MODE]
                        },
                        {
                            beginKeywords: 'use', end: ';',
                            contains: [hljs.UNDERSCORE_TITLE_MODE]
                        },
                        {
                            begin: '=>' // No markup, just a relevance booster
                        },
                        STRING,
                        NUMBER
                    ]
                };
            }
        );
        hljs.registerLanguage("css",
            function(hljs) {
                var IDENT_RE = '[a-zA-Z-][a-zA-Z0-9_-]*';
                var RULE = {
                    begin: /[A-Z\_\.\-]+\s*:/, returnBegin: true, end: ';', endsWithParent: true,
                    contains: [
                        {
                            className: 'attribute',
                            begin: /\S/, end: ':', excludeEnd: true,
                            starts: {
                                endsWithParent: true, excludeEnd: true,
                                contains: [
                                    {
                                        begin: /[\w-]+\(/, returnBegin: true,
                                        contains: [
                                            {
                                                className: 'built_in',
                                                begin: /[\w-]+/
                                            },
                                            {
                                                begin: /\(/, end: /\)/,
                                                contains: [
                                                    hljs.APOS_STRING_MODE,
                                                    hljs.QUOTE_STRING_MODE
                                                ]
                                            }
                                        ]
                                    },
                                    hljs.CSS_NUMBER_MODE,
                                    hljs.QUOTE_STRING_MODE,
                                    hljs.APOS_STRING_MODE,
                                    hljs.C_BLOCK_COMMENT_MODE,
                                    {
                                        className: 'number', begin: '#[0-9A-Fa-f]+'
                                    },
                                    {
                                        className: 'meta', begin: '!important'
                                    }
                                ]
                            }
                        }
                    ]
                };

                return {
                    case_insensitive: true,
                    illegal: /[=\/|'\$]/,
                    contains: [
                        hljs.C_BLOCK_COMMENT_MODE,
                        {
                            className: 'selector-id', begin: /#[A-Za-z0-9_-]+/
                        },
                        {
                            className: 'selector-class', begin: /\.[A-Za-z0-9_-]+/
                        },
                        {
                            className: 'selector-attr',
                            begin: /\[/, end: /\]/,
                            illegal: '$'
                        },
                        {
                            className: 'selector-pseudo',
                            begin: /:(:)?[a-zA-Z0-9\_\-\+\(\)"'.]+/
                        },
                        {
                            begin: '@(font-face|page)',
                            lexemes: '[a-z-]+',
                            keywords: 'font-face page'
                        },
                        {
                            begin: '@', end: '[{;]', // at_rule eating first "{" is a good thing
                                                     // because it doesn’t let it to be parsed as
                                                     // a rule set but instead drops parser into
                                                     // the default mode which is how it should be.
                            illegal: /:/, // break on Less variables @var: ...
                            contains: [
                                {
                                    className: 'keyword',
                                    begin: /\w+/
                                },
                                {
                                    begin: /\s/, endsWithParent: true, excludeEnd: true,
                                    relevance: 0,
                                    contains: [
                                        hljs.APOS_STRING_MODE, hljs.QUOTE_STRING_MODE,
                                        hljs.CSS_NUMBER_MODE
                                    ]
                                }
                            ]
                        },
                        {
                            className: 'selector-tag', begin: IDENT_RE,
                            relevance: 0
                        },
                        {
                            begin: '{', end: '}',
                            illegal: /\S/,
                            contains: [
                                hljs.C_BLOCK_COMMENT_MODE,
                                RULE,
                            ]
                        }
                    ]
                };
            }
        );
        hljs.registerLanguage("html",
        function(hljs) {
            var XML_IDENT_RE = '[A-Za-z0-9\\._:-]+';
            var TAG_INTERNALS = {
                endsWithParent: true,
                illegal: /</,
                relevance: 0,
                contains: [
                    {
                        className: 'attr',
                        begin: XML_IDENT_RE,
                        relevance: 0
                    },
                    {
                        begin: /=\s*/,
                        relevance: 0,
                        contains: [
                            {
                                className: 'string',
                                endsParent: true,
                                variants: [
                                    {begin: /"/, end: /"/},
                                    {begin: /'/, end: /'/},
                                    {begin: /[^\s"'=<>`]+/}
                                ]
                            }
                        ]
                    }
                ]
            };
            return {
                aliases: ['html', 'xhtml', 'rss', 'atom', 'xjb', 'xsd', 'xsl', 'plist'],
                case_insensitive: true,
                contains: [
                    {
                        className: 'meta',
                        begin: '<!DOCTYPE', end: '>',
                        relevance: 10,
                        contains: [{begin: '\\[', end: '\\]'}]
                    },
                    hljs.COMMENT(
                        '<!--',
                        '-->',
                        {
                            relevance: 10
                        }
                    ),
                    {
                        begin: '<\\!\\[CDATA\\[', end: '\\]\\]>',
                        relevance: 10
                    },
                    {
                        className: 'meta',
                        begin: /<\?xml/, end: /\?>/, relevance: 10
                    },
                    {
                        begin: /<\?(php)?/, end: /\?>/,
                        subLanguage: 'php',
                        contains: [
                            // We don't want the php closing tag ?> to close the PHP block when
                            // inside any of the following blocks:
                            {begin: '/\\*', end: '\\*/', skip: true},
                            {begin: 'b"', end: '"', skip: true},
                            {begin: 'b\'', end: '\'', skip: true},
                            hljs.inherit(hljs.APOS_STRING_MODE, {illegal: null, className: null, contains: null, skip: true}),
                            hljs.inherit(hljs.QUOTE_STRING_MODE, {illegal: null, className: null, contains: null, skip: true})
                        ]
                    },
                    {
                        className: 'tag',
                        /*
                        The lookahead pattern (?=...) ensures that 'begin' only matches
                        '<style' as a single word, followed by a whitespace or an
                        ending braket. The '$' is needed for the lexeme to be recognized
                        by hljs.subMode() that tests lexemes outside the stream.
                        */
                        begin: '<style(?=\\s|>|$)', end: '>',
                        keywords: {name: 'style'},
                        contains: [TAG_INTERNALS],
                        starts: {
                            end: '</style>', returnEnd: true,
                            subLanguage: ['css', 'xml']
                        }
                    },
                    {
                        className: 'tag',
                        // See the comment in the <style tag about the lookahead pattern
                        begin: '<script(?=\\s|>|$)', end: '>',
                        keywords: {name: 'script'},
                        contains: [TAG_INTERNALS],
                        starts: {
                            end: '\<\/script\>', returnEnd: true,
                            subLanguage: ['actionscript', 'javascript', 'handlebars', 'xml']
                        }
                    },
                    {
                        className: 'tag',
                        begin: '</?', end: '/?>',
                        contains: [
                            {
                                className: 'name', begin: /[^\/><\s]+/, relevance: 0
                            },
                            TAG_INTERNALS
                        ]
                    }
                ]
            };
        }
        );

        hljs.registerLanguage("javascript",
        function(hljs) {
            var IDENT_RE = '[A-Za-z$_][0-9A-Za-z$_]*';
            var KEYWORDS = {
                keyword:
                    'in of if for while finally var new function do return void else break catch ' +
                    'instanceof with throw case default try this switch continue typeof delete ' +
                    'let yield const export super debugger as async await static ' +
                    // ECMAScript 6 modules import
                    'import from as'
                ,
                literal:
                    'true false null undefined NaN Infinity',
                built_in:
                    'eval isFinite isNaN parseFloat parseInt decodeURI decodeURIComponent ' +
                    'encodeURI encodeURIComponent escape unescape Object Function Boolean Error ' +
                    'EvalError InternalError RangeError ReferenceError StopIteration SyntaxError ' +
                    'TypeError URIError Number Math Date String RegExp Array Float32Array ' +
                    'Float64Array Int16Array Int32Array Int8Array Uint16Array Uint32Array ' +
                    'Uint8Array Uint8ClampedArray ArrayBuffer DataView JSON Intl arguments require ' +
                    'module console window document Symbol Set Map WeakSet WeakMap Proxy Reflect ' +
                    'Promise'
            };
            var NUMBER = {
                className: 'number',
                variants: [
                    { begin: '\\b(0[bB][01]+)' },
                    { begin: '\\b(0[oO][0-7]+)' },
                    { begin: hljs.C_NUMBER_RE }
                ],
                relevance: 0
            };
            var SUBST = {
                className: 'subst',
                begin: '\\$\\{', end: '\\}',
                keywords: KEYWORDS,
                contains: []  // defined later
            };
            var TEMPLATE_STRING = {
                className: 'string',
                begin: '`', end: '`',
                contains: [
                    hljs.BACKSLASH_ESCAPE,
                    SUBST
                ]
            };
            SUBST.contains = [
                hljs.APOS_STRING_MODE,
                hljs.QUOTE_STRING_MODE,
                TEMPLATE_STRING,
                NUMBER,
                hljs.REGEXP_MODE
            ];
            var PARAMS_CONTAINS = SUBST.contains.concat([
                hljs.C_BLOCK_COMMENT_MODE,
                hljs.C_LINE_COMMENT_MODE
            ]);

            return {
                aliases: ['js', 'jsx'],
                keywords: KEYWORDS,
                contains: [
                    {
                        className: 'meta',
                        relevance: 10,
                        begin: /^\s*['"]use (strict|asm)['"]/
                    },
                    {
                        className: 'meta',
                        begin: /^#!/, end: /$/
                    },
                    hljs.APOS_STRING_MODE,
                    hljs.QUOTE_STRING_MODE,
                    TEMPLATE_STRING,
                    hljs.C_LINE_COMMENT_MODE,
                    hljs.C_BLOCK_COMMENT_MODE,
                    NUMBER,
                    { // object attr container
                        begin: /[{,]\s*/, relevance: 0,
                        contains: [
                            {
                                begin: IDENT_RE + '\\s*:', returnBegin: true,
                                relevance: 0,
                                contains: [{className: 'attr', begin: IDENT_RE, relevance: 0}]
                            }
                        ]
                    },
                    { // "value" container
                        begin: '(' + hljs.RE_STARTERS_RE + '|\\b(case|return|throw)\\b)\\s*',
                        keywords: 'return throw case',
                        contains: [
                            hljs.C_LINE_COMMENT_MODE,
                            hljs.C_BLOCK_COMMENT_MODE,
                            hljs.REGEXP_MODE,
                            {
                                className: 'function',
                                begin: '(\\(.*?\\)|' + IDENT_RE + ')\\s*=>', returnBegin: true,
                                end: '\\s*=>',
                                contains: [
                                    {
                                        className: 'params',
                                        variants: [
                                            {
                                                begin: IDENT_RE
                                            },
                                            {
                                                begin: /\(\s*\)/,
                                            },
                                            {
                                                begin: /\(/, end: /\)/,
                                                excludeBegin: true, excludeEnd: true,
                                                keywords: KEYWORDS,
                                                contains: PARAMS_CONTAINS
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                className: '',
                                begin: /\s/,
                                end: /\s*/,
                                skip: true,
                            },
                            { // E4X / JSX
                                begin: /</, end: /(\/[A-Za-z0-9\\._:-]+|[A-Za-z0-9\\._:-]+\/)>/,
                                subLanguage: 'xml',
                                contains: [
                                    { begin: /<[A-Za-z0-9\\._:-]+\s*\/>/, skip: true },
                                    {
                                        begin: /<[A-Za-z0-9\\._:-]+/, end: /(\/[A-Za-z0-9\\._:-]+|[A-Za-z0-9\\._:-]+\/)>/, skip: true,
                                        contains: [
                                            { begin: /<[A-Za-z0-9\\._:-]+\s*\/>/, skip: true },
                                            'self'
                                        ]
                                    }
                                ]
                            }
                        ],
                        relevance: 0
                    },
                    {
                        className: 'function',
                        beginKeywords: 'function', end: /\{/, excludeEnd: true,
                        contains: [
                            hljs.inherit(hljs.TITLE_MODE, {begin: IDENT_RE}),
                            {
                                className: 'params',
                                begin: /\(/, end: /\)/,
                                excludeBegin: true,
                                excludeEnd: true,
                                contains: PARAMS_CONTAINS
                            }
                        ],
                        illegal: /\[|%/
                    },
                    {
                        begin: /\$[(.]/ // relevance booster for a pattern common to JS libs: `$(something)` and `$.something`
                    },
                    hljs.METHOD_GUARD,
                    { // ES6 class
                        className: 'class',
                        beginKeywords: 'class', end: /[{;=]/, excludeEnd: true,
                        illegal: /[:"\[\]]/,
                        contains: [
                            {beginKeywords: 'extends'},
                            hljs.UNDERSCORE_TITLE_MODE
                        ]
                    },
                    {
                        beginKeywords: 'constructor get set', end: /\{/, excludeEnd: true
                    }
                ],
                illegal: /#(?!!)/
            };
        }
        );
        hljs.registerLanguage("magento",
            function(hljs) {
                var PARAMS = {
                    className: 'params',
                    begin: '\\(', end: '\\)'
                };

                var FUNCTION_NAMES2 = 'attribute block constant cycle date dump include ' +
                    'max min parent random range source template_from_string';
                var FUNCTION_NAMES = 'var attribute block constant cycle date dump include ' +
                    'max min parent random range source template_from_string';

                var FUNCTIONS = {
                    beginKeywords: FUNCTION_NAMES,
                    keywords: {name: FUNCTION_NAMES},
                    relevance: 0,
                    contains: [
                        PARAMS
                    ]
                };

                var FILTER = {
                    begin: /\|[A-Za-z_]+:?/,
                    keywords:
                        'abs batch capitalize convert_encoding date date_modify default ' +
                        'escape first format join json_encode keys last length lower ' +
                        'merge nl2br number_format raw replace reverse round slice sort split ' +
                        'striptags title trim upper url_encode',
                    contains: [
                        FUNCTIONS
                    ]
                };

                var TAGSO = 'snm_set autoescape block do embed extends filter flush for ' +
                    ' if import include macro sandbox set spaceless use verbatim';
                var TAGS = 'snm_set snm_when snm_otherwise widget block  ' +
                    'depend config if else var view media skin store trans css protocol customvar inlinecss layout';

                var TAGSI = 'var ';

                TAGS = TAGS + ' ' + TAGS.split(' ').map(function(t){return 'end' + t}).join(' ');
                TAGSI = TAGSI + ' ' + TAGSI.split(' ').map(function(t){return 'end' + t}).join(' ');

                return {
                    aliases: ['magento2'],
                    case_insensitive: true,
                    subLanguage: 'html',
                    contains: [
                        //hljs.COMMENT(/\{#/, /#}/),
                        //hljs.COMMENT(/\{#/, /#}/),
                        {
                            className: 'template-variable',
                            begin: /x\{\{/, end: /}}x/,
                            contains: [
                                {
                                    className: 'name',
                                    begin: /\w+/,
                                    keywords: TAGSI,
                                    starts: {
                                        endsWithParent: false,
                                        relevance: 1
                                    }
                                }
                            ]
                        },
                        {
                            className: 'template-tag',
                            begin: /\{\{/, end: /}}/,
                            contains: [
                                {
                                    className: 'name',
                                    begin: /\w+/,
                                    keywords: TAGS,
                                    starts: {
                                        endsWithParent: true,
                                        contains: [FILTER, FUNCTIONS],
                                        relevance: 0
                                    }
                                }
                            ]
                        },

                        /*
                        {
                            className: 'template-variable00',
                            begin: /x\{\{/, end: /}}x/,
                            contains: ['self', FILTER, FUNCTIONS]
                        }
                        */

                    ]
                };
            }

        );
    }
});
