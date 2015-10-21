<?php

  /** 
   * Project:    Plaza-PHP
   * File:       plaza.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @package TheCity
   */


  /** 
   * This is the generic class the different Plaza attributes 
   * (topics, prayers, needs, events, albums) will inherit from.
   *
   * @package TheCity
   */
  class Plaza {

    // Holds the data
    protected $json_data = '';    

    /**
     *  Generic constructor.
     */
    public function __construct() {
      // Do nothing
    }


    /**
     *  Alias for all_titles
     */
    public function titles() { return $this->all_titles(); }    
    

    /**
     * Get the number of items present.
     *
     * @return integer The number of items present.
     */
    public function size() { return count($this->json_data); }
    

    /**
     * Alias for size.
     */    
    public function length() { return $this->size(); }


    /**
     * Alias for size.
     */       
    public function count() { return $this->size(); }

  }
?>