<?

/**
 * This is the central class in the i18n package. It provides access to locale-specific 
 * information and methods for a given combination of country and dialect information.
 *
 * You access instances of this class via a call to static <method>I18N::getLocale</method>()
 * factory method. Then you call various formatting methods of this object to have your
 * data formatted in a locale-specific way.
 *
 * Locales support translation of messages via the <class>I18NMessageDictionary</class>
 * classes. Together with <package>html</package> you can create localizable templates
 * for multilingual sites.
 *
 * Every locale object can have its own message dictionary and time zone. All date/time
 * formattings will be performed on UNIX timestamps converted to the locale's time zone
 * (see <method>I18NLocale::setTimeZone</method>() method), while all messages will
 * be translated into the locale's dialect.
 *
 * The concrete implementations of the I18N API will likely use <class>I18NCountry</class> 
 * and <class>I18NDialect</class> classes to combine them into the locale object; all
 * calls to the <class>I18NLocale</class> will be rerouted to the corresponding
 * methods of the underlying objects (this is how the <package>free18n</package> package
 * works)
 * @since 1.2.0.Alpha
 * @author Dennis Popel
 * @see <package>free18n</package> package docs for tips on creating and using your own locale, country and dialect classes
 */
abstract class I18NLocale {
  private $md, $tz;
  
  /**
   * Each locale object is constructed with the country code, language code and dialect 
   * code. Subclasses will always have to call the inherited constructor.
   * @param  string $cc  the country code
   * @param  string $lc  the language code
   * @param  string $dc  the dialect code
   * @throws  I18NException  the subclasses may throw this if they cannot find specified country or dialect
   */
  function __construct($cc, $lc = '', $dc = '') { }
  
  /**
   * Return the I18NDialect class used by this locale object
   * @return  I18NDialect  the dialect
   */
  abstract function getDialect();
  
  /**
   * Return the I18NCountry class used by this locale object
   * @return  I18NDialect  the country
   */
  abstract function getCountry();
  
  /**
   * Return the time zone object that is used by this locale object
   * @return  I18NTimeZone  the time zone
   */
  function getTimeZone() {
    return $this->tz;
  }
  
  /**
   * Set the time zone for use by this locale object
   * @param  I18NTimeZone $tz  either a code or a time zone object to set
   */
  function setTimeZone($tz) {
    $this->tz = $tz;
  }
  
  /**
   * This method takes the UNIX timestamp $dt and returns the localized formatted
   * date and time string in the current time zone of this locale. The format specifiers are 
   * the same, except for <tt>%z</tt> that is used to show the time zone name (like EEST),
   * and <tt>%Z</tt> that is used to show the time zone offset (like +0300)
   * @param  int $dt  the UNIX timestamp to format. It will be converted to the current time zone before formatting
   * @param  string $format  the format string as in the PHP strftime function. If omitted, the locale default will be used 
   * @return  string  the formatted date/time
   * @see <class>HTMLDateTime</class> tag for date/time formatting in templates
   */
  abstract function formatDateTime($dt, $format = null);
  
  /**
   * This function formats time of the given UNIX timestamp using either short, long or full
   * format
   * @param  int $dt  the UNIX timestamp to format. It will be converted to the current time zone of the locale
   * @param  int $format  either the I18N::SHORT, I18N::LONG or I18N::FULL
   * @return  string  the formatted time 
   * @since 1.2.0.Beta
   */
  abstract function formatTime($dt, $format = I18N::SHORT);
  
  /**
   * This function formats date of the given UNIX timestamp using either short, long or full
   * format
   * @param  int $dt  the UNIX timestamp to format. It will be converted to the current time zone of the locale
   * @param  int $format  either the I18N::SHORT, I18N::LONG or I18N::FULL
   * @return  string  the formatted date 
   * @since 1.2.0.Beta
   */
  abstract function formatDate($dt, $format = I18N::SHORT);
  
  /**
   * This function formats a monetary value according to the dialect's format and
   * country's currency codes and symbols
   * @param  float $n  the value to format
   * @return  string  the formatted monetary value
   */
  abstract function formatCurrency($n);
  
  /**
   * This method formats a number according to the dialect's format
   * @param  float $n  the value to format
   * @param  int $decimals  number of digits in the fractional part
   * @return  string  the formatted value
   */
  abstract function formatNumber($n, $decimals = 2);
  
  /**
   * Return the currency code of the country. You may regard this method as a shortcut to 
   * the <method>I18NCountry::getCurrencyCode</method>, that will return the underlying
   * country's intl currency code
   * @return  string  the intl currency code
   */
  abstract function getCurrencyCode();
  
  /**
   * This method returns the currency symbol. Subclasses may want to override this
   * to return translated symbols for a particular mix of a dialect and a country
   * (for example, in the CA_uk_Official locale this will return 'дол.' instead of '$')
   * @return  string  the currency symbol
   */
  abstract function getCurrencySymbol();
  
  /**
   * This method returns the number of decimals in the monetary values used in the country
   * @return  int  the number of decimals
   */
  abstract function getCurrencyDecimals();   
  
  /**
   * Get the dial code of the country
   * @return  int  the intl dial code
   */
  abstract function getDialCode();

  /**
   * Set the message dictionary object for this locale object
   * @param  I18NMessageDictionary $md  the message dictionary
   */
  function setMessageDictionary(I18NMessageDictionary $md) {
    $this->md = $md;
  }
  
  /**
   * Translate the message identified by <tt>$id</tt> into this locale's dialect
   * @param  string $id  the message id
   * @return  string  the localized message
   */
  function translateMessage($id) {
    return $this->md->translateMessage($id, $this->getDialect()->getLanguage()->getCode(), $this->getDialect()->getCode());
  }
}

?>