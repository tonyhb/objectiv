<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The base class for API methods which deal with authentication, authorisation
 * and response handling
 *
 * @packaged App
 * @author Tony Holdstock-Brown
 **/
class Controller_API_1_Base extends Controller
{
	/**
	 * The response from the API call
	 *
	 * @var mixed
	 */
	public $output;

	/**
	 * Ran before every API call
	 *
	 */
	public function before()
	{
		// Internal requests aren't mapped by the routing logic; set
		$this->request->action($this->request->method());

		// Set our request format
		App_API::$format = $this->request->param('format');

		// Ensure the format is valid
		if (App_API::$format != 'json' AND App_API::$format != 'xml')
		{
			// By default, echo a JSON response (less taxing)
			App_API::$format = 'json';
			throw new App_API_Exception("Unknown encoding type '".$this->request->param('format')."'. Supported response encoding types are JSON and XML.", NULL, 400);
		}
	}

	/**
	 * Ran after every API call. Encodes the API output and sets the 
	 * content-type headers according to the requested format.
	 *
	 * @param array   Data to encode and send to client
	 * @param string  JSON or XML depending on desired format
	 * @param string  Desired format or NULL for the client requested format
	 * @return void
	 */
	public function after()
	{
		// Encode our response body according to the requested format
		App_API::encode_response($this->output, $this->response);
	}

} // END class API_1_Base
