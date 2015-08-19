<?php

/**
 * Implements hook_views_default_views().
 */
function fructmodule_views_default_views() {
  //Find all the files that matcha given mask in a given directory
  //Actually look for any files named *.view in the /views directory
  $files = file_scan_directory(drupal_get_path('module', 'fructmodule') . '/views', '/.view/');
  foreach ($files as $filepath => $file) {
    require $filepath;
    if (isset($view)) {
      $views[$view->name] = $view;
    }
  }
  //Check that there are views in the directory
  //This keeps the site from throwing errors if there are no views to return
  if ($views) {
    return $views;
  }
}

