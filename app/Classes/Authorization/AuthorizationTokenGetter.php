<?php

namespace App\Classes\Authorization;

/**
 * Class AuthorizationTokenGetter
 * @package App\Classes\Authorization
 */
class AuthorizationTokenGetter
{

    /**
     * Test fixed token
     *
     * @return string
     */
    public function getFixedAuthToken()
    {
        return 'eyJpc3MiOiJDb2RleCBUZWFtIiwic3ViIjoiYXV0aCIsImV4cCI6MTUwNTQ2Nzc1Njg2OSwiaWF0IjoxNTA1NDY3MTUyMDY5LCJ1c2VyIjoxfQ';
    }

    /**
     * @return mixed|null
     */
    public function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * @return string|null
     */
    protected function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        }
        return $headers;
    }
}
