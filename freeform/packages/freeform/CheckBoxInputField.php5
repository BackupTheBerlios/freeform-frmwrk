<?

/**
 * This class represents a single check box input field of an input form.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */
class CheckBoxInputField extends InputField {
  
  /**
   * Construct new check box form input field with flag $value. If the 
	 * $value evaluates to true then the check box will be on.
   *
   * @param   bool $value   the state of the check box.
   */
  function __construct($value = false) {
    parent::__construct($value, null);
  }
}

?>