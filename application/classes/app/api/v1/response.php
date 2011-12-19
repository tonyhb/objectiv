<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The response encoding framework.
 *
 */
abstract class App_API_V1_Response
{

	/**
	 * Stores the content type of the response.
	 *
	 * @param string
	 */
	protected $_encoding;

	/**
	 * The type of data returned with the body of the response. This
	 * describes the $_response_content, such as 'error', '{$object_name}'
	 * etc.
	 *
	 * @param string
	 */
	protected $_response_type;

	/**
	 * The response content from the API request.
	 *
	 * This always contains an array of response items (even for a single
	 * error message) for continuity and ease of API libraries.
	 *
	 * @var array
	 */
	protected $_response_content = array();

	/**
	 * Metadata from the API request.
	 *
	 * This always includes the request URI and the request timestamp,
	 * as well as other request specific items such as the number of query
	 * results from a search.
	 *
	 * @var array
	 */
	protected $_response_metadata = array();

	/**
	 * The HTTP status code to be returned from the API
	 *
	 * @var int
	 */
	protected $_status_code;

	/**
	 * Returns the content type associated with this response class.
	 * Note this has no set counterpart method; this cannot be overwritten
	 * at runtime.
	 *
	 * @return string
	 */
	public function encoding()
	{
		return $this->_encoding;
	}

	/**
	 * Sets the HTTP status code to be sent with the API response
	 *
	 * @param int  HTTP Status code number to return
	 */
	public function code($status = NULL) 
	{
		if ($status === NULL)
			return $this->_status_code;

		if (array_key_exists($status, Response::$messages))
		{
			// Set the status only if it is a valid status code.
			$this->_status_code = (int) $status;
		}

		return $this;
	}

	/**
	 * Sets the response type for the current API call.
	 *
	 * @param string
	 */
	public function type($type = NULL)
	{
		if ($type === NULL)
			return $this->_response_type;

		$this->_response_type = (string) $type;

		return $this;
	}

	/**
	 * Sets the response content for the current API call.
	 *
	 * @param array
	 * @return mixed The content, if none was supplied, or $this if setting
	 */
	public function content($content = NULL)
	{
		if ($content === NULL)
			return $this->_response_content;

		if ( ! is_array($content))
		{
			// Normalise every piece of content, even if singular, to be in an array of content items for continuity.
			$content = array($content);
		}

		$this->_response_content = $content;

		return $this;
	}

	/**
	 * Sets the response metadata for the current API call.
	 *
	 * @param array
	 */
	public function metadata($metadata = NULL)
	{
		if ($metadata === NULL)
			return $this->_response_metadata;

		if ( ! is_array($metadata))
		{
			// Response metadata is an array of values.
			$metadata = array($metadata);
		}

		$this->_response_metadata = $metadata;

		return $this;
	}

	abstract public function encode_response();

	/**
	 * This properly serializes and base64 encodes binary data in MongoBinData
	 *
	 * @param array   an array of model data
	 * @return array  array of model data with encoded binary fields
	 */
	protected function serialize_binary($model)
	{
		if ( ! array_key_exists('binary', $this->metadata()))
			return $model;

		$binary_fields = $this->_response_metadata['binary'];

		foreach ($model as $field => &$data)
		{
			if ( ! in_array($field, $binary_fields))
				continue;

			// Quick test: is the first element in this array a timestamp? 
			// If not, this is an array of bindatas (for example, the 
			// 'hist' field in an object
			if ($data[0] instanceof MongoDate)
			{
				$data = array(
					$data[0], // This is the mongo timestamp
					base64_encode($data[1]->bin)
				);
			}
			else
			{
				foreach ($data as $key => $element)
				{
					$data[$key] = array(
						$element[0], // This is the mongo timestamp
						base64_encode($element[1]->bin)
					);
				}
			}
		}

		return $model;
	}
}
