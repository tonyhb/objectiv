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
	 * Format the response should be encoded in
	 *
	 * @var string
	 */
	public $format;

	/**
	 * Ran before every API call
	 *
	 */
	public function before()
	{
		// Check if we're calling this controller's error action
		if ($this->request->controller() != 'base')
		{
			// Internal requests aren't mapped by the routing logic; set
			$this->request->action($this->request->method());
		}

		$this->format = $this->request->param('format');
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
		if ($this->format == 'json')
		{
			// Set our headers
			$this->response->headers('Content-Type', 'application/json');

			$this->response->body(json_encode($this->output));
			return;
		}

		$this->response->headers('Content-Type', 'application/xml');

		// Encode XML
		$output_xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');

		/**
		 * @todo Encode XML
		 */

		$this->response->body($output_xml->asXML());
	}

	/**
	 * This method is called when the API throws an error.  This encodes 
	 * the error message according to the requested format and displays it 
	 * to the user.
	 *
	 * We could not throw exceptions because internal API requests from
	 * the admin panel stop execution when they are raised.
	 *
	 * Note that this handles both server (5xx) and client (4xx) errors
	 *
	 * @param  string   Message from the thrown exception
	 * @param  int      HTTP Status code to send
	 * @return Response
	 */
	public function error($message, $status)
	{
		$this->output = array('status' => 'error', 'message' => $message);

		$this->response->status($status);
	}

	/**
	 * This action is only called when an error is caught in index.php.
	 * The code in bootstrap.php ensures that this method is not callable 
	 * to the outside world.
	 *
	 */
	public function action_error()
	{
		$this->error($this->request->post('_app_error'), $this->request->post('_app_error_status'));
	}

} // END class API_1_Base
