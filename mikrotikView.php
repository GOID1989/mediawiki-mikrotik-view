<?php
require_once( 'routeros_api.class.php' );
$wgHooks['ParserFirstCallInit'][] = 'mikrotikViewSetup';

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'MikrotikView',
	'version' => '0.1',
	'author' => 'GOID',
	'url' => 'https://github.com/GOID1989/mediawiki-mikrotik-view',
	'description' => 'Build mediawiki table from Mikrotik API queries',
	'license-name' => 'GPL-2.0-or-later'
);

function mikrotikViewSetup( Parser $parser ) {
	$parser->setHook( 'mikrotik', 'mikrotikViewRender' );
	return true;
}
function mikrotikViewRender( $input, array $args, Parser $parser, PPFrame $frame ) {
	$parser->disableCache();
	$API = new RouterosAPI();
	#$API->debug = true;
	$API->attempts = 1;
	$API->ssl = true;
	$API->port = 8729;
	
	if(!isset($args['ip'])) { return "IP not set"; }
	if(!isset($args['login'])) { return "Login not set"; }
	if(!isset($args['password'])) { return "Password not set"; }
	
	if ($API->connect($args['ip'], $args['login'], $args['password'])) {
		$API->write("/ip/firewall/nat/print");
		$READ = $API->read(false);
		$ARRAY = $API->parseResponse($READ);
		
		$tbl = "<table class='wikitable'>";
		$tbl = $tbl."<th>Внешний порт</th>
					<th>Внутренний порт</th>
					<th>Внутренний адрес</th>
					<th>Ограничение доступа</th>
					<th>Комментарий</th>";
		foreach( $ARRAY as $arr) {
			if($arr["action"] != "masquerade" and $arr["action"] != "redirect" ) {
				$tbl = $tbl."<tr><td>".(array_key_exists("dst-port", $arr)? $arr["dst-port"]: "")."</td>"; 
				$tbl = $tbl."<td>".(array_key_exists("to-ports", $arr)? $arr["to-ports"]: "")."</td>"; 
				$tbl = $tbl."<td>".(array_key_exists("to-addresses", $arr)? $arr["to-addresses"]: "")."</td>"; 
				# Combine two field with source IP into one cell
				$sourceIPs = "";
				$sourceIPs .= array_key_exists("src-address-list", $arr) ? $arr["src-address-list"].", " : "" ;
				$sourceIPs .= array_key_exists("src-address", $arr) ? $arr["src-address"].", " : "" ;
				$tbl = $tbl."<td>".preg_replace("/[,][\s]+$/","",$sourceIPs)."</td>";
				
				$tbl = $tbl."<td>".(array_key_exists("comment", $arr)? $arr["comment"]: "")."</td>"; 
				# Close table row
				$tbl = $tbl."</tr>";
			}
		}
		
		# Build access-lists table
		$API->write("/ip/firewall/address-list/print");
		$READ = $API->read(false);
		$ARRAY = $API->parseResponse($READ);
		$tbl = $tbl."<tr><td colspan='5'>Списки доступа</td></tr>";
		$ADDRESS_LISTS = array();
		foreach( $ARRAY as $arr) {
			if(array_key_exists($arr["list"], $ADDRESS_LISTS)) {
				$ADDRESS_LISTS[$arr["list"]] .= ", ".$arr["address"];
			}
			else {
				$ADDRESS_LISTS[$arr["list"]] = $arr["address"];
			}
		}
		foreach($ADDRESS_LISTS as $key => $value){
			$tbl = $tbl."<tr><td>".$key."</td><td colspan='4'>".$value."</td></tr>";
		}
		$API->disconnect();
		return $tbl."</table>"."\n\n" . htmlspecialchars( $input );
	}
	else { return "Ошибка подключения"; }
}
?>