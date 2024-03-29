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
		
		$('.showunder 50 €-search-form-trigger').attr('aria-expanded', false);
		$('.show-search-form-trigger.less').attr('aria-expanded', true);
		
		$('.show-search-form-trigger').text(showSearch);
		$('.show-search-form-trigger.less').text(hideSearch);
		
      });
	  
	  
	  $(".view-search-courses .view-empty", context).each(function () {
		  console.log($(this));
		  
		  $(this).appendTo("#block-finnishcourses-bootstrap-page-title .content");
		  
	  });	  
	  
	  
	   function sortSelectOptions(selector, skip_first) {
		var options = (skip_first) ? $(selector + ' option:not(:first)') : $(selector + ' option');
		var arr = options.map(function(_, o) { return { t: $(o).text(), v: o.value, s: $(o).prop('selected') }; }).get();
		arr.sort(function(o1, o2) {
		  var t1 = o1.t.toLowerCase(), t2 = o2.t.toLowerCase();
		  return t1 > t2 ? 1 : t1 < t2 ? -1 : 0;
		}); 
		options.each(function(i, o) {
			o.value = arr[i].v;
			$(o).text(arr[i].t);
			if (arr[i].s) {
				$(o).attr('selected', 'selected').prop('selected', true);
			} else {
				$(o).removeAttr('selected');
				$(o).prop('selected', false);
			}
		}); 
	  }
	  
	   $(".js-form-item-organization", context).each(function () {
	       sortSelectOptions('.js-form-item-organization select', true);
	   });
	  
	  // aria labelit hintakenttään
	  
	  $("#edit-course-fee", context).each(function () {
		  
		  $('html:lang(fi) .form-item-course-fee-1').attr('aria-label', 'alle 50 euroa');
		  $('html:lang(fi) .form-item-course-fee-2').attr('aria-label', '50 viiva 149 euroa');
		  $('html:lang(fi) .form-item-course-fee-3').attr('aria-label', 'yli 150 euroa');
		  
		  $('html:lang(en) .form-item-course-fee-1').attr('aria-label', 'under 50 €');
		  $('html:lang(en) .form-item-course-fee-2').attr('aria-label', '50 - 149 €');
		  $('html:lang(en) .form-item-course-fee-3').attr('aria-label', 'over 150 €');
		  
		  $('html:lang(ru) .form-item-course-fee-1').attr('aria-label', 'до 50 €');
		  $('html:lang(ru) .form-item-course-fee-2').attr('aria-label', '50 - 149 €');
		  $('html:lang(ru) .form-item-course-fee-3').attr('aria-label', 'свыше 150 €');
	  });
	  
	  
	  
	   $(".form-item-sort-bef-combine", context).each(function () {
			  
			  var originalSort = $(this);
			  
			  var originalSelect = $("select", this);
			  
			  var sort = originalSort.clone();
			  $('.form-item-sort-bef-combine').hide();
			  
			  $('.view-search-courses .view-header').append(sort);
			  
			  var sortText = Drupal.t("Sort");
			  
			  
			  $('.view-search-courses .view-header .form-item-sort-bef-combine label').attr('for','newsort');
			  $('.view-search-courses .view-header .form-item-sort-bef-combine select').attr('id','newsort');
			  $('.view-search-courses .view-header .form-item-sort-bef-combine select').wrap( "<div class='newsort-wrapper'></div>" );
			  $('.view-search-courses .view-header .form-item-sort-bef-combine').append('<span class="icon"></span>');
			  $('.view-search-courses .view-header .form-item-sort-bef-combine').append('<button type="submit" class="btn btn-primary">'+sortText+'</button>');
			  
		
			  
			  
			 /*  var sort = $(this);
			  
			  var newsort = sort.clone();
			  newsort.appendTo('.view-search-courses .view-header'); */
			  
			  $('.view-search-courses .view-header .form-item-sort-bef-combine button').click(function() {
				  var selectedValue = $('.view-search-courses .view-header .form-item-sort-bef-combine select').val();
				  console.log(selectedValue);
				  originalSelect.val(selectedValue);
				  $('.bef-exposed-form form').submit();
			  });	  
			  
			  $(sort).change(function() {
				  /* var selectedValue = $("select", this).val();
				  console.log(selectedValue);
				  originalSelect.val(selectedValue); */
				  
				  //$('.bef-exposed-form form').submit();
			  });
		 
		 });
	  

		 
		$("#edit-starting-level--2 input[type='radio'], #edit-starting-level input[type='radio']", context).click(function(){

            var radioValue = $(this).val();
			var newValue;
            if(radioValue){
				
				
				
				switch(radioValue) {
				  case 'All':
					radioValue = 0;
					break;
				}
				
				$('.level-description span').hide();
				$('.level-description span:eq('+radioValue+')').show();
				
				
				/* Remove Starting level selection */
				
				if(radioValue > 0){
					//console.log(radioValue);	
					$("#edit-starting-level-ext, #edit-starting-level-ext--2").val('All');
					
					
					
				}	
            }
			
			
			
			
			
			
        });
		
		
		/* Remove Current level selection */
		
		$("#edit-starting-level-ext, #edit-starting-level-ext--2", context).change(function(){

			var startingLevel = $(this).val();
			
			if(startingLevel != 'All'){
				//console.log(startingLevel);
				$("#edit-starting-level-all, #edit-starting-level-all--2").prop("checked", true);
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
	  
	  
	  var currentOffset;
	  
	 /*  
	  function scrollPos (currentOffset) {
		currentOffset = currentOffset;  
        return(currentOffset);		
	  } */
			
      // HAMBURGER MENU
      $("#toggle-navigation-menu-button").click(function () {
		  
		if (window.pageYOffset > 0) {
		  var currentOffset = window.pageYOffset;
	    }  
		  
        //$(".mobile-menu").css('backgroundColor', '#0073cf');
		$("#toggle-navigation-menu-button").toggleClass('active');
		$("#toggle-navigation-menu-button").attr('aria-expanded', false);
		$("#toggle-navigation-menu-button.active").attr('aria-expanded', true);
		
		//console.log(window.pageYOffset);
		
		if ($('#toggle-navigation-menu-button').hasClass('active')) {

		    $(window).scrollTop(0);
			
		} else {

			//console.log(currentOffset);
			//$(window).scrollTop(currentOffset);
		}
		
		
		$("#navbar-top").toggleClass('menu-active');
		
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
