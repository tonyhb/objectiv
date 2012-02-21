<?php defined('SYSPATH') or die('No direct script access.');

class App_API_V1_Response_Internal extends App_API_V1_Response {

	public function encode_response()
	{
		return array(
			'content_type' => $this->_response_type,
			'metadata' => $this->_response_metadata,
			'content' => $this->_response_content,
		);
	}

}
