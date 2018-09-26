/**
 * PAR postcode lookup implementation
 * Author: Transform
 * Author URL: http://www.transformuk.com/
 */

'use strict';

(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.LotusBehavior = {
        attach: function (context, settings) {
            // Instantiate auto complete lookup.
            new IdealPostcodes.Autocomplete.Controller({
                // Test key: "iddqd" passes the check
                api_key: drupalSettings.par_forms.address_lookup.api_key,
                checkKey: true,

                // Invoked to fill in processed address data.
                onAddressRetrieved: function (address) {
                    document.querySelector("#edit-country-code").value = 'GB';

                    var nation = document.querySelector("#edit-nation");
                    for (var i = 0; i < nation.options.length; i++) {
                        if (nation.options[i].text === address.country) {
                            nation.selectedIndex = i;
                            break;
                        }
                    }
                },

                inputField: "#edit-lookup",
                outputFields: {
                    line_1: "#edit-address-line1",
                    line_2: "#edit-address-line2",
                    post_town: "#edit-town-city",
                    county: "#edit-county",
                    postcode: "#edit-postcode"
                }
            });
        }
    };
})(jQuery, Drupal, drupalSettings);
