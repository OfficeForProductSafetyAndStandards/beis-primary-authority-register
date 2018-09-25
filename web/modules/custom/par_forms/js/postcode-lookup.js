/**
 * PAR postcode lookup implementation
 * Author: Transform
 * Author URL: http://www.transformuk.com/
 */

'use strict';

new IdealPostcodes.Autocomplete.Controller({
    // Test key: "iddqd" passes the check
    api_key: 'ak_jmgfkyttxpJ8rUkMIlaeoARpyV9dX',

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
