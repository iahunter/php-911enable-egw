<?php
namespace Test;

require_once __DIR__.'/../lib/Address.php';
require_once __DIR__.'/../lib/EGW.php';
// This is only needed for autoloading default constants for authentication credentials
require_once "/etc/networkautomation/config.inc.php";

class ERLTest extends \PHPUnit_Framework_TestCase
{
	// Soap client for calls
    protected $EGW;
	// data used in multiple test cases
	protected $ADDRESS;
	protected $ERLNAME;

    // This function is run before every test
    public function Setup()
    {
		ini_set("soap.wsdl_cache_enable", "0");			// disable cache for testing.
		$this->EGW = new \EmergencyGateway\EGW(	E911_ERL_SOAP_URL,
												E911_ERL_SOAP_WSDL,
												E911_SOAP_USER,
												E911_SOAP_PASS);
		$this->ERLNAME = "TRAVISTEST";

		$LOCATION = "";			// Location can be used to send other information such as GPS or floor info. Limited to max 60, First 20 most important
		$HOUSENUMBER = "1234";
		$STREET = "SE Main St";
		$CITY = "OMAHA";
		$STATE = "NE";
		$COUNTRY = "USA";
		$ZIP = "68164";
		$CUSTNAME = "COMPANY A";					// Can be used for other info. limited to 20 - 29 charactors. 

		$ADDRESS = array(
						"LOC" 		=> $LOCATION,
						"HNO" 		=> $HOUSENUMBER,
						"RD"		=> $STREET,
						"A3" 		=> $CITY,
						"A1" 		=> $STATE,
						"country" 	=> $COUNTRY,
						"PC" 		=> $ZIP,
						"NAM"		=> $CUSTNAME
						);
		$this->ADDRESS = \EmergencyGateway\Address::fromArray($ADDRESS);
    }

    /**
    * @test Validate a valid address
    */
    public function Success_Validate_Address()
    {
        print " Running " . __METHOD__  . "\n";
		$RESULT = $this->EGW->validateAddress($this->ADDRESS);
		$this->assertEquals("ok", $RESULT->errorReturned );
	}

    /**
    * @test Add an ERL to the EGW
    */
    public function Success_Add_ERL()
    {
        print " Running " . __METHOD__  . "\n";
		$RESULT = $this->EGW->addERL($this->ERLNAME,$this->ADDRESS);
		$this->assertTrue($RESULT);
	}

    /**
    * @test Get our test ERL from the EGW
    */
    public function Success_Get_ERL()
    {
        print " Running " . __METHOD__  . "\n";
		$RESULT = $this->EGW->getERL($this->ERLNAME);
		$this->assertEquals($this->ERLNAME, $RESULT["erl_id"] );
		// TODO: Add functional test coverage for civicAddress processing
		//print_r($RESULT);
	}

    /**
    * @test Delete our test ERL from EGW
    */
/*
    public function Success_Delete_ERL()
    {
        print " Running " . __METHOD__  . "\n";
		$RESULT = $this->EGW->deleteERL($this->ERLNAME);
		$this->assertTrue($RESULT);
	}/**/

}

