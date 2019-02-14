var AutoCompleter = function(options) {
  var vars,
    placeSearch,
    autocompleteByStreet,
    autocompleteByCity,
    oldCity,
    oldPostal,
    reFocusStreetField = false,
    missingHouseNumberPlaceholder,
    hideMissingHouseNumberHint = false,
    myForm;

  this.init = function() {
    this.setVariables();
    this.initAutoCompleter();
    this.prepareMissingHouseNumberPlaceholder();
  };

  this.setVariables = function() {
    vars = options;

    myForm = {
      street_number: {formFieldId: vars.streetFieldId, addressType: 'long_name'},
      route: {formFieldId: vars.streetFieldId, addressType: 'long_name'},
      postal_code: {formFieldId: vars.postalFieldId, addressType: 'short_name'},
      locality: {formFieldId: vars.cityFieldId, addressType: 'long_name'}
    };
  };

  /**
   * Handle events when chosing an autocomplete suggestion item.
   */
  this.selectFirstOnEnter = function(input) {      // store the original event binding function
    var _addEventListener = (input.addEventListener) ? input.addEventListener : input.attachEvent;
    function addEventListenerWrapper(type, listener) { // Simulate a 'down arrow' keypress on hitting 'return' when no pac suggestion is selected, and then trigger the original listener.
      if (type == "keydown") {
        var orig_listener = listener;
        listener = function (event) {
          var suggestion_selected = $(".pac-item-selected").length > 0;
          // 13 = enter, 9 = tab
          if ((event.which == 13 || event.which == 9) && !suggestion_selected) {
            var simulated_downarrow = $.Event("keydown", {keyCode:40, which:40});
            orig_listener.apply(input, [simulated_downarrow]);
          }

          // Set reFocus flag depending on pressed key.
          reFocusStreetField = (event.which != 9);

          orig_listener.apply(input, [event]);

          /**
           * Google autocomplete fills out city after street by default.
           * To remove city from the input field, only enter first part of the string splitted by comma,
           * so we get only the street name.
           */
          var originalStreetValue = $(input).val();
          var parts = originalStreetValue.split(',');
          if (parts.length > 0) {
            $(input).val(parts[0]);
          }
        };
      }
      _addEventListener.apply(input, [type, listener]); // add the modified listener
    }
    if (input.addEventListener) { input.addEventListener = addEventListenerWrapper; } else if (input.attachEvent) { input.attachEvent = addEventListenerWrapper; }
  };

  this.isNumberInString = function(testString) {
    return (testString.match(/\d+/g) != null);
  };

  this.prepareMissingHouseNumberPlaceholder = function() {
    var _self = this;
    var streetParent = $('#' + vars.streetFieldId).parent('.widget');
    missingHouseNumberPlaceholder = $('.missing-house-number.placeholder').clone().appendTo(streetParent).removeClass('placeholder');

    missingHouseNumberPlaceholder.on('click', function() {
      hideMissingHouseNumberHint = true;
      _self.hideMissingHouseNumberPlaceholder();
    });
  };

  this.showMissingHouseNumberPlaceholder = function() {
    missingHouseNumberPlaceholder.slideDown(150);
  };

  this.hideMissingHouseNumberPlaceholder = function() {
    missingHouseNumberPlaceholder.slideUp(150);
  };

  this.initAutoCompleter = function() {
    var _self = this;
    // Create autocomplete instances for shipping address.
    autocompleteByStreet = new google.maps.places.Autocomplete(document.getElementById(vars.streetFieldId), {types: ['address'], componentRestrictions: {country: 'de'}});
    autocompleteByCity = new google.maps.places.Autocomplete(document.getElementById(vars.cityFieldId), {types: ['(cities)'], componentRestrictions: {country: 'de'}});

    // Do stuff if a suggestion from google has been selected
    autocompleteByStreet.addListener('place_changed', this.fillInAddressFromStreet);

    var streetInput = document.getElementById(vars.streetFieldId);

    // Prevent form submission when confirming a google suggesction via enter key.
    google.maps.event.addDomListener(streetInput, 'keydown', function(event) {
      if (event.keyCode === 13) {
        event.preventDefault();
      }
    });

    // Do stuff if address inputs lose focus.
    $(streetInput).on('blur', function() {
      if (!_self.isNumberInString($(this).val()) && !hideMissingHouseNumberHint) {
        _self.showMissingHouseNumberPlaceholder();
      } else {
        _self.hideMissingHouseNumberPlaceholder();
      }
    });

    // Automatically select first entry of google suggestion.
    this.selectFirstOnEnter(document.getElementById(vars.streetFieldId));

    this.getBoundsByPostal();
  };

  this.getBoundsByPostal = function() {
    var postal = document.getElementById(vars.postalFieldId).value;

    if (postal.length > 0) {
      $.ajax({
        type: "GET",
        url: "https://maps.googleapis.com/maps/api/geocode/json?address=" + postal + ",Germany&key=AIzaSyCbLr8Kx_ifDi-5yp_gGyIuX96pgLRR69k",
        dataType: "json"
      }).success(function(data) {
        var postalBounds = {
          'north': data['results'][0]['geometry']['bounds']['northeast']['lat'],
          'east': data['results'][0]['geometry']['bounds']['northeast']['lng'],
          'south': data['results'][0]['geometry']['bounds']['southwest']['lat'],
          'west': data['results'][0]['geometry']['bounds']['southwest']['lng']
        };

        autocompleteByStreet.setBounds(postalBounds);
        autocompleteByCity.setBounds(postalBounds);
      });
    }

  };

  this.fillInAddressFromStreet = function() {
    // Autofocus after selection
    if (reFocusStreetField) {
      $(vars.streetFieldId).focus();
    }

    var place = autocompleteByStreet.getPlace();
    var streetPlusNumber = [];

    if (!place.hasOwnProperty('address_components')) {
      return false;
    }

    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (myForm[addressType]) {
        var val = place.address_components[i][myForm[addressType]['addressType']];
        document.getElementById(myForm[addressType]['formFieldId']).value = val;

        // Combine street and street number in address field.
        if (addressType === 'street_number') {
          streetPlusNumber[1] = val;
        } else if (addressType === 'route') {
          streetPlusNumber[0] = val;
        }
        document.getElementById(vars.streetFieldId).value = streetPlusNumber.join(" ");
      }
    }
  };

  this.init();
};