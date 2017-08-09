<?php 
namespace MarkConde\Onemap;

use Stash;

class Onemap {
	const BASE_URL = "https://developers.onemap.sg";
	const REST_ENDPOINT = "/commonapi/search";

	public $fs_cache;

	public function __construct( $cached = true ) {

		if( $cached ){
			$driver = new Stash\Driver\FileSystem(array());
			$this->fs_cache = new Stash\Pool($driver);
		}

	}

	public function getAddress( $search_val, $return_geom = 'Y', $get_addr_details = 'Y', $page_num = 1 ) {

		$cache_item = $this->fs_cache->getItem($search_val);

		$response = $cache_item->get();

		if($cache_item->isMiss()){

			$parameters = array(
				'searchVal' => $search_val,
				'returnGeom' => $return_geom,
				'getAddrDetails' => $get_addr_details,
				'pageNum' => $page_num
			);

			$response = $this->doRequest( $parameters );
			$result_obj = json_decode($response);
			$result_obj->from_cache = false;

			$cache_item->lock();
			$this->fs_cache->save($cache_item->set($response));

			return $result_obj;	
		}

		$result_obj = json_decode($response);
		$result_obj->from_cache = true;

		return $result_obj;

	}

	protected function doRequest( $parameters = array() ) {

		if( !count($parameters) ) return false;

		$request = self::BASE_URL . self::REST_ENDPOINT . "?" .http_build_query($parameters);
		$response = file_get_contents( $request );

		return $response;
	}

	public function clearCache() {
		$this->fs_cache->clear();
	}


}