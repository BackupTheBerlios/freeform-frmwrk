<?

/**
 * An Iterable interface over an array. Upon construction, an array 
 * is passed as a sole parameter, that will be iterated over.
 *
 * Note that the array passed will be copied, so it's possible to
 * alter the passed array not changing the behaviour of this object.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */
class IterableArray implements Iterable {
  private $names = array();
  private $values = array();
  private $position = 0;
  
  /**
   * Construct an IterableArray from array $x
   * @param  array $x  the array to be iterated over
   */
  function __construct($x) {
    if(!is_array($x)) {
      $x = array();
    }
    $this->names = array_keys($x);
    $this->values = array_values($x);
  }
  
  /**
   * Return true if there are more elements
   * @return  bool  true if there are more elements
   */
  function hasMore() {
    return $this->position < count($this->names);
  }
  
  /**
   * Return current key of the array
   * @return  string  the current key
   */
  function getCurrentKey() {
    return $this->names[$this->position];
  }
  
  /**
   * Return the current value of the array
   * @return  mixed  current value of the array
   */
  function getCurrentValue() {
    return $this->values[$this->position];    
  }
  
  /**
   * Return next element and advance pointer
   * @return  mixed  next element in the array
   * @throws  NoMoreElementsException  if there were no more items
   */
  function getNext() {
    if($this->hasMore()) {
      $rv = $this->values[$this->position];
      $this->position++;
      return $rv;
    } else {
      throw new NoMoreElementsException();
    }
  }
  
  /**
   * Rewind this iterable array
   * @since 1.2.0.Alpha
   */
  function reset() {
    $this->position = 0;
  }
}

?>