<?php

namespace Komakino\Identity\National;

use Komakino\Identity\Identity;
use Komakino\Modulus11\Modulus11;
use Komakino\Identity\Traits\BirthdayTrait;

class DanishIdentity extends Identity
{

    use BirthdayTrait;

    protected $properties = [
        'century'  => null,
        'year'     => null,
        'month'    => null,
        'day'      => null,
        'sequence' => null,
        'gender'   => null,
        'birthday' => null,
    ];

    function __construct($code) {
        $this->validationPattern = strtr("/^:day::month::year:-?[\d]{4}$/", $this->macros);
        $this->parsePattern      = strtr("/^%day%%month%%year%-?(?<sequence>(?<centuryHint>\d)\d{3})/", $this->macros);
        $this->outputFormat      = '{day}{month}{year}-{sequence}';

        parent::__construct($code);
    }

    protected function parse()
    {
        $matches = parent::parse();

        $this->properties['gender']   = $this->properties['sequence'] % 2 ? 'male' : 'female';
        $this->properties['century']  = $this->calculateCentury($matches['centuryHint']);
        $this->properties['birthday'] = $this->composeBirthday();
    }

    protected function validate()
    {
        $number = $this->format();
        return Modulus11::validate($number);
    }

    private function calculateCentury($hint)
    {
        extract($this->properties);
        switch(true){
            case $hint <= 3:
                return "19";
            case $hint == 4 || $hint == 9:
                if($year <= 37) return "20";
                else            return "19";
            case $hint >= 5 && $hint <= 8:
                if($year <= 57) return "20";
                else            return "18";
            default:
                return null;
        }
    }
}
