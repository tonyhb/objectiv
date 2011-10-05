<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This is the object that manages viewable resources on a website.
 *
 * In a CMS, pages provide basic content editing for pages such as about us, in
 * which people commonly edit just text and images.
 *
 * However, pages also provide functionality to show object content, extending
 * the power of the CMS greatly. When combined with objects, a page can display
 * data directly from the object or automatically group objects together, such 
 * as a category page.
 *
 * Because pages are responsible for all viewable resources on a website, pages
 * also handle caching. Pages should cache rendered HTML for as long as 
 * possible, and automatically detect when content or an object has changed and
 * invalidate its cache.
 *
 * @package App
 * @author Tony Holdstock-Brown
 **/
class Model_Page extends Mundo_Object
{
	protected $_collection = 'page';

	protected $_schemaless = array(
		'd'
	);

	/**
	 * This page model literally acts as glue that binds several objects
	 * together to create a HTML page.
	 *
	 * Simple page content is stored in a 'Model_Content' object - this
	 * contains page metadata, the title tag and visual content.
	 *
	 * This model duplicates all of the object's data for non-cached page
	 * renders, but also stores a cached version of the fully rendered and
	 * minified HTML.
	 *
	 * Any time an object is updated that the page referneces, the cache is
	 * deleted and, upon a hit, a new cached copy is generated.
	 */
	protected $_fields = array(
		'_id',
		'n', // Page name
		's', // Page slug
		'o', // Array of key => values of page options (show in sitemap, visibility etc.)
		'd', // Array of objects used as data, in the form of ObjectId => Data
		'c', // Cached 
		'l', // Language (reserved for future use). 
	);

	protected $_rules = array(
		't' => array(
			array('not_empty'),
			array('regex', array(':value', '#^[\w\s]+$#')),
		),
		'n' => array(
			array('not_empty'),
		),
	);

} // END class Model_Object
