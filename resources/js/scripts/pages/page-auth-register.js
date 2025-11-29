/*=========================================================================================
  File Name: form-validation.js
  Description: jquery bootstrap validation js
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: PIXINVENT
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(function () {
  'use strict';

  // Add custom regex validation method
  $.validator.addMethod("regex", function(value, element, regexp) {
    var re = new RegExp(regexp);
    return this.optional(element) || re.test(value);
  }, "Please check your input.");

  var pageResetForm = $('.auth-register-form');

  // jQuery Validation
  // --------------------------------------------------------------------
  if (pageResetForm.length) {
    pageResetForm.validate({
      /*
      * ? To enable validation onkeyup
      onkeyup: function (element) {
        $(element).valid();
      },*/
      /*
      * ? To enable validation on focusout
      onfocusout: function (element) {
        $(element).valid();
      }, */
      rules: {
        'register-username': {
          required: true,
          minlength: 2,
          regex: /^[a-zA-Z0-9\s]+$/
        },
        'name': {
          required: true,
          minlength: 2,
          regex: /^[a-zA-Z0-9\s]+$/
        },
        'organization_name': {
          required: true,
          minlength: 2
        },
        'register-email': {
          required: true,
          email: true
        },
        'email': {
          required: true,
          email: true
        },
        'register-password': {
          required: true
        },
        'password': {
          required: true,
          minlength: 8
        }
      },
      messages: {
        'register-username': {
          required: 'Please enter your name',
          minlength: 'Name must be at least 2 characters',
          regex: 'Name may only contain letters, numbers, and spaces. Special characters are not allowed.'
        },
        'name': {
          required: 'Please enter your name',
          minlength: 'Name must be at least 2 characters',
          regex: 'Name may only contain letters, numbers, and spaces. Special characters are not allowed.'
        },
        'organization_name': {
          required: 'Please enter organization name',
          minlength: 'Organization name must be at least 2 characters'
        },
        'register-email': {
          required: 'Please enter your email',
          email: 'Please enter a valid email address'
        },
        'email': {
          required: 'Please enter your email',
          email: 'Please enter a valid email address'
        },
        'register-password': {
          required: 'Please enter your password'
        },
        'password': {
          required: 'Please enter your password',
          minlength: 'Password must be at least 8 characters'
        }
      }
    });
  }
});
