<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Manages layouts within a site
 *
 **/
class Controller_Admin_Layouts extends Controller_Admin
{

	/**
	 * List all layouts we have
	 *
	 */
	public function action_index()
	{
		if ( ! App_Auth::authorise_user(array('admin')))
		{
			$this->request->redirect($this->template->base);
		}

		// Get all of the layouts
		$response = App::$api->call('GET', 'sites/'.App::$site->original('_id').'/objects?fields=id,name&search=type:layout');

		if ($response['contentType'] == 'error')
		{
			// Show the error status and return
			$this->template->body = $response['content'];
			return;
		}

		// List all layouts
		$this->template->body = View::factory("admin/layouts/list")
			->set('layouts', $response['content']);
	}

	/**
	 * Create a new layout
	 *
	 */
	public function action_new()
	{
		if ( ! App_Auth::authorise_user(array('admin')))
		{
			$this->request->redirect($this->template->base);
		}

		// By default there are no validation errors
		$errors = array();

		if ($this->request->post('token') == App::$user->original('csrf'))
		{
			// If the postdata's CSRF passed call the API to create a layout
			$response = App_API::call('/api.json/1/sites/'.App::$site->original('_id').'/layouts', array(
				'method' => 'POST',
				'post' => $this->request->post(),
			));

			if ($response->status() == 201)
			{
				// Redirect to the new layout
				$content = json_decode($response->body());
				$this->request->redirect($this->template->base.'/layouts/edit/'.$content->content[0]->_id->{'$id'});
			}
			else
			{
				// Get our validation errors
				$content = json_decode($response->body());
				$errors = $content->content[0]->help;
			}
		}

		// Show the new layout form
		$this->template->body = View::factory("admin/new_layout")->set(array(
			'data' => $this->request->post(),
			'errors' => $errors
		));
	}

	/**
	 * Edit a layout
	 *
	 */
	public function action_edit()
	{
		if ( ! App_Auth::authorise_user(array('admin')))
		{
			// Unauthorised, redirect to the dashboard
			$this->request->redirect($this->template->base);
		}

		if ($this->request->post('token') == App::$user->original('csrf'))
		{
			// Valid CSRF token and the form has been posted. Run the API call to edit the layout
			$response = App_API::call('/api.json/1/sites/'.App::$site->original('_id').'/layouts/'.$this->request->param('params'), array(
				'method' => 'PUT',
				'post' => $this->request->post(),
			));

			if ($response->status() == 200)
			{
				// Redirect to the new layout
				$content = json_decode($response->body());

				$this->template->body = View::factory("admin/edit_layout")
					->set("data", $content->content[0])
					->set('errors', array());
			}
			else
			{
				// Get our validation errors
				$content = json_decode($response->body());
				$errors = $content->content[0]->help;
			}
		}
		else
		{
			// Get the layout
			$response = App_API::call('/api.json/1/sites/'.App::$site->original('_id').'/layouts/'.$this->request->param('params'));

			// Decode the response
			$body = json_decode($response->body());

			if ( ! $response->status() == 200)
			{
				// Show the error status and return
				$this->template->body = $body->content[0]->description;
				return;
			}

			$this->template->body = View::factory("admin/edit_layout")
				->set('data', $body->content[0])
				->set('errors', array());
		}

	}

} // END class Controller_Admin_Layouts extends Controller_Admin
