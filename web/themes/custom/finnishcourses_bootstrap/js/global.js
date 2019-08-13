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
    }
  };
})(jQuery, Drupal);
