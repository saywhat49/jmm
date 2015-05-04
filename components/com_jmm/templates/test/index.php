<?php
/**
 * Add Your Custom PHP Code
 * Your Have access to this following variables
 * For Columns $col (ARRAY)
 * For Data Rows $rows (ARRAY)
 * If You want to hide the pagination then $this->defaultPagination=false;
 * Template Base URL $this->templateBaseURL  
 * Access Parameters $this->params;
 * Access Pagination Object $this->pagination
 * 
 */
defined('_JEXEC') or die('Restricted access');
?>
<table class="bordered">
  <thead>
    <tr>
      <?php
      foreach($cols as $col){
        echo '<th>'.$col.'</th>';
      }
      ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $i => $row): ?>

    <tr class="row<?php echo $i % 2?>">

      <?php
      foreach ($row as $col => $val) {
        echo '<td>' . $val . '</td>';
      }
      ?>
    </tr>

    <?php endforeach ?>
  </tbody>
</table>

