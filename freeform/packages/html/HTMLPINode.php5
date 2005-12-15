<?

class HTMLPINode extends SDProcessingInstruction {
  function call($vars) {
    $x = create_function('$vars', 'extract($vars);' . $this->getData());
    if($x) {
      $x($vars);
    }
  }
}

?>