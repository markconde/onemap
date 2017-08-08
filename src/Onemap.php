<?php namespace MarkConde\Onemap;

class Onemap {
	const BASE_URL = "https://developers.onemap.sg";
	const REST_ENDPOINT = "/commonapi/search";

	public function getAddress( $search_val, $return_geom = 'Y', $get_addr_details = 'Y', $page_num = 1 ) {

		$parameters = array(
			'searchVal' => $search_val,
			'returnGeom' => $return_geom,
			'getAddrDetails' => $get_addr_details,
			'pageNum' => $page_num
			);

		$response = $this->doRequest( $parameters );

		return json_decode($response);
	}

	protected function doRequest( $parameters = array() ) {

		if( !count($parameters) ) return false;

		$request = self::BASE_URL . self::REST_ENDPOINT . "?" .http_build_query($parameters);

		$response = file_get_contents( $request );

		return $response;
	}


}