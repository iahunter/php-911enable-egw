<?php

/**
 * lib/Address.php.
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

class Address
{
	// These are Emergency Gateway / E911 terms for location information
	public $LOC;	// Location (GPS or free-form other information) limit to 60 characters
	public $HNO;	// House Number (street address NUMBER)
	public $RD;		// Street
	public $A3;		// City
	public $A1;		// State/Province
	public $country;// Country
	public $PC;		// ZIP/Postal code
	public $NAM;	// Customer Name limited to 20 characters

	public static function fromArray($data)
	{
		print "I got an array as input\n";
		print_r($data);
		// TODO: check the input that contains all required fields!
		// TODO: Do we need to track any EXTRA fields?
		return new self(
						$data["LOC"],
						$data["HNO"],
						$data["RD"],
						$data["A3"],
						$data["A1"],
						$data["country"],
						$data["PC"],
						$data["NAM"]
					);
    }

	public static function fromString($address, $city, $state, $country, $zip, $customername)
	{
		$housenumber = "";
		$street = "";
		$location = "";

		// if there are MULTIPLE LINES of address, the first is house number + street, the rest is LOCATION info
		$lines = preg_split( '/\r\n|\r|\n/', $address );
		if(count($lines) > 1) {
			$address = array_shift($lines);
			$location = implode(' ', $lines);
		}

		/* Take the address and split it into house number + street name (digits?)[whitespaces](street)
		/^(\d+[\S]+)\s+(.+)/
			^ assert position at start of the string
			1st Capturing group (\d+[\S]+)
				\d+ match a digit [0-9]
					Quantifier: + Between one and unlimited times, as many times as possible, giving back as needed [greedy]
				[\S]+ match a single character present in the list below
					Quantifier: + Between one and unlimited times, as many times as possible, giving back as needed [greedy]
					\S match any non-white space character [^\r\n\t\f ]
			\s+ match any white space character [\r\n\t\f ]
				Quantifier: + Between one and unlimited times, as many times as possible, giving back as needed [greedy]
			2nd Capturing group (.+)
				.+ matches any character (except newline)
					Quantifier: + Between one and unlimited times, as many times as possible, giving back as needed [greedy]
		*/
		$REGEX = "/^(\d+[\S]*?)\s+(.+)/";
		if(preg_match($REGEX, $address, $hits)) {
			$housenumber = $hits[1];
			$street = $hits[2];
		}else{
			throw new \Exception("Address {$address} did not match house-number / street regex");
		}

		return new self(
						$location,
						$housenumber,
						$street,
						$city,
						$state,
						$country,
						$zip,
						$customername
					);
	}

    public function __construct($location,
								$housenumber,
								$street,
								$city,
								$state,
								$country,
								$zip,
								$customername
								)
	{
		$this->LOC	= $location;
		$this->HNO	= $housenumber;
		$this->RD	= $street;
		$this->A3	= $city;
		$this->A1	= $state;
		$this->country	= $country;
		$this->PC	= $zip;
		$this->NAM	= $customername;
		
		// dot dot dot
		//print "Got a bunch of little pieces\n";
    }

	public function AndrewAddressParser($STRING)
	{
		print "Address Function Goes here. ";
	}

	public function __toString()
	{
		print "Expelium lavoOOOOHie-sa\n";
	}

	public function __toArray()
	{
		return [
				"LOC" 		=> $this->LOC,
				"HNO" 		=> $this->HNO,
				"RD"		=> $this->RD,
				"A3" 		=> $this->A3,
				"A1" 		=> $this->A1,
				"country" 	=> $this->country,
				"PC" 		=> $this->PC,
				"NAM"		=> $this->NAM,
				];
	}

}

