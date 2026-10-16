<?php

echo json_encode([
    'welcome_message' => $_SERVER['API_WELCOME'],

    'status' => 1,
    'message' => 'Here is list of API Services Available on ' .$_SERVER['APP_NAME']. ' !',

    'data' => [
        [   
            'name' => 'login_api',
            'alias' => 'industry_login',
            'description' => 'Industry Login Gateway',
            'link' => $_SERVER['SERVER_NAME'].  '\api.php?request=industry_login',
            'request_method' => 'POST',
            'required_parameter' => [
                'token' => [ 'send_method' => 'POST'], 
                'username' => [ 'send_method' => 'POST'], 
                'password' => [ 'send_method' => 'POST']
                ]
        ],

        [   
            'name' => 'company_list',
            'description' => 'Give All Company Data Available',
            'link' => $_SERVER['SERVER_NAME'].  '\api.php?request=company_list',
            'request_method' => 'POST',
            'required_parameter' => ['token' => [ 'send_method' => 'POST']]
        ],

        [   
            'name' => 'company_detail',
            'description' => 'Get Company Data based on ID Company',
            'link' => $_SERVER['SERVER_NAME'].  '\api.php?request=company_detail',
            'request_method' => 'POST',
            'required_parameter' => [ 
                'token' => [ 'send_method' => 'POST'], 
                'id_company' => [ 'send_method' => 'GET']
            ]
        ],

        [   
            'name' => 'company_user',
            'description' => 'Get All User Company Data based on ID Company',
            'link' => $_SERVER['SERVER_NAME'].  '\api.php?request=company_user',
            'request_method' => 'POST',
            'required_parameter' => [ 
                'token' => [ 'send_method' => 'POST'], 
                'id_company' => [ 'send_method' => 'GET']
            ]
        ],

        [   
            'name' => 'job',
            'description' => 'Give All Job Offer of Current Student',
            'link' => $_SERVER['SERVER_NAME'].  '\api.php?request=job',
            'request_method' => 'GET',
            'required_parameter' => [
                'nim' => [ 'send_method' => 'GET']
            ]
        ],


        [   
            'name' => 'job_detail',
            'description' => 'Get Job Offer Data based on ID Offer',
            'link' => $_SERVER['SERVER_NAME'].  '\api.php?request=job_detail',
            'request_method' => 'GET',
            'required_parameter' => [
                'id_company' => [ 'send_method' => 'GET']
            ]
        ],

        [   
            'name' => 'job_list',
            'description' => 'Get Job Offer Data Available',
            'link' => $_SERVER['SERVER_NAME'].  '\api.php?request=job_list',
            'request_method' => 'GET',
            'required_parameter' => [
                'id_company' => [ 'send_method' => 'GET']
            ]
        ],

        
    ],


], JSON_PRETTY_PRINT);

http_response_code(200);