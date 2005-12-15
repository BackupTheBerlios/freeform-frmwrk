<?

/**
 * A text input field (either a text, textarea or password)
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */ 
class TextInputField extends InputField {
  const TEXT = 1;
  const TEXTAREA = 2;
  const PASSWORD = 3;
  
  private $type;
  
  /**
   * Construct a text input field with default value, validator and a given type
   *
   * @param  string $value          default value
   * @param  Validator $validator   validator of the field
   * @param  int $type              input field type (TEXT, TEXTAREA, PASSWORD)
   */
  function __construct($value = '', $validator = null, $type = TextInputField::TEXT) {
    parent::__construct($value, $validator);
    $this->type = $type;
  }
  
  /**
   * Return the type of the field
   *
   * @return  int   the type of the field
   */
  function getType() {
    return $this->type;
  }
}

?>