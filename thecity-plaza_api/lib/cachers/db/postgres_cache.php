<?php

  /** 
   * Project:    Plaza-PHP
   * File:       postgres_cache.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @package TheCity
   */


  

  /** 
   * This class caches the data in a postgresql database.
   *
   * @package TheCity
   */
  class PostgresCache implements CacheInterface {
    
    // The subdomain to load and store the data for.
    private $subdomain;
    
    
    /**
     *  Constructor.
     *
     * @param string $subdomain The church subdomain.
     * 
     */
    public function __construct($subdomain) {
      $this->subdomain = $subdomain;
    }
    
    
    /**
     *  Save data to the cache.
     *
     * @param string $key The key to use to save the cache.
     * @param string $data The JSON data to be saved.
     * @param string $expire_in The number of seconds to pass before expiring the cache.
     *
     * @return mixed Returns true on success or a string error message on false.
     */
    public function save_data($key, $data, $expire_in = null) {
    } 
    
    

    /**
     * Get the data from the cache.
     *
     * @param string $key The key to use to get the cache.
     *
     * @return JSON data.
     */
    public function get_data($key) {   
    }
    
    
    /**
     * Expire the cache.
     *
     * @param string $key The key to use to expire the cache.
     *
     * @throws Exception if unable to remove cache file.
     */
    public function expire_cache($key) {
    }
    
    
    /**
     * Check if the cache has expired.
     *
     * @param string $key The key to use to check if the cache has expired.
     *
     * @return boolean If the cache does not exist or is expired then true, otherwise false.
     */
    public function is_cache_expired($key) {
    }
    

    
  }
?>
