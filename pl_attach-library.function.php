<?php
/**
 * @file
 * Add "attach_library" function for Pattern Lab.
 */

use Symfony\Component\Yaml\Yaml;

$function = new Twig_SimpleFunction('attach_library', function ($string) {
  // Get Library Name from string.
  $library_name = substr($string, strpos($string, "/") + 1);

  // Find Library in libraries.yml file.
  $yaml_file = glob('*.libraries.yml');
  $yaml_output = Yaml::parseFile($yaml_file[0]);
  $library_tags = [];

  // Add appropriate HTML tag to $library_tags array.
  $add_library_tag = function ($library_tag) use (&$library_tags) {
    $string_loader = \PatternLab\Template::getStringLoader();
    $library_tags[] = $string_loader->render(["string" => $library_tag, "data" => []]);
  };

  // By default prefix paths with a /, but remove this for external JS
  // as it would break URLs.
  $build_path = function($options, $file) {
    return (isset($options['type']) && $options['type'] === 'external') ? $file : "/$file";
  };

  // For each item in .libraries.yml file.
  foreach($yaml_output as $file => $value) {

    // If the library exists.
    if ($file === $library_name) {
      $js_files = $yaml_output[$file]['js'];

      if (!empty($js_files)) {
        // For each file, create an async script to insert to the Twig component.
        foreach($js_files as $file => $options) {
          $add_library_tag('<script data-name="reload" data-src="' . $build_path($options, $file) . '"></script>');
        }
      }

      $css_groups = $yaml_output[$file]['css'];

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

  return implode($library_tags);
}, array('is_safe' => array('html')));
