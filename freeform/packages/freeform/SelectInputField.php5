<?

/**
 * A select form input field. 
 * @author    Dennis Popel  
 * @since   1.0 
 */
class SelectInputField extends InputField {
  private $data = array();
  private $empty = false;
  
  /**
   * Construct a select form field.
   * @param  scalar $value  current selected value
   * @param  array $data    array of scalar name=>value pairs that represent values and labels, respectively.
   * @param  string $empty  a string that will be used as a default label (i.e., 'please select...'). If this is set, then this field will validate itself even if no value has been selected. Otherwise, a value must be selected for this field to validate. 
   */
  function __construct($value, $data, $empty = false) {
    parent::__construct($value, null);
    $this->data = $data;
    $this->empty = $empty;
  }
  
  /**
   * Return the array of name=>value pairs that represent values and labels, respectively.
   * @return  array  array of scalar name=>value pairs
   */
  function getData() {
    return $this->data;
  }
  
  /**
   * Return the label of the empty selection if any
   * @return  string  text of the empty selection label
   */
  function getEmpty() {
    return $this->empty;
  }
  
  /**
   * Return the value of this field
   * @return  scalar  the selected value or false if none selected
   */
  function getValue() {
    if(($rv = parent::getValue()) == "") {
      return false;
    } else {
      return $rv;
    }
  }
  
  /**
   * Returns true if the field value is valid. It is valid only if the $value is a key in 
   * field data, or the empty property is set.
   * @return  bool  true if the filed value is valid
   */
  function isValid() {
    $data = $this->getData();
    return isSet($data[$this->getValue()]) || $this->empty !== false;
  }
}

?>