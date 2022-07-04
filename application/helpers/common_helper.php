<?php

function get_ws_data($url, $input, $request_type, $additional_data = []) {
    $CI = & get_instance();
    $CI->load->library('Curlphp');
    $curl = new Curlphp();
    $curl->setUrl($url);

    if (parse_url($url, PHP_URL_SCHEME) == 'https') {
        $curl->setSSL(false, false);
    }
    $curl->setRequestTimeout(300);
    $str = '';

	switch ($request_type) {
	    case 'register_request':
	        $str = json_encode($input);
            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('Content-Length', strlen($str));
            if (isset($additional_data['authorization']))
            {
                $curl->http_header('Authorization', $additional_data['authorization']);
            }

        case 'login_request':
            $str = json_encode($input);
            $curl->setData($str);
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('Content-Length', strlen($str));
            // if (isset($additional_data['authorization']))
            // {
            // 	$curl->http_header('x-api-key', $additional_data['authorization']);
            //     // $curl->http_header('Authorization', $additional_data['authorization']);
            // }
        break;
	}

	$curl->setMethod((isset($additional_data['request_method'])) ? strtolower($additional_data['request_method']) : 'post');

	$request_data = $additional_data['request_data'];

    $start_time = date('Y-m-d H:i:s');
    $response = $curl->executeCurl();
    $end_time = date('Y-m-d H:i:s');
    $curl_info = $curl->getCurlInfo();

    // echo "<pre>";
    // print_r($curl_info);exit;

	$some_data = [
		'section' => $request_data['section'],
		'url' => $request_data['url'],
		'method' => $request_data['method'],
		'request' => $str,
		'response' => $response['response'],
		'response_time' => $curl_info['total_time'],
		'created_at' => date('Y-m-d H:i:s')
	];

    $CI->db->reconnect();
    $CI->db->insert('webservice_request_response_data', $some_data);

	return $response['response'];
}

?>
