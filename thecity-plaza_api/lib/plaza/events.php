<?php

  /** 
   * Project:    Plaza-PHP
   * File:       events.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @package TheCity
   */


  /** 
   * This class is a wrapper for the events page.
   *
   * @package TheCity
   */
  class Events extends Plaza {

    /**
     *  Constructor.
     *
     * @param EventsLoader $loader The object that loaded the data.
     */
    public function __construct($loader) {
      parent::__construct();
      $this->json_data = $loader->load_feed();
    }


    /**
     *  All the public events on the Plaza.
     *
     *  @return array of events.
     */
    public function all_titles() {
      $events = array();
      foreach ($this->json_data as $event) { $events[] = $event->global_event->title; }
      return $events;
    }
    
    
    /**
     * Get the specified need.
     *
     * @param index The index of the event to get all the information for.
     *
     * @return Event
     */
    public function select($index) {
      if( !isset($this->json_data[$index]) ) { return null; }
      return new Event( $this->json_data[$index]->global_event );
    }
  }
?>