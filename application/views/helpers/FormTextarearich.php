<?php

class Zend_View_Helper_FormTextarearich extends Zend_View_Helper_FormTextarea
{
    /**
     * The default number of rows for a textarea.
     *
     * @access public
     *
     * @var int
     */
    public $rows = 24;

    /**
     * The default number of columns for a textarea.
     *
     * @access public
     *
     * @var int
     */
    public $cols = 80;

    /**
     * Generates a 'textarea' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formTextarearich( $name, $value, $attribs ) {
    	
    	$xhtml = '
			<script type="text/javascript">
				//<![CDATA[
					tinyMCE.init({
					  mode: "exact",
					  language: "en",
					  elements: "'.$name.'",
					  plugins: "table,advimage,advlink,flash",
					  theme: "advanced",
					  theme_advanced_toolbar_location: "top",
					  theme_advanced_toolbar_align: "left",
					  theme_advanced_path_location: "bottom",
					  theme_advanced_buttons1: "justifyleft,justifycenter,justifyright,justifyfull,separator,bold,italic,strikethrough,separator,sub,sup,separator,charmap",
					  theme_advanced_buttons2: "bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,image,flash,separator,cleanup,removeformat,separator,code",
					  theme_advanced_buttons3: "tablecontrols",
					  extended_valid_elements: "img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]",
					  relative_urls: false,
					  debug: false 
					  
					  ,language:\'pl\', culture:\'pl\'
					});
				//]]>
			</script>';
    	
    	$xhtml .= $this->formTextarea( $name, $value, $attribs );
    	
    	return $xhtml;
    }
    
}