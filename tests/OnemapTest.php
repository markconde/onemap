<?php

use MarkConde\Onemap\Onemap;
 
class OnemapTest extends PHPUnit_Framework_TestCase {
 
  public function testOnemapGetAddress()
  {
  	$onemap = new Onemap();
  	$onemap->clearCache();

    $response = $onemap->getAddress('307683');

    $this->assertObjectHasAttribute('found', $response);
    $this->assertGreaterThan(1, $response->found);
  }

  public function testOnemapCache()
  {
  	$onemap = new Onemap();
  	$onemap->clearCache();

  	$response_not_cached = $onemap->getAddress('307683');
  	$this->assertFalse($response_not_cached->from_cache);
  	$response_cached = $onemap->getAddress('307683');
  	$this->assertTrue($response_cached->from_cache);
  }
 
}