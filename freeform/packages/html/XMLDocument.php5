<?

/**
 * This document is used to generate generic XML documents. It just
 * overrides HTMLDocument::createBody() (internal) method to include
 * the xml signature. Everything else is done like in HTMLDocument
 */
class XMLDocument extends HTMLDocument {
  function getContentType() {
    return 'application/xml';
  }
  
  /**
   * Get the default file extension for the XMLDocument, 'xml'
   *
   * @return  string  'xml', the default eXtensible Markup Language template 
   * file name extension
   */
  function getFileNameExtension() {
    return 'xml';
  }
  
  function getDocType() {
    return '<?xml version="1.0" encoding="UTF-8"?' . '>'; 
  }
}
