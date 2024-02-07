<?php

namespace App\Exceptions;

class DriverNotSetException extends \Exception {

    public function __construct() {
        $this->message = 'The protected driver property has not been set on the custom provider class. This needs to match the "provider" column in sso_client_credentials';
    }

}
