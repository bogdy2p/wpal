<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of character3D
 *
 * @author pbc
 */
class character3D {

//put your code here
  public $error_messages;
  public $errors = 0;
  public $characterName;
  public $guid;
  public $race;
  public $gender;
  public $b;
  public $b2;
  public $ha;
  public $hc;
  public $fa;
  public $sk;
  public $fh;
  public $rg;
  public $char_race = array(
    1 => 'human',
    2 => 'orc',
    3 => 'dwarf',
    4 => 'nightelf',
    5 => 'scourge',
    6 => 'tauren',
    7 => 'gnome',
    8 => 'troll',
    10 => 'bloodelf',
    11 => 'draenei');
  public $char_gender = array(
    0 => 'male',
    1 => 'female');
  public $character_data;
  public $items_array;
  public $item_entries;
  public $displayid_inventoryType;
  public $eq;

  /**
   * Constructor
   * @param type $characterName
   */
  public function __construct($characterName) {
    $this->characterName = $characterName;
    $this->initialize();
  }

  /**
   * Initialize
   */
  public function initialize() {
    $this->get_the_character($this->characterName);
    $this->assign_character_variables();
    $this->set_character_features();
    $this->get_character_items();
    $this->get_item_entry();
    $this->get_display_ids_and_inventory_types();
    $this->create_the_eq_string();
    $this->create_the_div_and_display_the_character();
  }

  /**
   * Assign/set character variables
   * @param type $character_data
   */
  function assign_character_variables() {
    if ($this->errors == 0) {
      $this->guid = $this->character_data['guid'];
      $this->race = $this->character_data['race'];
      $this->gender = $this->character_data['gender'];
      $this->b = $this->character_data['playerBytes'];
      $this->b2 = $this->character_data['playerBytes2'];
    }
  }

  /**
   * Set Character Features
   * Set Character Race/Gender
   * @param type $character_data
   */
  public function set_character_features() {
    if ($this->errors == 0) {
      $this->ha = ($this->b >> 16) % 256;
      $this->hc = ($this->b >> 24) % 256;
      $this->fa = ($this->b >> 8) % 256;
      $this->sk = $this->b % 256;
      $this->fh = $this->b2 % 256;
      $this->rg = $this->char_race[$this->race] . $this->char_gender[$this->gender];
    }
  }

  /*
   * Get the character data by the character name from characters table
   * @return guid,race,gender,playerBytes,playerBytes2
   */

  function get_the_character($charname) {
    if ($this->errors == 0) {
      db_set_active('characters');
      $query_select_character = "SELECT guid, race, gender, playerBytes, playerBytes2 FROM characters WHERE name = '$charname' LIMIT 1";
      $result_select_character = db_query($query_select_character);
      db_set_active('default');
      if ($result_select_character->rowCount() == 1) {
        $this->character_data = $result_select_character->fetchAssoc();
        return $this;
      }
      else {
        $this->error_messages[] = "Could not retrieve the character using the specified name. Error.";
        $this->errors++;
        return NULL;
      }
    }
  }

  /**
   * Get the character's currently equipped items 
   * @return sets the items_array property of the object.
   */
  function get_character_items() {
    if ($this->errors == 0) {
      $items = array();
      db_set_active('characters');
      $guid = $this->character_data['guid'];
      $query_select_guid = "SELECT item FROM character_inventory WHERE guid = '$guid' AND bag='0' AND slot <'18'";
      $result_select_guid = db_query($query_select_guid);
      db_set_active('default');
      $items = $result_select_guid->fetchAll();
      if ($items) {
        $this->items_array = $items;
        return;
      }
      $this->error_messages[] = "Could not retrieve the character's items. Error.";
      $this->errors++;
      return null;
    }
  }

  /**
   * Return the item_entry for the current item.
   */
  function get_item_entry() {
    if ($this->errors == 0) {
      $items_array = $this->items_array;
      db_set_active('characters');
      foreach ($items_array as $item_in_array) {
        $itemString = $item_in_array->item;
        $query_select_itemEntry = "SELECT itemEntry FROM item_instance WHERE guid = '$itemString'";
        $result_select_itemEntry = db_query($query_select_itemEntry);
        $this->item_entries[$itemString] = $result_select_itemEntry->fetch();
      }
      db_set_active('default');
    }
  }

  /**
   * Return the display id and inventory type for the current item.
   */
  function get_display_ids_and_inventory_types() {
    if ($this->errors == 0) {
      if ($this->item_entries != "") {
        $items_array = $this->item_entries;

        db_set_active('world');
        foreach ($this->item_entries as $itemEntry) {
          $query_select_item_drupal = "SELECT displayid, InventoryType FROM item_template WHERE entry = '$itemEntry->itemEntry' LIMIT 1";
          $result_select_item_drupal = db_query($query_select_item_drupal);
          $this->displayid_inventoryType[] = $result_select_item_drupal->fetch();
        }
        db_set_active('default');
      }
    }
  }

  /**
   * Create the string to be sent to the model viewer
   */
  function create_the_eq_string() {
    if ($this->errors == 0) {
      foreach ($this->displayid_inventoryType as $row_item) {
        $displayid = $row_item->displayid;
        $inventory_type = $row_item->InventoryType;
        if ($this->eq == "") {
          $this->eq = $inventory_type . ',' . $displayid;
        }
        else {
          $this->eq .= ',' . $inventory_type . ',' . $displayid;
        }
      }
    }
  }

  /**
   * Creates the actual div and displays the 3dModel.
   */
  function create_the_div_and_display_the_character() {
    if ($this->errors == 0) {
      echo '<div id="model_scene" align="center">';
      echo '<object id="wowhead" type="application/x-shockwave-flash" data="http://static.wowhead.com/modelviewer/ModelView.swf" height="400px" width="300px">';
      echo '<param name="quality" value="high">';
      echo '<param name="allowscriptaccess" value="always">';
      echo '<param name="menu" value="false">';
      echo '<param value="transparent" name="wmode">';
      echo printf('<param name="flashvars" value="model=%s&amp;modelType=16&amp;ha=%s&amp;hc=%s&amp;fa=%s&amp;sk=%s&amp;fh=%s&amp;fc=0&amp;contentPath=http://static.wowhead.com/modelviewer/&amp;blur=0&amp;equipList=%s">', $this->rg, $this->ha, $this->hc, $this->fa, $this->sk, $this->fh, $this->eq);
      echo '<param name="movie" value="http://static.wowhead.com/modelviewer/ModelView.swf">';
      echo '</object>';
      echo '</div>';
    }
    else {
      print_r("There were " . $this->errors . " errors. ");
      foreach ($this->error_messages as $error_message) {
        print_r("<br />");
        print_r($error_message);
      }
    }
  }

}
