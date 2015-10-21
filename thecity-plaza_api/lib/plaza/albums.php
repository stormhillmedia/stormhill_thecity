<?php

  /** 
   * Project:    Plaza-PHP
   * File:       albums.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @package TheCity
   */


  /** 
   * This class is a wrapper for the albums page.
   *
   * @package TheCity
   */
  class Albums extends Plaza {

    /**
     *  Constructor.
     *
     * @param AlbumsLoader $loader The object that loaded the data.
     */
    public function __construct($loader) {
      parent::__construct();
      $this->json_data = $loader->load_feed();
    }


    /**
    *  All the public albums on the Plaza.
    *  @return array of albums.
    */
    public function all_titles() {
      $albums = array();
      foreach ($this->json_data as $album) { $albums[] = $album->global_album->title; }
      return $albums;
    }
    
    
    /**
     * Get the specified prayer.
     *
     * @param index The index of the prayer to get all the information for.
     *
     * @return Album
     */
    public function select($index) {
      if( !isset($this->json_data[$index]) ) { return null; }
      return new Album( $this->json_data[$index]->global_album );
    }
    
  }
?>