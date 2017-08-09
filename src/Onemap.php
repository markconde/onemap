<?php 
namespace MarkConde\Onemap;

use Stash;

class Onemap {
	const BASE_URL = 'https://developers.onemap.sg';
	const REST_EP_SEARCH = '/commonapi/search';
	const REST_EP_CONVERT = '/commonapi/convert/';
	const REST_EP_GET_STATIC_IMAGE = '/commonapi/staticmap/getStaticImage';
	const CONVERT_4326_TO_3857 = '4326to3857';
	const CONVERT_4326_TO_3414 = '4326to3414';
	const CONVERT_3414_TO_3857 = '3414to3857';
	const CONVERT_3414_TO_4326 = '3414to4326';
	const CONVERT_3857_TO_3414 = '3857to3414';
	const CONVERT_3857_TO_4326 = '3857to4326';
	const MAP_NIGHT = 'night';
	const MAP_GREY = 'grey';
	const MAP_ORIGINAL = 'original';
	const MAP_DEFAULT = 'default';
	const MAP_LANDLOT = 'landlot';

	protected $fs_cache;
	protected $fs_sec_expires;

	public function __construct( $cached = true, $cache_expiration = 604800 ) 
	{

		if( $cached ){
			$driver = new Stash\Driver\FileSystem(array());
			$this->fs_cache = new Stash\Pool($driver);
			$this->fs_sec_expires = $cache_expiration;
		}

	}

	public function search( $search_val, $return_geom = 'Y', $get_addr_details = 'Y', $page_num = 1 ) 
	{

		if(!$response = $this->getFromCache($search_val)){

			$parameters = array(
				'searchVal' => $search_val,
				'returnGeom' => $return_geom,
				'getAddrDetails' => $get_addr_details,
				'pageNum' => $page_num
			);

			$response = $this->doRequest( self::REST_EP_SEARCH, $parameters );
			$this->saveToCache($search_val, $response);

			$result_obj = json_decode($response);
			$result_obj->from_cache = false;

			return $result_obj;	
		}

		$result_obj = json_decode($response);
		$result_obj->from_cache = true;

		return $result_obj;

	}

	public function convert( $X, $Y, $format = self::CONVERT_4326_TO_3857 )
	{
		if($format == self::CONVERT_4326_TO_3857 || $format == self::CONVERT_4326_TO_3414) {
			$parameters = array(
				'latitude' => $X,
				'longitude' => $Y,
			);
		}
		else{
			$parameters = array(
				'X' => $X,
				'Y' => $Y,
			);
		}

		$endpoint = self::REST_EP_CONVERT.$format;

		$response = $this->doRequest( $endpoint, $parameters );

		return json_decode($response);
	}

	public function getStaticImage( $layer_chosen = self::MAP_DEFAULT, $latitude = false, $longitude = false, $postal = false, $zoom = 11, $width = 300, $height = 300, $filename = "image.png", $polygons = false, $lines = false, $points = false, $color = false, $fill_color = false){

		$parameters = array(
			'layerchosen' => $layer_chosen,
			'zoom' => $zoom,
			'width' => $width,
			'height' => $height
			);

		if ( $latitude != false && $longitude != false ) {
			$parameters['lat'] = $latitude;
			$parameters['lng'] = $longitude;
		}
		elseif( $postal != false ) {
			$parameters['postal'] = $postal;
		}
		else{
			return false;
		}

		if($polygons != false) $parameters['polygons'] = $polygons;
		if($lines != false) $parameters['lines'] = $lines;
		if($points != false) $parameters['color'] = $color;
		if($fill_color != false) $parameters['fillColor'] = $fill_color;

		$response = $this->doRequestFile( self::REST_EP_GET_STATIC_IMAGE, $parameters, $filename );
	}

	protected function doRequest( $endpoint, $parameters = array() ) 
	{

		if( !count($parameters) ) return false;

		$request = self::BASE_URL . $endpoint . '?' .http_build_query($parameters);
		$response = file_get_contents( $request );

		return $response;
	}

	protected function doRequestFile( $endpoint, $parameters = array(), $filename="image.png" )
	{
		if( !count($parameters) ) return false;

		$request = self::BASE_URL . $endpoint . '?' .http_build_query($parameters);

		$ch = curl_init($request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

		$response = curl_exec($ch);

		return $response;
	}

	protected function getFromCache($key){

		if(is_object($this->fs_cache)){
			$cache_item = $this->fs_cache->getItem($key);
			$data = $cache_item->get();

			if($cache_item->isHit()){
				return $data;
			}
			else{
				return false;
			}
		}
	}

	protected function saveToCache($key, $data){

		if(is_object($this->fs_cache)){

			$cache_item = $this->fs_cache->getItem($key);

			$cache_item->lock();
			$cache_item->set($data);

			if ( $this->fs_sec_expires ){
				$cache_item->expiresAfter($this->fs_sec_expires);	
			}

			$this->fs_cache->save($cache_item);

		}
	}

	public function clearCache() {
		$this->fs_cache->clear();
	}


}