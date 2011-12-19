<?php defined('SYSPATH') or die('No direct script access.');

class App_API_V1_Response_JSON extends App_API_V1_Response {


	protected $_content_type = 'application/json';

	public function encode_response()
	{
		$content = $this->_response_content;

		if (array_key_exists('binary', $this->_response_metadata))
		{
			if ( ! Arr::is_assoc($content))
			{
				// This is a list of many objects, so recursively loop over each 
				// one.
				foreach ($content as $key => &$object)
				{
					$object = $this->serialize_binary($object);
				}
			}
			else
			{
				$content = $this->serialize_binary($content);
			}
		}

		return json_encode(array(
			'contentType' => $this->_response_type,
			'metadata' => $this->_response_metadata,
			'content' => $content,
		));
	}

}
