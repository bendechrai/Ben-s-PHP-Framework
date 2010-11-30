<?php

class Network {

	static public function IP2Long( $ip ) {
		if( is_numeric( $ip ) ) return $ip;
		return intval( ip2long( $ip ) );
	}

	static public function IPInNetwork( $ip, $network ) {
		$ip = str_pad( decbin( self::IP2Long( $ip ) ), 32, '0', STR_PAD_LEFT );
		$network = explode( '/', $network );
		$net_addr = str_pad( decbin( self::IP2Long( $network[0] ) ), 32, '0', STR_PAD_LEFT );
		$cidr = $network[1];
		if( substr( $net_addr, 0, $cidr ) == substr( $ip, 0, $cidr ) ) return true;
		return false;
	}

}
