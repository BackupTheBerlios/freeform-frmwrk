<?

/**
 * This interface represents a particular collation rule for a dialect. While most languages
 * have one default collation, there are some languages that have many collations. In
 * addition to that, this class provides an easy way to convert case of localized strings.
 * For example, German letter Sharp S converts to double S in upper case.
 * @since 1.2.0.Beta
 * @author Dennis Popel
 */ 
interface I18NCollation {
  /**
   * Return the system code of this collation. You use the code to access collations
   * for a particular dialect.
   * @return  string  the code of the dialect, for example, 'Default'
   */
  function getCode();
  
  /**
   * Return the name of the collation in the language of the dialect it belongs to.
   * You can use this name for drop-dpwn selection lists, for example
   * @return  string  the human-readable collation name
   */
  function getName();
  
  /**
   * Use this method to compare UTF-8 strings according to national alphabets and sort orders
   * of different languages
   * @param  string $s1  string to be compared
   * @param  string $s2  string to be compared
   * @param  bool $ci  flag that forces case-insensitive comparison
   * @return  int  the comparison result as returned by the PHP strcmp function
   */
  function compareStrings($s1, $s2, $ci = true);
  
  /**
   * Convert a string to upper case
   * @param  string $s  the string to convert to upper case
   * @return  string  the uppercased string
   */
  function toUpper($s);
  
  /**
   * Convert a string to lower case
   * @param  string $s  the string to convert to lower case
   * @return  string  the lowercased string
   */
  function toLower($s);
}

?>