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
	 * The main content in the API response
	 *
	 * @var mixed
	 */
	public $content;

	/**
	 * Metadata included in the API response
	 *
	 * @var array
	 */
	public $metadata;

	/**
	 * The content type of the response
	 *
	 * @var string
	 */
	public $content_type;

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

		// In development, no OAuth2 server so just say it's the dev user. Sort this shit out!

		if (Kohana::$environment === Kohana::DEVELOPMENT AND ! App::$user)
		{
			App::$user = Mundo::factory('user')
				->set('_id', new MongoId('4e7fa54fef966fd75d000007'))
				->load();
		}

		if ($this->request->param('collection') == 'sites')
		{
			App::$site = Mundo::factory('site')
				->set('_id', new MongoId($this->request->param('collection_id')))
				->load();

			if ( ! App::$site->loaded() OR ! App_auth::authorise_user(array('admin')))
			{
				// We couldn't find the site or don't have privileges, so add a help message and throw an error
				App_API::$error_content = array('help' => 'Double check the site ID and ensure you have sufficient privileges for this operation.');
				throw new App_API_Exception("Could not load requested site ':id'", array(':id' => $this->request->param('collection_id')), 404);
			}
		}

		$this->metadata = array(
			'date' => gmdate("Y-m-d\TH:i:s\Z"),
		);
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
		App_API::encode_response(array(
			'contentType' => $this->content_type,
			'metadata' => $this->metadata,
			'content' => $this->content,
		), $this->response);
	}

} // END class API_1_Base
