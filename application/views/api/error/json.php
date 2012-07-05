<?php

echo json_encode(array(
	'error'    => $message,
	'medatada' => array(
		'status'        => $status,
		'uri'           => Request::$current->uri(),
		'request_time'  => gmdate("Y-m-d\TH:i:s\Z", $_SERVER['REQUEST_TIME']),
		'response_time' => gmdate("Y-m-d\TH:i:s\Z", time()),
	)
));
