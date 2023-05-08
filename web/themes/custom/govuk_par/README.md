CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Recommended modules
 * Requirements
 * Installation
 * Configuration
 * Troubleshooting
 * Maintainers

INTRODUCTION
------------
 
 * GOV.UK is a Drupal 8 theme. The theme is not dependent on any core theme.
 * This theme utilises the GOV.UK FrontEnd node module and has Twig template
   files for the majority of GOV.UK styles, components and patterns.
   In no way will this meet 100% of you requirements, but it is a good start.
   You will still have to create/modify Twig files to get your required look & feel.
   See https://design-system.service.gov.uk

RECOMMENDED MODULES
-------------------

 * No extra module is required.
 
REQUIREMENTS
-------------------

 * No extra module is required.
 * Node.js >= V10.0 See https://nodejs.org
 * Gulp >= V4.0

INSTALLATION
------------

 * Install as usual, see
   https://www.drupal.org/docs/user_guide/en/extend-theme-install.html
 * cd to the themes directory eg. /themes/custom/govuk_theme
 * Issue the command 'npm build'. This will build all the required node
   modules into /themes/custom/govuk_theme/node_modules.
 * Install Gulp with 'npm install gulp'.
 * Issuing 'gulp' by its self (or 'gulp build') will compile the SASS files into the css folder.
 * Issuing 'gulp watch' will watch the SASS folder and compile any changes into the css folder.

CONFIGURATION
-------------

 * Configuration is available in Admin > Appearance.

TROUBLESHOOTING
---------------

 * Theme is compatible and tested on IE9+, Opera, Firefox & Chrome browsers, so it won't make any troubles.
 * Support for IE8 is no longer a GDS requirement and is not supported by this theme.


MAINTAINERS
-----------

 * https://www.drupal.org/u/webfaqtory
