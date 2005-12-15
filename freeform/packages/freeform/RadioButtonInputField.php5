<?

/**
 * This class represents a single set of radio buttons in the input form. Since the radio
 * buttons all share the same field name in the form, this class needs an array of values 
 * to be constructed. These values are then placed in the form as you display the fields of
 * the set.
 * @since 1.1.0.Beta
 * @author Dennis Popel
 */
class RadioButtonInputField extends InputField {
  private $values;
  private $ia = null;
  
  /**
   * Construct a radio button object
   * @param  mixed $value  the value of the field
   * @param  array $values  the array of values of radio buttons in the set
   */
  function __construct($value, $values) {
    parent::__construct($value, null);
    $this->values = $values;
    $this->ia = new IterableArray($values);
    if(!$value) {
      $this->setValue(array_shift($values));
    }
  }
  
  /**
   * Returns true if the field is valid. It will be valid if the value form the request
   * is among of those passed upon construction.
   * @return  bool  true if the field is valid
   */
  function isValid() {
    return in_array($this->getValue(), $this->values);
  }
  
  function getNextValue() {
    if($this->ia->hasMore()) {
      return $this->ia->getNext();
    } else {
      return null;
    }
  }
}

?>