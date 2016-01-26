<?php

namespace Komakino\Identity\Tests;

use Komakino\Identity\National\SwedishIdentity;
use Komakino\Identity\Errors\IdentityInvalidFormatException;

class SwedishIdentityTest extends \PHPUnit_Framework_TestCase
{
    private $nineteenthCenturyMan;
    private $twentiethCenturyMan;
    private $twentyFirstCenturyWoman;
    private $twentyFirstCenturyBabyGirl;
    private $organization;

    public function setUp() {
        $this->nineteenthCenturyMan       = new SwedishIdentity('830517+0956');
        $this->twentiethCenturyMan        = new SwedishIdentity('451023-5411');
        $this->twentyFirstCenturyWoman    = new SwedishIdentity('20011077-2723'); // Temporary number
        $this->twentyFirstCenturyBabyGirl = new SwedishIdentity('121124-1540');
        $this->organization               = new SwedishIdentity('556925-7297');
    }

    public function testValidity()
    {
        $this->assertTrue($this->nineteenthCenturyMan->valid);
        $this->assertTrue($this->twentiethCenturyMan->valid);
        $this->assertTrue($this->twentyFirstCenturyWoman->valid);
        $this->assertTrue($this->twentyFirstCenturyBabyGirl->valid);
    }

    public function testCounty()
    {
        $this->assertEquals('Stockholms län', $this->nineteenthCenturyMan->county);
        $this->assertEquals('Göteborgs och Bohus län', $this->twentiethCenturyMan->county);
        $this->assertEquals('Kronobergs län', $this->twentyFirstCenturyWoman->county);
    }

    public function testGenders()
    {
        $this->assertEquals('male', $this->nineteenthCenturyMan->gender);
        $this->assertEquals('male', $this->twentiethCenturyMan->gender);
        $this->assertEquals('female', $this->twentyFirstCenturyWoman->gender);
    }

    /**
     * @expectedException Komakino\Identity\Errors\IdentityInvalidFormatException
     */
    public function testStaticMethods()
    {
        $this->assertTrue(SwedishIdentity::validate('556925-7297'));
        $this->assertInstanceOf(IdentityInvalidFormatException::class,SwedishIdentity::validate('123456789'));
        $this->assertTrue(SwedishIdentity::validate('451023-5411'));
        $this->assertEquals('451023-5411', (string)SwedishIdentity::format('194510235411'));
        $this->assertEquals('54', SwedishIdentity::parse('4510235411')['locality']);
    }

    public function testBirthdays()
    {
        $this->assertEquals('1883-05-17', $this->nineteenthCenturyMan->birthday->format('Y-m-d'));
        $this->assertEquals('1945-10-23', $this->twentiethCenturyMan->birthday->format('Y-m-d'));
        $this->assertEquals('2001-10-17', $this->twentyFirstCenturyWoman->birthday->format('Y-m-d'));
        $this->assertEquals('2012-11-24', $this->twentyFirstCenturyBabyGirl->birthday->format('Y-m-d'));
    }

    public function testPersonProperties()
    {
        $this->assertEquals('person', $this->twentiethCenturyMan->type);
        $this->assertEquals('19', $this->twentiethCenturyMan->century);
        $this->assertEquals('45', $this->twentiethCenturyMan->year);
        $this->assertEquals('10', $this->twentiethCenturyMan->month);
        $this->assertEquals('23', $this->twentiethCenturyMan->day);
        $this->assertEquals('54', $this->twentiethCenturyMan->locality);
        $this->assertEquals('1', $this->twentiethCenturyMan->number);
        $this->assertEquals('male', $this->twentiethCenturyMan->gender);
        $this->assertEquals('1', $this->twentiethCenturyMan->checkdigit);

        $this->assertEquals('451023-5411', (string)$this->twentiethCenturyMan);
    }

    public function testCompanyProperties()
    {
        $this->assertEquals('organization', $this->organization->type);
        $this->assertEquals('556925-7297', (string)$this->organization);
    }

    public function testInvalidPersonProperties()
    {
        $identity = new SwedishIdentity('1234567890');
        $this->assertFalse($identity->valid);
    }
}
