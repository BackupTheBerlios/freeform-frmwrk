<?

/**
 * This is the base class for documents that the 'html' package produces. 
 * The HTML Document API allows creation of custom tags that can output
 * complex presentations in resulting HTML documents displayed at client
 * end. 
 *
 * Each HTMLDocument requires a template that is a valid XML file and has
 * &lt;html&gt; root container. The resulting HTML document will be produced
 * by parsing the template and executing each tag.
 * If the parser encounters a tag that has a custom implementation,
 * it will instantiate that class and call its methods to render its part
 * of the document. So doing it is easy to create custom tag classes that
 * render complex views. For example, if the parser encounters the &lt;HTMLLink&gt;
 * tag in the template, it will instantiate the <class>HTMLLink</class> tag class
 * that will produce valid <tt>a</tt> tag that will link to the specified URL.
 * @since 1.0.0
 * @author Dennis Popel
 * @see <package>i18n</package> for detailed discussion on i18n in Freeform
 */
class HTMLDocument implements Document {
  private $stack = array(0 => array());
  private $stackPosition = 0;
  
  private $templateFile = '';
  private $responce = null;
  
  private $bodyGzipped = false;
  private $notModified = false;
  private $lastModified = 0;
  private $lifeTime = 0;
  private $private = false;
  
  private $regions = array();
  private $locale;
  
  protected $body = '';
  
  /**
   * Construct a new HTMLDocument
   * @param  Responce $responce  the Responce object this document is bound to
   * @param  mixed $locale  the locale to use for mulitlanguage documents (either a Locale or a string name)
   */
  function __construct(Responce $responce, $locale = null) {
    $this->responce = $responce;
    $this->lastModified = time();
    $this->setLocale($locale);
  }
  
  /**
   * Returns the locale this Document was created with
   * @return  I18NLocale  the locale
   * @since 1.2.0.Alpha
   */ 
  function getLocale() {
    return $this->locale;
  }
  
  /**
   * Set the locale of this document. 
   * @param  I18NLocale $locale  the locale to use for the document
   */
  function setLocale($locale) {
    $this->locale = $locale;
  }
  
  /**
   * Return localised message with given id in current locale
   * @param  string $id  the id of the message
   * @return  string  the localised message
   * @since 1.2.0.Alpha
   */
  function getMessage($id) {
    if($this->locale) {
      return $this->locale->translateMessage($id);
    } else {
      return null;
    }
  }
  
  /**
   * Return the filename extension for this document. By default, this returns
   * '.html'. However, subclasses may override this and return default extensions
   * for template files common to the type of those documents.
   *
   * The idea behind this i such that your application may be producing pages
   * both for web and wap, for example. Then you can have same template file names
   * with different extensions to automate template processing like:
   *
   * <source>
   * class MyAction extends Action {
   *   function process() {
   *     $rq = $this->getRequest();
   *     $accept = $rq->getHeader('Accept');
   *     if(strPos($accept, 'wap') !== false) {
   *       $d = new WMLDocument($this->getResponce());
   *     } else {
   *       $d = new HTMLDocument($this->getResponce());
   *     }
   *     $d->setTemplate($this->getPackage()->getResourceName('MyAction.' . $d->getFileNameExtension()));
   *     $this->getResponce()->setDocument($d)
   *   }
   * }
   * </source>
   *
   * This will make your action fairly independent of the user agent
   *
   * @return  string  the extension of the file name that is commonly used for templates of this document type
   */
  function getFileNameExtension() {
    return 'html';
  }
  
  /**
   * Set the template file name to process
   * @param  string $fileName  the name of the template file
   */
  function setTemplate($fileName) {
    $this->templateFile = $fileName;
  }
  
  /**
   * Return the template file name
   * @return  string  name of the template file
   * @since 1.2.0.Beta
   */
  function getTemplate() {
    return $this->templateFile;
  }
  
  /**
   * Sets template variable in current variable stack.
   *
   * @param  string $name  the name of the variable
   * @param  mixed $value  the value
   */
  function setVariable($name, $value) {
    $this->stack[$this->stackPosition][$name] = $value;
  }
  
  /**
   * Set multiple variables in a single call
   *
   * @param  array $vars   array of name=>value pairs to set as template variables
   */
  function setVariables($vars) {
    foreach($vars as $k=>$v) {
      $this->setVariable($k, $v);
    }
  }
  
  /**
   * Get all variables as a one-dimensional array. The variables of the upper stacks will 
   * overwrite those of lower if name collision occurs.
   *
   * @since 1.1.1.Beta
   * @return  array  the key-value pairs that represent current variables in the document
   */
  function getVariables() {
    $rv = array();
    foreach($this->stack as $frame) {
      $rv = array_merge($rv, $frame);
    }
    return $rv;
  }
  
  /**
   * Gets variable from current variable stack frame. If no variable found,
   * previous stack frames are searched. 
   *
   * @param   string $name  name of the variable
   * @param   mixed $defValue  the default value if the variable not found
   * @return  mixed         value of the variable or null
   */
  function getVariable($name, $defValue = null) {
    if(is_array($name)) {
      $name = $name[1];
    }
    
    $i = $this->stackPosition;
    while($i >= 0 && !array_key_exists($name, $this->stack[$i])) {
      $i--;
    }     
    
    return $i >= 0 ? $this->stack[$i][$name] : $defValue;  
  }
  
  /**
   * Get headers that will be sent with this document. These include 
   * Content-Type, Content-Length, and Content-Encoding: gzip if the client
   * supports compression, Cache-Control if the document has been assigned
   * a lifetime.
   *
   * @return  array  HTTP responce headers
   */
  function getHeaders() {
    $rv[] = 'Content-Type: ' . $this->getContentType();
    if($this->isGzipBody()) {
      $rv[] = 'Content-Encoding: gzip';
    }
    $rv[] = 'Content-Length: ' . strLen($this->getBody());
    return $rv;
  }
  
  /**
   * Return the body of this document
   * @return  string  the body
   */
  function getBody() {
    if(!$this->body && !$this->notModified) {
      $this->createBody();
      $this->body = $this->getDocType() . "\r\n" . $this->body;
      if($this->isGzipBody()) {
        $this->body = 
          "\x1f\x8b\x08\x00\x00\x00\x00\x00" .
          substr(gzcompress($this->body, 9), 0, -4);      
      }
    }
    return $this->body;
  }
  
  /** 
   * Returns true if the document body will be gzipped. By default, this is 
   * determined by the Accept-Encoding HTTP header. You can override this
   * if you want to prevent gzipping.
   * @return  bool  true if the document body will be gzipped
   */
  function isGzipBody() {
    $rs = $this->getResponce();
    if($rs) {
      $rq = $rs->getRequest();
      $accept = strToLower($rq->getHeader('Accept-Encoding'));
      return $accept && strPos($accept, 'gzip') !== false;
    } else {
      return false;
    }
  }

  /**
   * Return the response object this document is associated with
   * @return  Response  the response object
   */
  function getResponse() {
    return $this->responce;
  }
  
  
  /**
   * Return the response object this document is associated with
   * @deprecated This method is deprecated, use <method>HTMLDocument::getResponse</method>
   * @return  Response  the response
   */
  function getResponce() {
    return $this->responce;
  }
  
  /**
   * Return the content type header for this type of document. Override this to
   * return other MIME types in subclasses.
   *
   * @return  string  the MIME content type for this document
   */
  function getContentType() {
    return 'text/html; charset=utf-8';
  }
  
  /**
   * Return the XML DOCTYPE declaration for this type of document. For HTML, it
   * inspects the package properties <tt>html.version</tt> and <tt>html.type</tt>
   * to create the proper doctype. Technically the return value of this method will
   * get prepended to the actual body of the resulting document.
   *
   * @return  string  the DOCTYPE for this document
   */
  function getDocType() {
    $pkg = Package::getPackage($this);
    $version = $pkg->getProperty('html.version', '4.01');
    $type = strToLower($pkg->getProperty('html.type', 'transitional'));
    
    $docTypes = array(
      'strict'=>"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML $version//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">",
      'transitional'=>"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML $version Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">",
      'xhtml'=>'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
    );

    return $docTypes[$type];
  }

  /**
   * Get the parser object to parse the template for the current document.
   * You can override this in subclasses to force template parsing with other
   * parser that conforms to SimpleDOM API.
   *
   * @return  SDParser  parser that will parse the template for this document
   */
  function getParser() {
    return new HTMLParser($this);
  }
  
  /**
   * Set a region 
   *
   * @param  string $name  the name of the region
   * @param  HTMLTag $region  the region root element
   * @since 1.1.0
   */
  function setRegion($name, HTMLTag $region) {
    $this->regions[$name] = $region;
  }

  /**
   * Get a region
   *
   * @param  string $name  the name of the region to return
   * @return  HTMLTag  the root element of the region
   * @since 1.1.0
   */
  function getRegion($name) {
    return @$this->regions[$name];
  }

  /**
   * This method is called to return the actual body of this document. It is
   * guaranteed to be called only once per this document lifetime. Subclasses
   * should override this method to add any modifications to the body of the
   * document (i.e., add the doctype declarations). On the contrary, the
   * getBody() method may be called several times. If the body has not been
   * created yet, this method gets called and its result is assigned to the
   * <tt>$body</tt> protected property.<br/>
   * It is unlikely that you will have to call this directly from client code.
   * @return  string  the actual body of this document
   */
  function createBody() {
    $root = $this->getParser()->parse($this->templateFile);
    $this->process($root);
  }

  /**
   * This method is called by HTMLDocument and its subclasses to actually
   * generate the body. It traverses the recursive tree of elements and text
   * nodes and converts them into their string representations. The result
   * will be valid XML string, but not necessary will validate against the
   * (x)HTML DTD - it depends on the template and tag classes
   * @param  SDNode $root  the node to parse
   */
  function process(SDNode $root) {
    if(is_null($root)) {
      return '';
    }
    if($root instanceof HTMLTag && $root->isEnabled()) {
      $this->stack[++$this->stackPosition] = array();
      $c = $root->onOpen();
      if($c == HTMLTag::PROCESS_BODY) {
        if($root->hasChildren()) {
          if($root->isExposed()) {
            $this->body .= ('<' . $root->getName());
            foreach($root->getAttributes() as $k=>$v) {
              $this->body .= (' ' . $k . '="' . htmlSpecialChars($v, ENT_QUOTES, 'UTF-8') . '"');
            }
            $this->body .= ('>');
          }
          do {
            $root->onBeforeBody();
            foreach($root->getChildren() as $c) {
              $this->body .= $this->process($c);
            }
            $root->onAfterBody();
          } while($root->isRepeatBody());
          $root->onClose();
          if($root->isExposed()) {
            $this->body .= ('</' . $root->getName() . '>');
          }
        } else {
          if($root->isExposed()) {
            $this->body .= ('<' . $root->getName());
            foreach($root->getAttributes() as $k=>$v) {
              $this->body .= (' ' . $k . '="' . htmlSpecialChars($v, ENT_QUOTES, 'UTF-8') . '"');
            }
            $this->body .= (' />');
          }
          $root->onClose();
        }
      }
      unSet($this->stack[$this->stackPosition--]);      
    } elseif($root instanceof HTMLTextNode) {
      $c = $root->getContent();
      if($root->getRaw()) {
        $c = htmlSpecialChars($c, ENT_QUOTES, 'UTF-8');
      }
      $this->body .= $c;
    } elseif($root instanceof HTMLPINode) {
      ob_start();
      // see PHP bug 21909
      //$x = $root->function;
      //$x($this->getVariables());
      $root->call($this->getVariables());
      $this->body .= ob_get_contents();
      ob_end_clean();
    }
  }
  
  /**
   * Replace the variable placeholders with their actual content in an arbitrary string.
   * This is called by the <method>HTMLTextNode::getContent</method>() and <method>HTMLTag::getAttribute</method>();
   * you will be unlikely to call this method directly.
   * @param  string $x  the string to expand variables in
   * @return  string  the resutling string
   * @since 1.2.0.Beta
   */
  function expandVars($x) {
    $rv = preg_replace_callback('|{@%([[:alnum:]_\.]+)}|mu', array($this, 'translateByRef'), $x);
    $rv = preg_replace_callback('|{%([[:alnum:]_\.]+)}|mu', array($this, 'expandVariable'), $rv);
    $rv = preg_replace_callback('|{@([[:alnum:]_\.]+)}|mu', array($this, 'translateMessage'), $rv);
    return $rv;
  }
  
  // These are used internally by the expandVars method.
  private function expandVariable($id) {
    return $this->getVariable($id[1]);
  }
  
  private function translateMessage($id) {
    return $this->getMessage($id[1]);
  }
  
  private function translateByRef($name) {
    $value = $this->getVariable($name[1]);
    return $this->getMessage($value);
  }
}

?>