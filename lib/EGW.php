<?php

/**
 * lib/EGW.php.
 *
 * This class connects to 911Enable Emergency Gateway via the SOAP/XML interface
 * 
 *
 * PHP version 5
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category  default
 *
 * @author    Travis Riesenberg
 * @copyright 2016 @authors
 * @license   http://www.gnu.org/copyleft/lesser.html The GNU LESSER GENERAL PUBLIC LICENSE, Version 3.0
 */
namespace EmergencyGateway;

class EGW
{
	public $SOAPCLIENT;
	
	// Running array of SOAP calls made during this session
    public $SOAPCALLS;
	
	// Credentials
	private $USERNAME;
	private $PASSWORD;
	private $SNMP_RW = SNMP_RW;

    // Constructor sets up our soap client with WSDL to talk to Call Manager

    public function __construct(
                                $URL,
                                $SCHEMA,
								$USERNAME,
								$PASSWORD
                                ) {
        $OPTIONS = [
                    'trace'                => true,
                    'exceptions'           => true,
                    'connection_timeout'   => 10,
                    'location'             => $URL,
                   ];
        $this->SOAPCLIENT = new \SoapClient($SCHEMA, $OPTIONS);
        $this->SOAPCALLS = [];
		$this->USERNAME = $USERNAME;
		$this->PASSWORD = $PASSWORD;
    }

	public function addERL($NAME,$ADDRESS,$ELINS)
	{	
	// TODO: make sure this is validated. seperate add and updates. throw exception on adds if exists. 
		$params = array(
				'username' 						=> $this->USERNAME,
				'password' 						=> $this->PASSWORD,
				'erl_id' 						=> $NAME,
				'local_gateway_enabled' 		=> false,
				'force_csz'						=> 1,
				'civicAddress' 					=> $ADDRESS,
				'elins'							=> $ELINS
		);
		
		// This function adds a new ERL to the EGW database with the parameters passed in.
		$result = $this->SOAPCLIENT->addorUpdateLocationRequest($params);
		//print_r($result);
		if ($result->status){
			throw new \Exception($result->errorReturned);
		}
		return true;
}

	public function deleteERL($NAME)
	{
		// This function deletes an existing ERL to the EGW database with the parameters passed in.
		$params = array(
				'username' 						=> $this->USERNAME,
				'password' 						=> $this->PASSWORD,
				'erl_id' 						=> $NAME,
			);
		$result = $this->SOAPCLIENT->deleteLocationRequest($params);
		//print_r($result);
		if ($result->status){
			throw new \Exception($result->errorReturned);
		}
		return true;
}


	public function getERL($NAME)
	{
		// This function adds a new ERL to the EGW database with the parameters passed in.
		$params = array(
				'username' 						=> $this->USERNAME,
				'password' 						=> $this->PASSWORD,
				'erl_id' 						=> $NAME,
			);
		$result = $this->SOAPCLIENT->qryLocationRequest($params);
		//print_r($result);
		if ($result->status){
			throw new \Exception($result->errorReturned);
		}
		return (array)$result->LocationInfo;
}

	public function validateAddress(Address $ADDRESS)
	{
		// This function adds a new ERL to the EGW database with the parameters passed in.
		$params = array(
			'username' 						=> $this->USERNAME,
			'password' 						=> $this->PASSWORD,
			'civicAddress' 					=> $ADDRESS->__toArray(),
			);
		$result = $this->SOAPCLIENT->validateAddressRequest($params);
		if ($result->status){
			throw new \Exception($result->errorReturned);
		}
		return $result;
	}
	
	public function get_switch($SWITCH)
	{
		// This function tries to query the switch by IP or ID. //ID and ERL seem to be broken in 911Enable API. Following up with vendor. 
		
		// Feed in $SWITCH array with any of following variables included. 
		$SWITCHIP = $SWITCH['switch_ip'];
		$SWITCHID = $SWITCH['switch_id'];
		$ERLNAME = $SWITCH['switch_or_port_erl'];
		
		$params = array( 
					'Authentication'			=> [
							'Username' 			=> $this->USERNAME,
							'Password' 			=> $this->PASSWORD,
					],
					'QuerySwitchEntry'			=> [
							'switch_id'			=> $SWITCHID,
							'SwitchIpOrERLCombination'		=> [
								'switch_ip'					=> $SWITCHIP,
								'switch_or_port_erl'		=> $ERLNAME,
								],
					],
		);
		$result = $this->SOAPCLIENT->querySwitchRequest($params);
		return $result;	
}

	public function list_switches()
	{
		// This function lists all the switchs in the database.
		$SWITCHIP = "%";
		$params = array(
					'Authentication'			=> [
							'Username' 			=> $this->USERNAME,
							'Password' 			=> $this->PASSWORD,
					],
					'QuerySwitchEntry'			=> [
							'switch_id'			=> "",
							'SwitchIpOrERLCombination'		=> [
								'switch_ip'					=> $SWITCHIP,
								'switch_or_port_erl'		=> "",
								],
					],
		);
		$result = $this->SOAPCLIENT->querySwitchRequest($params);
		return $result;	
}

	public function add_switch($SWITCH)
	{
		// This function adds a new Switch to the EGW database with the parameters passed in.
		// Feed in $SWITCH array with any of following variables included. 
		$SWITCHIP = $SWITCH['switch_ip'];
		$VENDOR = $SWITCH['switch_vendor'];
		$ERLNAME = $SWITCH['switch_erl'];
		$SWITCHNAME = $SWITCH['switch_description'];
		
		// Feed in $SWITCH array with any of following variables included. 
		$params = array(
							'Authentication'			=> [
									'Username' 			=> $this->USERNAME,
									'Password' 			=> $this->PASSWORD,
							],
							'AddSwitchEntry'			=> [
									'switch_ip'						=>  $SWITCHIP,
									'snmp_version'					=>  "2c",
									'snmp_community'				=>  $this->SNMP_RW,
									'snmp_timeout'					=>	5,
									'snmp_retry_count'				=>  2,
									'switch_erl'					=>  $ERLNAME,
									'switch_type'					=>	$VENDOR,
									'switch_is_scannable'			=>	'enable',
									'log_level'						=>	'INFO',
									'switch_description'			=>	$SWITCHNAME,
									'switch_vendor'					=>	$VENDOR,
									'switch_trunk_port_detection'	=>	'disable',
									'switch_scan_voice_vlans'		=>	'disable',

							],
			);
		$result = $this->SOAPCLIENT->addSwitchRequest($params);
		return $result;
}
	
	public function delete_switch($SWITCHIP)
	{
		// This function adds a new ERL to the EGW database with the parameters passed in.
		// Feed in the IP Address of the switch as the parameter. 
		
		$params = array( 
					'Authentication'			=> [
							'Username' 			=> $this->USERNAME,
							'Password' 			=> $this->PASSWORD,
					],
					'DeleteSwitchEntry'			=> [
						'switch_port_combination' => [
								'switch_ip'						=> $SWITCHIP,
								'switch_port_name'				=> "",
							],
						'switch_or_port_erl'	=> $ERLNAME,
					
				],
	);
		$result = $this->SOAPCLIENT->deleteSwitchRequest($params);
		return $result;
	}


	public function update_switch($SWITCH)
	{
		// This function updates and existing Switch in the EGW database with the parameters passed in.
		// FYI - Update Switch IP Not supported. Must Delete and ReAdd with new IP and Settings. 
		// Feed in $SWITCH array with any of following variables included. 
		$SWITCHIP = $SWITCH['switch_ip'];
		$VENDOR = $SWITCH['switch_vendor'];
		$ERLNAME = $SWITCH['switch_erl'];
		$SWITCHNAME = $SWITCH['switch_description'];
		
		// Feed in $SWITCH array with any of following variables included. 
		$params = array(
							'Authentication'			=> [
									'Username' 			=> $this->USERNAME,
									'Password' 			=> $this->PASSWORD,
							],
							'UpdateSwitchEntry'			=> [
									'switch_ip'						=>  $SWITCHIP,
									'snmp_version'					=>  "2c",
									'snmp_community'				=>  $this->SNMP_RW,
									'snmp_timeout'					=>	5,
									'snmp_retry_count'				=>  2,
									'switch_erl'					=>  $ERLNAME,
									'switch_type'					=>	$VENDOR,
									'switch_is_scannable'			=>	'enable',
									'log_level'						=>	'INFO',
									'switch_description'			=>	$SWITCHNAME,
									'switch_vendor'					=>	$VENDOR,
									'switch_trunk_port_detection'	=>	'disable',
									'switch_scan_voice_vlans'		=>	'disable',
							],
			);
		$result = $this->SOAPCLIENT->updateSwitchRequest($params);
		return $result;
}

}


