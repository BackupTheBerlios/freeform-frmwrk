<?
/**
 * A complex validator that consists of several <class>Validator</class>s and validates only 
 * if every validator in the set validates the test value
 * @author Alexey Bukhtin
 * @since 1.2.0.Beta
 */
class ValidatorSet extends Validator {
  private $validators = array();
  
  /**
   * Construct a ValidatorSet object from either an array of validators or 
   * a single validator object. You can later add more validators calling the
   * <method>ValidatorSet::add</method>() method  
   * @param  array $validators  an array of validators (optional)
   */
  function __construct($validators = array()) {
    foreach($validators as $v) {
      $this->add($v);
    }
  }
  
  /**
   * Validate a value. Returns true if and only if all validators in the set validate this
   * value. Make sure that all the validators in the set can understand the value. If the set is 
   * empty, returns true as there is no criterion to validate against
   * @param  mixed $value  the value to validate
   * @return  bool  the result of validation
   */
  function isValid($value) {
    foreach($this->validators as $v) {
      if(!$v->isValid($value)) {
        return false;
      }
    }
    return true;
  }
  
  /**
   * Add a validator to the set
   * @param  Validator $validator  The validator to add to the set
   */
  function add(Validator $validator) {
    $this->validators[] = $validator;
  }
}

?>