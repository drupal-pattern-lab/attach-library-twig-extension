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

  $directoryPath = explode('/', dirname(__FILE__));

  $rootPathString = '';
  // If the themes directory is in the path (Drupal).
  if (in_array('themes', $directoryPath)) {
    $rootPathLocation = array_search('themes', $directoryPath);
    $themePathOffset = -1 * (count($directoryPath) - array_search('components', $directoryPath));
    $rootPath = array_slice($directoryPath, $rootPathLocation, $themePathOffset, true);
    $rootPathString = implode('/', $rootPath) . '/';
  }

  // For each item in .libraries.yml file.
  foreach($yamlOutput as $key => $value) {

    // If the library exists.
    if ($key === $libraryName) {
      $files = $yamlOutput[$key]['js'];
      // For each file, create an async script to insert to the Twig component.
      foreach($files as $key => $jsPath) {
        $scriptString = '<script async src="/' . $rootPathString . $key . '"></script>';
        $stringLoader = \PatternLab\Template::getStringLoader();
        $output = $stringLoader->render(array("string" => $scriptString, "data" => []));
        return $output;
    	}
    }
  }
}, array('is_safe' => array('html')));
