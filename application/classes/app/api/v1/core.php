<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Version 1 of the CMS API
 *
 */
class App_API_V1_Core
{

	/**
	 * API Version
	 *
	 * @var string
	 */
	const API_VERSION = '1';

	/**
	 * The client-requested format for the response of the API
	 *
	 * @var mixed JSON or XML API response class.
	 */
	private $_response;

	public function __construct($format = NULL)
	{
		if (Route::name(Request::$current->route()) !== 'api')
		{
			$this->set_format('internal');
		}
		elseif ($format !== NULL)
		{
			$this->set_format($format);
		}
	}

	/**
	 * Sets the format of the API response.
	 *
	 * @param string
	 * @return $this
	 */
	public function set_format($format = NULL)
	{
		// This specifies the only formats this API will accept.
		$valid_formats = array('json', 'xml');

		if (Route::name(Request::$current->route()) !== 'api')
		{
			array_push($valid_formats, 'internal');
		}

		if ($format === NULL)
		{
			/* @TODO Parse HTTP Accept header and respect priority parameters */
			// If a format hasn't been specified, use the HTTP Accept parameter by default
			$format = (strpos($_SERVER['HTTP_ACCEPT'], 'application/xml')) ? 'xml' : 'json';
		}

		if ( ! in_array(strtolower($format), $valid_formats))
		{
			// Set the default response type to JSON and throw an error saying the requested response type isn't supported
			$response = 'App_API_V1_Response_JSON';
			$this->_response = new $response;

			throw new App_API_Exception("Accepted response formats are JSON and XML", NULL, 400);
		}

		$response = 'App_API_V1_Response_'.$format;

		// Instantiate the API response class which handles the requested format
		$this->_response = new $response;

		// Give us some basic information
		$this->_response->metadata(array(
			'uri' => Request::$current->uri(),
			'request_time' => gmdate("Y-m-d\TH:i:s\Z", $_SERVER['REQUEST_TIME']),
		));

		return $this;
	}

	/**
	 * Calls the appropriate method to handle logic from the request method.
	 *
	 * The $uri parameter must be passed in the form of 
	 * /parent_coll/id/coll/id, therefore uri parts are grouped in twos and 
	 * formatted accordingly. For example:
	 *
	 * /sites/{$site_id}/pages/{$page_id}
	 *
	 * This URL points to the page object within the specified site. URIs can 
	 * only be two objects deep (the maximum URI segments are shown above).
	 *
	 * @param  string  PUT, POST, GET or DELETE, as required
	 * @param  string  URI of the API call to make.
	 * @return mixed
	 */
	public function call($method, $uri)
	{
		if (empty($uri))
		{
			// @todo Discoverability: show which objects a user can currently 
			// access and manipulate
			return TRUE;
		}

		// The + array('', ..) is a quick hack to get around an empty/non max 
		// length $uri parameter
		list($collection['name'], $collection['id'], $object['name'], $object['id']) = explode('/', $uri, 4) + array('', '', '', '');

		if (empty($object['name']))
		{
			// We're accessing a collection (ie. a site) as an object directly, 
			// as opposed to something like /site/$id/page/$page
			$object = $collection;
			unset($collection);
		}

		$params = $_GET;

		// Was the last URI segment a resource name or ID? We use this for 
		// internal query string parsing when $_GET isn't set
		$last_item = (empty($object['id'])) ? array('key' => 'name', 'data' => $object['name']) : array('key' => 'id', 'data' => $object['id']);

		if (strpos($last_item['data'], '?') !== FALSE)
		{
			// Find out if there are any query strings in the model name, from 
			// internal requests. If so, parse them as GET query parameters
			list($object[$last_item['key']], $params) = explode('?', $last_item['data']);
			parse_str($params, $params);
		}

		if( ! Kohana::find_file('classes/model', $object['name']))
		{
			throw new App_API_Exception("The requested collection ':model' could not be found", array(':model' => $object['name']), 404);
		}

		$model = Mundo::factory($object['name']);

		if (empty($object['name']) AND $method != 'GET')
		{
			throw new App_API_Exception("GET is the only valid HTTP method to collections", NULL, 400);
		}

		if ( ! empty($object['id']))
		{
			$model->set('_id', new MongoId($object['id']));
		}

		if (isset($collection))
		{
			$model->set_parent($collection);
		}

		$method = 'API_'.$method;

		$response = $model->$method($params);

		// Get the current response metadata to add the response time
		$metadata = $this->_response->metadata();
		$metadata['response_time'] = gmdate("Y-m-d\TH:i:s\Z");

		$this->_response->content($response['content']);
		$this->_response->type($object['name']);

		if (isset($response['status']))
		{
			$this->_response->code($response['status']);
			$metadata['status'] = (int) $response['status'];
		}
		else
		{
			$metadata['status'] = 200;
		}

		$this->_response->metadata($metadata + $response['metadata']);
		return $this->_response->encode_response();
	}

	/**
	 * This method is called when the API throws an API exception.
	 *
	 * This encodes the error message according to the requested format and
	 * returns a response class with the appropriate headers and response.
	 *
	 * Note that this handles both server (5xx) and client (4xx) errors
	 *
	 * @param  string   Message from the thrown exception
	 * @param  int      HTTP Status code to send
	 * @param  string   Optional URI to show in the error message
	 * @return Response
	 */
	public function error($message, $status, $uri = NULL)
	{
		// Ensure that a response object has been set to handle the API response. If not, set one.
		if ($this->_response === NULL)
		{
			$this->set_format();
		}

		// Create a new response
		$response = new Response;

		$this->_response->type('error');

		// Add the status and description by default
		$this->_response->content(array(
			'status' => $status,
			'description' => $message,
		));

		if (array_key_exists($status, Response::$messages))
		{
			// Set the status
			$this->_response->code($status);
		}
		else
		{
			// This was an internal PHP error which has a status of something like '8'. This isn't a HTTP status code; throw a 500 server error.
			$this->_response->code(500);
		}

		$response->body($this->_response->encode_response());
		$response->status($this->_response->code());
		$response->headers('Content-Type', $this->_response->type());

		return $response;
	}


	/**
	 * Compresses data using the 'deflate' algorithm.
	 *
	 * @see http://www.php.net/manual/en/function.gzdeflate.php
	 * @param string  data to compress
	 * @return array  array containing a mongodate of the current timestamp and 
	 *                compressed data.
	 */
	public function deflate_bindata($data, $date)
	{
		$compressed = gzdeflate($data, 7);
		return array($date, new MongoBinData($compressed));
	}

	/**
	 * Inflates (uncompresses) data compressed using {@link deflate_bindata}
	 *
	 * @param string   Binary data compressed with the 'deflate' algorithm
	 * @return string  Uncompressed data
	 */
	public function inflate_bindata($data)
	{
		if ($data instanceof MongoBinData)
		{
			$data = $data->bin;
		}

		return gzinflate($data);
	}
} // end App_API_1
