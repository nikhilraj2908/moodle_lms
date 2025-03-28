define(["jquery"], function($) {
    var slideshow_degrade = {

        slideIndex: 1,

        show: function() {
            slideshow_degrade.showSlides(slideshow_degrade.slideIndex);

            setInterval(function() {
                slideshow_degrade.plusSlides(1);
            }, 7000);

            $(".slideshow-prev").click(function() {
                slideshow_degrade.plusSlides(-1);
            });
            $(".slideshow-next").click(function() {
                slideshow_degrade.plusSlides(1);
            });

            $(".slideshow-dot").click(function() {
                slideshow_degrade.slideIndex = $(this).attr("data-slidenun");
                slideshow_degrade.showSlides(slideshow_degrade.slideIndex);
            });
        },
        plusSlides: function(n) {
            slideshow_degrade.showSlides(slideshow_degrade.slideIndex += n);
        },
        showSlides: function(slideshow_num) {
            var slides_length = $(".slideshow-item").hide().length;
            if (slideshow_num > slides_length) {
                slideshow_degrade.slideIndex = 1
            }
            if (slideshow_num < 1) {
                slideshow_degrade.slideIndex = slides_length;
            }

            $(".slideshow-item-" + slideshow_degrade.slideIndex).show();

            $(".slideshow-dot").removeClass("active");
            $(".slideshow-dot-" + slideshow_degrade.slideIndex).addClass("active");
        }
    };

    return slideshow_degrade;
});
