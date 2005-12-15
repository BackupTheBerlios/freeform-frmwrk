<?

/**
 * This class is the base class for all form input fields. A form field has a value and 
 * can have a validator. The form renderer tag (e.g., HTMLShowForm) will automatically
 * render respective form fields for different descendants of this class, taking care of
 * correctly outputting the 'value' attribute or generating 'select' field.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */ 
abstract class InputField {
  private $value;
  private $form;
  private $validator;
  
  /**
   * Create a new input filed with the given value and validator
   * @param  mixed $value  the default value of the field
   * @param  Validator $validator  the validator
   */
  function __construct($value, $validator = null) {
    $this->value = $value;
    $this->validator = $validator;
  }
  
  /**
   * Set the form that ownt this field. Normally you will not call this method directly
   * @param  Form $form  the owning form
   */
  function setForm(Form $form) {
    $this->form = $form;
  }
  
  /**
   * Get the owning form
   * @return  Form  the owning form
   */
  function getForm() {
    return $this->form;
  }
  
  /**
   * Get the value of the field
   * @return  mixed  the value
   */
  function getValue() {
    return $this->value;
  }
  
  /**
   * Set the value of the field
   * @param  mixed $value  the value to set
   */
  function setValue($value) {
    $this->value = $value;
  }
  
  /**
   * Returns true if the field has not been assigned a validator, otherwise
   * it returns result of validation
   * @return  bool  true if the field is valid
   */
  function isValid() {
    return $this->validator ? $this->validator->isValid($this->value) : true;
  }
}

?>