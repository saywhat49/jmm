<?php
defined('_JEXEC') or die('Restricted access');
class JMM{

	public static function baseURL(){
		return JURI::root().DS.'components'.DS.'com_jmm';
	}
}