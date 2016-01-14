<?php

namespace Komakino\Identity\Tests;

use Komakino\Identity\National\FinnishIdentity;

class FinnishIdentityTest extends \PHPUnit_Framework_TestCase
{
    private $nineteenthCenturyMan;
    private $twentiethCenturyMan;
    private $twentyFirstCenturyMan;

    public function setUp() {
        $this->nineteenthCenturyMan    = new FinnishIdentity('170583+123C');
        $this->twentiethCenturyWoman   = new FinnishIdentity('311280-888Y');
        $this->twentyFirstCenturyMan   = new FinnishIdentity('171001A413L');
        $this->erroneous               = new FinnishIdentity('311280-8880');
    }

    public function testValidity()
    {
        $this->assertTrue($this->nineteenthCenturyMan->valid);
        $this->assertTrue($this->twentiethCenturyWoman->valid);
        $this->assertTrue($this->twentyFirstCenturyMan->valid);
        $this->assertFalse($this->erroneous->valid);
    }

    public function testCenturyCalculation()
    {
        $this->assertEquals('18', $this->nineteenthCenturyMan->century);
        $this->assertEquals('19', $this->twentiethCenturyWoman->century);
        $this->assertEquals('20', $this->twentyFirstCenturyMan->century);
    }

    public function testGenders()
    {
        $this->assertEquals('male', $this->nineteenthCenturyMan->gender);
        $this->assertEquals('female', $this->twentiethCenturyWoman->gender);
        $this->assertEquals('male', $this->twentyFirstCenturyMan->gender);
    }

    public function testFormat()
    {
        $this->assertEquals('170583+123C', (string)$this->nineteenthCenturyMan);
        $this->assertEquals('311280-888Y', (string)$this->twentiethCenturyWoman);
        $this->assertEquals('171001A413L', (string)$this->twentyFirstCenturyMan);
    }
}
