let config = {
    map: {
        '*': {
            'fancyboxuby': 'TiDesign_Core/js/fancyboxuby.min',
            'vanillapicker': 'TiDesign_Fundraiser/js/vanilla-picker.min',
            'LeaderLine': 'TiDesign_Fundraiser/js/leader-line.min',
            'waitforimages': 'TiDesign_Fundraiser/js/jquery.waitforimages.min',
            'rotatable': 'TiDesign_Fundraiser/js/jquery.ui.rotatable.min',
            'html2canvas' : 'TiDesign_Fundraiser/js/html2canvas.min',
            'canvas2image' : 'TiDesign_Fundraiser/js/canvas2image',
            'domtoimage' : 'TiDesign_Fundraiser/js/dom-to-image.min'
        }
    },
    shim: {
        'TiDesign_Core/js/fancyboxuby.min': {
            deps: ['jquery']
        },
        'TiDesign_Fundraiser/js/vanilla-picker.min': {
            deps: ['jquery']
        },
        'TiDesign_Fundraiser/js/leader-line.min': {
            deps: ['jquery']
        },
        'TiDesign_Fundraiser/js/jquery.waitforimages.min': {
            deps: ['jquery']
        },
        'TiDesign_Fundraiser/js/jquery.ui.rotatable.min': {
            deps: ['jquery',"jquery/ui"]
        },
        'TiDesign_Fundraiser/js/html2canvas': {
            deps: ['jquery']
        },
        'TiDesign_Fundraiser/js/canvas2image': {
            deps: ['jquery']
        }
    }
};
