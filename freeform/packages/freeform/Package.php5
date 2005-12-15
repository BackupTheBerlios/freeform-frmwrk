<?

/**
 * This class defines methods for runtime resolution of package paths and provides access
 * to package configuration and resources. In Freeform a package is a programmed replacement
 * for namespaces, support for which has been dropped for not so clear reasons.
 *
 * Since PHP now cannot distinguish same class names in different packages (i.e.,
 * com.package1.MyClass and com.package2.MyClass, nesting packages is not supported
 * However, packages offer a flexible way of distributing sets of classes that  
 * have distinct application. (Note that the support for nested packages is planned for 2.0.0)
 * 
 * Given the power of Actions and Forms API, Security API,
 * Document API, and Session API, different packages can be easily combined into
 * existing applications using shared security policies and styled templates of resulting
 * documents. 
 *
 * The static method of special interest is <class>Package::getPackageByName</class> that
 * lets you find other packages (not the package current action is declared in). You
 * can set a configuration option in a package, say 'mainpackage' to the name of
 * the application main package and then set config options like 'template.displayforumtree'
 * to some resource in the main app package and have your package use templates from
 * that main app package.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */
final class Package {
  private $name;
  private $path;
  private $config = array();
  
  private static $entryPackage = null;
  private static $cache = array();
  
  /**
   * We hide the constructor since all packages are instantiated and cached by its factory
   * methods.
   */
  private function __construct($name, $path) {
    $this->path = $path;
    $this->name = $name;
    if(file_exists($cf = $this->path . '/.config')) {
      $this->config = parse_ini_file($cf, true);
    }
  }
  
  /**
   * Get the name of this package
   *
   * @return  string    the name of this package
   */
  function getName() {
    return $this->name;
  }
  
  /**
   * Get a config property $name. If it is not found, the $defValue is returned.
   * @param   string $name      name of the config property
   * @param   mixed $defValue   optional default value to be returned if the property not found
   * @return  string            value of the property
   */
  function getProperty($name, $defValue = null) {
    if(isSet($this->config[$name])) {
      return $this->config[$name];
    } else {
      return $defValue;
    }
  }
  
  /**
   * Return this package's configuration
   * @return  array  the configuration array as returned by <tt>parse_ini_file</tt>
   * @since 1.2.0.Beta
   * @author Alexey Bukhtin
   */
  function getConfig() {
    return $this->config;
  }
   
  /**
   * Get fully qualified file name of a resource
   *
   * @param   string $name      name of the resource
   * @return  string            fully qualified file name of the resource file
   */
  function getResourcePath($name) {
    return $this->path . '/resources/' . $name;
  }
   
   /**
    * Get the path to this package folder
    *
    * @return  string           fully qualified name of the directory where package files are located
    */
   function getPath() {
     return $this->path;
   }
   
   /**
    * Return the list of all class names in this package
    *
    * @return  array  list of all class names in this package
    */
   function getClasses() {
     $rv = array();
     foreach(glob($this->getPath() . '/*.php5') as $fileName) {
       $rv[] = baseName($fileName, '.php5');
     }
     return $rv;
   }
   
   /**
    * Get a package for object where its class file is located
    *
    * @param   object $obj       the object to get package for 
    * @return  Package           the package where the $obj class file is located 
    */
   static function getPackage($obj) {
     $pn = self::getPackageNameForObject($obj);
     return self::getPackageByName($pn);
   }
   
   /**
    * Get the name of the package where the class of instance $obj was defined
    *
    * @return  string  name of the package
    */
   static function getPackageNameForObject($obj) {
     return self::getPackageNameForClass(get_class($obj));
   }
   
   /**
    * Get the name of the package where the class $className was defined
    * @return  string  name of the package
    */   
   static function getPackageNameForClass($className) {
     $rc = new ReflectionClass($className);
     return baseName(dirName($rc->getFileName()));
   }
   
   /**
    * Gets a package with name $name or null
    *
    * @param   string $name  the name of package to find
    * @return  Package  package with name $name or null
    */
   static function getPackageByName($name) {
     if(isSet(self::$cache[$name])) {
       return self::$cache[$name];
     } else {
       global $FREEFORM_HOME;
       if(is_dir($path = $FREEFORM_HOME . '/packages/' . $name)) {
         return self::$cache[$name] = new Package($name, $path);
       } else {
         return null;
       }
     }
   }
   
   /**
    * Get the list of installed packages names
    *
    * @return  array  list of package names that have been installed
    */
   static function getPackages() {
     global $FREEFORM_PACKAGES;
     return $FREEFORM_PACKAGES;
   }
   
   static function setEntryPackage(Package $package) {
		 self::$entryPackage = $package;
	 }

   static function getEntryPackage() {
		 return self::$entryPackage;
	 }
}

?>