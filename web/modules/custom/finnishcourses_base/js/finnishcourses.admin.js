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
      // Set event forms title default value to url argument from node title field
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

    attach: function (context, settings) {

      Drupal.behaviors.finnishcourses_base.autofillUsername(context);
     
    }

  };     

})(jQuery, Drupal);
