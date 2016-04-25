<?php
namespace Test;

require __DIR__.'/../lib/Address.php';

class Address extends \PHPUnit_Framework_TestCase
{
    //protected $CLIENT; // for api call stuff later?

    // This function is run before every test
    public function Setup()
    {
		// Nothing here yet
    }

    /**
    * @test Normal street address
    */
    public function Valid_Address_Standard()
    {
        print " Running " . __METHOD__  . "\n";
		$egwaddress = \EmergencyGateway\Address::fromString(
                                                        "123 soap st.",
                                                        "tampa",
                                                        "florida",
                                                        "united states",
                                                        "12345",
                                                        "fight club"
                                                        );
		// make sure the house number and street were parsed correctly
        $this->assertEquals("123", $egwaddress->HNO );
        $this->assertEquals("soap st.", $egwaddress->RD );
		$this->assertEquals("tampa", $egwaddress->A3 );
		$this->assertEquals("florida", $egwaddress->A1 );
		$this->assertEquals("united states", $egwaddress->country );
		$this->assertEquals("12345", $egwaddress->PC );
		$this->assertEquals("fight club", $egwaddress->NAM );
    }

    /**
    * @test Invalid street address - junk before house number
    */
    public function Negative_Invalid_Address_Junk_Before_House_Number()
    {
        print " Running " . __METHOD__  . "\n";
		$this->setExpectedException('Exception');
		$egwaddress = \EmergencyGateway\Address::fromString(
                                                        "garbage 123 soap st.",
                                                        "tampa",
                                                        "florida",
                                                        "united states",
                                                        "12345",
                                                        "fight club"
                                                        );
    }

    /**
    * @test Single number house number street address
    */
    public function Valid_Address_Single_House_Number()
    {
        print " Running " . __METHOD__  . "\n";
		$egwaddress = \EmergencyGateway\Address::fromString(
                                                        "1 fake st.",
                                                        "somewhere",
                                                        "whatever",
                                                        "united states",
                                                        "54321",
                                                        "somewhere"
                                                        );
		// make sure the house number and street were parsed correctly
        $this->assertEquals("1", $egwaddress->HNO );
        $this->assertEquals("fake st.", $egwaddress->RD );
    }

    /**
    * @test Suite and location on new line after house number and street
    */
    public function Valid_Suite_After_Address()
    {
        print " Running " . __METHOD__  . "\n";
		$egwaddress = \EmergencyGateway\Address::fromString(
                                                        "1 fake st.
Suite #3A",
                                                        "somewhere",
                                                        "whatever",
                                                        "united states",
                                                        "54321",
                                                        "somewhere"
                                                        );
		// make sure the house number and street were parsed correctly
        $this->assertEquals("Suite #3A", $egwaddress->LOC );
        $this->assertEquals("1", $egwaddress->HNO );
        $this->assertEquals("fake st.", $egwaddress->RD );
    }

    /**
    * @test Hyphenated house number and street
    */
    public function Valid_Hyphenated_House_Number()
    {
        print " Running " . __METHOD__  . "\n";
		$egwaddress = \EmergencyGateway\Address::fromString(
                                                        "123-456 fake st.
Suite #3A",
                                                        "somewhere",
                                                        "whatever",
                                                        "united states",
                                                        "54321",
                                                        "somewhere"
                                                        );
		// make sure the house number and street were parsed correctly
        $this->assertEquals("Suite #3A", $egwaddress->LOC );
        $this->assertEquals("123-456", $egwaddress->HNO );
        $this->assertEquals("fake st.", $egwaddress->RD );
    }

}

