<?php

use MarkConde\Onemap\Onemap;
 
class OnemapTest extends PHPUnit_Framework_TestCase {

  protected $onemap;

  protected function setUp()
  {
    $this->onemap = new Onemap();
  }
 
  public function testOnemapGetAddress()
  {
  	$this->onemap->clearCache();

    $response = $this->onemap->search('307683');

    $this->assertObjectHasAttribute('found', $response);
    $this->assertGreaterThan(1, $response->found);
  }

  public function testOnemapCache()
  {
    $this->onemap->clearCache();

  	$response_not_cached = $this->onemap->search('307683');
  	$this->assertFalse($response_not_cached->from_cache);

  	$response_cached = $this->onemap->search('307683');
  	$this->assertTrue($response_cached->from_cache);
  }
 
  public function testOnemapConvert4326to3857(){
    $lat = 1.31972;
    $lng = 103.8421581;

    $response = $this->onemap->convert($lat, $lng, Onemap::CONVERT_4326_TO_3857);

    $this->assertEquals('double', gettype($response->Y));
    $this->assertEquals('double', gettype($response->X));
  }

  public function testOnemapConvert4326to3414(){
    $lat = 1.31972;
    $lng = 103.8421581;

    $response = $this->onemap->convert($lat, $lng, Onemap::CONVERT_4326_TO_3414);

    $this->assertEquals('double', gettype($response->Y));
    $this->assertEquals('double', gettype($response->X));
  }

  public function testOnemapConvert3414to3857(){
    $X = 28983.788791079794;
    $Y = 33554.5098132845;

    $response = $this->onemap->convert($X, $Y, Onemap::CONVERT_3414_TO_3857);

    $this->assertEquals('double', gettype($response->Y));
    $this->assertEquals('double', gettype($response->X));
  }

  public function testOnemapConvert3414to4326(){
    $X = 28983.788791079794;
    $Y = 33554.5098132845;

    $response = $this->onemap->convert($X, $Y, Onemap::CONVERT_3414_TO_4326);

    $this->assertEquals('double', gettype($response->latitude));
    $this->assertEquals('double', gettype($response->longitude));
  }
  
  public function testOnemapConvert3857to3414(){
    $X = 11559656.16256661;
    $Y = 146924.54200324757;

    $response = $this->onemap->convert($X, $Y, Onemap::CONVERT_3857_TO_3414);

    $this->assertEquals('double', gettype($response->Y));
    $this->assertEquals('double', gettype($response->X));
  }
  
  public function testOnemapConvert3857to4326(){
    $X = 11559656.16256661;
    $Y = 146924.54200324757;

    $response = $this->onemap->convert($X, $Y, Onemap::CONVERT_3857_TO_4326);

    $this->assertEquals('double', gettype($response->latitude));
    $this->assertEquals('double', gettype($response->longitude));
  }

}