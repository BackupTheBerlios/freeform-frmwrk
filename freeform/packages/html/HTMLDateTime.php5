<?

/**
 * This tag displays formatted date. The template variable name holding the timestamp 
 * to display is passed via the <tt>key</tt> attribute, and the format is specified by the 
 * <tt>format</tt> attribute.
 *
 * Note that the timestamp must always be the UNIX timestamp, i.e., it is considered to 
 * be the GMT time (exactly as returned by the <tt>time()</tt> function). The time displayed
 * will be adjusted to the timezone of the document's locale. If no timestamp given (i.e.,
 * the <tt>key</tt> attribute points to an invalid template variable), the current time
 * will be displayed. If there is no locale set for the document, this tag will use 
 * <tt>strftime</tt> function to format the date and time according to the value of the
 * <tt>format</tt> attribute. If there is no <tt>format</tt> attribute specified, 
 * this tag will revert to the locale's default; if there is no locale, it will
 * use the "%H:%M:%S GMT" format string.
 *
 * <b>Note</b><br/>
 * Since the format string uses the '%' symbol, make sure you have not template variables
 * named like first format specifier or include a leading space in the <tt>format</tt>
 * attribute.
 *
 * The format string is the same as for PHP <tt>strftime</tt> function (also see 
 * <method>I18NLocale::formatDateTime</method> for notes).
 *
 * Since 1.2.0.Beta this tag supports the <tt>mode</tt> attribute, which, if present, 
 * must contain either 'date' or 'time' strings. If so, the meaning and values for the
 * <tt>format</tt> attribute change: it must be either 'short', 'long' or 'full'. If these 
 * conditions are met, this tag will use the <method>I18NLocale::formatDate</method> or
 * <method>I18NLocale::formatTime</method> methods depending on the value of the <tt>mode</tt>
 * attribute. The format specifier will be either I18N::SHORT, I18N::LONG or I18N::FULL depending
 * on the value of the <tt>format</tt> attribute value.
 * @since 1.1.0
 * @author Dennis Popel
 * @see <package>i18n</package> package
 */
class HTMLDateTime extends HTMLTag {
  private $formats = array(
    'short' => I18N::SHORT,
    'long' => I18N::LONG,
    'full' => I18N::FULL
  );
  
  function isExposed() {
    return false;
  }
  
  function onOpen() {
    $this->removeAll();
    $d = $this->getDocument();
    $l = $d->getLocale();
        
    if($k = $this->getAttribute('key')) {
      $time = $d->getVariable($k, time());
    } else {
      $time = time();
    }
    
    if($l) {
      // If we have locale, we can use it to format values
      $f = $this->getAttribute('format', null);
      // Check if we have to format date or time
      switch($this->getAttribute('mode')) {
        case 'date':
          $rv = $l->formatDate($time, $this->formats[$f]);
          break;
        case 'time':
          $rv = $l->formatTime($time, $this->formats[$f]);
          break;
        default:  
         $rv = $l->formatDateTime($time, $f);
      }
    } else {
      // Else pass to strftime()
      $f = $this->getAttribute('format', '%H:%M:%S GMT');
      $rv = gmstrftime($f, $time);
    }
      
    $n = new HTMLTextNode($this, $rv);
    $this->addNode($n);
    return self::PROCESS_BODY;
  }
}

?>