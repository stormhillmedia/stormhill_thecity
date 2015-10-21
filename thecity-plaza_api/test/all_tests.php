<?php

  require_once 'helper.php';
  
  require_once 'topics_loader_test.php';
  require_once 'prayers_loader_test.php';
  require_once 'needs_loader_test.php';
  require_once 'events_loader_test.php';
  require_once 'albums_loader_test.php';

  class AllTests extends TestSuite {
      function __construct() {
          parent::__construct();
          // All require_once above tests cases will run
      }
  }
  
?>