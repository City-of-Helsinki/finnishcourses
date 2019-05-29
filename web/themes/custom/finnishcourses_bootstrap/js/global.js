/**
 * @file
 * Global utilities.
 *
 */
(function($, Drupal) {

  'use strict';

  Drupal.behaviors.bootstrap_barrio_subtheme = {
    attach: function(context, settings) {
      var position = $(window).scrollTop();
      $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
          $('body').addClass("scrolled");
        }
        else {
          $('body').removeClass("scrolled");
        }
        var scroll = $(window).scrollTop();
        if (scroll > position) {
          $('body').addClass("scrolldown");
          $('body').removeClass("scrollup");
        } else {
          $('body').addClass("scrollup");
          $('body').removeClass("scrolldown");
        }
        position = scroll;
      });

      // COURSE AVAILABILITY
        $(document).ready(function () {
            // Probably need to specify "field__item"
            $('.field__item:contains("Available")').css('color', '#129942');
            $('.field__item:contains("Not available")').css('color', '#EC3620');
            $('.field__item:contains("Ask for course organizer")').css('color', '#F08300');
        });

        // SEARCH MORE OPTIONS "BUTTON"
        $(document).ready(function(){

            $(".more-search-options-trigger").on('click', function (){
                $(this).parent().children(".more-options-wrapper").toggle();
                $(".more-search-options-trigger").text($(".more-search-options-trigger").text()
                == "Less search options" ? "More search options" : "Less search options")
            });
        });
    }
  };
})(jQuery, Drupal);