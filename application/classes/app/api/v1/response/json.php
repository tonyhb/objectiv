<?php defined('SYSPATH') or die('No direct script access.');

class App_API_V1_Response_JSON extends App_API_V1_Response {


	protected $_content_type = 'application/json';

	public function encode_response()
	{
		return json_encode(array(
			'contentType' => $this->_response_type,
			'metadata' => $this->_response_metadata,
			'content' => $this->_response_content,
		));
	}

}
