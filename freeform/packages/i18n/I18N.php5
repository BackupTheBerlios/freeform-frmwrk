<?

/**
 * This class provides methods for accessing particular country, language, dialect, collation
 * and locale objects. It interfaces with the concrete implementation of the Freeform I18N API 
 * (such as Free18n) and reroutes calls to its methods to this implementation. This is the main
 * class you use to access different localization classes.
 * @since 1.2.0.Beta
 * @author Dennis Popel 
 */
class I18N {
  /**
   * Represents short date/time format
   */
  const SHORT = 1;
  
  /**
   * Represents full date/time format
   */
  const FULL = 3;
  
  /**
   * Represents long date/time format
   */
  const LONG = 2;
  
  private static $impl = null;
  
  private static function getImpl() {
    if(is_null(self::$impl)) {
      $pkg = Package::getPackageByName('i18n');
      $implName = $pkg->getProperty('provider');
      try {
        $rc = new ReflectionClass($implName);
        self::$impl = $rc->newInstance();
      } catch(ReflectionException $re) {
        throw new ConfigurationException('i18n', 'provider', ($implName ? 'Cannot instantiate i18n provider ' . $$implName : 'i18n provider not set (provider option empty in i18n/.config)'));
      }
    }
    return self::$impl;
  }
  
  /**
   * Return the country object for a given country code
   * @param  string $cc  the country code
   * @return  I18NCountry  the country
   * @throws  I18NException  if the country could not be found
   */
  static function getCountry($cc) {
    return self::getImpl()->getCountry($cc);
  }
  
  /**
   * Return the list of countries installed
   * @return  array  list of countries in form of cc=>I18NCountry
   */
  static function getCountries() {
    return self::getImpl()->getCountries();
  }
  
  /**
   * Return a language object for a given language code
   * @param  string $lc  the language code
   * @return  I18NLanguage  the language object
   * @throws  I18NException  if the language could not be found
   */
  static function getLanguage($lc) {   
    return self::getImpl()->getLanguage($lc);
  }
  
  /**
   * Return all installed languages
   * @return  array  a list of all languages in form of lc=>I18NLanguage
   */
  static function getLanguages() {
    return self::getImpl()->getLanguages();
  }
  
  /**
   * Return a specified dialect
   * @param  string $lc  the language code
   * @param  string $dc  the dialect code. Defaults to Official
   * @return  I18NDialect  the dialect
   * @throws  I18NException  if the dialect could not be found
   */
  static function getDialect($lc, $dc = 'Official') {
    return self::getImpl()->getDialect($lc, $dc);
  }
  
  /**
   * Return the list of all installed dialects for all languages
   * @return  array  the list of all dialects grouped by language in form of lc_dc=>I18NDialect
   */
  static function getDialects() {
    return self::getImpl()->getDialects();
  }
  
  /**
   * Get specified time zone by code
   * @param  string $code   the system code of the time zone
   * @return  I18NTimeZone  the requested time zone
   * @throws  I18NException  if no such time zone found
   */
  static function getTimeZone($code) {
    return self::getImpl()->getTimeZone($code);
  }
  
  /**
   * Return all installed time zones
   * @return  array  list of time zones in form of code=>I18NTimeZone
   */
  static function getTimeZones() {
    return self::getImpl()->getTimeZones();
  }
  
  /**
   * Get a locale object by combining the country, language and dialect codes. If there is 
   * no locale for such dialect, the Official dialect will be tried before failure. 
   * @param  string $cc  the country code (like US). If omitted the default country will be assumed (as specified in the i18n package's config file)
   * @param  string $lc  the language code (like en). If omitted, country's best dialect will be used
   * @param  string $dc  the dialect code (like American). If omitted, the Official dialect will be used
   * @throws  I18NException  if no such locale could be found (either the country or the dialect are not found)
   */
  static function getLocale($cc = null, $lc = '', $dc = 'Official') {
    return self::getImpl()->getLocale($cc, $lc, $dc);
  } 
}

?>