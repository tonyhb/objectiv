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


	public static $test_url = 'http://epithet.local/';

	/**
	 * Ensure that the API requests XML/JSON formatting
	 *
	 * @test
	 */
	public function test_invalid_format_request_throws_error()
	{
		$response = Request::Factory(self::$test_url.'api.foobar/1')->execute();

		$this->assertEquals(400, $response->status());

		$expected_message = array(
			'content_type' => 'error',
			'metadata' => array(
				'uri' => 'api.foobar/1',
			),
			'content' => array(
				array(
					'status' => 400,
					'description' => "Unknown encoding type 'foobar'. Supported response encoding types are JSON and XML."
				)
			),
		);

		$this->assertEquals(json_encode($expected_message), $response->body());
	}

	/**
	 * Ensure that the API throws the correct message when an
	 * unknown API version is requested
	 *
	 * @test
	 */
	public function test_invalid_api_version_request_throws_error()
	{
		$response = Request::factory(self::$test_url.'api.json/foobar')->execute();

		$this->assertEquals(400, $response->status());

		$expected_message = array(
			'content_type' => 'error',
			'metadata' => array(
				'uri' => 'api.json/foobar',
			),
			'content' => array(
				array(
					'status' => 400,
					'description' => "Unknown API version 'foobar'. Supported versions are: 1"
				)
			),
		);

		$this->assertEquals(json_encode($expected_message), $response->body());
	}

	/**
	 * Ensures the API returns a valid response when accessing invalid
	 * objects
	 *
	 * @test
	 */
	public function test_invalid_object_type_throws_error()
	{
		$response = Request::factory(self::$test_url.'api.json/1/foobar')->execute();

		$this->assertEquals(404, $response->status());

		$expected_message = array(
			'content_type' => 'error',
			'metadata' => array(
				'uri' => 'api.json/1/foobar',
			),
			'content' => array(
				array(
					'status' => 404,
					'description' => "The requested resource 'foobar' was not found."
				)
			),
		);

		$this->assertEquals(json_encode($expected_message), $response->body());
	}

	/**
	 * Ensure invalid API Key fails
	 *
	 * @test
	 */
	public function test_api_authentication_with_invalid_credentials_throws_error()
	{
		// Not yet implemented
		return;

		$response = Request::factory(self::$test_url.'api.json/1/accounts')
			->headers(array('X-API-Key' => 'foobar'))
			->execute();

		$this->assertEquals(401, $response->status());

		$expected_message = array(
			'content_type' => 'error',
			'metadata' => array(
				'uri' => 'api.json/1/accounts',
				'headers' => array(
					'X-API-Key' => 'foobar'
				),
			),
			'conent' => array(
				array(
					'status' => 401,
					'description' => 'You are not authorised for this request. Please ensure your API key and API secret is correct, and you are including a current timestamp with your request.',
				)
			),
		);

		$this->assertEquals($expected_message, $response->body());
	}

	/**
	 * @todo Test invalid API Secret
	 */

	/**
	 * @todo Test invalid HMAC
	 */

	/**
	 * @todo Test request with old timestamp
	 */

	/**
	 * @todo Test invalid object types
	 */
}
