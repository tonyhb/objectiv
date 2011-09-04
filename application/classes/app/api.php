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
	 * This method is called when the API throws an API exception.
	 *
	 * This encodes the error message according to the requested format and
	 * returns a response class with the appropriate headers and response.
	 *
	 * Note that this handles both server (5xx) and client (4xx) errors
	 *
	 * @param  string   Message from the thrown exception
	 * @param  int      HTTP Status code to send
	 * @return Response
	 */
	public static function error($message, $status)
	{
		// Create a new response
		$response = new Response;

		// Format the data
		$message = array('status' => 'error', 'message' => $message);

		// Set the status
		$response->status($status);

		// Encode the response
		self::encode_response($message, $response);

		return $response;
	}

	/**
	 * This method allows communication with the API from internal requests
	 *
	 * We use this because the API throws exceptions which aren't caught
	 * in internal requests by default; this is a helper function to wrap
	 * requests in a try-catch block and always return a response class.
	 *
	 * @param string  URI of the api method to call
	 * @param string  REST HTTP mode to use (GET, PUT, POST, DELETE)
	 * @param array   Post-data to send along with PUT and POST
	 * @param response
	 */
	public static function call($uri, $method = 'GET', $postdata = NULL)
	{
		try
		{
			// Create a new response using the supplied HTTP method
			$response = Request::factory($uri)->method($method);

			if ($postdata)
			{
				// Only set POSTDATA if it is supplied
				$response->post($postdata);
			}

			// Return the API response
			return $response->execute();
		}
		catch(App_API_Exception $e)
		{
			// There was an error with the API call. Return the API error.
			return self::error($e->getMessage(), $e->getCode());
		}
	}
}
