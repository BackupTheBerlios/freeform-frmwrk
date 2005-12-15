<?

/**
 * This is an implementation of <class>I18NMessageDictionary</class> that uses an ini file
 * for storing messages
 * @see <doc file="messagefileformat.txt">Message file format</doc>
 * @since 1.2.0.Beta
 * @author Dennis Popel
 */
class I18NMessageFile implements I18NMessageDictionary {
  private $messages = array();
  
  /**
   * Create the dictionary from the file $path. Note this should be the absolute path
   * (i.e., this method will not call <method>Package::getResourcePath</method> on this path
   * @param  string $path  the path to the message file
   * @throws  I18NException   if file could not be located and read
   */
  function __construct($path) {
    if(is_array($x = parse_ini_file($path, true))) {
      $this->messages = $x;
    } else {
      throw new I18NException('Message file ' . $path . ' could not be loaded');
    }
  }
  
  function translateMessage($id, $lc, $dc = 'Official', $throwException = false) {
    if(isSet($this->messages[$lc . '_' . $dc][$id])) {
      return $this->messages[$lc . '_' . $dc][$id];
    } elseif(isSet($this->messages[$lc . '_Official'])) {
      return $this->messages[$lc . '_Official'][$id];
    } else {
      if($throwException) {
        throw new I18NException('Could not translate message ' . $id . ' into ' . $lc . '_' . $dc);
      } else {
        return null;
      }
    }
  }
}

?>