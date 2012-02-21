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


		/**
		 * Parse the URI into model names and validate these.
		 */

		// Explode our URI into individual resources and IDs
		$segments = explode('/', $uri, 6);

		// This is a container which stores all resources called in the URI
		$resources = array();

		// This is a temporary variable to add all object names together to get 
		// our requested object's model. For example, the user model is accessed 
		// through accounts/{id}/users, so this ends up as accounts_users
		$model_name = '';

		foreach ($segments as $key => $value)
		{
			// Odd URI segments are always resource names
			if (($key % 2) === 0)
			{
				array_push($resources, array(
					'name' => $value,
					'id'     => Arr::get($segments, $key + 1, '')
				));

				$model_name .= $value.'/';
			}
		}

		// Remove the trailing underscore
		$model_name = rtrim($model_name, '/');

		if (empty($model_name) AND $method != 'GET')
			throw new App_API_Exception("GET is the only valid HTTP method to collections", NULL, 400);

		if ( ! Kohana::find_file('classes/model', $model_name))
			throw new App_API_Exception("The requested collection ':model' could not be found", array(':model' => $model_name), 404);


		/**
		 * Parse internal query paramaters
		 */

		// For all external requests by default
		$params = $_GET;

		// Get the final segment of the API URI. We're not using $segments any 
		// more so a destructive method is fine.
		$last_segment = array_pop($segments);

		// Get the last object from the parsed URI which has the get 
		// parameter on.
		$last_object = array_pop($resources);

		if (strpos($last_segment, '?') !== FALSE)
		{
			// This was an internal request and the URI had an appended query 
			// string simulating GET parameters. Remove this from the last URI 
			// segment and parse as a standard GET request.

			if (empty($last_object['id']))
			{
				list($last_object['name'], $params) = explode('?', $last_object['name']);
			}
			else
			{
				list($last_object['id'], $params) = explode('?', $last_object['id']);
			}

			// Find out if there are any query strings in the model name, from 
			// internal requests. If so, parse them as GET query parameters
			parse_str($params, $params);
		}


		/**
		 * Instantiate the requested resource (the last resource name) and set 
		 * its parent resources, where necessary
		 */
		$model = Mundo::factory($model_name);

		if ( ! empty($last_object['id']))
		{
			$model->set('_id', new MongoId($last_object['id']));
		}

		// Because of the array_pop to get the $last_object above we are now 
		// left with parent objects in $resources, so set the parent 
		// accordingly.
		if ( ! empty($resources))
		{
			$model->set_parent($resources);
		}


		/**
		 * Execute the API call and get the response.
		 */
		$method = 'API_'.$method;
		$response = $model->$method($params);


		/**
		 * Encode the response and add the necessary metadata.
		 */

		// Get the current response metadata to add the response time
		$metadata = $this->_response->metadata();
		$metadata['response_time'] = gmdate("Y-m-d\TH:i:s\Z");

		$this->_response->content($response['content']);
		$this->_response->type($last_object['name']);

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
