var BlankonOnePageRSPhotographyType2 = function () {

    return {

        // =========================================================================
        // CONSTRUCTOR APP
        // =========================================================================
        init: function () {
            BlankonOnePageRSPhotographyType2.handleRevolutionSlider();
        },

        // =========================================================================
        // REVOLUTION SLIDER
        // =========================================================================
        handleRevolutionSlider: function () {
            var tpj=jQuery;
            var revapi50;
            tpj(document).ready(function() {
                if(tpj("#rev_slider_50_1").revolution == undefined){
                    revslider_showDoubleJqueryError("#rev_slider_50_1");
                }else{
                    revapi50 = tpj("#rev_slider_50_1").show().revolution({
                        sliderType:"carousel",
                        jsFileLocation:"../../revolution/js/",
                        sliderLayout:"fullscreen",
                        dottedOverlay:"none",
                        delay:9000,
                        navigation: {
                            keyboardNavigation:"off",
                            keyboard_direction: "horizontal",
                            onHoverStop:"off",
                        },
                        carousel: {
                            maxRotation: 5,
                            vary_rotation: "off",
                            minScale: 15,
                            vary_scale: "off",
                            horizontal_align: "center",
                            vertical_align: "center",
                            fadeout: "on",
                            vary_fade: "on",
                            maxVisibleItems: 3,
                            infinity: "off",
                            space: -80,
                            stretch: "off"
                        },
                        responsiveLevels:[1240,1024,778,480],
                        gridwidth:[1024,900,778,480],
                        gridheight:[868,768,960,720],
                        lazyType:"none",
                        shadow:0,
                        spinner:"off",
                        stopLoop:"on",
                        stopAfterLoops:0,
                        stopAtSlide:1,
                        shuffle:"off",
                        autoHeight:"off",
                        fullScreenAlignForce:"off",
                        fullScreenOffsetContainer: "",
                        fullScreenOffset: "",
                        disableProgressBar:"on",
                        hideThumbsOnMobile:"off",
                        hideSliderAtLimit:0,
                        hideCaptionAtLimit:0,
                        hideAllCaptionAtLilmit:0,
                        debugMode:false,
                        fallbacks: {
                            simplifyAll:"off",
                            nextSlideOnWindowFocus:"off",
                            disableFocusListener:false,
                        }
                    });
                }
            });	/*ready*/
        }

    };

}();

// Call main app init
BlankonOnePageRSPhotographyType2.init();