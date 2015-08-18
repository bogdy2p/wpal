<?php

/**
 * adk;asjdpsadjas;kd
 */
//function wowcharacters_views_data() {
//
//
//  //Define the base group of this table.
//  //Fields that don't have a group defined will go into this field by default.
//  $data['characters']['table']['group'] = t('Characters');
//  //Advertise this table as a possible base table.
//  $data['characters']['table']['base'] = array(
//    'field' => 'guid',
//    'title' => t('Character'),
//    'weight' => -10,
//    'acces query tag' => 'node_access',
//    'defaults' => array(
//      'field' => 'name',
//    )
//  );
//  //For other base tables , explain how we join
//  $data['characters']['table']['join'] = array(
//    //this explains how the 'characters' table links towards the account table.
//    'account' => array(
//      'handler' => 'views_join',
//      'left_table' => 'account',
//      'left_field' => 'id',
//      'field' => 'account',
//    ),
//  );
//  //account table -- fields
//  // username
////  $data['account']['title'] = array(
////    'title' => t('Username'), //The item it appears as on the UI,
////    'help' => t('Account username '), //The help that appears on the UI
////    'field' => array(
////      'field' => 'username',
////      'group' => t('Characters'),
////      'handler' => 'views_handler_field_node',
////      'click sortable' => TRUE,
//////      'link_to_node default' => FALSE,
////    ),
////    'sort' => array(
////      'handler' => 'views_handler_sort',
////    ),
////    //Information for accepting a username as a filter
////    'filter' => array(
////      'handler' => 'views_handler_filter_string',
////    ),
////    'argument' => array(
////      'handler' => 'views_handler_argument_string',
////    ),
////  );
//}
