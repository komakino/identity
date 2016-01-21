<?php

namespace Komakino\Identity\Tests;

use Komakino\Identity\National\DanishIdentity;

class DanishIdentityTest extends \PHPUnit_Framework_TestCase
{
    private $nineteenthCenturyMan;
    private $twentiethCenturyMan;
    private $twentyFirstCenturyWoman;

    public function setUp() {
        $this->nineteenthCenturyMan    = new DanishIdentity('170583-7321');
        $this->twentiethCenturyMan     = new DanishIdentity('110674-1227');
        $this->twentyFirstCenturyWoman = new DanishIdentity('171001-4142');
    }

    public function testCenturyCalculation()
    {
        $this->assertEquals('18', $this->nineteenthCenturyMan->century);
        $this->assertEquals('19', $this->twentiethCenturyMan->century);
        $this->assertEquals('20', $this->twentyFirstCenturyWoman->century);
    }

    public function testGenders()
    {
        $this->assertEquals('male', $this->nineteenthCenturyMan->gender);
        $this->assertEquals('male', $this->twentiethCenturyMan->gender);
        $this->assertEquals('female', $this->twentyFirstCenturyWoman->gender);
    }

    public function testValidity()
    {
        $this->assertTrue($this->nineteenthCenturyMan->valid);
        $this->assertTrue($this->twentiethCenturyMan->valid);
        $this->assertFalse($this->twentyFirstCenturyWoman->valid);
    }
}
