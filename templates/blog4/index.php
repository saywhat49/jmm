<?php
/**
 * @package    JMM
 * @link    http://adidac.github.com/jmm/index.html
 * @license    GNU/GPL
 * @copyright  Biswarup Adhikari
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
?>
<?php foreach($rows as $row):?>


    <h3><?php echo $row['title'];?></h3>
    <p><?php echo $row['body'];?></p>
    <hr>



<?php endforeach;?>