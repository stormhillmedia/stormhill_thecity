<?php

  /** 
   * Project:    Plaza-PHP
   * File:       album.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @package TheCity
   */


  /** 
   * A album instance.  This object is immutable.
   *
   * @package TheCity
   */
  class Album extends PlazaCommon {
    
    /**
     * Constructor.
     *
     * @param array $data a Hash containing all the data to initialize this album.
     */
    public function __construct($data) {
      parent::__construct($data);
    }


    /**
     * @return The id of this album.     
     */
    public function id() { 
      return isset($this->data->auid) ? $this->data->auid : '';
    }    
    
    
    /**
     * @return The responses to the album.
     */
    public function posts() {
      $rposts = array();
      foreach ($this->data->album_responses as $album_response) { 
        $name = 'Unknown';
        if( !is_null($album_response->user) ) {
          $name = $album_response->user->long_name;
        } 
        else if( !is_null($album_response->facebook_user) ) {
          $name = $album_response->facebook_user->first.' '.$album_response->facebook_user->last;
        }

        $rposts[] = array(
          'created_at' => $album_response->created_at,
          'who_posted' => $name,
          'content'    => $this->clean_text( $album_response->body ),
        );
      }       
      return $rposts;
    }
    
  }
?>
