CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers

INTRODUCTION
------------

* This is a very simple module to make twig values available as an input filter.

USAGE
------------

* For a full description of the module, visit the project page: https://www.drupal.org/project/filter_twig

* To submit bug reports and feature suggestions, or to track changes: https://www.drupal.org/project/issues/filter_twig

REQUIREMENTS
------------

* This module does not require some modules outside of Drupal core.

INSTALLATION
------------

* Install the Filter Twig module as you would normally install a contributed Drupal module. Visit https://www.drupal.org/node/1897420 for further information.

CONFIGURATION
------------
* Navigate to Administration > Extend and enable the module.

* Navigate to `Administration` > `Configuration` > `Content Authoring` > `Text formats and editors` > `Format to edit` > `Configure`.

* In the `Enabled filters` section, select the `Replaces Twig values` filter and save the text format.

* Or visit the text format administration page at `/admin/config/content/formats` and edit a text format.
  Check the `Replaces Twig values` filter and save the text format.

* When editing a form where this text format is used in a field,
  you can type twig that will be replaced when the filed is rendered.

MAINTAINERS
-----------

* Current maintainer: Andriy Malyeyev (slivorezka) - https://www.drupal.org/u/slivorezka
