<?

/**
 * This interface represents a family of language dialects. For example, there are 2 dialects
 * of Norwegian or several dialects of Spanish. For example, in US English
 * has changed to become what we call American English. 
 *
 * This class encapsulates possible dialects under the same language. There always exists
 * a special dialect, called Official, that is spoken in the country of the language
 * origin. This class gives you access to the list of dialects.
 *
 * Languages are distinguished by the two-letter system codes.
 * @since 1.2.0.Beta
 * @author Dennis Popel
 */
interface I18NLanguage {
  /**
   * Return the two-letter language code
   * @return  string  the language code
   */
  function getCode();
  
  /**
   * Return the name of the language (in that language)
   * @return  string  the name of the language
   */
  function getName();
  
  /**
   * Return a specified dialect by its code
   * @param  string $code  the dialect code. If null, will return the Official dialect
   * @return  I18NDialect  the dialect
   * @throws  I18NException  if the dialect could not be found
   */
  function getDialect($code = 'Official');
  
  /**
   * Return the list of dialects of this language
   * @return  array  the list of dialects in the form of code=>dialect pairs
   */
  function getDialects();
  
  
  
}

?>