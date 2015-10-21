<?php

  /** 
   * Project:    Plaza-PHP
   * File:       topics_loader.php
   *
   * @author Wes Hays <wes@onthecity.org> 
   * @link https://github.com/thecity/thecity-plaza-php
   * @package TheCity
   */


  /** 
   * This class loads the Plaza topics for the subdomain.
   *
   * @package TheCity
   */
  class TopicsLoader extends BaseLoader {

    /**
     *  Constructor.
     *
     * @param string $subdomain The church subdomain.
     * @param integer $num_per_page The number of items to show.  Max is 15. Default is 10.
     * @param CacheInterface The cacher to be used to cache data.
     * @param string $group_nickname (optional) The group to get plaza items for.
     */
    public function __construct($subdomain, $num_per_page = 10, $cacher = null, $group_nickname = null) {
      parent::__construct();
      $group_nickname = $this->clean_group_nickname($group_nickname);
      $this->class_key = implode('_', array('topics', $num_per_page, $group_nickname));
      $nickname = empty($group_nickname) ? '' : '/'.$group_nickname;
      $this->url = "http://$subdomain.onthecity.org/plaza$nickname/topics?format=json&per_page=$num_per_page";  
      if( !is_null($cacher) ) { $this->cacher = $cacher; }  
    }

  }

?>