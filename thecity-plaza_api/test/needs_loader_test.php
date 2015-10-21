<?php
 
  require_once 'helper.php';

  /** 
   * These are the tests for the NeedsLoader class.
   *
   * @package TheCity
   * @author Wes Hays <wes@onthecity.com>
   */
   
   class TestNeedsLoader extends UnitTestCase {
     function setUp() {
       // $subdomain = 'livingstones';
       // $cacher = new JsonCache($subdomain);
       // $loader = new NeedsLoader($subdomain, $cacher);
       // $needs = new Needs($loader);
     }

     function tearDown() {
     }
     
     function testNeedsLoaderNew() {
       $this->assertNotNull( new NeedsLoader('somechurch') );
       // print_r( $needs->all() );
     }
   }
  
  
?>