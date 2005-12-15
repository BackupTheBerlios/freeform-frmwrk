<?

/**
 * This is a conditional tag that will display its body if the template variable denoted by the
 * <tt>key</tt> attribute evaluates to false ir is null.
 * @since 1.1.0
 * @author Dennis Popel
 */
class HTMLIfNotSet extends HTMLTag {
  function isExposed() {
    return false;
  }

  function onOpen() {
    $key = $this->getAttribute('key');
    $v = $this->getDocument()->getVariable($key);
    return $v == false || is_null($v);
  }
}

?>