<?php

/**
* Tests the error handling capabilities of the API, ensuring
* they perform correctly.
*
* @package App
* @subpackage API
* @category Tests
* @author Tony Holdstock-Brown
*/
class API_Error_Handling extends PHPUnit_Framework_TestCase {

	/**
	 * Ensure that the API requests XML/JSON formatting
	 */
	public function test_invalid_format_request_throws_error()
	{
		$response = Request::factory('api.foobar/1')
			->execute();

		$this->assertEquals(400, $response->status());

		$expected_message = array(
			'contentType' => 'error',
			'metadata' => array(
				'uri' => 'api.foobar/1',
			),
			'content' => array(
				array(
					'status' => 400,
					'description' => "Unknown encoding type 'foobar'. Supported response encoding types are JSON or XML."
				)
			),
		);

		$this->assertEquals($expected_message, $response->body());
	}

	/**
	 * Ensure that the API throws the correct message when an
	 * unknown API version is requested
	 */
	public function test_invalid_api_version_request_throws_error()
	{
		$response = Request::factory('api.json/foobar')
			->execute();

		$this->assertEquals(400, $response->status());

		$expected_message = array(
			'contentType' => 'error',
			'metadata' => array(
				'uri' => 'api.json/foobar',
			),
			'content' => array(
				array(
					'status' => 400,
					'description' => "Unknown API version 'foobar'. Supported versions are: '1'."
				)
			),
		);

		$this->assertEquals($expected_message, $response->body());
	}

	/**
	 * Ensure invalid authentication fails
	 */
	public function test_api_authentication_with_invalid_credentials_throws_error()
	{
		$response = Request::factory('api.json/1/accounts')
			->method('get')
			->headers('api-key', 'foobar')
			->execute();

		$this->assertEquals(401, $response->status());

		$expected_message = array(
			'contentType' => 'error',
			'metadata' => array(
				'uri' => 'api.json/1/accounts',
				'API key' => 'foobar',
			),
			'conent' => array(
				array(
					'status' => 401,
					'description' => 'You are not authorised for this request. Please ensure your API key is correct and you are using the correct API secret.',
				)
			),
		);

		$this->assertEquals($expected_message, $response->body());
	}


}
