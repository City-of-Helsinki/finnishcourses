/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.bootstrap_barrio_subtheme = {
    attach: function (context, settings) {

      // SEARCH MORE OPTIONS "BUTTON"
      let lessSearch = Drupal.t("Less search options");
      let moreSearch = Drupal.t("More search options");

      $(".more-search-options-trigger").on('click', function () {
        $(this).parent().children(".more-options-wrapper").toggle();

        // Adding less class for "Less search options", so expand more svg icon is added through css
        $('.more-search-options-trigger').toggleClass('less');

        // Twice  $(".more-search-options-trigger").text, otherwise it won't toggle between
        // "Less search options" and "More search options"
        $(".more-search-options-trigger").text($(".more-search-options-trigger").text()
        === lessSearch ? moreSearch : lessSearch);
      });

      // STICKY MOBILE BUTTON
      // Observer API
      // Root is the browser viewport / screen
      let observer = new IntersectionObserver(function(entries) {
        // Since there is a single target to be observed, there will be only one entry
          if(entries[0]['isIntersecting'] === true) {
           if(entries[0]['intersectionRatio'] > 0.5)
              // More or less than 50% of target is showing on the screen
            $("#edit-submit-search-courses").hide();
          }
          else {
            // Target is not visible on the screen, show sticky mobile button
            if ($(window).width() < 844.98) {
              $("#edit-submit-search-courses").show();
            }
          }
        }, { threshold: [0, 0.5, 1] });

      observer.observe(document.querySelector("#edit-submit-fixed"));
    }
  };
})(jQuery, Drupal);
