<?php

/**
*  This is a helper class allows <class>PaginatedIterable</class> be accessed via SPL <code>Iterator</code> interface (ie using <code>foreach</code> statement).

* @author   Marian Gracon (mailto:gmarik.at.gmail.com)
*/

class PaginatedIterableIterator extends IterableIterator
{  
  public function __construct(PaginatedIterable $iterable) {
    parent::__construct($iterable);
  } 

  public function key() {      
    return parent::key() + $this->iterable->getFirstRowNumber();
  }
  
}
?> 
