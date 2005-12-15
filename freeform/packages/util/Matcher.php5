<?

/**
 * This is a general-purpose matcher interface. Its sole method, <tt>match</tt>,
 * should return true if the passed parameter satisfies conditions of the
 * matcher object.
 */
interface Matcher {
  /**
   * Check is the item $i complies with conditions of this matcher object
	 *
	 * @param  mixed &i  item to test
	 * @return  bool  true if the item complies with conditions of this matcher
	 */
  function match($i);
} 

?>
