<?php

namespace Komakino\Identity\National;

use Komakino\Luhn\Luhn;
use Komakino\Identity\Identity;

class SwedishIdentity extends Identity
{

    protected $properties = [
        'type'        => null,
        'form'        => null,
        'century'     => null,
        'year'        => null,
        'month'       => null,
        'day'         => null,
        'centuryHint' => null,
        'locality'    => null,
        'county'      => null,
        'number'      => null,
        'gender'      => null,
        'checkdigit'  => null,
        'birthday'    => null,
        'temporary'   => false,
    ];

    function __construct($code) {

        $macros = [
            ':century:' => '(?:1[68-9]|20)',                            # 16,18,19,20 - 16 is organizations
            '%century%' => '(?<century>1[68-9]|20)',
            ':day:'     => '(?:\d{2})',                                 # 01-31,61-91 - temporary numbers have 60 added to them
            '%day%'     => '(?<day>\d{2})',
            ':month:'   => '(?:0[1-9]|1[0-2]|[2-9][0-9])',              # 01-12,20-99 - organizations have >=20
            '%month%'   => '(?<month>0[1-9]|1[0-2]|[2-9][0-9])',
            ':year:'    => '(?:\d{2})',                                 # 00-99
            '%year%'    => '(?<year>(?<form>\d)\d)',
        ];

        $this->validationPattern = strtr("/^:century:?:year::month::day:[-+]?[\d]{4}$/", $macros);
        $this->parsePattern      = strtr("/^%century%?%year%%month%%day%(?<centuryHint>[-+]?)(?<locality>\d{2})(?<number>\d{1})(?<checkdigit>\d{1})/", $macros);
        $this->outputFormat      = '{year}{month}{day}{centuryHint}{locality}{number}{checkdigit}';

        parent::__construct($code);
    }

    protected function validate(){
        return Luhn::validate($this->format());
    }

    protected function parse()
    {
        $matches = parent::parse();

        if(!$matches['centuryHint']){
            $this->properties['centuryHint'] = '-';
        }

        if($this->properties['month'] >= 20){
            $this->properties['type']       = 'organization';
            $this->properties['century']    = '16';
            $this->properties['form']       = $this->parseForm($matches['form']);
        } else {
            if($this->properties['day'] >= 60){
                $this->properties['temporary'] = true;
            }

            $this->properties['type']       = 'person';
            $this->properties['century']    = $matches['century'] ?: $this->calculateCentury($matches['centuryHint']);
            $this->properties['gender']     = $this->properties['number'] % 2 ? 'male' : 'female';
            $this->properties['birthday']   = $this->composeBirthday();
            $this->properties['county']     = $this->parseCounty();
        }
    }

    private function composeBirthday(){
        extract($this->properties);
        $day -= $day > 60 ? 60 : 0;
        return date_create("{$century}{$year}-{$month}-{$day}");
    }

    private function calculateCentury($centuryHint)
    {
        extract($this->properties);
        if($year <= date('y')){
            $century = 20;
        } else {
            $century = 19;
        }
        if($centuryHint == '+') $century -= 1;

        return (string)$century;
    }

    private function parseCounty(){
        extract($this->properties);
        $countyMap = [
            "Stockholms"          => [00,13],
            "Uppsala"             => [14,15],
            "Södermanlands"       => [16,18],
            "Östergötlands"       => [19,23],
            "Jönköpings"          => [24,26],
            "Kronobergs"          => [27,28],
            "Kalmar"              => [29,31],
            "Gotlands"            => [32,32],
            "Blekinge"            => [33,34],
            "Kristianstads"       => [35,38],
            "Malmöhus"            => [39,45],
            "Hallands"            => [46,47],
            "Göteborgs och Bohus" => [48,54],
            "Älvsborgs"           => [55,58],
            "Skaraborgs"          => [59,61],
            "Värmlands"           => [62,64],
            "Örebro"              => [66,68],
            "Västmanlands"        => [69,70],
            "Kopparbergs"         => [71,73],
            "Gävleborgs"          => [75,77],
            "Västernorrlands"     => [78,81],
            "Jämtlands"           => [82,84],
            "Västerbottens"       => [85,88],
            "Norrbottens"         => [89,92],
        ];

        if($year < 90){ // After Jan 1 1990 this is no longer used.
            foreach ($countyMap as $county => list($min,$max)) {
                if($locality >= $min && $locality <= $max) return $county . ' län';
            }
        }

        return null;
    }

    public function parseForm($form)
    {
        switch($form){
            case 1: return 'estate';        # Dödsbon
            case 2: return 'public';        # Stat, landsting, kommuner, församlingar
            case 3: return 'foreign';       # Utländska företag som bedriver näringsverksamhet eller äger fastigheter i Sverige
            case 5: return 'limited';       # Aktiebolag
            case 6: return 'partnership';   # Enkelt bolag
            case 7: return 'association';   # Ekonomiska föreningar
            case 8: return 'foundation';    # Ideella föreningar och stiftelser
            case 9: return 'trading';       # Handelsbolag, kommanditbolag och enkla bolag
        }
    }

}
