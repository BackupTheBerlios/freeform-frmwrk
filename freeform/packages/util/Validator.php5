<?

/**
 * This is an abstract Validator class. Validators serve the sole purpose of
 * validating values passed to their <method>Validator::isValid</method> method based on the different
 * criteria set in extending classes.
 * @author Dennis Popel
 * @since 1.0.0
 */
abstract class Validator {
  /**
	 * Validate a value
	 *
	 * @return  bool  true if the value is valid
	 */
  abstract function isValid($value);
}

?>