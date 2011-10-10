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
		if ( ! App_Auth::authorise_user(array('admin')))
		{
			throw new App_API_Exception("You do not have permission to view layouts", NULL, 403);
		}

		// This query runs every time in this method
		$query = array('t' => 'layout', 's' => App::$site->original('_id'));

		if ($layout_id = $this->request->param('resource_id'))
		{
			// If we're requesting a specific layout, add the ID
			$query += array('_id' => new MongoId($layout_id));
		}

		// We're requesting all fields, normally
		$fields = array();

		if ($this->request->query('fields'))
		{
			// See if we're requesting certain fields only
			$fields = explode(',', $this->request->query('fields'));

			foreach($fields as $key => $field)
			{
				if ($field == 'id')
				{
					// This is a standard
					$field = '_id';
				}
				else
				{
					// In layouts, field names are saved using the first character in the DB
					$field = substr($field, 0, 1);
				}

				// Re-set the field using it's database moniker
				$fields[$key] = $field;
			}
		}


		$coll = Mundo::$db->object;
		$result = $coll->find($query, $fields);

		$this->metadata += array(
			'total' => $result->count(),
			'offset' => 0,
			'limit' => 0,
		);

		$result = iterator_to_array($result);

		$this->content = array_values($result);
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
				'd' => $this->request->post('data'),
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

	/**
	 * The PUT method modifies a layout specified by the resource_id 
	 * parameter in the URI.
	 *
	 */
	public function action_put()
	{
		if( ! $this->request->param('resource_id'))
		{
			// We need a resource ID to update a resource
			throw new App_API_Exception("A resource ID must be passed in the URI", NULL, 400);
		}

		if ( ! App_Auth::authorise_user(array('admin')))
		{
			throw new App_API_Exception("You do not have permission to modify a layout", NULL, 403);
		}

		// Load our layout
		$layout = Mundo::factory('object')->set(array(
			'_id' => new MongoId($this->request->param('resource_id')),
			's' => App::$site->original('_id'),
		))->load();

		if ( ! $layout->loaded())
		{
			// Throw an error if we couldn't load it.
			throw new App_API_Exception("The resource with an ID of ':id' could not be loaded", array(':id', $this->request->param('resource_id')), 400);
		}

		if ($this->request->post('data') != $layout->original('d'))
		{
			// We're changing data so add the old data to history
			$compressed = gzdeflate($layout->original('d'), 7);

			$layout->push('h', array(new MongoDate(), new MongoBinData($compressed)));
		}

		$layout->set(array(
			'n' => $this->request->post('name'),
			'd' => $this->request->post('data'),
		));

		try
		{
			$db_result = $layout->update();
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
			// The data was saved OK, send a 201 OK response
			$this->response->status(200);

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
