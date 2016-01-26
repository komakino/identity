<?php

namespace Komakino\Identity\Errors;

class IdentityInvalidFormatException extends \Exception
{
    function __construct($code) {
        parent::__construct("Invalid format '{$code}'");
    }
}
