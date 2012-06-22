<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This is the model for layout objects in the CMS. Specifying this model allows us 
 * to search for and show layout objects in the REST API.
 *
 * @package App
 * @author Tony Holdstock-Brown
 */
class Model_Core_Sites_Themes_Layouts extends Model_CSS {

	protected $_collection = 'layout';

}
