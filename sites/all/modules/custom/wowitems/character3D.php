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

  public function __construct($characterName) {
    $this->characterName = $characterName;
    $this->initialize($this->characterName);
  }

  public function initialize($characterName) {
    $this->get_the_character($characterName);
    $this->assign_character_variables($this->character_data);
    $this->set_character_features($this->character_data);
  }

  /**
   * Assign/set character variables
   * @param type $character_data
   */
  function assign_character_variables($character_data) {
    $this->guid = $character_data['guid'];
    $this->race = $character_data['race'];
    $this->gender = $character_data['gender'];
    $this->b = $character_data['playerBytes'];
    $this->b2 = $character_data['playerBytes2'];
  }

  /**
   * Set Character Features
   * Set Character Race/Gender
   * @param type $character_data
   */
  public function set_character_features($character_data) {

    $this->ha = ($this->b >> 16) % 256;
    $this->hc = ($this->b >> 24) % 256;
    $this->fa = ($this->b >> 8) % 256;
    $this->sk = $this->b % 256;
    $this->fh = $this->b2 % 256;
    $this->rg = $this->char_race[$this->race] . $this->char_gender[$this->gender];
  }

  /*
   * Get the character data by the character name from characters table
   * @return guid,race,gender,playerBytes,playerBytes2
   */

  function get_the_character($charname) {
    db_set_active('characters');
    $query_select_character = "SELECT guid, race, gender, playerBytes, playerBytes2 FROM characters WHERE name = '$charname' LIMIT 1";
    $result_select_character = db_query($query_select_character);
    db_set_active('default');
    if ($result_select_character->rowCount() == 1) {
      $this->character_data = $result_select_character->fetchAssoc();
      return $this;
    }
    else {
      return NULL;
    }
  }

  /**
   * Get the character's currently equipped items 
   * @return array of Item Objects
   */
  function get_character_items() {
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
    return null;
  }

  /**
   * 
   */
  function get_item_entry() {
    $items_array = $this->items_array;
    db_set_active('characters');
    foreach ($items_array as $item_in_array) {
      $itemString = $item_in_array->item;
//      print_r($itemString);
      $query_select_itemEntry = "SELECT itemEntry FROM item_instance WHERE guid = '$itemString'";
      $result_select_itemEntry = db_query($query_select_itemEntry);
      $this->item_entries[$itemString] = $result_select_itemEntry->fetch();
    }
    db_set_active('default');
  }

  /**
   * 
   */
  function get_display_ids_and_inventory_types() {
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
/**
 * 
 */
  function test() {
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
//    print_r($this->eq);
  }
  /**
   * 
   */
  function complete(){
//     if ($errors == 0) {
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
//}

}
