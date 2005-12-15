<?

/**
 * This is a conditional tag that will display its body if there is current user in the current session.
 * The optional <tt>role</tt> attribute can be used to enforce additional check of the user's role
 * via a call to <method>User::isRole</method>.
 * @since 1.1.0
 * @author Dennis Popel
 */
class HTMLIfUser extends HTMLTag {
  function isExposed() {
    return false;
  }
  
  function onOpen() {
    $role = $this->getAttribute('role');
    if($u = Session::getUser()) {
      $this->getDocument()->setVariable('userName', $u->getUserName());
      if($role) {
        return $u->isRole($role) ? HTMLTag::PROCESS_BODY : HTMLTag::SKIP_BODY;
      } else {
        return HTMLTag::PROCESS_BODY;
      }
    } else {
      return HTMLTag::SKIP_BODY;
    }
  }
}

?>