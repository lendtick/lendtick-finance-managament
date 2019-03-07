<?php

namespace App\Helpers;

class XMLHelper
{ 
	public static function response(array $arr, \SimpleXMLElement $xml)
	{
		foreach ($arr as $k => $v) {
			is_array($v)
			? $this->response($v, $xml->addChild($k))
			: $xml->addChild($k, $v);
		}
		return $xml;
	} 
}
