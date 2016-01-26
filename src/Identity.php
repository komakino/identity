<?php

namespace Komakino\Identity;

use Komakino\Identity\Errors\IdentityInvalidFormatException;

abstract class Identity
{
    public $valid  = false;
    public $pretty  = "";

    protected $macros = [
        ':day:'   => '(?:0[1-9]|[1-2][0-9]|3[0-1])',        # 01-31
        '%day%'   => '(?<day>0[1-9]|[1-2][0-9]|3[0-1])',
        ':month:' => '(?:0[0-9]|1[0-2])',                   # 01-12
        '%month%' => '(?<month>0[0-9]|1[0-2])',
        ':year:'  => '(?:\d{2})',                           # 00-99
        '%year%'  => '(?<year>\d{2})',
    ];

    protected $code              = null;
    protected $properties        = ['code' => null];
    protected $validationPattern = '/.*/';
    protected $parsePattern      = '/(?<code>.*)/';
    protected $outputFormat      = '<code>';

    public function __construct($code) {
        $this->code = $code;
        if($this->validatePattern()){
            $this->parse();
            $this->valid  = $this->validate();
            $this->pretty = $this->format();
        } else {
            throw new IdentityInvalidFormatException($code);
        }
    }

    protected function validatePattern()
    {
        return preg_match($this->validationPattern,$this->code);
    }

    protected function validate()
    {
        return true;
    }

    protected function parse()
    {
        preg_match($this->parsePattern,$this->code,$matches);

        foreach(array_keys($this->properties) as $part){
            if(array_key_exists($part, $matches) && $matches[$part] !== ""){
                $this->properties[$part] = $matches[$part];
            }
        }

        return $matches;
    }

    protected function format(){
        $output = $this->outputFormat;
        if(preg_match_all('/(?:{(\w*)})/',$this->outputFormat,$matches)){
            foreach($matches[0] as $i => $match){
                $output = str_replace($match, $this->properties[$matches[1][$i]], $output);
            }
        }

        return $output;
    }

    public function listProperties()
    {
        return $this->properties;
    }

    public function hasProperty($property){
        return array_key_exists($property,$this->properties);
    }

    public function __toString()
    {
        return $this->pretty;
    }

    public function __get($name)
    {
        if($this->hasProperty($name)){
            return $this->properties[$name];
        }
        return null;
    }

    public static function __callStatic($method,$arguments)
    {
        $identity = new static($arguments[0]);
        switch($method){
            case 'validate':
                return $identity->valid;
            case 'parse':
                return $identity->properties;
            case 'format':
                return (string)$identity;
        }
        return static::$method;
    }
}
