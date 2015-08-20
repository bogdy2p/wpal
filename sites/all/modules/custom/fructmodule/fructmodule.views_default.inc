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

/**
 * @file
 * Views definitions for mymodule module.
 */

/**
 * Implements hook_views_data().
 */
function fructmodule_views_data() {

//  $data = array();
//  $data['fruct']['handlername'] = array(
//    'title' => t('Name of my handler'),
//    'help' => t('Description of my handler.'),
//    'field' => array(
//      'handler' => 'fructmodule_handler_handlername2',
//    ),
//  );
//  die('ASD DEAD');
  $data['fructe']['table']['name'] = 'testdb1.fructe';
  $data['fructe']['table']['group'] = t('Fructe');
  $data['fructe']['table']['join'] = array(
    'fruct' => array(
      'table' => 'testdb2.fruct',
      'left_field' => 'tip_fruct',
      'field' => 'tip'
    ),
  );

//  // Define the 'uid' column.
//  $data['ext']['uid'] = array(
//    'title' => t('External User Data'),
//    'help' => t('User data from external database.'),
//    'relationship' => array(
//      'base' => 'users',
//      'field' => 'name',
//      'handler' => 'views_handler_relationship',
//      'label' => t('External User Data'),
//    ),
//  );

//  // Define the 'email' column.
//  $data['ext']['email'] = array(
//    'title' => t('External e-mail'),
//    'help' => t("User's e-mail address in external database."),
//    'field' => array(
//      'handler' => 'views_handler_field_user_mail',
//      'click sortable' => TRUE,
//    ),
//    'sort' => array(
//      'handler' => 'views_handler_sort',
//    ),
//    'filter' => array(
//      'handler' => 'views_handler_filter_string',
//    ),
//  );
//
//  // Define the 'fname' column.
//  $data['ext']['fname'] = array(
//    'title' => t('External First Name'),
//    'help' => t("User's first name in external database."),
//    'field' => array(
//      'handler' => 'views_handler_field',
//      'click sortable' => TRUE,
//    ),
//    'sort' => array(
//      'handler' => 'views_handler_sort',
//    ),
//    'filter' => array(
//      'handler' => 'views_handler_filter_string',
//    ),
//    'argument' => array(
//      'handler' => 'views_handler_argument_string',
//    ),
//  );
//
//  // Define the 'lname' column.
//  $data['ext']['lname'] = array(
//    'title' => t('External Last Name'),
//    'help' => t("User's last name in external database."),
//    'field' => array(
//      'handler' => 'views_handler_field',
//      'click sortable' => TRUE,
//    ),
//    'sort' => array(
//      'handler' => 'views_handler_sort',
//    ),
//    'filter' => array(
//      'handler' => 'views_handler_filter_string',
//    ),
//    'argument' => array(
//      'handler' => 'views_handler_argument_string',
//    ),
//  );




  return $data;
}
