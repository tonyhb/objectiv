<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Manages layouts within a site
 *
 **/
class Controller_Admin_Layouts extends Controller_Admin
{

	public function action_index()
	{
		// List all layouts
		$this->template->body = View::factory("admin/list_layouts");
	}

	public function action_new()
	{
		if ( ! App_Auth::authorise_user(array('admin')))
		{
			$this->request->redirect($this->template->base);
		}

		// By default there are no validation errors
		$errors = '';

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

} // END class Controller_Admin_Layouts extends Controller_Admin
