<?

/**
 * This interface publishes methods that any i18n API provider must implement. 
 * i18n API privider's methods are called by the <class>I18N</class> class.
 * @see <doc file="i18n.txt">Freeform i18n API overview</doc>
 */
interface I18NProvider {
  /**
   * Return the country object for a given country code
   * @param  string $cc  the country code
   * @return  I18NCountry  the country
   * @throws  I18NException  if the country could not be found
   */
  function getCountry($cc);
  
  /**
   * Return the list of countries installed
   * @return  array  list of countries in form of cc=>I18NCountry
   */
  function getCountries();
  
  /**
   * Return a language object for a given language code
   * @param  string $lc  the language code
   * @return  I18NLanguage  the language object
   * @throws  I18NException  if the language could not be found
   */
  function getLanguage($lc);
  
  /**
   * Return all installed languages
   * @return  array  a list of all languages in form of lc=>I18NLanguage
   */
  function getLanguages();
  
  /**
   * Return a specified dialect
   * @param  string $lc  the language code
   * @param  string $dc  the dialect code. Defaults to Official
   * @return  I18NDialect  the dialect
   * @throws  I18NException  if the dialect could not be found
   */
  function getDialect($lc, $dc = 'Official');
  
  /**
   * Return the list of all installed dialects for all languages
   * @return  array  the list of all dialects grouped by language in form of lc_dc=>I18NDialect
   */
  function getDialects();
  
  /**
   * Get specified time zone by code
   * @param  string $code   the system code of the time zone
   * @return  I18NTimeZone  the requested time zone
   * @throws  I18NException  if no such time zone found
   */
  function getTimeZone($code);
  
  /**
   * Return all installed time zones
   * @return  array  list of time zones in form of code=>I18NTimeZone
   */
  function getTimeZones();
  
  /**
   * Get a locale object by combining the country, language and dialect codes. If there is 
   * no locale for such dialect, the Official dialect will be tried before failure. 
   * @param  string $cc  the country code (like US). If omitted the default country will be assumed (as specified in the i18n package's config file)
   * @param  string $lc  the language code (like en). If omitted, country's best dialect will be used
   * @param  string $dc  the dialect code (like American). If omitted, the Official dialect will be used
   */
  function getLocale($cc = null, $lc = '', $dc = 'Official');
}

?>