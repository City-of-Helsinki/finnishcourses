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
	  
	   $(".more-search-options-trigger", context).on('click', function (event) {
		event.preventDefault();  
        $(".more-options-wrapper").toggle();
		
		//console.log($(".more-options-wrapper"));

        // Adding less class for "Less search options", so expand more svg icon is added through css
        $('.more-search-options-trigger').toggleClass('less');
		
		$('.more-search-options-trigger').attr('aria-expanded', false);
		$('.more-search-options-trigger.less').attr('aria-expanded', true);
		

		
		

        // Twice  $(".more-search-options-trigger").text, otherwise it won't toggle between
        // "Less search options" and "More search options"
        $(".more-search-options-trigger").text($(".more-search-options-trigger").text()
        === lessSearch ? moreSearch : lessSearch);
      });
	  
	  
	  
	  // SHOW SEARCH FORM "BUTTON"
      let hideSearch = Drupal.t("Hide search form");
      let showSearch = Drupal.t("Show search form");
	  
	   $(".show-search-form-trigger", context).on('click', function (event) {
		event.preventDefault();  
        $(".block-views-exposed-filter-blocksearch-courses-page-1 form").toggle();
		
		//console.log($(".more-options-wrapper"));

        // Adding less class for "Less search options", so expand more svg icon is added through css
        $('.show-search-form-trigger').toggleClass('less');
		
		$('.show-search-form-trigger').attr('aria-expanded', false);
		$('.show-search-form-trigger.less').attr('aria-expanded', true);
		
		$('.show-search-form-trigger').text(showSearch);
		$('.show-search-form-trigger.less').text(hideSearch);
		
      });
	  
	  
	  
	   $(".form-item-sort-bef-combine", context).each(function () {
			  
			/*   var originalSort = $(this);
			  
			  var originalSelect = $("select", this);
		 
			  
			  var sort = originalSort.clone();
			  $('.form-item-sort-bef-combine').hide();
			  
			  $('.view-search-courses .view-header').append(sort); */
			  
			  var sort = $(this);
			  
			  sort.detach().appendTo('.view-search-courses .view-header');
			  
			  $(sort).change(function() {
				  var selectedValue = $("select", this).val();
				  //console.log(selectedValue);
				  sort.val(selectedValue);
				  
				  $('.bef-exposed-form form').submit();
			  });
		 
		 });
	  

		 
		$("#edit-starting-level--2 input[type='radio'], #edit-starting-level input[type='radio']", context).click(function(){

            var radioValue = $(this).val();
			var newValue;
            if(radioValue){
				
				//console.log(radioValue);	
				
				switch(radioValue) {
				  case 'All':
					radioValue = 0;
					break;
				}
				
				$('.level-description span').hide();
				$('.level-description span:eq('+radioValue+')').show();
            }
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


	  // Mobile Navigation "BUTTON"
      let hideNavigation = Drupal.t("Hide navigation");
      let showNavigation = Drupal.t("Show navigation");
	  
	  
      // HAMBURGER MENU
      $("#toggle-navigation-menu-button").click(function () {
        //$(".mobile-menu").css('backgroundColor', '#0073cf');
		$("#toggle-navigation-menu-button").toggleClass('active');
		$("#toggle-navigation-menu-button").attr('aria-expanded', false);
		$("#toggle-navigation-menu-button.active").attr('aria-expanded', true);
		
		$('#toggle-navigation-menu-button .assistive').text(showNavigation);
		$('#toggle-navigation-menu-button.active .assistive').text(hideNavigation);
		
        $(".region-secondary-menu").css('backgroundColor', '#0073cf');
        $(".mobile-menu-wrapper").slideToggle("fast", function () {
          if ($(window).width() < 850) {
            $(".mobile-menu-icon").hide();
            $(".mobile-close-icon").show();
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
		
	
     //console.log($('.views-exposed-form'));
	  
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
	  
	  
	  // siirretään h1 otsikko kurssihaun tulossivulla
	  
	  var newTitle = $('.path-search .view-search-courses .view-header h2').text();
	  //console.log(newTitle);
	  $('.path-search .view-search-courses .view-header h2').remove();
	  $('.path-search h1').text(newTitle).show();
	  /* $('.path-search h1.title').text(newTitle);
	  $('.path-search h1.title').show(); */
	  
	 
	 // Lisätään lang atribuutti kielen vaihtajaan
	  $('.language-switcher-language-url li a').each(function () {

		var lang = $( this ).attr( "hreflang" );
		$( this ).attr( "lang", lang );

	  });

		 
	
		 
	});
  
})(jQuery, Drupal);
