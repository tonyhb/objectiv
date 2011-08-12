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

	public function before()
	{
		// Internal requests aren't mapped by the routing logic; set
		// the correct action
		$this->request->action($this->request->method());
	}

} // END class API_1_Base
