<?php

namespace Komakino\Identity\National;

use Komakino\Identity\Identity;
use Komakino\Identity\Traits\BirthdayTrait;

class FinnishIdentity extends Identity
{

    use BirthdayTrait;

    protected $properties = [
        'day'         => null,
        'month'       => null,
        'year'        => null,
        'centuryHint' => null,
        'number'      => null,
        'checkdigit'  => null,
        'century'     => null,
        'gender'      => null,
        'birthday'    => null,
    ];

    function __construct($code) {
        $this->validationPattern = strtr("/^:day::month::year:[+-A][\d]{3}[0-9A-Y]$/", $this->macros);
        $this->parsePattern      = strtr("/^%day%%month%%year%(?<centuryHint>[+-A])(?<number>\d{3})(?<checkdigit>[0-9A-Y])/", $this->macros);
        $this->outputFormat      = '{day}{month}{year}{centuryHint}{number}{checkdigit}';

        parent::__construct($code);
    }

    protected function parse()
    {
        $matches = parent::parse();

        $this->properties['gender'] = $matches['number'] % 2 ? 'male' : 'female';
        $this->properties['century'] = $this->calculateCentury($matches['centuryHint']);
    }

    public function validate()
    {
        extract($this->properties);
        $base      = (int)"{$day}{$month}{$year}{$number}";
        $remainder = $base % 31;
        $check     = substr("0123456789ABCDEFHJKLMNPRSTUVWXY", $remainder, 1);

        return $check == $checkdigit;
    }

    private function calculateCentury($centuryHint){
        switch($centuryHint){
            case '-': return 19;
            case '+': return 18;
            case 'A': return 20;
        }
    }
}
