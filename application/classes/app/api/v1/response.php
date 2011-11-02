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
	protected $_content_type;

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
	public function get_content_type()
	{
		return $this->_content_type;
	}

	/**
	 * Returns the HTTP status code to be sent with the API response
	 *
	 * @return int
	 */
	public function get_status_code()
	{
		return $this->_status_code;
	}

	/**
	 * Sets the HTTP status code to be sent with the API response
	 *
	 * @param int  HTTP Status code number to return
	 */
	public function set_status_code($status) 
	{
		if (array_key_exists($status, Response::$messages))
		{
			// Set the status only if it is a valid status code.
			$this->_status_code = (int) $status;
		}

		return $this;
	}

	/**
	 * Describes the data type in $_response_content
	 *
	 * @return string
	 */
	public function get_response_type()
	{
		return $this->_response_type;
	}

	/**
	 * Sets the response type for the current API call.
	 *
	 * @param string
	 */
	public function set_response_type($type)
	{
		$this->_response_type = (string) $type;

		return $this;
	}

	/**
	 * Returns the response to be returned from the API call
	 *
	 * @return array
	 */
	public function get_response_content()
	{
		return $this->_response_content;
	}

	/**
	 * Sets the response content for the current API call.
	 *
	 * @param array
	 */
	public function set_response_content($content)
	{
		if ( ! is_array($content))
		{
			// Normalise every piece of content, even if singular, to be in an array of content items for continuity.
			$content = array($content);
		}

		$this->_response_content = $content;

		return $this;
	}

	/**
	 * Returns the response metadata to be returned from the API call
	 *
	 * @return array
	 */
	public function get_response_metadata()
	{
		return $this->_response_metadata;
	}

	/**
	 * Sets the response metadata for the current API call.
	 *
	 * @param array
	 */
	public function set_response_metadata($metadata)
	{
		if ( ! is_array($metadata))
		{
			// Response metadata is an array of values.
			$metadata = array($metadata);
		}

		$this->_response_metadata = $metadata;

		return $this;
	}

	abstract public function encode_response();
}
