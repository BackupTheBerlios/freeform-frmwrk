<?

/**
 * This is a general-purpose comparator interface. Its sole method, 
 * <method>Comparator::compare</method> should return -1, 0, or 1 if the first argument is
 * less, equal or greater than the second, respectively
 * @since 1.0.0
 * @author Dennis Popel
 */
interface Comparator {
  /**
   * Compare two items
   *
   * @param  mixed $i1  first item
   * @param  mixed $i2  second item
   * @return  int  the result of the comparison
   */
  function compare($i1, $i2); 
}

?>
