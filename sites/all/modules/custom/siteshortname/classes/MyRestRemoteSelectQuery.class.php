<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MyRestRemoteSelectQuery
 *
 * @author pbc
 */

/**
 * Select query for our remote data.
 *
 * @todo Make vars protected once no longer developing.
 */
class MyRestRemoteSelectQuery extends RemoteEntityQuery {

  /**
   * Determines whether the query is RetrieveMultiple or Retrieve.
   *
   * The query is Multiple by default, until an ID condition causes it to be
   * single.
   */
  public $retrieve_multiple = TRUE;

  /**
   * An array of conditions on the query. These are grouped by the table they
   * are on.
   */
  public $conditions = array();

  /**
   * The from date filter for event searches
   */
  public $from_date = NULL;

  /**
   * The to date filter for event searches
   */
  public $to_date = NULL;

  /**
   * The user id.
   */
  public $user_id = NULL;

  /**
   * Constructor to generically set up the user id condition if
   * there is a current user.
   *
   * @param $connection
   */
  function __construct($connection) {
    parent::__construct($connection);
    if (user_is_logged_in()) {
      global $user;
      $this->useridCondition($user->name);
    }
  }

  /**
   * Add a condition to the query.
   *
   * Originally based on the entityCondition() method in EntityFieldQuery, but
   * largely from USDARemoteSelectQuery (Programming Drupal 7 Entities) and
   * MSDynamicsSoapSelectQuery.
   *
   * @param $name
   *  The name of the entity property.
   */
  function entityCondition($name, $value, $operator = NULL) {

    // We only support the entity ID for now.
    if ($name == 'entity_id') {

      // Get the remote field name of the entity ID.
      $field = $this->entity_info['remote entity keys']['remote id'];

      // Set the remote ID field to the passed value.
      $this->conditions[$this->remote_base][] = array(
        'field' => $field,
        'value' => $value,
        'operator' => $operator,
      );

      // Record that we'll only be retrieving a single item.
      if (is_null($operator) || ($operator == '=')) {
        $this->retrieve_multiple = FALSE;
      }
    }
    else {

      // Report an invalid entity condition.
      $this->throwException(
          'OURRESTREMOTESELECTQUERY_INVALID_ENTITY_CONDITION', 'The query object can only accept the \'entity_id\' condition.'
      );
    }
  }

  /**
   * Add a condition to the query, using local property keys.
   *
   * Based on MSDynamicsSoapSelectQuery::propertyCondition().
   *
   * @param $property_name
   *  A local property. Ie, a key in the $entity_info 'property map' array.
   */
  function propertyCondition($property_name, $value, $operator = NULL) {

    // Make sure the entity base has been set up.
    if (!isset($this->entity_info)) {
      $this->throwException(
          'OURRESTREMOTESELECTQUERY_ENTITY_BASE_NOT_SET', 'The query object was not set with an entity type.'
      );
    }

    // Make sure that the provided property is valid.
    if (!isset($this->entity_info['property map'][$property_name])) {
      $this->throwException(
          'OURRESTREMOTESELECTQUERY_INVALID_PROPERY', 'The query object cannot set a non-existent property.'
      );
    }

    // Adding a field condition (probably) automatically makes this a multiple.
    // TODO: figure this out for sure!
    $this->retrieve_multiple = TRUE;

    // Use the property map to determine the remote field name.
    $remote_field_name = $this->entity_info['property map'][$property_name];

    // Set the condition for use during execution.
    $this->conditions[$this->remote_base][] = array(
      'field' => $remote_field_name,
      'value' => $value,
      'operator' => $operator,
    );
  }

  /**
   * Add a user id condition to the query.
   *
   * @param $user_id
   *   The user to search for appointments.
   */
  function useridCondition($user_id) {
    $this->user_id = $user_id;
  }

  /**
   * Run the query and return a result.
   *
   * @return
   *  Remote entity objects as retrieved from the remote connection.
   */
  function execute() {

    // If there are any validation errors, don't perform a search.
    if (form_set_error()) {
      return array();
    }

    $querystring = array();

    $path = variable_get($this->base_entity_type . '_resource_name', '');

    // Iterate through all of the conditions and add them to the query.
    if (isset($this->conditions[$this->remote_base])) {
      foreach ($this->conditions[$this->remote_base] as $condition) {
        switch ($condition['field']) {
          case 'event_id':
            $querystring['eventId'] = $condition['value'];
            break;
          case 'login_id':
            $querystring['userId'] = $condition['value'];
            break;
        }
      }
    }

    // "From date" parameter.
    if (isset($this->from_date)) {
      $querystring['startDate'] = $this->from_date;
    }

    // "To date" parameter.
    if (isset($this->to_date)) {
      $querystring['endDate'] = $this->to_date;
    }

    // Add user id based filter if present.
    if (isset($this->user_id)) {
      $querystring['userId'] = $this->user_id;
    }

    // Assemble all of the query parameters.
    if (count($querystring)) {
      $path .= '?' . drupal_http_build_query($querystring);
    }

    // Make the request.
    try {
      $response = $this->connection->makeRequest($path, 'GET');
    }
    catch (Exception $e) {
      if ($e->getCode() == OUR_REST_LOGIN_REQUIRED_NO_SESSION) {
        drupal_set_message($e->getMessage());
        drupal_goto('user/login', array('query' => drupal_get_destination()));
      }
      elseif ($e->getCode() == OUR_REST_LOGIN_REQUIRED_TOKEN_EXPIRED) {

        // Logout
        global $user;
        module_invoke_all('user_logout', $user);
        session_destroy();

        // Redirect
        drupal_set_message($e->getMessage());
        drupal_goto('user/login', array('query' => drupal_get_destination()));
      }
    }

    switch ($this->base_entity_type) {
      case 'siteshortname_entities_remote_event' :
        $entities = $this->parseEventResponse($response);
        break;
    }

    // Return the list of results.
    return $entities;
  }

  /**
   * Helper for execute() which parses the JSON response for event entities.
   *
   * May also set the $total_record_count property on the query, if applicable.
   *
   * @param $response
   *  The JSON/XML/whatever response from the REST server.
   *
   * @return
   *  An list of entity objects, keyed numerically.
   *  An empty array is returned if the response contains no entities.
   *
   * @throws
   *  Exception if a fault is received when the REST call was made.
   */
  function parseEventResponse($response) {

    // Fetch the list of events.
    if ($response->code == 404) {
      // No data was returned so let's provide an empty list.
      $events = array();
    }
    else /* we have response data */ {

      // Convert the JSON (assuming that's what we're getting) into a PHP array.
      // Do any unmarshalling to convert the response data into a PHP array.
      $events = json_decode($response->data, TRUE);
    }

    // Initialize an empty list of entities for returning.
    $entities = array();

    // Iterate through each event.
    foreach ($events as $event) {
      $entities[] = (object) array(
            // Set event information.
            'event_id' => isset($event['id']) ? $event['id'] : NULL,
            'event_name' => isset($event['name']) ? $event['name'] : NULL,
            'event_date' => isset($event['date']) ? $event['date'] : NULL,
      );
    }

    // Return the newly-created list of entities.
    return $entities;
  }

  /**
   * Throw an exception when there's a problem.
   *
   * @param string $code
   *   The error code.
   *
   * @param string $message
   *   A user-friendly message describing the problem.
   *
   * @throws Exception
   */
  function throwException($code, $message) {

    // Report error to the logs.
    watchdog('siteshortname_entities_remote', 'ERROR: OurRestRemoteSelectQuery: "@code", "@message".', array(
      '@code' => $code,
      '@message' => $message,
    ));

    // Throw an error with which callers must deal.
    throw new Exception(t("OurRestRemoteSelectQuery error, got message '@message'.", array(
      '@message' => $message,
    )), $code);
  }

}
