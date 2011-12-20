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
		$response = App::$api->call('GET', 'sites/'.App::$site->original('_id').'/layouts?fields=id,name');

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
			// Prepare the postdata by removing the CSRF token and putting 
			// layout defaults for the object
			unset($_POST['token']);

			$_POST['type'] = 'layout';
			$_POST['site'] = App::$site->original('_id');

			try
			{
				$response = App::$api->call('PUT', 'sites/'.App::$site->original('_id').'/layouts');
			}
			catch (Validation_Exception $e)
			{
				$errors = $e->array->errors('layouts');
			}

			if ($response['metadata']['status'] == 201)
			{
				$this->request->redirect($this->template->base.'/layouts/edit/'.$response['content']['_id'].'?notice=created');
			}
			else
			{
				// @todo Error handling
				$errors = array('Could not save the layout');
			}
		}

		// Show the new layout form
		$this->template->body = View::factory("admin/layouts/new")->set(array(
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

		$data = $errors = array();
		$notices = '';

		if (isset($_GET['notice']))
		{
			switch($_GET['notice'])
			{
				case 'created':
					$notices = 'The layout has been created';
					break;
				case 'saved':
					$notices = 'The layout has been saved';
					break;
			}
		}

		if ($this->request->post('token') == App::$user->original('csrf'))
		{
			$_POST['_id'] = $this->request->param('params');

			unset($_POST['token']);

			// Valid CSRF token and the form has been posted. Run the API call to edit the layout
			try
			{
				$response = App::$api->call('POST', 'sites/'.App::$site->original('_id').'/layouts/'.$this->request->param('params'));
				$data = $response['content'];
				$notices = 'The layout has been saved';
			}
			catch (Validation_Exception $e)
			{
				$errors = $e->array->errors('layouts');
			}
		}
		else
		{
			// Get the layout
			$response = App::$api->call('GET', 'sites/'.App::$site->original('_id').'/layouts/'.$this->request->param('params'));
			$data = $response['content'];
		}

		// Are we looking at past data?
		if (isset($_GET['history']))
		{
			if (isset($data['hist']) && array_key_exists($_GET['history'], $data['hist']))
			{
				$history_key = $_GET['history'];
				$data['data'] = App::$api->inflate_bindata($data['hist'][$history_key][1]);
			}
			else
			{
				$errors = 'The requested history item could not be loaded';
			}
		}


		$this->template->body = View::factory("admin/layouts/edit")
			->set('data', $data)
			->set('errors', $errors)
			->set('notices', $notices);

	}

} // END class Controller_Admin_Layouts extends Controller_Admin
