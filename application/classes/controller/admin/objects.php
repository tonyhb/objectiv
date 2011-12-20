<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Objects extends Controller_Admin {

	/**
	 * The model that we are controlling. Extended in places such as 
	 * Controller_Admin_Layouts to use the layout model
	 *
	 */
	protected $_object = 'objects';

	public function action_index()
	{
		if ( ! App_Auth::authorise_user(array('admin')))
		{
			$this->request->redirect($this->template->base);
		}

		// Get all of the objects
		$response = App::$api->call('GET', 'sites/'.App::$site->original('_id').'/'.$this->_object.'?fields=id,name');

		if ($response['contentType'] == 'error')
		{
			// Show the error status and return
			$this->template->body = $response['content'];
			return;
		}

		// List all objects
		$this->template->body = View::factory("admin/".$this->_object."/list")
			->set($this->_object, $response['content']);
	}

	public function action_new()
	{
		if ( ! App_Auth::authorise_user(array('admin')))
		{
			$this->request->redirect($this->template->base);
		}

		// By default there are no validation errors
		$errors = array();

		if ($this->request->post('token') == Cookie::get('csrf') && $this->request->post('token') != NULL)
		{
			// Prepare the postdata by removing the CSRF token and putting 
			// layout defaults for the object
			unset($_POST['token']);

			$_POST['site'] = App::$site->original('_id');
			$_POST['type'] = $this->_object;

			try
			{
				$response = App::$api->call('PUT', 'sites/'.App::$site->original('_id').'/'.$this->_object);
			}
			catch (Validation_Exception $e)
			{
				$errors = $e->array->errors($this->_object);
			}

			if ($response['metadata']['status'] == 201)
			{
				$this->request->redirect($this->template->base.'/'.$this->_object.'/edit/'.$response['content']['_id'].'?notice=created');
			}
			else
			{
				// @todo Error handling
				$errors = array('Could not save the layout');
			}
		}

		// Show the new layout form
		$this->template->body = View::factory("admin/".$this->_object."/new")->set(array(
			'data' => $this->request->post(),
			'errors' => $errors
		));
	}

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
					$notices = Kohana::message($this->_object, 'created');
					break;
				case 'saved':
					$notices = Kohana::message($this->_object, 'saved');
					break;
			}
		}

		if ($this->request->post() && $this->request->post('token') == App::$user->original('csrf'))
		{
			$_POST['_id'] = $this->request->param('params');
			$_POST['type'] = $this->_object;

			unset($_POST['token']);

			// Valid CSRF token and the form has been posted. Run the API call to edit the layout
			try
			{
				$response = App::$api->call('POST', 'sites/'.App::$site->original('_id').'/'.$this->_object.'/'.$this->request->param('params'));
				$data = $response['content'];
				$notices = Kohana::message($this->_object, 'saved');
			}
			catch (Validation_Exception $e)
			{
				$errors = $e->array->errors($this->_object);
			}
		}
		else
		{
			// Get the layout
			$response = App::$api->call('GET', 'sites/'.App::$site->original('_id').'/'.$this->_object.'/'.$this->request->param('params'));
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

		$this->template->body = View::factory("admin/".$this->_object."/edit")
			->set('data', $data)
			->set('errors', $errors)
			->set('notices', $notices);

	}
}
