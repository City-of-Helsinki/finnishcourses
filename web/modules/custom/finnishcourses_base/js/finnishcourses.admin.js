/**
 * @file
 * finnishcourses_base module's admin related javascript
 */
(function ($, Drupal) {

  "use strict";


  /**
   * Initialise the JS.
   */
  Drupal.behaviors.finnishcourses_base = {

    context:{},

    autofillUsername: function(context) {
      // Autofill username field based email field so only email must be filled and name field can be hidden
      if ($(context).find('#user-register-form #edit-mail').length && $(context).find('#user-register-form #edit-name').length) {
        $(context).find('#edit-mail').once('emailFocusOut').focusout(function(event) {
          var $email = $(context).find('#edit-mail').val();
          $(context).find('#edit-name').val($email);
        });
      }

      if ($(context).find('#user-form #edit-mail').length && $(context).find('#user-form #edit-name').length) {
        $(context).find('#edit-mail').once('emailFocusOut').focusout(function(event) {
          var $email = $(context).find('#edit-mail').val();
          $(context).find('#edit-name').val($email);
        });
      }
    },

    courseLocationDefaults: function(context) {

      // Set course location default values after ajax callback
      // 
      if ($(context).find('.js-form-item-field-course-street-address-select').length) {
        var $addressSelect = $(context).find('.js-form-item-field-course-street-address-select select');
        if ($addressSelect.find('option:eq(1)').length > 0) {
          $addressSelect.removeAttr('selected').find('option:eq(1)').attr('selected', 'selected');
        }
      }

      if ($(context).find('.js-form-item-field-course-town-select').length) {
        var $townSelect = $(context).find('.js-form-item-field-course-town-select select');
        if ($townSelect.find('option:eq(1)').length > 0) {
          $townSelect.removeAttr('selected').find('option:eq(1)').attr('selected', 'selected');
        }
      }
    },

    fillRegistrationDefaultTimes: function(context) {

      // Set registration date's time field value to 00:00

      if ($(context).find('.field--name-field-registration-start-date').length) {
        var $regStartDateInput = $(context).find('.field--name-field-registration-start-date .js-form-item-field-registration-start-date-0-value-date input');
        var $regStartTimeInput = $(context).find('.field--name-field-registration-start-date .js-form-item-field-registration-start-date-0-value-time input');

        $regStartDateInput.on('change', function(event) {
          if ($regStartDateInput.val() && !$regStartTimeInput.val()) {
            $regStartTimeInput.val("00:00");
          }
        });
      }

      if ($(context).find('.field--name-field-registration-end-date').length) {
        var $regEndDateInput = $(context).find('.field--name-field-registration-end-date .js-form-item-field-registration-end-date-0-value-date input');
        var $regEndTimeInput = $(context).find('.field--name-field-registration-end-date .js-form-item-field-registration-end-date-0-value-time input');

        $regEndDateInput.on('change', function(event) {
          if ($regEndDateInput.val() && !$regEndTimeInput.val()) {
            $regEndTimeInput.val("23:59");
          }
        });
      }
    },

    attach: function (context, settings) {

      Drupal.behaviors.finnishcourses_base.autofillUsername(context);
      Drupal.behaviors.finnishcourses_base.fillRegistrationDefaultTimes(context);

      $(document).ajaxComplete(function(e, xhr, settings) {

        if (typeof(settings.extraData) != 'undefined' && typeof(settings.extraData._triggering_element_name) != 'undefined') {
          var $trigger = settings.extraData._triggering_element_name;
          if ($trigger == 'field_course_organization') {
            Drupal.behaviors.finnishcourses_base.courseLocationDefaults(context);
          }
        }
        
        //$('#' + settings.extraData.parentElement + ' td.subtotal').fadeTo('150', '1');
      });
     
    }

  };     

})(jQuery, Drupal);
