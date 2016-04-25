<?php
namespace Test;

require_once __DIR__.'/../lib/Address.php';
require_once __DIR__.'/../lib/EGW.php';
// This is only needed for autoloading default constants for authentication credentials
require_once "/etc/networkautomation/config.inc.php";

class SwitchTest extends \PHPUnit_Framework_TestCase
{
	// Soap client for calls
    protected $EGW;
	protected $SWITCHIP;
	protected $ERLNAME;

    // This function is run before every test
    public function Setup()
    {
		ini_set("soap.wsdl_cache_enable", "0");			// disable cache for testing.
		$this->EGW = new \EmergencyGateway\EGW(	E911_SWITCH_SOAP_URL,
												E911_SWITCH_SOAP_WSDL,
												E911_SOAP_USER,
												E911_SOAP_PASS);
		$this->ERLNAME = "TRAVISTEST";
		$this->SWITCHIP = "10.10.10.1";
    }

    /**
    * @test List switches in the database
    */
    public function Success_List_Switches()
    {
		print " Running " . __METHOD__ . " ";
		$RESULT = $this->EGW->list_switches();
		//print_r($RESULT);
		$this->assertEquals(200, $RESULT->Response->Status );
		print $RESULT->Response->Message . "\n";
	}

    /**
    * @test Add a switch to the EGW
    */
    public function Success_Add_Switch()
    {
		print " Running " . __METHOD__ . " ";
		$ADD_SWITCH = [
					'switch_ip'				=>  $this->SWITCHIP,
					'switch_erl'			=>  $this->ERLNAME,
					'switch_description'	=>	'Test',
					'switch_vendor'			=>	'Cisco',
					];
		$RESULT = $this->EGW->add_switch($ADD_SWITCH);
		$this->assertEquals(200, $RESULT->Response->Status );
		print $RESULT->Response->Message . "\n";
	}

    /**
    * @test Get our switch from the EGW
    */
    public function Success_Get_Switch()
    {
		print " Running " . __METHOD__ . " ";
		$GET_SWITCH = array(
//					'switch_id'					=> $SWITCHID, // does not work, vendor sucks
					'switch_ip'					=> $this->SWITCHIP,
//					'switch_or_port_erl'		=> $this->ERLNAME, // does not work
		);
		$RESULT = $this->EGW->get_switch($GET_SWITCH);
		$this->assertEquals(200, $RESULT->Response->Status );
		print $RESULT->Response->Message . "\n";
		//print_r($RESULT);
	}

    /**
    * @test Update our switch in the EGW
    */
    public function Success_Update_Switch()
    {
		print " Running " . __METHOD__ . " ";
//					'switch_id'					=> $SWITCHID, // does not work, vendor sucks
//					'switch_or_port_erl'		=> $this->ERLNAME, // does not work
		$UPDATE_SWITCH = [
					'switch_ip'					=> $this->SWITCHIP, // Key to search off of
					'switch_erl'				=> $this->ERLNAME,
					'switch_description'		=> 'New Description',
					'switch_vendor'				=> 'Cisco',
					];
		$RESULT = $this->EGW->update_switch($UPDATE_SWITCH);
		$this->assertEquals(200, $RESULT->Response->Status );
		print $RESULT->Response->Message . "\n";

		// Now get our updated switch
		$GET_SWITCH = array(
					'switch_ip'					=> $this->SWITCHIP,
		);
		$RESULT = $this->EGW->get_switch($GET_SWITCH);
		$this->assertEquals(200, $RESULT->Response->Status );
		// TODO: Write checks to make sure the switch actually got updated
	}

    /**
    * @test Delete our switch in the EGW
    */
    public function Success_Delete_Switch()
    {
		print " Running " . __METHOD__ . " ";
		$RESULT = $this->EGW->delete_switch($this->SWITCHIP);
		print $RESULT->Response->Message . "\n";
		//print_r($RESULT);
	}

    /**
    * @test Delete our test ERL the EGW
    */
    public function Success_Delete_ERL()
    {
		print " Running " . __METHOD__ . " ";
		$this->EGW = new \EmergencyGateway\EGW(	E911_ERL_SOAP_URL,
												E911_ERL_SOAP_WSDL,
												E911_SOAP_USER,
												E911_SOAP_PASS);

		$RESULT = $this->EGW->deleteERL($this->ERLNAME);
		$this->assertTrue($RESULT);
	}

}

