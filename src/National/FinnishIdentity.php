<?php

namespace Komakino\Identity\National;

use Komakino\Identity\Identity;
use Komakino\Identity\Traits\BirthdayTrait;

class FinnishIdentity extends Identity
{

    use BirthdayTrait;

    protected $validationPattern = '/^[\d]{6}[+-A][\d]{3}[0-9A-Y]$/';
    protected $parsePattern      = '/^(?<day>\w{2})(?<month>\w{2})(?<year>\w{2})(?<centuryHint>[+-A])(?<number>\w{3})(?<checkdigit>[0-9A-Y])/';
    protected $outputFormat      = '{day}{month}{year}{centuryHint}{number}{checkdigit}';

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

    protected function parse()
    {
        $matches = parent::parse();

        # The sign for the century is either + (1800–1899), - (1900–1999), or A (2000–2099).
        # The individual number ZZZ is odd for males and even for females and for people
        # born in Finland its range is 002-899 (larger numbers may be used in special cases). An example of a valid code is 311280-888Y.

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
