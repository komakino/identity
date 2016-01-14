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
