<?

/**
 * This file is an example file to be used in conjunction with the 
 * FDLocationRewriter1 class that base64-recodes URLs found in responce
 * documents. To enable this rewriter, you have to do two things:
 * 1. Edit the .htaccess or httpd.conf file and set the ErrorDocument directive:
 *
 *    ErrorDocument 404 /error.php5
 *
 *    (note that you can rename this file as you like, but be sure to supply the correct
 *    name in the above directive)
 * 
 * 2. Add the following directive to the .htaccess file or httpd.conf file:
 *
 *    DirectoryIndex index.php5
 *
 *    (note that you can rename index.php5 file as you like, but be sure to supply the correct
 *    name in the above directive)
 * 
 * 3. Edit the {FREEFORM_HOME}/packages/freeform/.config file to include the following
 *    line:
 *
 *    locationRewriter=FDLocationRewriter1
 *
 * 4. Access the host where you installed Freeform:
 *
 *    E.g.: http://localhost/
 *
 * 5. Now you can see how the links changed themselves
 * 6. To disable the URL rewriting, comment out the relevant line in the .config file
 *    of the freeform package.
 */

// Set this to whatever you renamed the index.php5 file
$index = 'index.php5';

// Uncomment these two lines if you want to use Location-header-based redirects
// In this case the real URL will be revealed, but this will enable caching
// header("Location: $index?" . baseName($_SERVER['REQUEST_URI']));
// exit();

// This will redefine certain server variables for direct include of the index.php5 file
// so that we do not redirect. In this case the Freeform caching features will not be
// available since the responce will actually appear as the HTTP 404 Not Found which is
// not cacheable
$_SERVER['PHP_SELF'] = str_replace(baseName(__FILE__), $index, $_SERVER['PHP_SELF']);
$_SERVER['QUERY_STRING'] = baseName($_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];

require("./$index");

?>