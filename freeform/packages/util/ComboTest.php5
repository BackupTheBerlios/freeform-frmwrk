<?

class ComboTest extends Action {
  function process() {
    $a = array('one'=>'1', 'two'=>'2', 'three'=>'3'); // We will iterate over key=>value pairs of this array
    $ci = new ComboIterable(array('key', array_keys($a)), array('val', array_values($a)), array('index', range(1, 3)));
    while($ci->hasMore()) {
      $r = $ci->getNext();
      var_dump($r);
    }    
    
    $a1 = new IterableArray(array(array('alpha'=>1), array('alpha'=>2), array('alpha'=>3)));
    $a2 = new IterableArray(array(array('beta'=>10), array('beta'=>20), array('beta'=>30)));
    $ci = new ComboIterable($a1, $a2, array('idx', range(1, 3)));
    while($ci->hasMore()) {
      $r = $ci->getNext();
      var_dump($r);
    }
  }
}

?>