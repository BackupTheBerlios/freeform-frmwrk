<?

/**
 * This is the interface to message dictionaries. A message dictionary is an abstraction 
 * of a message translation facility. This interface declares just a single method,
 * <method>I18NMessageDictionary::translateMessage</method> that will fetch localized
 * message by its ID and target language/dialect code. Note that the dialect code is 
 * required; if the message for this dialect is not found, this will look up the
 * Official dialect for the message translation.
 *
 * This package ships with the <class>I18NMessageFile</class> class that is an implementation
 * of this interface and allows you to keep the translation infomation in ini-like files.
 *
 * <b>Example</b><br/>
 * Given the following message file:
 * 
 * <pre>
 *   [en_Official]
 *   hello='Hello!'
 *   bye='Bye!'
 *   something='Oops!'
 *
 *   [en_American]
 *   hello='Hi!'
 *   bye='So long!' 
 * </pre>
 * 
 * and the code:
 * 
 * <source>
 *   $mf = new I18NMessageFile('/path/to/file');
 *   $s = $mf->translateMessage('hello', 'en', 'Official');
 *   // $s will contain 'Hello!'
 *   $s = $mf->translateMessage('hello', 'en', 'American');
 *   // $s will contain 'Hi!'
 *   $s = $mf->translateMessage('something', 'en', 'American');
 *   // $s will contain 'Oops!' as it will search the Official dialect
 *   $s = $mf->translateMessage('someotherthing', 'en', 'American', true);
 *   // will throw an exception
 * </source>
 * @author Dennis Popel
 * @since 1.2.0.Beta
 */
interface I18NMessageDictionary {
  
  /**
   * Return translated message with the given ID, into the given language dialect.
   * If the specified dialect or translation could not be found, this will try to translate into the
   * Official dialect of the language.
   * @param  string $id  the message ID
   * @param  string $lc  the language code (like en)
   * @param  string $dc  the dialect code (like Official)
   * @param  bool $throwException  set this to true if you want this method to throw an exception if the message could not be translated
   * @return  string  the translated message
   * @throws  I18NException  if the message could not be translated and throwing was forced by $throwException
   */
  function translateMessage($id, $lc, $dc = 'Official', $throwException = false);
}

?>