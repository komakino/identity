<?php

namespace Komakino\Identity\National;

use Komakino\Luhn\Luhn;
use Komakino\Identity\Identity;
use Komakino\Identity\Traits\BirthdayTrait;

class SwedishIdentity extends Identity
{
    use BirthdayTrait;

    protected $validationPattern = '/^(?:[\d]{2})?[\d]{6}[-+]?[\d]{4}$/';
    protected $parsePattern      = '/^(?<century>\w{2})?(?<year>\w{2})(?<month>\w{2})(?<day>\w{2})(?<centuryHint>[-+]?)(?<locality>\w{2})(?<number>\w{1})(?<checkdigit>\w{1})/';
    protected $outputFormat      = '{year}{month}{day}{centuryHint}{locality}{number}{checkdigit}';

    protected $properties = [
        'type'        => null,
        'century'     => null,
        'centuryHint' => null,
        'year'        => null,
        'month'       => null,
        'day'         => null,
        'locality'    => null,
        'county'      => null,
        'number'      => null,
        'gender'      => null,
        'checkdigit'  => null,
        'birthday'    => null,
        'temporary'   => false,
    ];


    protected function validate(){
        return Luhn::validate($this->format());
    }

    protected function parse()
    {
        $matches = parent::parse();

        if($this->properties['day'] >= 60){
            $this->properties['day'] -= 60;
            $this->properties['temporary'] = true;
        }

        if(!$matches['centuryHint']){
            $this->properties['centuryHint'] = '-';
        }

        if($this->properties['month'] > 12){
            $this->properties['type']       = 'organization';
            $this->properties['century']    = '16';
        } else {
            $this->properties['type']       = 'person';
            $this->properties['century']    = $matches['century'] ?: $this->calculateCentury($matches['centuryHint']);
            $this->properties['gender']     = $this->properties['number'] % 2 ? 'male' : 'female';
            $this->properties['birthday']   = $this->composeBirthday();
            $this->properties['county']     = $this->parseCounty();
        }

    }

    private function calculateCentury($centuryHint)
    {
        extract($this->properties);
        $thisYear = date('y');
        if($year <= $thisYear){
            $century = "20";
        } else {
            $century = "19";
        }
        if($centuryHint == '+') $century -= 1;

        return (string)$century;
    }

    private function parseCounty(){
        extract($this->properties);
        $countyMap = [
            "Stockholms län"          => [00,13],
            "Uppsala län"             => [14,15],
            "Södermanlands län"       => [16,18],
            "Östergötlands län"       => [19,23],
            "Jönköpings län"          => [24,26],
            "Kronobergs län"          => [27,28],
            "Kalmar län"              => [29,31],
            "Gotlands län"            => [32,32],
            "Blekinge län"            => [33,34],
            "Kristianstads län"       => [35,38],
            "Malmöhus län"            => [39,45],
            "Hallands län"            => [46,47],
            "Göteborgs och Bohus län" => [48,54],
            "Älvsborgs län"           => [55,58],
            "Skaraborgs län"          => [59,61],
            "Värmlands län"           => [62,64],
            "Örebro län"              => [66,68],
            "Västmanlands län"        => [69,70],
            "Kopparbergs län"         => [71,73],
            "Gävleborgs län"          => [75,77],
            "Västernorrlands län"     => [78,81],
            "Jämtlands län"           => [82,84],
            "Västerbottens län"       => [85,88],
            "Norrbottens län"         => [89,92],
        ];

        if($year < 90){ // After Jan 1 1990 this is no longer used.
            foreach ($countyMap as $county => list($min,$max)) {
                if($locality >= $min && $locality <= $max) return $county;
            }
        }

        return null;
    }

}
