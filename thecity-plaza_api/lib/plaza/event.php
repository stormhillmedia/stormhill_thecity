<?php

  /** 
   * Project:    Plaza-PHP
   * File:       event.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @package TheCity
   */


  /** 
   * A event instance.  This object is immutable.
   *
   * @package TheCity
   */
  class Event extends PlazaCommon {
    
    /**
     * Constructor.
     *
     * @param array $data a Hash containing all the data to initialize this event.
     */
    public function __construct($data) {
      parent::__construct($data);
    }


    /**
     * @return The id of this event.     
     */
    public function id() { 
      return isset($this->data->euid) ? $this->data->euid : '';
    }
    

    /**
     * @return When this event starts.
     */
    public function starting_at() { 
      return $this->data->starting_at;
    }


    /**
     * @return When this event ends.
     */
    public function ending_at() { 
      return $this->data->ending_at;
    }
    
    /**
     * @return The responses to the event.
     */
    public function posts() {
      $rposts = array();
      foreach ($this->data->event_responses as $event_response) { 
        $name = 'Unknown';
        if( !is_null($event_response->user) ) {
          $name = $event_response->user->long_name;
        } 
        else if( !is_null($event_response->facebook_user) ) {
          $name = $event_response->facebook_user->first.' '.$event_response->facebook_user->last;
        }
        
        $rposts[] = array(
          'created_at'   => $event_response->created_at,
          'who_posted'   => $name,
          'attending'    => $event_response->attending,
          'total_coming' => $event_response->total_coming
        );
      }
      return $rposts;
    }
    
  }
?>