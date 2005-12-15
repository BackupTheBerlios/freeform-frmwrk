<?

/**
 * A regular expression validator. It uses the mb_ereg function to validate the values so
 * it will correctly handle non-latin characters.
 * @author Dennis Popel  
 * @since 1.0.0 
 */
class RegexValidator extends Validator {
  /**
   * A valid user name regex pattern
   */
  const USER_NAME = '^([[:alpha:]]{1})([[:alnum:]_]{1,})$';
  
  /**
   * A valid email regex pattern
   */
  const EMAIL = '^[a-zA-Z0-9\\._-]+@[a-zA-Z0-9\\.-]+\\.[a-zA-Z.]{2,6}$'; 
  
  private $pattern = '.*';
  
  /**
   * Construct new RegexValidator with given pattern
   *
   * @param  string $pattern  a regular expression 
   */
  function __construct($pattern) {
    $this->pattern = $pattern;
  }
  
  /**
   * Return true if $value matches the pattern
   *
   * @param  string $value  a value to validate
   */
  function isValid($value) {
    return mb_ereg($this->pattern, $value);
  }
}

?>