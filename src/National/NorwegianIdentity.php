<?php

namespace Komakino\Identity\National;

use Komakino\Identity\Identity;
use Komakino\Modulus11\Modulus11;

class NorwegianIdentity extends Identity
{

    protected $properties = [
        'century'     => null,
        'year'        => null,
        'month'       => null,
        'day'         => null,
        'number'      => null,
        'gender'      => null,
        'birthday'    => null,
        'checkdigits' => null,
        'd-number'    => null,
        'h-number'    => null,
    ];

    function __construct($code) {
        $macros = array_merge($this->macros,[
            ':day:'     => '(?:[04][1-9]|[1-25-6][0-9]|[37][0-1])',     # 01-31, 41-71, D-nummer
            '%day%'     => '(?<day>[04][1-9]|[1-25-6][0-9]|[37][0-1])',
            ':month:'   => '(?:[04][0-9]|[15][0-2])',                   # 01-12, 41-52, H-nummer
            '%month%'   => '(?<month>[04][0-9]|[15][0-2])',

        ]);

        $this->validationPattern = strtr("/^:day::month::year:\d{5}$/", $macros);
        $this->parsePattern      = strtr("/^%day%%month%%year%(?<number>\d{3})(?<checkdigits>\d{2})/", $macros);
        $this->outputFormat      = '{day}{month}{year}{number}{checkdigits}';

        parent::__construct($code);
    }

    private function modulus11($number,$factors)
    {
        for($i=0, $sum = 0;$i<count($factors);$i++) $sum += $number{$i} * $factors[$i];
        return $sum % 11 ? 11 - $sum % 11 : 0;
    }

    protected function validate(){
        $number = $this->format();
        if(Modulus11::calculate(substr($number,0,9), [2,5,4,9,8,1,6,7,3]) != $number{9}) return false;
        return Modulus11::validate($number);
    }

    protected function parse()
    {
        $matches = parent::parse();

        $this->properties['century']  = $this->calculateCentury();
        $this->properties['gender']   = $matches['number'] % 2 ? 'male' : 'female';
        $this->properties['d-number'] = $matches['day'] > 40;
        $this->properties['h-number'] = $matches['month'] > 40;
        $this->properties['birthday'] = $this->composeBirthday();
    }

    private function composeBirthday(){
        extract($this->properties);
        $day   -= $day > 40 ? 40 : 0;
        $month -= $month > 40 ? 40 : 0;
        return date_create("{$century}{$year}-{$month}-{$day}");
    }

    public function calculateCentury()
    {
        extract($this->properties);
        if($number < 500) return 19;
        elseif($number >= 500 && $number < 750 && $year >= 54) return 18;
        elseif($number >= 500 && $year < 40) return 20;
        elseif($number >= 900 && $year >= 40) return 19;
    }
}
