<?php

/**
*  This is a helper class allows <class>Iterable</class> be accessed via SPL <code>Iterator</code> interface (ie using <code>foreach</code> statement).

* @author   Marian Gracon (mailto:gmarik.at.gmail.com)
*/

class IterableIterator implements Iterator
{
  protected $iterable = null;
  private $current  = null;
  private $position = null;
  

  public function __construct(Iterable $iterable)
  {
    $this->iterable = $iterable;      
  }

  public function rewind() {
    $this->iterable->reset();
    $this->position = -1;
    $this->current = $this->next();
  }

  public function current() {             
    return $this->current;
  }

  public function key() {      
    return $this->position;
  }

  public function next() {
    $this->position += 1;
    return  $this->current = (($this->iterable->hasMore()) ? $this->iterable->getNext() : null);    
  }

  public function valid() {     
    return isSet($this->current);    
  }
}
?> 
