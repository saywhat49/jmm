<?php
error_reporting(-1);
ini_set('display_errors',1);
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMTableTemplate extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__jmm_templates','id',$db);
	}

	function check(){
		$jinput=JFactory::getApplication()->input;
		$data=$jinput->get('jform',array(),'ARRAY');
		$id=$data['id'];
		$title=$data['title'];
		$php=$data['php'];
		$css=$data['css'];
		$js=$data['js'];
		$templateFolder=JPATH_SITE.DS.'components'.DS.'com_jmm'.DS.'templates'.DS.$title;
		if(isset($id) && $id>0){
				
				$oldTitle=$this->getTitle($id);	
				if($oldTitle!=$title){
					$oldTemplateFolder=JPATH_SITE.DS.'components'.DS.'com_jmm'.DS.'templates'.DS.$oldTitle;
					//$this->deleteDirectory($oldTemplateFolder);
					JFolder::move($oldTemplateFolder,$templateFolder);
				}
		}else{
			if(!is_dir($templateFolder)){				
				JFolder::create($templateFolder);
			}
		}
		if(!is_dir($templateFolder.DS.'js')){				
				JFolder::create($templateFolder.DS.'js');
		}
		if(!is_dir($templateFolder.DS.'css')){				
			JFolder::create($templateFolder.DS.'css');
		}
		if(!is_dir($templateFolder.DS.'images')){				
			JFolder::create($templateFolder.DS.'images');
		}
		$phpFile=$templateFolder.DS.'index.php';
		$jsFile=$templateFolder.DS.'js'.DS.'custom.js';
		$cssFile=$templateFolder.DS.'css'.DS.'default.css';
		$this->createFile($phpFile,$php);
		$this->createFile($cssFile,$css);
		$this->createFile($jsFile,$js);
		return true;
	}

	function getTitle($id){
		$id=(INT)$id;
		$db=JFactory::getDBO();
		$db->setQuery("SELECT title FROM `#__jmm_templates` WHERE `id`=$id LIMIT 1");
		$row=$db->loadObject();
		return $row->title;
	}

	function deleteDirectory($dir) { 
	    if (!file_exists($dir)) return true; 
	    if (!is_dir($dir) || is_link($dir)) return unlink($dir); 
	        foreach (scandir($dir) as $item) { 
	            if ($item == '.' || $item == '..') continue; 
	            if (!$this->deleteDirectory($dir . "/" . $item)) { 
	                chmod($dir . "/" . $item, 0777); 
	                if (!$this->deleteDirectory($dir . "/" . $item)) return false; 
	            }; 
	        } 
	        return rmdir($dir); 
    } 


    function createFile($file,$data){
    	file_put_contents($file, $data);
    }


}
