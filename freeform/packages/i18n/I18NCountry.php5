<?

/**
 * This interface represents country-specific info such as intl and national currency code 
 * and symbol, currency decimal places, best dialect and time zone.
 * @since 1.2.0.Beta
 * @author Dennis Popel
 */
interface I18NCountry {
  /**
   * Return the intl country code (like US, GB or UA)
   * @return  string  the intl country code
   */
  function getCode();
  
  /**
   * Return the name of the country (in that country's official language)
   * @return  string  the national name of the country
   */
  function getName();
  
  /**
   * Return intl currency code, such like UAH, EUR, CAD etc. You should not call this
   * method if you have a locale object with this country; preferred way is to call relevant 
   * method of the <class>I18NLocale</class>.
   * @return  string  the intl currency code
   */
  function getCurrencyCode();
  
  /**
   * Return the currency symbol, such as $. You should not call this
   * method if you have a locale object with this country; preferred way is to call relevant 
   * method of the <class>I18NLocale</class>.
   * @return  string  the currency symbol
   */
  function getCurrencySymbol();
  
  /**
   * Return the number of decimal places for money format used in this country. You should not call this
   * method if you have a locale object with this country; preferred way is to call relevant 
   * method of the <class>I18NLocale</class>.
   * @return  int  the number of digits in the fractional part of money sums in this country
   */
  function getCurrencyDecimals();
  
  /**
   * Get the best intl time zone code for this country
   * @return  I18NTimeZone  the time zone code
   */
  function getBestTimeZone();
  
  /**
   * Return the best dialect for this country (for example, for United States this will
   * be en, American).
   * @return  I18NDialect  the best dialect for this country
   */
  function getBestDialect();
  
  /**
   * Return the dial code of the country
   * @return  int  the intl dial code
   */
  function getDialCode();
}

?>