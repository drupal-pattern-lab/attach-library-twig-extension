# attach-library-twig-extension
Twig function that lets Pattern Lab use a simple version of Drupal's [attach_library](https://www.drupal.org/docs/8/creating-custom-modules/adding-stylesheets-css-and-javascript-js-to-a-drupal-8-module#twig) function to add javascript files per component.

## Requirements

To use in Pattern Lab, simply place in the `_twig-components/functions` directory. Since this function is specifically for Pattern Lab (Drupal has its own), it is namespaced with `pl_` so if you're using the [Unified Twig Extensions](https://github.com/drupal-pattern-lab/unified-twig-extensions/) module, it will ignore it when syncing functions between Drupal and Pattern Lab.

## Usage

Simply add `{{ attach_library(MODULE/FILENAME) }}` to any component Twig file using the same syntax as Drupal. Pattern Lab will then locate the theme's `*.libraries.yml` file and load the JavaScript file from the path in that library whenever that component is loaded. Since this function uses the same syntax as Drupal, nothing else needs to be done when loading the component in Drupal. It will work as it always did!

## Current Limitations

This function is limited to loading the js file referenced in the library. It will not load other assets and will not load any dependencies either. If you need to load a dependency or other file in Pattern Lab, it can be done as usual in `/meta/foot.twig`. See the commented out parts in [Emulsify's file](https://github.com/fourkitchens/emulsify/blob/develop/components/_meta/_01-foot.twig) for examples of how to do this.
