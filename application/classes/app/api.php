<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Helper class for API methods in the controller
 *
 * @package App
 * @subpackage API
 */
class App_API
{
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
	 * @param array   Data to encode and send to client
	 * @param string  JSON or XML depending on desired format
	 * @param string  Desired format or NULL for the client requested format
	 * @return void
	 */
	public static function encode_output($output, Response &$response, $format = NULL)
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

			return json_encode($output);
		}

		$response->headers('Content-Type', 'application/xml');

		// Encode XML
		$output_xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');

		/**
		 * @todo Encode XML
		 */

		return $output_xml->asXML();
	}

	/**
	 * This method is called from index.php when the API throws an error.
	 *
	 * This encodes the error message according to the requested format and
	 * displays it to the user.
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

		$message = self::encode_output($message, $response);

		return $response->status( (int) $status)
			->body($message);
	}

}
