<?php defined('SYSPATH') or die('No direct script access.');

class App_API_V1_Response_XML extends App_API_V1_Response {


	protected $_content_type = 'application/xml';

	public function encode_response()
	{
		// Encode XML
		$output_xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');

		/**
		 * @todo Encode XML
		 */

		return $output_xml->asXML();
	}

}
