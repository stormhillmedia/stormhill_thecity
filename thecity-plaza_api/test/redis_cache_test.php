<?php
 
  require_once 'helper.php';

  /** 
   * These are the tests for the RedisCache class.
   *
   * @package TheCity
   * @author Wes Hays <wes@onthecity.com>
   */
   
   class TestRedisCache extends UnitTestCase {
     function setUp() {
       // $cacher = new JsonCache($subdomain);
     }

     function tearDown() {
     }
     
     function testRedisCacheNew() {
       $this->assertNotNull( new RedisCache('somechurch') );
     }
     
     function test_store_data() {
       $cacher = new RedisCache('somechurch');
       
       $this->assertNotNull( new RedisCache('somechurch') );
     }
   }
  
  
?>