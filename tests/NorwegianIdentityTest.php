<?php

namespace Komakino\Identity\Tests;

use Komakino\Identity\National\NorwegianIdentity;

class NorwegianIdentityTest extends \PHPUnit_Framework_TestCase
{
    private $twentiethCenturyMan;

    public function setUp() {
        $this->twentiethCenturyMan    = new NorwegianIdentity('17058332143');
        $this->twentiethCenturyWoman  = new NorwegianIdentity('01015000232');
    }

    public function testCenturyCalculation()
    {
        $this->assertEquals('19', $this->twentiethCenturyMan->century);
        $this->assertEquals('19', $this->twentiethCenturyWoman->century);
    }

    public function testGenders()
    {
        $this->assertEquals('male', $this->twentiethCenturyMan->gender);
        $this->assertEquals('female', $this->twentiethCenturyWoman->gender);
    }

    public function testValidity()
    {
        $this->assertTrue($this->twentiethCenturyMan->valid);
        $this->assertTrue($this->twentiethCenturyWoman->valid);
    }

    public function testBirthdays()
    {
        $this->assertEquals('1983-05-17', $this->twentiethCenturyMan->birthday->format('Y-m-d'));
        $this->assertEquals('1950-01-01', $this->twentiethCenturyWoman->birthday->format('Y-m-d'));
    }
}
