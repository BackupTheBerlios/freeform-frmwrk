<?

/**
 * This is a conditional tag that will display its body if the template variable denoted by the
 * <tt>key</tt> attribute evaluates is not false and is not null.
 * @since 1.1.0
 * @author Dennis Popel
 */
class HTMLIfSet extends HTMLTag {
  function isExposed() {
    return false;
  }

  function onOpen() {
    $key = $this->getAttribute('key');
    return (bool)$this->getDocument()->getVariable($key);
  }
}

?> 