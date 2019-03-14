<?php
/**
 * @file
 * Add "attach_library" function for Pattern Lab.
 */

use Symfony\Component\Yaml\Yaml;

$function = new Twig_SimpleFunction('attach_library', function ($string) {
  // Get Library Name from string.
  $libraryName = substr($string, strpos($string, "/") + 1);

  // Find Library in libraries.yml file.
  $yamlFile = glob('*.libraries.yml');
  $yamlOutput = Yaml::parseFile($yamlFile[0]);
  $libraryTags = [];

  // Add appropriate HTML tag to $libraryTags array.
  $add_library_tag = function ($library_tag) use (&$libraryTags) {
    $stringLoader = \PatternLab\Template::getStringLoader();
    $libraryTags[] = $stringLoader->render(["string" => $library_tag, "data" => []]);
  };

  // By default prefix paths with a /, but remove this for external JS
  // as it would break URLs.
  $build_path = function($options, $file) {
    return (isset($options['type']) && $options['type'] === 'external') ? $file : "/$file";
  };

  // For each item in .libraries.yml file.
  foreach($yamlOutput as $file => $value) {

    // If the library exists.
    if ($file === $libraryName) {
      $js_files = $yamlOutput[$file]['js'];

      if (!empty($js_files)) {
        // For each file, create an async script to insert to the Twig component.
        foreach($js_files as $file => $options) {
          $add_library_tag('<script data-name="reload" data-src="' . $build_path($options, $file) . '"></script>');
        }
      }

      $css_groups = $yamlOutput[$file]['css'];

      if (!empty($css_groups)) {
        // For each file, create an async script to insert to the Twig component.
        foreach($css_groups as $group => $files) {
          foreach($files as $file => $options) {
            $add_library_tag('<link rel="stylesheet" href="' . $build_path($options, $file) . '" media="all" />');
          }
        }
      }
    }
  }

  return implode($libraryTags);
}, array('is_safe' => array('html')));
