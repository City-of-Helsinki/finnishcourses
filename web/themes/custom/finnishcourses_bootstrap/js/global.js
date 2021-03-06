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
      // Root is the browser viewport / screen
      if (document.querySelector(".path-frontpage")) {
        let searchButtonObserver = new IntersectionObserver(function (entries) {
          // Since there is a single target to be observed, there will be only one entry
          if (entries[0]['isIntersecting'] === true) {
            if (entries[0]['intersectionRatio'] > 0.5)
            // More or less than 50% of target is showing on the screen
              $("#edit-submit-search-courses").hide();
          } else {
            // Target is not visible on the screen, show sticky mobile button
            if ($(window).width() < 620) {
              $("#edit-submit-search-courses").show();
            }
          }
        }, {threshold: [0, 0.5, 1]});

        searchButtonObserver.observe(document.querySelector("#edit-submit-fixed"));
      }

      // STICKY NAVBAR - TRANSPARENT BACKGROUND WHEN SCROLLED
      function checkScroll() {
        // Navbar changes to px
        let startY = $('#navbar-top').height() * 2;

        if (document.querySelector(".path-frontpage")) {
          if ($(window).scrollTop() > startY) {
            $('.region-secondary-menu').addClass("nav-container-sticky");
          } else {
            $('.region-secondary-menu').removeClass("nav-container-sticky");
          }
        }
      }

      if ($('#navbar-top').length > 0) {
        $(window).on("scroll load resize", function () {
          checkScroll();
        });
      }

      // HAMBURGER MENU
      $(".mobile-menu-icon").click(function () {
        //$(".mobile-menu").css('backgroundColor', '#0073cf');
		$(".mobile-menu").toggleClass('active');
        $(".region-secondary-menu").css('backgroundColor', '#0073cf');
        $(".mobile-menu-wrapper").slideToggle("fast", function () {
          if ($(window).width() < 850) {
            $(".mobile-menu-icon").hide();
            $(".mobile-close-icon").show();
          }
        });
      });

      $(".mobile-close-icon").click(function () {
        $(".mobile-menu-wrapper").slideToggle(function () {
		$(".mobile-menu").removeClass('active');	
          if ($(window).width() < 850) {
            $(".mobile-close-icon").hide();
            $(".mobile-menu-icon").show();
           // $(".mobile-menu").css('backgroundColor', 'transparent');
            //$(".region-secondary-menu").css('backgroundColor', 'transparent');
          }
        });
      });
	  // Front page advanced search date format
	  if ( $( ".bef-datepicker" ).length ) {
		 console.log('test');
		$('html:lang(fi) .bef-datepicker').datepicker({ dateFormat: 'd.m.yy' });
		$('html:lang(ru) .bef-datepicker').datepicker({ dateFormat: 'd.m.yy' });
		$('html:lang(en) .bef-datepicker').datepicker({ dateFormat: 'd.m.yy' });
	  }
    }
  };
})(jQuery, Drupal);
