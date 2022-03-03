(function ($) {
   $(document).ready(function () {

      elementor.channels.editor.on('GenLoc', () => {
         $button = '.elementor-control-refresh_locations button'
         $($button).text('Please Wait...').attr('disabled', 'disabled');
         // get location control
         $location_controls = '.elementor-control-premium_maps_location_map_pins';

         // grab locations' data controls
         $location_pins = $($location_controls + ' .elementor-repeater-row-controls');

         $locations_data = [];
         // loop through each location getting all data
         $($location_pins).each(function () {
            $location_data = new Map();
            $(this).find('input').each(function () {
               $location_data.set($(this).data('setting'), $(this).val());
            });

          $locations_data.push($location_data);
         });


         // Grab updated location data from locations plugin (in addons-integration.php)
         var new_location_data = LOCATIONS;
         // Compare locations--keep all new locations; add custom data
         // assumes lat/long always stay the same -- will catch updates to name not updates to location
         // if lat/long change, a new location needs to be added in the locations plugin
         new_location_data.forEach(new_location => {
            $locations_data.forEach(old_location => {
               if (old_location.get('map_longitude') == new_location['map_longitude'] && old_location.get('map_latitude') == new_location['map_latitude']) {
                  //copy new pin_title to old pin
                  if(old_location.get('pin_title') != new_location['pin_title']){
                     $($location_controls + ' .elementor-repeater-row-item-title').each(function () {
                        if($(this).text() == old_location.get('pin_title')) {
                           $(this).click();
                           $(this).parents('.elementor-repeater-fields').find('input[data-setting="pin_title"]').val(new_location['pin_title'])
                           $(this).parents('.elementor-repeater-fields').find('input[data-setting="pin_title"]').trigger('input')
                        }
                     })
                  }

                  // remove the matching lat/long pairs from the original and new lists
                  // so we can use the unmatched
                 $locations_data = $locations_data.filter(old_loc => !(old_loc.get('map_latitude') == old_location.get('map_latitude') && old_loc.get('map_longitude') == old_location.get('map_longitude')))
                 new_location_data = new_location_data.filter(new_loc => !(new_loc['map_latitude'] == old_location.get('map_latitude') && new_loc['map_longitude'] == old_location.get('map_longitude')))
               }
            });
         });

         // remove the unmatched old locations...must have been deleted
         $locations_data.forEach(old_location => {
            $($location_controls + ' .elementor-repeater-fields').each(function() {
               if($(this).find('input[data-setting="map_longitude"]').val() == old_location.get('map_longitude') &&
                  $(this).find('input[data-setting="map_latitude"]').val() == old_location.get('map_latitude'))
                  $(this).find('.elementor-repeater-tool-remove').click();
            })
         })

      // clunky...try to add a new location every 10 seconds to give it time to save each location
      // would be nice to know when it finishes saving
      // if 10 seconds is not enough, can increase or just refresh once per location added
      let i = 0;
      addLocation() //execute once immediately
      var addLocations = setInterval(addLocation, 10000);
      function addLocation() {
         if(new_location_data[i]) {
            $($location_controls + ' .elementor-repeater-add').click();

         // grab the newly added item
         $new = $location_controls + ' .elementor-repeater-fields:last-child';

         // get the item's inputs
         $map_longitude = ' input[data-setting="map_longitude"]';
         $map_latitude = ' input[data-setting="map_latitude"]';
         $pin_title = ' input[data-setting="pin_title"]';

         $($new + $map_longitude).val(new_location_data[i]['map_longitude']).trigger('input');
         $($new + $map_latitude).val(new_location_data[i]['map_latitude']).trigger('input');
         $($new + $pin_title).val(new_location_data[i]['pin_title']).trigger('input');
         i++;
         }
         else {
            clearInterval(addLocations);
            $($button).text('Refresh Locations').removeAttr('disabled');
         }
      }

         // Also clunky, loop doesn't work because can't add a new location until previous one saves so
         // requires hitting refresh location for each addition (only adds 1 at a time)
         // // add new locations to map
         // new_location_data.forEach((location) => {
         // // add new item
         // $($location_controls + ' .elementor-repeater-add').click();

         // // grab the newly added item
         // $new = $location_controls + ' .elementor-repeater-fields:last-child';

         // // get the item's inputs

         // $map_longitude = ' input[data-setting="map_longitude"]';
         // $map_latitude = ' input[data-setting="map_latitude"]';
         // $pin_title = ' input[data-setting="pin_title"]';

         //    $($new + $map_longitude).val(location['map_longitude']).trigger('input');
         //    $($new + $map_latitude).val(location['map_latitude']).trigger('input');
         //    $($new + $pin_title).val(location['pin_title']).trigger('input');

         // });
      });
   })
})(jQuery);
