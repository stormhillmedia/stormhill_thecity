<?php

  /** 
   * Project:    Plaza-PHP
   * File:       topic.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @package TheCity
   */


  /** 
   * A topic instance.  This object is immutable.
   *
   * @package TheCity
   */
  class Topic extends PlazaCommon {
    
    /**
     * Constructor.
     *
     * @param array $data a Hash containing all the data to initialize this topic.
     */
    public function __construct($data) {
      parent::__construct($data);
    }


    /**
     * @return The id of this album.     
     */
    public function id() { 
      return isset($this->data->tuid) ? $this->data->tuid : '';
    }    
    
    
    /**
     * @return The responses to the topic.
     */
    public function posts() {
      $rposts = array();
      foreach ($this->data->posts as $post) { 
        $name = 'Unknown';
        if( !is_null($post->user) ) {
          $name = $post->user->long_name;
        } 
        else if( !is_null($post->facebook_user) ) {
          $name = $post->facebook_user->first.' '.$post->facebook_user->last;
        }
        
        $rposts[] = array(
          'created_at' => $post->created_at,
          'who_posted' => $name,
          'content'    => $this->clean_text( $post->body )
        );
      }
       
      return $rposts;
    }
    
  }
?>