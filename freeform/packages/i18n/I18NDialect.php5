<?

/**
 * This interface represents a language dialect, or a variant - the most important piece of 
 * information about a language. It describes a concrete dialect, country of origin,
 * possible collations, as well as it is used to format numbers, currency values and dates/times
 * for this dialect. The formatting methods are rarely used directly, the client code should 
 * call respective methods of the <class>I18NLocale</class> object.
 * @since 1.2.0.Beta
 * @author Dennis Popel
 */
interface I18NDialect {
  /**
   * Return the language object this dialect is assotiated with
   * @return  I18NLanguage  the language object
   */
  function getLanguage();
  
  /**
   * Return the country in which this dialect is spoken (the country of origin)
   * @return  I18NCountry  the origin country
   */
  function getCountry();
  
  /**
   * Return the name of the dialect (in the national language)
   * @return  string  the name of the dialect
   */
  function getName();
  
  /**
   * Return the code of the dialect
   * @return  string  the code of the dialect
   */
  function getCode();
  
  /**
   * Returns a specified collation for this dialect
   * @param  string $code  the code of the collation (e.g., Default). If omitted, the Default collation will be returned
   * @return  I18NCollation  the collation
   * @throws  I18NException  if no such collation
   */
  function getCollation($code = 'Default');
  
  /**
   * Return the list of collations for this dialect
   * @return  array  the list of I18NCollation objects for this dialect
   */
  function getCollations();
  
  /**
   * Return a formatted floating number representation for this dialect
   * @param  float $m  the number to format
   * @param  int $decimals  the number of digits in fractional part. If omitted, will use the dialect's default
   */
  function formatNumber($n, $decimals = null);
  
  /**
   * Format a monetary value for this dialect using country currency info from the
   * given locale object
   * @param  float $n  the value to format
   * @param  I18NLocale  the locale to query for the currency symbol and code
   */
  function formatCurrency($n, $locale);
  
  /**
   * Format date and time. The formatting parameters 
   * are those of <tt>strftime()</tt> function, except for <tt>%z</tt> that is used to show the time zone abbreviation (like EEST),
   * and <tt>%Z</tt> that is used to show the current time zone offset (like +0300). If no format is given, this will
   * return the default format for this dialect
   * @param  int $dt  the UNIX timestamp to format 
   * @param  string $format  the format string. If null, the dialect's default will be used
   * @param  I18NTimeZone  the time zone
   * @return  string  the formatted date/time
   * @see <method>I18NLocale::formatDateTime</method>()
   */
  function formatDateTime($time, $format, $timeZone);
  
  /**
   * Format date using either short, long or full format
   * @param  int $dt  the UNIX timestamp to format 
   * @param  int $format  the format specifier
   * @param  I18NTimeZone  the time zone
   * @return  string  the formatted date
   * @see <method>I18NLocale::formatDate</method>()
   */
  function formatDate($time, $format, $timeZone);
  
  /**
   * Format time using either short, long or full format
   * @param  int $dt  the UNIX timestamp to format 
   * @param  int $format  the format specifier
   * @param  I18NTimeZone  the time zone
   * @return  string  the formatted time
   * @see <method>I18NLocale::formatTime</method>()
   */
  function formatTime($time, $format, $timeZone);
  
  /**
   * Return the name of the best encoding for this dialect for exporting string in this
   * dialect for non-Unicode applications.
   * @return  string  the name of the encoding as defined by the mbstring extension
   */
  function getBestEncoding();
}

?>