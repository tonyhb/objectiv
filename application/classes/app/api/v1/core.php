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

		if ($format === NULL)
		{
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

		return $this;
	}

	/**
	 * Calls the appropriate method to handle logic from the request method
	 *
	 * @param  string  PUT, POST, GET or DELETE, as required
	 * @param  string  URI of the API call to make.
	 * @return mixed
	 */
	public function call($method, $uri)
	{
		// Store the name of the requested API class with the version number in a string for instatiation.
		$class = 'App_API_V1_Method_'.$method;

		$class = new $class($uri);

		/**
		 * @todo make models a member of the API
		 */

		return TRUE;
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

		$this->_response->set_response_type('error');

		// Give us some basic information for prognosis
		$this->_response->set_response_metadata(array(
			'uri' => Request::$current->uri(),
			'request_time' => gmdate("Y-m-d\TH:i:s\Z", $_SERVER['REQUEST_TIME']),
			'response_time' => gmdate("Y-m-d\TH:i:s\Z"),
		));

		// Add the status and description by default
		$this->_response->set_response_content(array(
			'status' => $status,
			'description' => $message,
		));

		if (array_key_exists($status, Response::$messages))
		{
			// Set the status
			$this->_response->set_status_code($status);
		}
		else
		{
			// This was an internal PHP error which has a status of something like '8'. This isn't a HTTP status code; throw a 500 server error.
			$this->_response->set_status_code(500);
		}

		$response->body($this->_response->encode_response());
		$response->status($this->_response->get_status_code());
		$response->headers('Content-Type', $this->_response->get_content_type());

		return $response;
	}


	/**
	 * This function checks Kohana 404 errors agains incorrect versions and
	 * resources.
	 *
	 * It is called in index.php and App_API::call when an
	 * HTTP_Exception_404 is called.
	public static function http_404($version, $format, $uri)
	{
		$status = 404;

		// Did this 404 occured because of an incorrect version number in the URI?
		if ( ! in_array($version, self::$api_versions))
		{
			// Requesting an incorrect verison of the API
			$message = "Unknown API version '".$version."'. Supported versions are: ";

			// Show supported API versions
			$message .= implode(', ', App_API::$api_versions);

			// Override Kohana's "Controller Not Found" and 500 internal with our own 400
			$status = 400;
		}
		else
		{
			// If not, there was an incorrect resource in the URI
			$object = str_replace('api.'.$format.'/'.$version.'/', '', $uri);

			// Write our error message
			$message = "The requested resource '".$object."' was not found.";
		}

		// Overwritten because the error() method wouldn't detect API URI from internal call
		self::$error_metadata = array(
			'uri' => $uri,
		);

		// There was an incorrect version number or resource supplied
		return self::error($message, $status);
	}

	public function __construct($data = NULL)
	{
		if ($data !== NULL)
		{
			// Load our array keys from the empty request object
			$keys = array_keys($this->_request);

			// Explode the request URI 
			$data = explode('/', $data, 4);

			// Empty the data for REST discoverability
			$data = array_filter($data);

			if (empty($data))
			{
				// @todo A user hasn't passed a collection or object so run the discoverable API method to inform the user of their options
				throw new App_API_Exception("You must pass a resource or object in the API request", NULL, 400);
				return;
			}

			foreach ($data as $key => $value)
			{
				// Add values to our $keys from the URI
				$this->_request[$keys[$key]] = $value;
			}


			// This manages our discoverable API
			if ( ! $this->_request['collection'])
			{
			}

			// Add defaults from site keys to collection/object keys for site manipulation
			if ( ! $this->_request['object'])
			{
				$this->_request['object'] = 'sites';

				// Similarly, for manipulation we need the ID of the site in the object_id, not site_id
				if ( ! $this->_request['object_id'] AND $this->_request['collection_id'])
				{
					// If we haven't rqeuested a
					$this->_request['object_id'] = $this->_request['collection_id'];
				}
			}
			// Authorisation here ?
		}
	}

	/**
	 * Returns information about a specific collection or object
	 *
	public function get($object = NULL, $object_id = NULL)
	{
		if ( ! $object)
		{
			// Use the collection given in the URI
			$object = $this->_request['object'];
		}

		if ( ! $object_id)
		{
			// Use the object ID given in the request URI
			$object_id = $this->_request['object_id'];
		}

		$object = Inflector::singular($object);

		if ( ! $object_id)
		{
			// We're asking for a whole collection
			$model = NULL;
		}
		else
		{
			$model = Mundo::factory($object)
				->set('_id', new MongoId($object_id));

			if ($this->_request['collection'] == 'sites' AND $object != 'site')
			{
				// If the object we are after belongs to a site specifiy it.
				$model->set('s', new MongoId($this->_['collection_id']));
			}

			$model->load();

			if ( ! $model->loaded())
			{
				throw new App_API_Exception("Could not find requested resource", NULL, 404);
			}
		}

		return $model;
	}
	 */

} // end App_API_1
