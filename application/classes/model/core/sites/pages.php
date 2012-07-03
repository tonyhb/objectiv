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
class Model_Core_Sites_Pages extends Mundo_Object
{
	protected $_collection = 'page';

	protected $_parent_coll = array(
		'uri' => 'sites',
		'mongo' => 'site'
	);

	protected $_schemaless = array(
		'page.data',
		'hist'
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
		'page.slug', // Despite data being schema-less this is here for clarification
		'page.name', // Despite data being schema-less this is here for clarification
		'page.data', // Page content
		'hist', // Data history
		'cche', // Cached 
		'site'
	);

	protected $_rules = array(
		'name' => array(
			array('not_empty'),
			array('regex', array(':value', '#^[\w\s]+$#')),
		),
	);

	protected $_read_only_fields = array(
		'_id',
		'hist',
		'site'
	);

	protected $_binary_fields = array(
		'binary' => array('hist')
	);


} // END class Model_Object
