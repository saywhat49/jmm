<?php
/**
 * @package     JMM
 * @link        http://adidac.github.com/jmm/index.html
 * @license     GNU/GPL
 * @copyright   Biswarup Adhikari
*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;

/**
 * TableFields Form Field class
 * 
 * @since  1.0.0
 */
class JFormFieldTableFields extends FormField {
        
    /**
     * The form field type.
     *
     * @var    string
     */
    protected $type = 'TableFields';
    
    /**
     * Form data container
     *
     * @var    string
     */
    protected $formData = '';
 
    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    public function getInput() {
        $app = Factory::getApplication();
        $input = $app->input;
        
        $table = $input->getString('tbl');
        $dbname = $input->getString('dbname');
        $tableFields = JMMCommon::showTableStructure($table);
        
        for ($i = 0; $i < count($tableFields); $i++) {
            $field = $tableFields[$i];
            $tmp = explode('(', $field['Type']);
            $name = $field['Field'];
            $type = $tmp[0];
            
            // Check if type has length specification
            $length = 20; // Default length
            if (isset($tmp[1])) {
                $length = (int)str_replace(')', '', $tmp[1]);
                $length += 1;
            }
            
            $id = 'jform_' . $name;
            $label_name = ucwords($name) . ' (' . $type . ')';
            $label_id = 'jform_' . $name . '-lbl';
            
            $this->drawForm("jform[$name]", $type, $id, $label_name, $label_id, $length);
        }
        
        return $this->formData;
    }

    /**
     * Method to draw the appropriate form element based on field type
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  void
     */
    function drawForm($name, $type, $id, $label_name, $label_id, $length) {
        $elementFun = str_replace(' ', '_', 'draw' . $type . 'Element');
        
        // Check if method exists
        if (method_exists($this, $elementFun)) {
            $this->formData .= $this->$elementFun($name, $type, $id, $label_name, $label_id, $length);
        } else {
            // Default to text input if specific method doesn't exist
            $this->formData .= $this->drawvarcharElement($name, $type, $id, $label_name, $label_id, $length);
        }
    }

    /**
     * Draw Int Box
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawintElement($name, $type, $id, $label_name, $label_id, $length) {
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= '<input type="text" name="' . $name . '" id="' . $id . '" value="" size="' . $length . '" class="' . $type . '">';        
        $element .= '</li>';
        
        return $element;
    }

    /**
     * Function Display Decimal 
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawdecimalElement($name, $type, $id, $label_name, $label_id, $length) {
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= '<input type="text" name="' . $name . '" id="' . $id . '" value="" size="' . $length . '" class="' . $type . '">';        
        $element .= '</li>';
        
        return $element;
    }

    /**
     * Draw Char Box
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawcharElement($name, $type, $id, $label_name, $label_id, $length) {
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= '<input type="text" name="' . $name . '" id="' . $id . '" value="" size="' . $length . '" class="' . $type . '">';        
        $element .= '</li>';
        
        return $element;
    }

    /**
     * Draw Varchar Box
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawvarcharElement($name, $type, $id, $label_name, $label_id, $length) {
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= '<input type="text" name="' . $name . '" id="' . $id . '" value="" style="width:394px;" class="' . $type . '">';
        $element .= '</li>';
        
        return $element;
    }

    /**
     * Draw Tiny Element
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawtinyintElement($name, $type, $id, $label_name, $label_id, $length) {
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= '<input type="text" name="' . $name . '" id="' . $id . '" value="" size="' . $length . '" class="' . $type . '">';
        $element .= '</li>';
        
        return $element;
    }
    
    /**
     * Draw Bigint Element
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawbigintElement($name, $type, $id, $label_name, $label_id, $length) {
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= '<input type="text" name="' . $name . '" id="' . $id . '" value="" size="' . $length . '" class="' . $type . '">';
        $element .= '</li>';
        
        return $element;
    }

    /**
     * Draw Unsigned Float Element
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawfloat_unsignedElement($name, $type, $id, $label_name, $label_id, $length) {
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= '<input type="text" name="' . $name . '" id="' . $id . '" value="" size="' . $length . '" class="' . $type . '">';
        $element .= '</li>';
        
        return $element;
    }

    /**
     * Draw Double Unsigned Element
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawdouble_unsignedElement($name, $type, $id, $label_name, $label_id, $length) {
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= '<input type="text" name="' . $name . '" id="' . $id . '" value="" size="' . $length . '" class="' . $type . '">';
        $element .= '</li>';
        
        return $element;
    }

    /**
     * Draw DateTime Element
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawdatetimeElement($name, $type, $id, $label_name, $label_id, $length) {
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= '<input type="text" name="' . $name . '" id="' . $id . '" value="" class="' . $type . '">';
        $element .= '</li>';
        
        return $element;
    }

    /**
     * Draw Timestamp Element
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawtimestampElement($name, $type, $id, $label_name, $label_id, $length) {
        return $this->drawdatetimeElement($name, $type, $id, $label_name, $label_id, $length);
    }

    /**
     * Draw Text Field
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawtextElement($name, $type, $id, $label_name, $label_id, $length) {
        return $this->getWYSIWYGEditor($name, $type, $id, $label_name, $label_id, $length);
    }
    
    /**
     * Draw Medium Text Field
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawmediumtextElement($name, $type, $id, $label_name, $label_id, $length) {
        return $this->getWYSIWYGEditor($name, $type, $id, $label_name, $label_id, $length);
    }
    
    /**
     * Draw Long Text Field
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawlongtextElement($name, $type, $id, $label_name, $label_id, $length) {
        return $this->getWYSIWYGEditor($name, $type, $id, $label_name, $label_id, $length);
    }
    
    /**
     * Draw Medium Blob Field
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function drawmediumblobElement($name, $type, $id, $label_name, $label_id, $length) {
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= '<textarea name="' . $name . '" id="' . $id . '" style="margin: 5px 5px 5px 0px; width: 394px; height: 132px;" class="' . $type . '"></textarea>';
        $element .= '</li>';
        
        return $element;
    }

    /**
     * Get WYSIWYG Editor
     *
     * @param   string   $name         Field name
     * @param   string   $type         Field type
     * @param   string   $id           Field ID
     * @param   string   $label_name   Field label
     * @param   string   $label_id     Field label ID
     * @param   integer  $length       Field length
     *
     * @return  string   HTML for the form element
     */
    function getWYSIWYGEditor($name, $type, $id, $label_name, $label_id, $length) {
        $editor = Factory::getEditor();
        $element = '<li>';
        $element .= '<label id="' . $label_id . '" for="' . $id . '">';
        $element .= $label_name;
        $element .= '</label>';
        $element .= $editor->display($name, null, '800', '300', '60', '20', true);
        $element .= '</li>';
        
        return $element;
    }
}
