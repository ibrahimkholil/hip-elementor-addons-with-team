(function ($) {

   "use strict";

   var settings = hipAddonsSettings.settings;

   window.PremiumAddonsNavigation = function () {

      var self = this,
         $tabs = $(".pa-settings-tab"),
         $elementsTabs = $(".pa-elements-tab");

      self.init = function () {

         if (!$tabs.length) {
            return;
         }

         self.initNavTabs($tabs);

         self.initElementsTabs($elementsTabs);

         self.handleElementsActions();

         self.handleFormSubmit();

      };

      // Handle settings form submission
      self.handleFormSubmit = function () {

         var ajaxData = {
            'hip-integrations': 'hip_additional_settings',
         };

         $('form.hip-settings-form').on(
            'submit',
            function (e) {

               var $form = $(this),
                  id = $form.attr("id");

               e.preventDefault();

               var action = ajaxData[id];

               if (!action) {
                  return;
               }

               if ('hip_additional_settings' === action) {
                  $form = $('form#hip-integrations');
               }

               $.ajax(
                  {
                     url: settings.ajaxurl,
                     type: 'POST',
                     data: {
                        action: action,
                        security: settings.nonce,
                        fields: $form.serialize(),
                     },
                     success: function (response) {

                        console.log(response);
                        $('p.result').html('Settings Saved.').delay(3000).fadeOut();
                     },
                     error: function (err) {

                        console.log(err);
                        $('p.result').html('Uh oh. An error occurrred.').delay(3000).fadeOut();

                     }
                  }
               );
            }
         );

      };

      // Handle global enable/disable buttons
      self.handleElementsActions = function () {

         $(".pa-elements-filter select").on(
            'change',
            function () {
               var filter = $(this).val(),
                  $activeTab = $(".pa-switchers-container").not(".hidden");

               $activeTab.find(".pa-switcher").removeClass("hidden");

               if ('free' === filter) {
                  $activeTab.find(".pro-element").addClass("hidden");
               } else if ('pro' === filter) {
                  $activeTab.find(".pa-switcher").not(".pro-element").addClass("hidden");
               }
            }
         );

         // Enable/Disable all widgets
         $(".pa-btn-group").on(
            "click",
            '.pa-btn',
            function () {

               var $btn = $(this),
                  isChecked = $btn.hasClass("pa-btn-enable");

               if (!$btn.hasClass("active")) {
                  $(".pa-btn-group .pa-btn").removeClass("active");
                  $btn.addClass("active");

                  $.ajax(
                     {
                        url: settings.ajaxurl,
                        type: 'POST',
                        data: {
                           action: 'pa_save_global_btn',
                           security: settings.nonce,
                           isGlobalOn: isChecked
                        }
                     }
                  );

               }

               $("#pa-modules .pa-switcher input").prop("checked", isChecked);

            }
         );

         $("#pa-modules .pa-switcher input").on(
            'change',
            function () {
               var $this = $(this),
                  id = $this.attr('id'),
                  isChecked = $this.prop('checked');

               $("input[name='" + id + "']").prop('checked', isChecked);
            }
         )

      };

      // Handle Tabs Elements
      self.initElementsTabs = function ($elem) {

         var $links = $elem.find('a'),
            $sections = $(".pa-switchers-container");

         $sections.eq(0).removeClass("hidden");
         $links.eq(0).addClass("active");

         $links.on(
            'click',
            function (e) {

               e.preventDefault();

               var $link = $(this),
                  href = $link.attr('href');

               // Set this tab to active
               $links.removeClass("active");
               $link.addClass("active");

               // Navigate to tab section
               $sections.addClass("hidden");
               $("#" + href).removeClass("hidden");

            }
         );
      };

      // Handle settings tabs
      self.initNavTabs = function ($elem) {

         var $links = $elem.find('a'),
            $lastSection = null;

         $(window).on(
            'hashchange',
            function () {

               var hash = window.location.hash.match(new RegExp('tab=([^&]*)')),
                  slug = hash ? hash[1] : $links.first().attr('href').replace('#tab=', ''),
                  $link = $('#pa-tab-link-' + slug);

               if (!$link.length) {
                  return

                  $link.closest('.pa-settings-tab').addClass('pa-tab-active').siblings().removeClass('pa-tab-active');
               }
               $links.removeClass('pa-section-active');
               $link.addClass('pa-section-active');

               // Hide the last active section
               if ($lastSection) {
                  $lastSection.hide();
               }

               var $section = $('#pa-section-' + slug);
               $section.css(
                  {
                     display: 'block'
                  }
               );

               $lastSection = $section;

            }
         ).trigger('hashchange');

      };

   };

   var instance = new PremiumAddonsNavigation();

   instance.init();

})(jQuery);
