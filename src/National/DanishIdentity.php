<?php

namespace Komakino\Identity\National;

use Komakino\Identity\Identity;
use Komakino\Identity\Traits\BirthdayTrait;

class DanishIdentity extends Identity
{

    use BirthdayTrait;

    protected $validationPattern = '/^[\d]{6}-?[\d]{4}$/';
    protected $parsePattern      = '/^(?<day>\w{2})(?<month>\w{2})(?<year>\w{2})-?(?<sequence>(?<centuryHint>\w)\w{3})/';
    protected $outputFormat      = '{day}{month}{year}-{sequence}';

    protected $properties = [
        'century'  => null,
        'year'     => null,
        'month'    => null,
        'day'      => null,
        'sequence' => null,
        'gender'   => null,
        'birthday' => null,
    ];

    protected function parse()
    {
        $matches = parent::parse();

        $this->properties['gender']   = $this->properties['sequence'] % 2 ? 'male' : 'female';
        $this->properties['century']  = $this->calculateCentury($matches['centuryHint']);
        $this->properties['birthday'] = $this->composeBirthday();
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
