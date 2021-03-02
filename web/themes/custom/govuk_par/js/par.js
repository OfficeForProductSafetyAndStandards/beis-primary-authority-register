/**
 * PAR Frontend API
 * Author: Transform
 * Author URL: http://www.transformuk.com/
 */

'use strict';

(function (window, $, undefined) {
  var parjs = {
      glocals : {
        printClassSelector :'.print',
        collapseClassSelector :'.collapse',
        expandClassSelector : '.expand',
        facetClassSelector : '.facet',
        fileSizeClassSelector : '.partnership-document span ul li a, .areas-of-advice span ul li a'
      },
      init : function() {
        $(document).ready(function(){
          //if( $('#tabs').length > 0 ) parjs.tabs.init();
          if( $(parjs.glocals.facetClassSelector).length > 0 ) parjs.facets.init(parjs.glocals.facetClassSelector);
          if( $(parjs.glocals.printClassSelector).length > 0 ) parjs.print.init(parjs.glocals.printClassSelector);
          if( $(parjs.glocals.collapseClassSelector).length > 0 ) parjs.collapseExpand.init(parjs.glocals.collapseClassSelector);
          if( $(parjs.glocals.fileSizeClassSelector).length > 0) parjs.stringFormatters.cleanFilesizeDisplay(parjs.glocals.fileSizeClassSelector);

          if ( $('.search-partnership-journey-selector-form').length > 0) parjs.validate.init('.search-partnership-journey-selector-form');

          //Enable GOVUK show/hide content
          //var showHideContent = new GOVUK.ShowHideContent()
          //showHideContent.init();

        return Drupal.attachBehaviors(document, Drupal.settings);
        });
      },
      validate : {
        init : function(el) {
          var validated = true;

          $('#find-by-business-name__textfield').on('click', function(e) {

            if($(this).parents('form').find('input[name="gds-radio-group"]:checked').length ) {
              return true
            } else {
              return false;
            }
          });
        }
      },
      tabs : {
        init : function() {
          if( $('#tabs').tabs() ) {
            PARJS.tabs.setTabFocus();
          }
        },
        setTabFocus : function() {
          Drupal.behaviors.setTabFocus = {
            attach: function(context, settings) {
              //check PAR settings object exists, and contains a setTabFocus property
              if (typeof settings.PAR.scripts != "undefined" && settings.PAR.scripts.setTabFocus != "undefined") {
                //set tab focus if setTabFocus property does not exceed number of available tabs
                if( settings.PAR.scripts.setTabFocus < $('#tabs > ul > li').length ) {
                  $('#tabs').tabs({ active: settings.PAR.scripts.setTabFocus });
                } else {
                  console.error('Error: setTabFocus value exceeds number of available tabs');
                }
              }
            }
          };
        }
      },
      facets: {
        init : function(sel) {
          $(sel).each(function() {
            parjs.facets.facetize(this);
          });
        },
        facetize : function(el) {
          var count = parjs.helpers.countItems($(el).parent().siblings('span').find('ul'), 'li');
          $(el)
            .text($(el).text() + ' (' + count + ')')
            .attr('data-items', count);

          // find a better way to remove collapse/expand behaviour from ul's with zero li's
          if( count == 0 )
            $(el)
              .addClass('no-pointer-events')
              .parent()
              .removeClass('collapse');
        }
      },
      stringFormatters : {
        cleanFilesizeDisplay : function(sel) {
          return $(sel).each(function() {
            $(this).text(PARJS.stringFormatters.removeFileSizeDecimals($(this).text()));
          });
        },
        removeFileSizeDecimals : function(str) {
          var delimiter = '';

          if(str.indexOf('KB') != -1) delimiter = 'KB';
          if(str.indexOf('MB') != -1) delimiter = 'MB';
          if(str.indexOf('GB') != -1) delimiter = 'GB';
          if(str.indexOf('TB') != -1) delimiter = 'TB';

          return str.substring(0, str.indexOf('.')) + delimiter + ')';
        }
      },
      collapseExpand : {
        init : function(sel) {
          $(sel).each(function(){
            parjs.collapseExpand.collapse(this);
          });

          this.clickHandler();
        },
        collapse : function(el) {
          return $(el)
            .siblings('span')
            .find('ul')
            .hide();
        },
        expand: function(el) {
          return $(el)
            .siblings('span')
            .find('ul')
            .show();
        },
        clickHandler: function() {
          $(parjs.glocals.collapseClassSelector).on('click', function(){
            if( $(this).hasClass('collapse') ) parjs.collapseExpand.expand(this);
            if( $(this).hasClass('expand') ) parjs.collapseExpand.collapse(this);
            $(this).toggleClass('collapse');
            $(this).toggleClass('expand');
          return false;
          });
        }
      },
      print : {
        init: function(sel) {
          if( $(sel).length ) parjs.print.initPrintViewHandler();
        },
        initPrintViewHandler: function() {
          $('.print').attr('disabled', false);
          $('.print').on('click', function() {
            window.print();
            return false;
          });
        }
      },
      reveals : {
      },
      helpers : {
        countItems : function(parent, childSelector) {
          return $(parent).find(childSelector).length;
        }
      }
    }
  parjs.init();
  window.PARJS = parjs;
}(window, jQuery));
