<?php

  /** 
   * Project:    Plaza-PHP
   * File:       base_loader.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @package TheCity
   */


  /** 
   * This class has the defaults for the loaders.
   *
   * @package TheCity
   */
  class BaseLoader {

    public $class_key = 'NOT_SET';
    
    // The URL to load the albumns from.
    public $url = '';
    
    // The object to store and load the cache.
    public $cacher;

    /**
     *  Generic constructor.
     */
    public function __construct() {
      // Do nothing
    }


    /**
     * Cleans the group nickname to make sure it is valid.
     *
     * @return string The sanitized group nickname.
     */
    public function clean_group_nickname($group_name) {
      $nickname = $group_name == null ? '' : $group_name;
      $nickname = strip_tags($nickname);
      $nickname = strtolower($nickname);
      return trim($nickname);      
    }


    /**
     * Adds additional url params to be sent to the server.  These may be used by the system
     * now or in the future.
     *
     * @param string $url_params The additional params to send to the server.
     */
    public function add_url_params($url_params) {
      $this->other_url_params = $url_params;
    }    


    /**
     *  Loads all the prayers on the Plaza for the subdomain.
     *
     * @return JSON The data loaded in a JSON object.
     */  
    public function load_feed() {     
      if( !is_null($this->cacher) && !$this->cacher->is_cache_expired( $this->class_key ))  { 
        return $this->cacher->get_data( $this->class_key ); 
      }
   
      $url_to_use = $this->url;
      if( !empty($this->other_url_params) ) {
        $url_to_use = $this->url .'&'.$this->other_url_params;
      }

      $json = '{}';
      if( function_exists('curl_version') ) {     
        $json = $this->_load_data_using_curl($url_to_use); 
      } else if( ini_get('allow_url_fopen') == 1 ) { // On     
        $json = $this->_load_data_using_file_get_contents($url_to_use);         
      } else {
        throw new Exception('Cannot pull data from plaza.  Either enable allow_url_fopen in the php.ini file, or install curl for php.');
      }
      $data = json_decode($json);    
     
      if( !is_null($this->cacher) ) { 
        $this->cacher->save_data($this->class_key, $data);
      }      

      return $data;
    }      

    private function _load_data_using_file_get_contents($url) {
      return file_get_contents($url); 
    }

    private function _load_data_using_curl($url) {
      $ch = curl_init();
       
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
      curl_setopt($ch, CURLOPT_URL, $url);
       
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;      
    }    

  }

  ?>