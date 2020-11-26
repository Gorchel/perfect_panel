<?php

$router->get('/api/auth', ['uses' => 'MainController@auth']);
$router->get('/api/v1', ['middleware' => 'check_authorization'], ['uses' => 'MainController@entryPoint']);

