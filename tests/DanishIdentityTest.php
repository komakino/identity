<?php

namespace Komakino\Identity\Tests;

use Komakino\Identity\National\DanishIdentity;

class DanishIdentityTest extends \PHPUnit_Framework_TestCase
{
    private $nineteenthCenturyMan;
    private $twentiethCenturyMan;
    private $twentyFirstCenturyWoman;

    public function setUp() {
        $this->nineteenthCenturyMan    = new DanishIdentity('170583-7315');
        $this->twentiethCenturyMan     = new DanishIdentity('110674-1225');
        $this->twentyFirstCenturyWoman = new DanishIdentity('171001-4130');
    }

    public function testCenturyCalculation()
    {
        $this->assertEquals('19', $this->twentiethCenturyMan->century);
        $this->assertEquals('20', $this->twentyFirstCenturyWoman->century);
        $this->assertEquals('18', $this->nineteenthCenturyMan->century);
    }

    public function testGenders()
    {
        $this->assertEquals('male', $this->nineteenthCenturyMan->gender);
        $this->assertEquals('male', $this->twentiethCenturyMan->gender);
        $this->assertEquals('female', $this->twentyFirstCenturyWoman->gender);
    }
}
