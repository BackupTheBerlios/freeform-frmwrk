<?

/**
 * This is the basic XML parser that parses valid XML documents and returns
 * their root elements. Using methods of <class>SDElement</class> and <class>SDTextNode</class> it is
 * easy to traverse the elements and their attributes. 
 * The parser and the SimpleDOM API support UTF-8 encoding only.
 * You can subclass SDParser and/or SDElement and SDTextNode to extend the 
 * default functionality (like the HTML package does).
 * @since 1.1.0 beta
 * @author Dennis Popel
 */
class SDParser {
  private $root = null;
  private $current = null;
  private $xmlParser = null;
  private $fileName = null;
  
  /**
   * This method will be called when the parser reaches a new tag and
   * must return a SDNode. You can override this function if you want
   * to create custom parsers that will return subclasses of SDElement.
   * By default, it returns an SDElement.
   * @param   SDElement $parent   the parent of the element
   * @param   string $tagName     the name of the tag
   * @param   array $attrs        attributes of the tag
   * @return  SDElement           a SimpleDOM node or null
   */
  function onTagOpen($parent, $tagName, $attrs) {
    return new SDElement($parent, $tagName, $attrs);
  }
  
  /**
   * This method is called whenever a closing tag is encountered in the XML file
   * @param  SDElement $e  The tag that is being closed
   */
  function onTagClose(SDElement $e) {}

  /**
   * This method is called when the parser encounters character data.
   * It should return an SDTextNode, you can override this to return
   * a subclass of SDTextNode for particular parser. The parser will take care
   * of linking the element with parent.
   * @param   SDElement $parent   the parent of this text node
   * @param   string $data        the character data
   * @return  SDTextNode          a text node or null
   */
  function onCData($parent, $data) {
    return new SDTextNode($parent, $data);
  }
  
  /**
   * This method is called whenever a processing instruction is encountered in the source document
   * @param  SDElement $parent  the parent element that contains this PI
   * @param  string $target  the target of the PI
   * @param  string $data    the data of the PI
   * @return  SDProcessingInstruction  the PI node
   * @since 1.1.0
   */
  function onProcessingInstruction($parent, $target, $data) {
    return new SDProcessingInstruction($parent, $target, $data);
  }

  /**
   * Parse an XML file and return the root element
   * @param   string $fileName    the file to parse
   * @return  SDElement  the root element
   * @throws  SDParserException  if the file cannot be parsed or read or has no data
   */
  function parse($fileName) {
    $this->fileName = $fileName;
    $data = @file_get_contents($fileName);
    if(!$data) {
      throw new SDParserException(SDParserException::FILE_NOT_FOUND_OR_EMPTY, null, 0, 0, $fileName);
    } else {
      try {
        return $this->parseString($data);
      } catch(SDParserException $sdpe) {
        $sdpe->setFileName($fileName);
        throw $sdpe;
      }
    }
  }

  /**
   * Parse an XML data contained in a string
   * @param  string $data         the XML data to parse
   * @throws  SDParserException  if the data cannot be parsed
   */
  function parseString($data) {
    $this->xmlParser = xml_parser_create('UTF-8');
    xml_parser_set_option($this->xmlParser, XML_OPTION_CASE_FOLDING, 0);
    xml_set_element_handler($this->xmlParser, array($this, 'parseStartElement'), array($this, 'parseEndElement'));
    xml_set_character_data_handler($this->xmlParser, array($this, 'parseCData'));
    xml_set_processing_instruction_handler($this->xmlParser, array($this, 'parsePI'));
    xml_set_default_handler($this->xmlParser, array($this, 'parseCData'));
    xml_set_start_namespace_decl_handler($this->xmlParser, array($this, 'parseNSStart'));
    xml_set_end_namespace_decl_handler($this->xmlParser, array($this, 'parseNSEnd'));

    if(xml_parse($this->xmlParser, $data)) {
      return $this->root;
    } else {
      $code = xml_get_error_code($this->xmlParser);
      $pos = $this->getPosition();
      throw new SDParserException($code, $this->root, $pos[0], $pos[1]);
    }
  }
	
  /**
   * Get the root element of the currently parsing document
   * @return  SDElement  the root node
   */
  function getRoot() {
    return $this->root;
  }
  
  /**
   * Get the current parsing file name
   * @return  string  the file being parsed
   */
  function getFileName() {
    return $this->fileName;
  }
  
  /**
   * Return the current parse position as an array
   * @return  array  the current position of the parser, index 0 is the line, index 1 is the column
   */
  function getPosition() {
    return array(
      xml_get_current_line_number($this->xmlParser),
      xml_get_current_column_number($this->xmlParser));
  }
  
  private function parseStartElement($parser, $name, $attrs) {
    $tag = $this->onTagOpen($this->current, $name, $attrs);
    if(is_null($this->root)) {
      $this->current = $tag;
      $this->root = $tag;
    } else {
      $this->current->addNode($tag);
      $this->current = $tag;
    }
  }

  private function parseEndElement($parser, $name) {
    $this->onTagClose($this->current);
    $this->current = $this->current->getParent();
  }

  private function parseCData($parser, $data) {
    if($this->current) {
      $node = $this->onCData($this->current, $data);
      if($node) {
        $this->current->addNode($node);
      }
    } else {
      $pos = $this->getPosition();
      throw new SDParserException(SDParserException::NON_ELEMENT_BEFORE_ROOT, null, $pos[0], $pos[1], $this->getFileName());
    }
  }

  private function parsePI($parser, $target, $data) {
    if($pi = $this->onProcessingInstruction($this->current, $target, $data)) {
      $this->current->addNode($pi);
    }
  }
}

?>