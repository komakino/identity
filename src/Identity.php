<?php

namespace Komakino\Identity;

abstract class Identity
{
    public $valid  = false;

    protected $code              = null;
    protected $properties        = ['code' => null];
    protected $validationPattern = '/.*/';
    protected $parsePattern      = '/(?<code>.*)/';
    protected $outputFormat      = '<code>';

    public function __construct($code) {
        $this->code = $code;
        if($this->validatePattern()){
            $this->parse();
            $this->valid = $this->validate();
        } else var_dump($code);
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
            if(array_key_exists($part, $matches)){
                $this->properties[$part] = $matches[$part] ?: null;
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
        if($this->valid){
            return $this->format();
        } else {
            return '';
        }
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
