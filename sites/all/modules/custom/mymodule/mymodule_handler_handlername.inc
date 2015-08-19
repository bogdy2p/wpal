<?php

/**
 * @file
 * Definition of mymodule_handler_handlername.
 */

/**
 * Description of what my handler does.
 */
class mymodule_handler_handlername extends views_handler_field {

  /**
   * Add some required fields needed on render().
   */
  function construct() {
    parent::construct();
    
    $this->additional_fields['field_something'] = array(
      'table' => 'field_data_field_something',
      'field' => 'field_something_value',
    );
    die('dead in construct');
  }

  /**
   * Loads additional fields.
   */
  function query() {
    $this->ensure_my_table();
    $this->add_additional_fields();
  }

  /**
   * Default options form.
   */
  function option_definition() {
    $options = parent::option_definition();

    $options['option_a'] = array('default' => '');
    $options['option_b'] = array('default' => '');

    return $options;
  }

  /**
   * Creates the form item for the options added.
   */
  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);

    $form['option_a'] = array(
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#default_value' => $this->options['option_a'],
      '#description' => t('Some description.'),
      '#weight' => -10,
    );

    $form['option_b'] = array(
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#default_value' => $this->options['option_b'],
      '#description' => t('Some description.'),
      '#weight' => -9,
    );
  }

  /**
   * Renders the field handler.
   */
  function render($values) {
    return format_string('<div class="@option_a" data-tag="@field_something" data-follow_name="@option_b"></div>', array(
      '@field_something' => $values->field_data_field_something_field_something_value,
      '@option_a' => $this->options['option_a'],
      '@option_b' => $this->options['option_b'],
    ));
  }

}
