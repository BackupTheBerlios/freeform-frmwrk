<?

/**
 * This class represents a single time zone. A time zone has the following characteristics:
 * <ul>
 *   <li>the offset from UTC in seconds during the standard (winter) time</li>
 *   <li>the offset of daylight (summer time) that is added to the UTC offset to calculate the actual time in summer</li>
 *   <li>the list of locations in this time zone</li>
 *   <li>a possible intl abbreviation of the zone, like CET or EEST</li>
 * </ul>
 * Also a timezone can be queried whether a particular UNIX timestamp is in winetr/summer time as well to
 * convert a UNIX timestamp to a local time of the timezone.
 * @since 1.2.0.Beta
 */
abstract class I18NTimeZone {
  /**
   * This method formats an offset following these rules:
   * <ul>
   * <li>%s - the sign, either "+" or "-"</li>
   * <li>%h - the hours, without the leading zero</li>
   * <li>%H - the hours, with the leading zero if needed</li>
   * <li>%m - the minutes, without the leading zero</li>
   * <li>%M - the minutes, with the leading zero if needed</li>
   * </ul>
   * All other symbols are not modified. For example, for an offset of 3600 calling this
   * method with the '(%s%H%M)' as the format string will return '(+0200)'.
   * @param  int $offset  the offset, in seconds
   * @param  string $format  the format
   * @return  string  the formatted offset
   */
  static function formatOffset($offset, $format = '%s%H:%M') {
    $sign = $offset < 0 ? '-' : '+';
    $h = floor(abs($offset) / 3600);
    $H = $h < 10 ? '0' . $h : $h;
    $m = floor((abs($offset) % 3600) / 60);
    $M = $m < 10 ? '0' . $m : $m;

    $reps = array(
      '%s' => $sign,
      '%h' => $h,
      '%H' => $H,
      '%m' => $m,
      '%M' => $M
    );
    
    return strtr($format, $reps);
  }
    
  /**
   * Return the standard abbreviation of this time zone (if any).
   * @return  string  the standard abbreviation of the time zone (e.g. EET)
   */
  abstract function getStandardAbbrev();
  
  /**
   * Return the current abbreviation of this time zone for a given timestamp.
   * For example, it will return EDT instead of EST during summer time for the 
   * Eastern Standard Time zone
   * @param  int $time  the UNIX time stamp
   * @return  string  the current abbreviation of this time zone
   * @since 1.2.0.Beta
   */
  abstract function getCurrentAbbrev($time = null);
  
  /**
   * Return the current offset of the time zone from UTC for a given timestamp. 
   * For example, it will return 3600 for CET during winter time and 7200 during 
   * summer time. Note that this works just like <method>I18NTimeZone::getCurrentCode</method> 
   * but returns the current UTC offset depending on whether DST is in effect.
   * @param  int $time  the UNIX time stamp
   * @return  string  the current offset in seconds
   * @since 1.2.0.Beta  
   */
  abstract function getCurrentOffset($time = null);
  
  /**
   * Return the locations for this time zone (a list of countries or cities,
   * possibly translated to the local names)
   * @return  array  the locations where this time zone is used. Each element may contain locations of the same longitude/country/region
   */
  abstract function getLocations();
  
  /**
   * Return the offset of this time zone from UTC during standard time, in seconds
   * @return  int  offset
   */
  abstract function getUTCOffset();
  
  /**
   * Return the daylight savings time offset that is added to the UTC offset to 
   * calculate the final offset from UTC during DST
   * @return  int DST offset
   */
  abstract function getDSTOffset();
  
  /**
   * Return true if the time zone experiences transitions to summer (DST) time
   * @return  bool  flag that indicates if this time zone uses DST
   */
  abstract function isDSTEnabled();
  
  /**
   * Returns true if the specified timestamp is in summer time
   * @param  int $time  the timestamp to check, if not specified, the current UNIX timestamp will be used
   * @return  bool  true if the timestamp is in summer time
   */
  abstract function isSummerTime($time = null);
  
  /**
   * Return the "local" timestamp, i.e., the timestamp offset by the UTC and possibly DST for
   * this time zone
   * @param  int $time  the UNIX timestamp to convert (GMT)
   * @return  int  the "local" timestamp
   */
  function getLocalTime($time = null) {
    if(is_null($time)) {
      $time = time();
    }
    $rv = $time + $this->getUTCOffset();
    if($this->isSummerTime($time)) {
      $rv += $this->getDSTOffset();
    }
    return $rv;
  }
}

?>