var BlankonOnePageRSRestaurant = function () {

    return {

        // =========================================================================
        // CONSTRUCTOR APP
        // =========================================================================
        init: function () {
            BlankonOnePageRSRestaurant.handleRevolutionSlider();
        },

        // =========================================================================
        // REVOLUTION SLIDER
        // =========================================================================
        handleRevolutionSlider: function () {
            var tpj=jQuery;
            var revapi24;
            tpj(document).ready(function() {
                if(tpj("#rev_slider_24_1").revolution == undefined){
                    revslider_showDoubleJqueryError("#rev_slider_24_1");
                }else{
                    revapi24 = tpj("#rev_slider_24_1").show().revolution({
                        sliderType:"standard",
                        jsFileLocation:"../../../../../assets/commercial/plugins/slider-revolution/revolution/js/",
                        sliderLayout:"fullscreen",
                        dottedOverlay:"none",
                        delay:9000,
                        navigation: {
                            keyboardNavigation:"off",
                            keyboard_direction: "horizontal",
                            mouseScrollNavigation:"on",
                            onHoverStop:"off",
                            touch:{
                                touchenabled:"on",
                                swipe_threshold: 75,
                                swipe_min_touches: 1,
                                swipe_direction: "vertical",
                                drag_block_vertical: false
                            }
                            ,
                            bullets: {
                                enable:true,
                                hide_onmobile:true,
                                hide_under:1024,
                                style:"uranus",
                                hide_onleave:false,
                                direction:"vertical",
                                h_align:"right",
                                v_align:"center",
                                h_offset:30,
                                v_offset:0,
                                space:5,
                                tmp:'<span class="tp-bullet-inner"></span>'
                            }
                        },
                        viewPort: {
                            enable:true,
                            outof:"wait",
                            visible_area:"80%"
                        },
                        responsiveLevels:[1240,1024,778,480],
                        gridwidth:[1240,1024,778,480],
                        gridheight:[868,768,960,720],
                        lazyType:"single",
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
BlankonOnePageRSRestaurant.init();