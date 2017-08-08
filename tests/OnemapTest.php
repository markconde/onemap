<?php
 
use MarkConde\Onemap\Onemap;
 
class OnemapTest extends PHPUnit_Framework_TestCase {
 
  public function testOnemapGetAddress()
  {
    $onemap = new Onemap;

    $response = $onemap->getAddress('307683');

    $this->assertObjectHasAttribute('found', $response);
    $this->assertGreaterThan(1, $response->found);
  }
 
}