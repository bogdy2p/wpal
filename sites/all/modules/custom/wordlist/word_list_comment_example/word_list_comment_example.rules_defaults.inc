<?php
/**
 * @file
 * word_list_comment_example.rules_defaults.inc
 */

/**
 * Implements hook_default_rules_configuration().
 */
function word_list_comment_example_default_rules_configuration() {
  $items = array();
  $items['rules_check_if_new_comments_contains_words_from_word_list'] = entity_import('rules_config', '{ "rules_check_if_new_comments_contains_words_from_word_list" : {
      "LABEL" : "Check if new comments contains words from Word list",
      "PLUGIN" : "reaction rule",
      "OWNER" : "rules",
      "TAGS" : [ "Word list" ],
      "REQUIRES" : [ "rules", "wordlist", "comment" ],
      "ON" : { "comment_insert" : [] },
      "IF" : [
        { "entity_has_field" : { "entity" : [ "comment" ], "field" : "comment_body" } },
        { "wordlist_condition_words_in_text" : {
            "long_text" : [ "comment:comment-body:value" ],
            "list_of_words" : [ "site:wordlist-words" ]
          }
        }
      ],
      "DO" : [
        { "drupal_message" : { "message" : "Your comment contains blacklisted words and has been hidden awaiting manual approval. Thanks for patience." } },
        { "data_set" : { "data" : [ "comment:status" ], "value" : "0" } }
      ]
    }
  }');
  return $items;
}
