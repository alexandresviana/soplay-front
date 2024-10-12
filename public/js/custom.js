/*
Template: Streamit - Responsive Bootstrap 4 Template
Author: iqonicthemes.in
Design and Developed by: iqonicthemes.in
NOTE: This file contains the styling for responsive Template.
*/

/*----------------------------------------------
Index Of Script
------------------------------------------------

:: Sticky Header Animation & Height
:: Back to Top
:: Header Menu Dropdown
:: Slick Slider
:: Owl Carousel
:: Page Loader
:: Mobile Menu Overlay
:: Equal Height of Tab Pane
:: Active Class for Pricing Table
:: Select 2 Dropdown
:: Video Popup
:: Flatpicker
:: Custom File Uploader

------------------------------------------------
Index Of Script
----------------------------------------------*/

(function (jQuery) {
    "use strict";
    jQuery(document).ready(function () {

        function activaTab(pill) {
            jQuery(pill).addClass('active show');
        }

        /*---------------------------------------------------------------------
            Sticky Header Animation & Height
        ----------------------------------------------------------------------- */
        /*function headerHeight() {
            var height = jQuery("#main-header").height();
            jQuery('.iq-height').css('height', height + 'px');
        }
        jQuery(function() {
            var header = jQuery("#main-header"),
                yOffset = 0,
                triggerPoint = 80;

            headerHeight();

            jQuery(window).resize(headerHeight);
            jQuery(window).on('scroll', function() {

                yOffset = jQuery(window).scrollTop();

                if (yOffset >= triggerPoint) {
                    header.addClass("menu-sticky animated slideInDown");
                } else {
                    header.removeClass("menu-sticky animated slideInDown");
                }

            });
        });*/

        /*---------------------------------------------------------------------
            Back to Top
        ---------------------------------------------------------------------*/

        if (window.onload = function () { }) {
            $('html, body').animate({ scrollTop: 0 }, '300');
        }

        var btn = $('#back-to-top');
        $(window).scroll(function () {
            if ($(window).scrollTop() > 50) {
                btn.addClass('show');
            } else {
                btn.removeClass('show');
            }
        });
        btn.on('click', function (e) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: 0 }, '300');
        });

        function sleepFor(sleepDuration) {
            var now = new Date().getTime();
            while (new Date().getTime() < now + sleepDuration) {
                /* Do nothing */
            }
        }

        var volume = document.getElementById("volume");
        var vid = document.getElementById("bgvid");
        if (vid != undefined) {
            vid.onplay = function () {
                volume.style.opacity = 1;
            }

            vid.onended = function () {
                vid.src = vid.poster;
                volume.style.opacity = 0;
            }

            volume.onclick = function () {
                if (vid.muted) {
                    vid.muted = false;
                }
                else {
                    vid.muted = true;
                }
                $('#volume').find('span').toggleClass('glyphicon-volume-off glyphicon-volume-up');
            }
        }
        /*---------------------------------------------------------------------
            Header Menu Dropdown
        ---------------------------------------------------------------------*/
        jQuery('[data-toggle=more-toggle]').on('click', function () {
            jQuery(this).next().toggleClass('show');
        });

        $(document).on('keypress', function (e) {
            if (e.which == 13) {
                if ($("#input-pesquisar").val().length > 2) {
                    window.location.href = "http://" + window.location.hostname + "/pesquisar/" + $("#input-pesquisar").val();
                    $("#input-pesquisar").val("Pesquisando...");
                }
            }
        });

        jQuery(document).on('click', function (e) {

            let myTargetElement = e.target;
            let selector, mainElement;
            if (jQuery(myTargetElement).hasClass('search-toggle') || jQuery(myTargetElement).parent().hasClass('search-toggle') || jQuery(myTargetElement).parent().parent().hasClass('search-toggle')) {
                if (jQuery(myTargetElement).hasClass('search-toggle')) {
                    selector = jQuery(myTargetElement).parent().parent();
                    mainElement = jQuery(myTargetElement);
                } else if (jQuery(myTargetElement).parent().hasClass('icon-pesquisa')) {
                    //Animação
                    $("#input-pesquisar").show();
                    $("#input-pesquisar").animate({
                        width: '120px',
                    }, 1500, function () {
                        $("#input-pesquisar").val("");
                        $("#input-pesquisar").focus();
                    });

                    if ($("#input-pesquisar").val().length > 2) {
                        window.location.href = "https://" + window.location.hostname + "/pesquisar/" + $("#input-pesquisar").val();
                    }

                    selector = jQuery(myTargetElement).parent().parent();
                    mainElement = jQuery(myTargetElement).parent();
                } else if (jQuery(myTargetElement).parent().parent().hasClass('search-toggle')) {
                    selector = jQuery(myTargetElement).parent().parent().parent();
                    mainElement = jQuery(myTargetElement).parent().parent();
                }
                if (!mainElement.hasClass('active') && jQuery(".navbar-list li").find('.active')) {
                    jQuery('.navbar-right li').removeClass('iq-show');
                    jQuery('.navbar-right li .search-toggle').removeClass('active');
                }

                selector.toggleClass('iq-show');
                mainElement.toggleClass('active');

                e.preventDefault();
            } else if (jQuery(myTargetElement).is('.search-input')) { } else {
                jQuery('.navbar-right li').removeClass('iq-show');
                jQuery('.navbar-right li .search-toggle').removeClass('active');
            }
        });

        /*---------------------------------------------------------------------
            Slick Slider
        ----------------------------------------------------------------------- */

        $('#home-slider').on('afterChange', function (slick, currentSlide) {
            //console.log(currentSlide.$slideTrack[0]);
            //$(currentSlide.$slideTrack[0]).animate({zoom: '110%'}, "slow");
        });

        $('#home-slider').slick({
            autoplay: false,
            speed: 800,
            lazyLoad: 'progressive',
            arrows: false,
            fade: true,
            dots: false,
            prevArrow: '<div class="slick-nav prev-arrow"><i></i><svg><use xlink:href="#circle"></svg></div>',
            nextArrow: '<div class="slick-nav next-arrow"><i></i><svg><use xlink:href="#circle"></svg></div>',
            responsive: [{
                breakpoint: 767,
                settings: {
                    dots: false,
                    arrows: false,
                }
            }]
        }).slickAnimation();
        $('.slick-nav').on('click touch', function (e) {
            e.preventDefault();

            var arrow = $(this);

            if (!arrow.hasClass('animate')) {
                arrow.addClass('animate');
                setTimeout(() => {
                    arrow.removeClass('animate');
                }, 1600);
            }
        });

        jQuery('.favorites-slider.favorites-slider__aovivo').slick({
            dots: false,
            infinite: true,
            autoplay: false,
            slidesToScroll: 4,
            slidesToShow: 9,
            nextArrow: '<a href="#" class="slick-arrow slick-next"><i class= "fa fa-chevron-right"></i></a>',
            prevArrow: '<a href="#" class="slick-arrow slick-prev"><i class= "fa fa-chevron-left"></i></a>',
            variableWidth: true,
            responsive: [{
                breakpoint: 1170,
                settings: {
                    slidesToShow: 8,
                }
            },
            {
                breakpoint: 1100,
                settings: {
                    slidesToShow: 7,
                    slidesToScroll: 4,
                }
            },
            {
                breakpoint: 910,
                settings: {
                    slidesToShow: 7,
                }
            },
            {
                breakpoint: 790,
                settings: {
                    slidesToShow: 6,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 6,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 630,
                settings: {
                    slidesToShow: 5,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 510,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 410,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2,
                }
            },
            ]
        });

        jQuery('.favorites-slider.favorites-slider__video').slick({
            arrows: true,
            dots: false,
            infinite: false,
            autoplay: false,
            slidesToScroll: 4,
            slidesToShow: 1,
            variableWidth: true,
            nextArrow: '<a href="#" class="slick-arrow slick-next"><i class= "fa fa-chevron-right"></i></a>',
            prevArrow: '<a href="#" class="slick-arrow slick-prev"><i class= "fa fa-chevron-left"></i></a>',
            responsive: [{
                breakpoint: 1040,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 795,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 615,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 470,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 350,
                settings: {
                    slidesToShow: 1,
                }
            },
            ]
        });

        jQuery('#top-ten-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 4,
            arrows: false,
            fade: true,
            variableWidth: true,
            asNavFor: '#top-ten-slider-nav',
        });

        jQuery('#top-ten-slider-nav').slick({
            slidesToShow: 3,
            slidesToScroll: 4,
            asNavFor: '#top-ten-slider',
            dots: false,
            arrows: true,
            infinite: false,
            vertical: true,
            verticalSwiping: true,
            centerMode: false,
            nextArrow: '<button class="NextArrow"><i class="ri-arrow-down-s-line"></i></button>',
            prevArrow: '<button class="PreArrow"><i class="ri-arrow-up-s-line"></i></button>',
            focusOnSelect: true,
            responsive: [{
                breakpoint: 1200,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    asNavFor: false,
                }
            },
            ]
        });

        jQuery('#episodes-slider2').slick({
            dots: false,
            arrows: true,
            infinite: false,
            speed: 300,
            autoplay: false,
            slidesToShow: 4,
            slidesToScroll: 2,
            responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 2,
                    infinite: false,
                    dots: true,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2,
                }
            }
            ]
        });

        jQuery('#episodes-slider3').slick({
            dots: false,
            arrows: true,
            infinite: false,
            speed: 300,
            autoplay: false,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 2,
                    infinite: false,
                    dots: true,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2,
                }
            }
            ]
        });

        jQuery('#trending-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 2,
            arrows: false,
            fade: true,
            draggable: false,
            asNavFor: '#trending-slider-nav',
        });

        jQuery('#trending-slider-nav').slick({
            slidesToShow: 5,
            slidesToScroll: 2,
            asNavFor: '#trending-slider',
            dots: false,
            arrows: true,
            nextArrow: '<a href="#" class="slick-arrow slick-next"><i class= "fa fa-chevron-right"></i></a>',
            prevArrow: '<a href="#" class="slick-arrow slick-prev"><i class= "fa fa-chevron-left"></i></a>',
            infinite: false,
            centerMode: true,
            centerPadding: 0,
            focusOnSelect: true,
            responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2
                }
            }
            ]
        });

        jQuery('#tvshows-slider').slick({
            centerMode: true,
            centerPadding: '200px',
            slidesToShow: 1,
            nextArrow: '<button class="NextArrow"><i class="ri-arrow-right-s-line"></i></button>',
            prevArrow: '<button class="PreArrow"><i class="ri-arrow-left-s-line"></i></button>',
            arrows: true,
            dots: false,
            responsive: [{
                breakpoint: 991,
                settings: {
                    arrows: false,
                    centerMode: true,
                    centerPadding: '20px',
                    slidesToShow: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    centerMode: true,
                    centerPadding: '20px',
                    slidesToShow: 1
                }
            }
            ]
        });

        /*---------------------------------------------------------------------
            Owl Carousel
        ----------------------------------------------------------------------- */
        jQuery('.episodes-slider1').owlCarousel({
            loop: true,
            margin: 20,
            nav: true,
            navText: ["<i class='ri-arrow-left-s-line'></i>", "<i class='ri-arrow-right-s-line'></i>"],
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 4
                }
            }
        });

        /*---------------------------------------------------------------------
            Page Loader
        ----------------------------------------------------------------------- */
        jQuery("#load").fadeOut();
        jQuery("#loading").delay(0).fadeOut("slow");

        jQuery('.widget .fa.fa-angle-down, #main .fa.fa-angle-down').on('click', function () {
            jQuery(this).next('.children, .sub-menu').slideToggle();
        });

        /*---------------------------------------------------------------------
        Mobile Menu Overlay
        ----------------------------------------------------------------------- */
        jQuery(document).on("click", function (event) {
            var $trigger = jQuery(".main-header .navbar");
            if ($trigger !== event.target && !$trigger.has(event.target).length) {
                jQuery(".main-header .navbar-collapse").collapse('hide');
                jQuery('body').removeClass('nav-open');
            }
        });
        jQuery('.c-toggler').on("click", function () {
            jQuery('body').addClass('nav-open');
        });

        /*---------------------------------------------------------------------
            Equal Height of Tab Pane
        -----------------------------------------------------------------------*/
        jQuery('.trending-content').each(function () {
            var highestBox = 0;
            jQuery('.tab-pane', this).each(function () {
                if (jQuery(this).height() > highestBox) {
                    highestBox = jQuery(this).height();
                }
            });
            jQuery('.tab-pane', this).height(highestBox);
        });

        /*---------------------------------------------------------------------
                Active Class for Pricing Table
                -----------------------------------------------------------------------*/
        jQuery("#my-table tr th").on("click", function () {
            jQuery('#my-table tr th').children().removeClass('active');
            jQuery(this).children().addClass('active');
            jQuery("#my-table td").each(function () {
                if (jQuery(this).hasClass('active')) {
                    jQuery(this).removeClass('active')
                }
            });
            var col = jQuery(this).index();
            jQuery("#my-table tr td:nth-child(" + parseInt(col + 1) + ")").addClass('active');
        });

        /*---------------------------------------------------------------------
            Select 2 Dropdown
        -----------------------------------------------------------------------*/
        if (jQuery('select').hasClass('season-select')) {
            jQuery('select').select2({
                theme: 'bootstrap4',
                allowClear: false,
                width: 'resolve'
            });
        }
        if (jQuery('select').hasClass('pro-dropdown')) {
            jQuery('.pro-dropdown').select2({
                theme: 'bootstrap4',
                minimumResultsForSearch: Infinity,
                width: 'resolve'
            });
            jQuery('#lang').select2({
                theme: 'bootstrap4',
                placeholder: 'Language Preference',
                allowClear: true,
                width: 'resolve'
            });
        }

        /*---------------------------------------------------------------------
            Video popup
        -----------------------------------------------------------------------*/
        jQuery('.video-open').magnificPopup({
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false,
            iframe: {
                markup: '<div class="mfp-iframe-scaler">' +
                    '<div class="mfp-close"></div>' +
                    '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
                    '</div>',

                srcAction: 'iframe_src',
            }
        });

        /*---------------------------------------------------------------------
            Flatpicker
        -----------------------------------------------------------------------*/
        if (jQuery('.date-input').hasClass('basicFlatpickr')) {
            jQuery('.basicFlatpickr').flatpickr();
        }
        /*---------------------------------------------------------------------
            Custom File Uploader
        -----------------------------------------------------------------------*/
        jQuery(".file-upload").on("change", function () {
            ! function (e) {
                if (e.files && e.files[0]) {
                    var t = new FileReader;
                    t.onload = function (e) {
                        jQuery(".profile-pic").attr("src", e.target.result)
                    }, t.readAsDataURL(e.files[0])
                }
            }(this)
        }), jQuery(".upload-button").on("click", function () {
            jQuery(".file-upload").click();
        });
        // AOS.init();
        //mostrar o conteúdo apenas quando for carregado
        if ($('.main-content').length) {
            $('.main-content').show();
        }
    });
})(jQuery);

if (window.location.toString().includes("ao_vivo_radios")) {
    console.log("RADIOS");
    $('.tv__player-channels').css('height', '600px');
    $('.tv__player-channels').css('overflow', 'initial');
    $('.tv__player-channels').css('width', '100%');
    $('.tv__player-container').css('width', '80%');
    $('.tv__player-container').css('flex-direction', 'column');
    $('.tv__player').css('height', '150px');
    $('.tv__player').css('margin-bottom', '100px');
}

$(document).ready(function () {
    let elemento;
    elemento = $('.img-box').find('img');
    elemento.lazyload();
})

var idserie = 0;
var apiGlobal = '';
function abrirModal(itemTemporada, itemId, itemDescricao, im, api) {
    var url = api;
    apiGlobal = api;
    idserie = itemId;
    var imdbNota = document.getElementById("imdbNota");
    var ano = document.getElementById("ano");
    var duracao = document.getElementById("duracao");
    var txtDesc = document.getElementById("text-desc");
    var imgMod = document.getElementById("img-modal");
    if (itemTemporada != null && itemTemporada < 1) {
        var btnPlay = document.getElementById("link-play");
        btnPlay.href = '/video/' + itemId;
    }
    if (itemTemporada != null && itemTemporada > 0) {
        $("#temporadas").empty();
        var btnPlaySerie = document.getElementById("link-play-serie");
        $(".btnplay").css('display', 'none');
        //btnPlaySerie.href = '/video/series/' + itemId;
        var token = sessionStorage.getItem('current_token_login');
        var settings = {
            "url": url + "/api/v1/series/" + itemId,
            "method": "GET",
            "timeout": 0,
            "crossDomain": true,
            "dataType": 'json',
            "contentType": "application/json; charset=utf-8",
            "headers": {
                "Authorization": "Bearer " + token,
                "Access-Control-Allow-Origin": "*"

            },
        };
        $.ajax(settings).done(function (response) {
            $("#temporadas").empty();
            $("#temporadas").append($('<option>', {
                value: '0',
                text: 'Selecione... ',
                class: "optionGroup",
                selected: 'selected'
            }));
            $("#temporadas").show();
            $(".eps").show();
            response.temporadas.forEach(element => {
                $("#temporadas").append($('<option>', {
                    value: element.temporada,
                    text: 'Temporada ' + element.temporada,
                    class: "optionGroup"
                }));
            });

        }).fail(function (jqXHR, textStatus, msg) {
            console.log("Erro: ", msg);
        });
    }

    imgMod.src = im;
    //bloco para adição de informações ao conteúdo quando for solicidato
    // imdbNota.innerHTML = itemImbd;
    // ano.innerHTML = itemAno;
    // duracao.innerHTML = 'Duração: ' + itemTempoVideo + ' Minutos';
    txtDesc.innerText = itemDescricao;

    $('#myModal').modal({ show: true });
    $(".temporadas").change();
};

$("#temporadas").on('change', function () {
    var token = sessionStorage.getItem('current_token_login');
    var settings = {
        "url": apiGlobal + "/api/v1/series/" + idserie + '/temporada/' + $("#temporadas").val(),
        "method": "GET",
        "timeout": 0,
        "crossDomain": true,
        "dataType": 'json',
        "contentType": "application/json; charset=utf-8",
        "headers": {
            "Authorization": "Bearer " + token,
            "Access-Control-Allow-Origin": "*"

        },
    };
    $.ajax(settings).done(function (response) {

        $(".episodios").empty();
        $(".episodios").show();
        response.episodios.forEach(element => {
            $img = null;
            if (element.imagem) {
                $img = element.capa;
            } else {
                $img = '/geral/playimg.png';
            }

            $(".episodios").append($('<div>', {
                class: 'ep-div' + element.id
            }));

            $(".ep-div" + element.id).append($('<a>', {
                class: 'aEp',
                href: '/video/playseries/' + element.id
            }));

            $(".ep-div" + element.id).append($('<img>', {
                src: $img
            }));

            $(".ep-div" + element.id).append($('<div>', {
                class: 'ep',
                text: element.titulo + ' ' + element.subtitulo
            }));

        });


    }).fail(function (jqXHR, textStatus, msg) {
        console.log("Erro: ", msg);
    });
});
