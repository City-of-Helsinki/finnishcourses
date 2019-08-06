/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.bootstrap_barrio_subtheme = {
    attach: function (context, settings) {

      // COURSE AVAILABILITY
      // todo: Probably need to specify "field__item"
      $('.field__item:contains("Available")').css('color', '#129942');
      $('.field__item:contains("Not available")').css('color', '#EC3620');
      $('.field__item:contains("Ask for course organizer")').css('color', '#F08300');

      // COURSE AVAILABILITY - for "View courses"
      // todo: Probably need to specify "field-content"
      $('.field-content:contains("Available")').css('color', '#129942');
      $('.field-content:contains("Not available")').css('color', '#EC3620');
      $('.field-content:contains("Ask for course organizer")').css('color', '#F08300');

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
