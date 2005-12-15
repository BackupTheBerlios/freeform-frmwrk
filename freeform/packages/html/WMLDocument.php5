<?

/**
 * This document is used to generate WML pages for WAP devices. It just
 * overrides HTMLDocument::createBody() (internal) method to include
 * the required doctype string. Everything else is done like in HTMLDocument
 */
class WMLDocument extends HTMLDocument {
  function getDocType() {
    return
      '<?xml version="1.0"?' . '>' . "\r\n" .
      '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
  }

  /**
   * Get the default file extension for the WMLDocument, 'wml'
   *
   * @return  string  'wml', the default Wireless Markup Language template 
   * file name extension
   */
  function getFileNameExtension() {
    return 'wml';
  }
  
  function getContentType() {
    return 'text/vnd.wap.wml';
  }
}

?>
