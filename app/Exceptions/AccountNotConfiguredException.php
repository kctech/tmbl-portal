<?php

namespace App\Exceptions;

class AccountNotConfiguredException extends \Exception {

    public function __construct(public int $account_id, public ?string $driver='NO DRIVER FOUND') {}

}
