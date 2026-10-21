<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
/**
 * Check Query Is Formated for Blog Or Not
 */
/**
 * Check Id Column Exists OR Not
 */
if(!in_array('id', $cols)){
	echo "id Column or Alias Not Exist";
	exit();
}
/**
 * Check Title Column Exists OR Not
 */
if(!in_array('title', $cols)){
	echo "title Column or Alias Not Exist";
	exit();
}
/**
 * Check Body Column Exists OR Not
 */
if(!in_array('body', $cols)){
	echo "body Column or Alias Not Exist";
	exit();
}

/**
 * Le corps est du HTML : on retire ce qui est dangereux (script, gestionnaires on*, iframe...)
 * au lieu de l'afficher tel quel. Filtre Joomla en liste noire (1, 1) : les balises de mise
 * en forme sont conservées.
 */
$filter = \Joomla\CMS\Filter\InputFilter::getInstance([], [], 1, 1);
?>
<?php foreach($rows as $row):?>


		<h3><?php echo $this->escape($row['title']);?></h3>
		<p><?php echo $filter->clean((string) $row['body'], 'html');?></p>
		<hr>



<?php endforeach;?>