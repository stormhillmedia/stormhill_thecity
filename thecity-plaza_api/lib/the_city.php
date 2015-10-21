<?php

  /** 
   * Project:    Plaza-PHP
   * File:       the_city.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @version 1.0a
   * @package TheCity
   */
   

  /**
   * Includes the autoloader.
   */
  include_once(dirname(__FILE__) . '/auto_load.php');


  /** 
   * This class is meant to be a wrapper for the OnTheCity.org API.
   *
   * @package TheCity
   */
  class TheCity {

    // The subdomain to load the data for.
    private $subdomain;

    // The albums loaded for the subdomain.
    private $albums;
    
    // The events loaded for the subdomain.
    private $events;
    
    // The needs loaded for the subdomain. 
    private $needs;
    
    // The prayers loaded for the subdomain.
    private $prayers;
    
    // The topics loaded for the subdomain. 
    private $topics;


    // The last number of albums requested.
    private $albums_num_requested = 0;
    
    // The last number of events requested.
    private $events_num_requested = 0;
    
    // The last number of needs requested.
    private $needs_num_requested = 0;
    
    // The last number of prayers requested.
    private $prayers_num_requested = 0;
    
    // The last number of topics requested.
    private $topics_num_requested = 0;

    // Paramters that may be used by the system now or in the future.
    private $other_url_params = '';

    // The object to store and load the cache.
    private $cacher;


    // The group's nickname to reference.
    private $group_nickname = null;


    /**
     *  Constructor.
     *
     * @param string $subdomain The church subdomain.
     * @param boolean $cache_data Cache the data. Default true.
     * @param CacheInterface $cacher The object that will store and load the cache. Default type JsonCache.
     */
    public function __construct($subdomain, $cache_data = true, $cacher = null) {
      $this->subdomain = $subdomain;
      if($cache_data === true) {
        $this->cacher = empty($cacher) ? new JsonCache($subdomain) : $cacher;
      }
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
     * Se the group nickname and only pull from that groups items.
     *
     * @param string nickname
     */
    public function set_group_nickname($nickname) {
      $this->group_nickname = $nickname;
    }
    
    
    /**
     * Shows all the topics posted on the Plaza.
     *
     * @param integer $num_per_page The number of items to show.  Max is 15. Default is 10.
     *
     * @return array of all the topics posted on the Plaza.
     */
    public function topics($num_per_page = 10) {
      if( $this->topics_num_requested == $num_per_page && !is_null($this->topics) ) { return $this->topics; }
      $this->topics_num_requested = $num_per_page;
      $loader = new TopicsLoader( $this->subdomain, $num_per_page, $this->cacher, $this->group_nickname );
      $loader->add_url_params($this->other_url_params);
      $this->topics = new Topics( $loader );
      return $this->topics;
    }
    

    /**
     * Shows all the events posted on the Plaza.
     *
     * @param integer $num_per_page The number of items to show.  Max is 15. Default is 10.
     *
     * @return array of all the events posted on the Plaza.
     */
    public function events($num_per_page = 10) {
      if( $this->events_num_requested == $num_per_page && !is_null($this->events) ) { return $this->events; }
      $this->events_num_requested = $num_per_page;
      $loader = new EventsLoader( $this->subdomain, $num_per_page, $this->cacher, $this->group_nickname );
      $loader->add_url_params($this->other_url_params);
      $this->events = new Events( $loader );
      return $this->events;
    }    
 
    
    /**
     * Shows all the prayers posted on the Plaza.
     *
     * @param integer $num_per_page The number of items to show.  Max is 15. Default is 10.
     *
     * @return array of all the prayers posted on the Plaza.
     */
    public function prayers($num_per_page = 10) {
      if( $this->prayers_num_requested == $num_per_page && !is_null($this->prayers) ) { return $this->prayers; }
      $this->prayers_num_requested = $num_per_page;
      $loader = new PrayersLoader( $this->subdomain, $num_per_page, $this->cacher, $this->group_nickname );
      $loader->add_url_params($this->other_url_params);
      $this->prayers = new Prayers( $loader );
      return $this->prayers;
    }
    
    /**
     * Shows all the needs posted on the Plaza.
     *
     * @param integer $num_per_page The number of items to show.  Max is 15. Default is 10.
     *
     * @return array of all the needs posted on the Plaza.
     */
    public function needs($num_per_page = 10) {
      if( $this->needs_num_requested == $num_per_page && !is_null($this->needs) ) { return $this->needs; }
      $this->needs_num_requested = $num_per_page;
      $loader = new NeedsLoader( $this->subdomain, $num_per_page, $this->cacher, $this->group_nickname );
      $loader->add_url_params($this->other_url_params);
      $this->needs = new Needs( $loader );
      return $this->needs;
    }
    

    /**
     * Shows all the events posted on the Plaza.
     *
     * @param integer $num_per_page The number of items to show.  Max is 15. Default is 10.
     *
     * @return array of all the events posted on the Plaza.
     */
    public function albums($num_per_page = 10) {
      if( $this->albums_num_requested == $num_per_page && !is_null($this->albums) ) { return $this->albums; }
      $this->albums_num_requested = $num_per_page;
      $loader = new AlbumsLoader( $this->subdomain, $num_per_page, $this->cacher, $this->group_nickname );
      $loader->add_url_params($this->other_url_params);
      $this->albums = new Albums( $loader );
      return $this->albums;
    }
    

  }

?>