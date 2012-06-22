<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Helper class for API methods. 
 *
 * @package App
 * @subpackage API
 */
class App_API
{

	/**
	 * Instantiates and returns an instance of an API model.
	 *
	 * @var string      Class name of the model to instantiate
	 * @var string|int  API version to load
	 * @return mixed    Model
	 */
	public static function model($name, $version = NULL)
	{
		if ($version == NULL)
		{
			$version = self::$api_latest;
		}

		if (is_int($version) OR strpos($version, 'v') !== 0)
		{
			$version = 'v'.$version;
		}

		$model = 'Api_'.$version.'_'.$name;

		if ( ! Kohana::find_file('classes', str_replace('_', '/', 'Model_'.$model)))
		{
			throw new App_API_Exception("Could not find the model file ':model'", array(':model' => $name));
		}

		return Mundo::factory($model);
	}

	/**
	 * The latest version of the API
	 *
	 * @var string
	 */
	public static $api_latest = '1';

	/**
	 * Stores the available API versions
	 *
	 * @var array
	 */
	public static $api_versions = array('1');

	/**
	 * The client-requested format for the response of the API
	 *
	 * @var string  JSON or XML. The default is JSON.
	 */
	public static $format = 'json';

	/**
	 * This is set before throwing an API error to set any metadata
	 * necessary. 
	 *
	 * This allows us to provide error information over and above
	 * a single error message from getMessage()
	 */
	public static $error_metadata = array();

	/**
	 * This is set before throwing an API error to set content within the
	 * response.
	 *
	 * This allows us to provide error information over and above
	 * a single error message from getMessage()
	 */
	public static $error_content = array();

	/**
	 * This method allows communication with the API from internal requests
	 *
	 * We use this because the API throws exceptions which aren't caught
	 * in internal requests by default; this is a helper function to wrap
	 * requests in a try-catch block and always return a response class.
	 *
	 * The $options array allows us to set different header values in the
	 * request. For example, we could use the following:
	 *
	 * array(
	 *   'method' => 'PUT',
	 *   'post' => array('key' => 'value'),
	 *   'headers' => array('X-Header-Key' => 'value', 'Content-Type' => 'application/json')
	 * );
	 *
	 * Note that all keys in the $options array must be a method of the
	 * Request class.
	 *
	 * @param string  URI of the api method to call
	 * @param array   An array of options for the request.
	 * @param array   Post-data to send along with PUT and POST
	 * @param response
	 */
	public static function call($uri, $options = NULL)
	{
		try
		{
			// Create a new response using the supplied HTTP method
			$response = Request::factory($uri);

			if ($options)
			{
				// Loop through each request option
				foreach ($options as $key => $value)
				{
					// Test that the key is a method in the Response class
					if (is_callable(array($response, $key)))
					{
						// And call it with the value passed.
						call_user_func(array($response, $key), $value);
					}
				}
			}

			// Return the API response
			return $response->execute();
		}
		catch(App_API_Exception $e)
		{
			// There was an error with the API call. Return the API error.
			return self::error($e->getMessage(), $e->getCode());
		}
		catch(HTTP_Exception_404 $e)
		{
			// Explode our URI into sections for error management.
			$segmented_uri = explode('/', $uri);

			// Take the period from /api.json/1/
			$format = explode('.', $segmented_uri[0]);

			// Generate the 404 error.
			return self::http_404($segmented_uri[1], $format[1], $uri);
		}
		catch(Validation_Exception $e)
		{
			// ! NOTE: Validation errors should be caught in each action for custom validation help messages

			// Add validation errors to the help key in the error
			self::$error_content = array(
				'help' => $e->array->errors(''),
			);

			// Throw a 400 Bad Request 
			return self::error("Could not validate data", 400);
		}
		catch(Exception $e)
		{
			// Server error?
			if (Kohana::$environment === Kohana::DEVELOPMENT)
			{
				throw $e;
			}

			return self::error($e->getMessage(), 500);
		}
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
	public static function error($message, $status, $uri = NULL)
	{
		// Create a new response
		$response = new Response;

		if (empty(self::$error_metadata))
		{
			// Give us some basic information for prognosis
			self::$error_metadata = array(
				'uri' => Request::$current->uri(),
				'date' => gmdate("Y-m-d\TH:i:s\Z"),
			);
		}

		if (empty(self::$error_content))
		{
			// Add the status and description by default
			array_push(self::$error_content, array(
				'status' => $status,
				'description' => $message,
			));
		}
		else
		{
			// Overwrite the status and description anyway.
			self::$error_content = Arr::merge(array(
				'status' => $status,
				'description' => $message,
			), self::$error_content);

			// Wrap it in a container array for continuity with multiple content items
			self::$error_content = array(self::$error_content);
		}

		// Format the data
		$message = array(
			'contentType' => 'error',
			'metadata' => self::$error_metadata,
			'content' => self::$error_content,
		);

		// Set the status
		$response->status($status);

		// Encode the response
		self::encode_response($message, $response);

		return $response;
	}

	/**
	 * Encodes the API output and sets the content-type headers 
	 * according to the requested format.
	 *
	 * The $response argument allows us to call this method within the API
	 * for standard output; we need to pass $this->response in the after()
	 * method.
	 *
	 * It is included here because it is also used when throwing an error,
	 * and this solves code duplication issues.
	 *
	 * @param array   Data to encode and send to client
	 * @param string  JSON or XML depending on desired format
	 * @param string  Desired format or NULL for the client requested format
	 * @return void
	 */
	public static function encode_response($output, Response &$response, $format = NULL)
	{
		if ( ! $format)
		{
			// Use the client-requested format instead of an override
			$format = self::$format;
		}

		if ($format == 'json')
		{
			// Set our headers
			$response->headers('Content-Type', 'application/json');

			// Set the response to our encoded content
			$response->body(json_encode($output));

			// Don't return the response because it was passed by reference - theres no point.
			return;
		}

		$response->headers('Content-Type', 'application/xml');

		// Encode XML
		$output_xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');

		/**
		 * @todo Encode XML
		 */

		$response->body($output_xml->asXML());
	}

	/**
	 * This function checks Kohana 404 errors agains incorrect versions
	 * resources.
	 *
	 * It is called in index.php and App_API::call when an
	 * HTTP_Exception_404 is called.
	 */
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

}
