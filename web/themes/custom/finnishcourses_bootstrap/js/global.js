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
	  
	   $(".more-search-options-trigger", context).on('click', function () {
       $(".more-options-wrapper").toggle();
		
		console.log($(".more-options-wrapper"));

        // Adding less class for "Less search options", so expand more svg icon is added through css
        $('.more-search-options-trigger').toggleClass('less');

        // Twice  $(".more-search-options-trigger").text, otherwise it won't toggle between
        // "Less search options" and "More search options"
        $(".more-search-options-trigger").text($(".more-search-options-trigger").text()
        === lessSearch ? moreSearch : lessSearch);
      });

	
	
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
		$('html:lang(fi) .bef-datepicker').datepicker({ dateFormat: 'd.m.yy' });
		$('html:lang(ru) .bef-datepicker').datepicker({ dateFormat: 'd.m.yy' });
		$('html:lang(en) .bef-datepicker').datepicker({ dateFormat: 'd.m.yy' });
	  }
	  
	  
	   //$('html:lang(fi) #edit-course-fee-1--2').attr('aria-label', '0  49');
	  
	 
	  
		
		  
		  
	
    }
  };

  
	$(document).ready(function () {
		
	
     console.log($('.views-exposed-form'));
	  
	  // STICKY MOBILE BUTTON
      // Root is the browser viewport / screen
      if (document.querySelector(".path-frontpage") && document.querySelector("#edit-submit-fixed")) {
		  
		 
		  
        let searchButtonObserver = new IntersectionObserver(function (entries) {
			
			// console.log(entries); 
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
	  
	  
	  $(".form-item-sort-bef-combine select").each(function () {
			  
			  var originalSort = $(this);
		 
			  //console.log("test");
			  
			  var sort = originalSort.clone();
			  $('.form-item-sort-bef-combine').hide();
			  
			  $('.view-search-courses .view-header').append( '<div class="newsort"></div>' );
			  $('.view-search-courses .view-header .newsort').append(sort);
			  
			  $(sort).change(function() {
				  var selectedValue = $(this).val();
				  console.log(selectedValue);
				  originalSort.val(selectedValue);
				  
				  $('.bef-exposed-form form').submit();
			  });
		 
		 });
	});
  
})(jQuery, Drupal);
