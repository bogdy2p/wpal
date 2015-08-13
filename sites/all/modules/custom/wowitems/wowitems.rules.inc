<?php

/**
 * @file
 * wowitems Rules 
 */

/**
 * Implements hook_rules_condition_info().
 * @return type
 */
function wowitems_rules_condition_info() {
//  $conditions = array(
//    'wordlist_condition_words_in_text' => array(
//      'label' => t('Any of words is in text'),
//      'parameter' => array(
//        'long_text' => array(
//          'label' => t('Text to investigate'),
//          'type' => 'text',
//        ),
//        'list_of_words' => array(
//          'label' => t('List of words'),
//          'type' => 'list<text>',
//        ),
//      ),
//      'group' => t('Word list'),
//    ),
//  );
//  return $conditions;
}

/**
 * The condition callback for wordlist_conditions_words_in_text
 */
function wowitems_condition_words_in_text($long_text, $list_of_words) {
  //Loop trough all the words , return true if found.
//  foreach ($list_of_words as $word) {
//    if (stristr($long_text, $word)) {
//      return TRUE;
//    }
//  }
//  // If we've gotten this far , then no words were present. Return FALSE.
//  return FALSE;
}
