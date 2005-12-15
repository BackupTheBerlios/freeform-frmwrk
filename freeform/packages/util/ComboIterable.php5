<?

/**
 * This class allows you to combine several iterables into one so that you
 * iterate them in parallel. The result of a call to <method>ComboIterable::getNext</method>() will be
 * an array that holds key=>value pairs combined from the results of calls to
 * <method>Iterable::getNext</method>() methods of each iterable contained by this
 * ComboIterable. The primary use of this class is allowing to iterate over 
 * keys and values of a single array so that each call to <method>ComboIterable::getNext</method>
 * will return an array containing current key and value. The keys of this resulting array
 * will be fixed like 'key' and 'value' (you can specify them when instantiating a
 * ComboIterable). 
 *
 * The usage of this class is best illustrated by the following example:
 *
 * <source>
 * $a = array('one'=>'1', 'two'=>'2', 'three'=>'3'); // We will iterate over key=>value pairs of this array
 * $ci = new ComboIterable(array('key', array_keys($a)), array('val', array_values($a)));
 * while($ci->hasMore()) {
 *   $r = $ci->getNext();
 *   echo $r['key'], '=', $r['val'], "\n";
 * }
 * </source>
 *
 * This snippet will print
 *
 * <pre>
 * one=1
 * two=2
 * three=3
 * </pre>
 *
 * Such approach is very convenient when you need to display keys and values of a single array
 * with the <class>HTMLShowIterable</class> tag - if this tag iterates over an iterable that
 * returns scalar values, it is impossible to access array keys from the template; only values
 * are available as the template variable <tt>html.showiterable.value</tt>. Using ComboIterable
 * you can easily loop through array keys and values, as well as combine more parallel arrays
 * into one <class>Iterable</class>.
 *
 * You can also use iterables that return arrays with this class. In this case just
 * pass the iterables themselves to the constructor. For example:
 *
 * <source>
 * $a1 = new IterableArray(array(array('alpha'=>1), array('alpha'=>2), array('alpha'=>3)));
 * $a2 = new IterableArray(array(array('beta'=>10), array('beta'=>20), array('beta'=>30)));
 * $ci = new ComboIterable($a1, $a2);
 * while($ci->hasMore()) {
 *   $r = $ci->getNext();
 *   echo $r['alpha'], ', ', $r['beta'], "\n";
 * }
 * </source>
 *
 * produces
 *
 * <pre>
 * 1, 10
 * 2, 20
 * 3, 30
 * </pre> 
 * @since 1.2.0.Beta
 * @author Dennis Popel
 */
class ComboIterable implements Iterable {
  private $iters = array();
  
  /**
   * Construct a ComboIterable from arbitrary number of <class>Iterable</class>s
   * @param  mixed $i  instance of an <class>Iterable</class> that returns arrays or an array of two elements - first containing the record key, second containing a list of values to return under this key in every call to <method>getNext</method>(). Accepts any number of parameters (at least one).
   */
  function __construct() {
    if(func_num_args() == 0) {
      throw new IllegalArgumentException('No iterables specified');
    }
    foreach(func_get_args() as $arg) {
      if(is_array($arg)) {
        $this->iters[$arg[0]] = new IterableArray($arg[1]);
      } else {
        $this->iters[] = $arg;
      }
    }
  }
  
  function hasMore() {
    foreach($this->iters as $i) {
      if(!$i->hasMore()) {
        return false;
      }
    }
    return true;
  }
  
  function getNext() {
    $rv = array();
    foreach($this->iters as $l=>$i) {
      $r = $i->getNext();
      if(is_scalar($r)) {
        $rv = array_merge($rv, array($l=>$r));
      } elseif(is_object($r)) {
        $rr = array();
        foreach($r as $k=>$v) {
          $rr[$k] = $v;
        }
        $rv = array_merge($rv, $rr);
      } elseif(is_array($r)) {
        $rv = array_merge($rv, $r);
      }
    }
    return $rv;
  }
  
  function rewind() {
    foreach($this->iters as $i) {
      $i->rewind();
    }
  }
}

?>