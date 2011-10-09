<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API for account management
 *
 * @packaged App
 * @author Tony Holdstock-Brown
 **/
class Controller_API_1_Layouts extends Controller_API_1_Base
{

	public $content_type = 'layout';

	public function action_get()
	{

		$this->output = "GET";
	}

	public function action_post()
	{

		if($this->request->param('resource_id'))
		{
			// You cannot post to a specific resource.
			App_API::$error_content = array(
				'help' => "A POST request creates a new object and can only be performed on a collection. To update a resource use the PUT command. To create a new resource POST to a collection (remove the id '".$this->request->param('resource_id')."' from the url)."
			);
			throw new App_API_Exception("Cannot send a POST query to a specific resource", NULL, 400);
		}

		if ( ! App_Auth::authorise_user(array('admin')))
		{
			throw new App_API_Exception("You do not have permission to create a new layout", NULL, 403);
		}

		// Set our data
		$layout = Mundo::factory('object')
			->set(array(
				't' => 'layout',
				's' => App::$site->original('_id'),
				'n' => $this->request->post('name'),
				'd' => $this->request->post('content'),
			));

		try
		{
			$db_result = $layout->create();
		}
		catch(Validation_Exception $e)
		{
			// Set our help content in the error message to validation errors from our layout message file
			App_API::$error_content = array(
				'help' => $e->array->errors('layouts')
			);

			// Throw a 400 Bad Request
			throw new App_API_Exception("Could not validate data", NULL, 400);
		}

		if ($db_result['ok'] == 1 AND $db_result['err'] === NULL)
		{
			// The data was saved OK, send a 201 Created response
			$this->response->status(201);

			// We're only sending one result back
			$this->metadata += array(
				'total' => 1,
			);

			// Send back our postdata
			$this->content = array(
				$layout->original()
			);
		}
		else
		{
			// Throw an exception
			throw new App_API_Exception("An error occured when creating the layout.", NULL, 500);
		}
	}
}
