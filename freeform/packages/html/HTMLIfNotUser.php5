<?

/**
 * This is a conditional tag that will display its body if there is no user in the current session.
 * @since 1.1.0
 * @author Dennis Popel
 */
class HTMLIfNotUser extends HTMLIfUser {
  function onOpen() {
    return Session::getUser() ? self::SKIP_BODY : self::PROCESS_BODY;
  }
}

?>