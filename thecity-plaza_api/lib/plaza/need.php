<?php

  /** 
   * Project:    Plaza-PHP
   * File:       need.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @package TheCity
   */


  /** 
   * A need instance.  This object is immutable.
   *
   * @package TheCity
   */
  class Need extends PlazaCommon {
    
    /**
     * Constructor.
     *
     * @param array $data a Hash containing all the data to initialize this need.
     */
    public function __construct($data) {
      parent::__construct($data);
    }
    
    
    /**
     * @return The id of this need.     
     */
    public function id() { 
      return isset($this->data->nuid) ? $this->data->nuid : '';
    }

    
    /**
     * @return The responses to the need.
     */
    public function posts() {      
      $rposts = array();
      foreach ($this->data->need_responses as $need_response) { 
        $name = 'Unknown';
        if( !is_null($need_response->user) ) {
          $name = $need_response->user->long_name;
        } 
        else if( !is_null($need_response->facebook_user) ) {
          $name = $need_response->facebook_user->first.' '.$need_response->facebook_user->last;
        }
        
        $rposts[] = array(
          'created_at' => $need_response->created_at,
          'who_posted' => $name,
          'content'    => $this->clean_text( $need_response->body )
        );
      }
       
      return $rposts;
    }
    
  }
?>