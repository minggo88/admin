<?php
/*
 * Fork this project on GitHub!
 * https://github.com/Philipp15b/php-i18n
 *
 * License: MIT
 */
class i18n {
    /**
     * Language file path
     * This is the path for the language files. You must use the '{LANGUAGE}' placeholder for the language or the script wont find any language files.
     *
     * @var string
     */
    protected $filePath = './lang/lang_{LANGUAGE}.ini';
    /**
     * Cache file path
     * This is the path for all the cache files. Best is an empty directory with no other files in it.
     *
     * @var string
     */
    protected $cachePath = './langcache/';
    /**
     * Fallback language
     * This is the language which is used when there is no language file for all other user languages. It has the lowest priority.
     * Remember to create a language file for the fallback!!
     *
     * @var string
     */
    protected $fallbackLang = 'en';
    /**
     * Merge in fallback language
     * Whether to merge current language's strings with the strings of the fallback language ($fallbackLang).
     *
     * @var bool
     */
    protected $mergeFallback = false;
    /**
     * The class name of the compiled class that contains the translated texts.
     * @var string
     */
    protected $prefix = 'L';
    /**
     * Forced language
     * If you want to force a specific language define it here.
     *
     * @var string
     */
    protected $forcedLang = NULL;
    /**
     * This is the separator used if you use sections in your ini-file.
     * For example, if you have a string 'greeting' in a section 'welcomepage' you will can access it via 'L::welcomepage_greeting'.
     * If you changed it to 'ABC' you could access your string via 'L::welcomepageABCgreeting'
     *
     * @var string
     */
    protected $sectionSeparator = '_';
    /*
     * The following properties are only available after calling init().
     */
    /**
     * User languages
     * These are the languages the user uses.
     * Normally, if you use the getUserLangs-method this array will be filled in like this:
     * 1. Forced language
     * 2. Language in $_GET['lang']
     * 3. Language in $_SESSION['lang']
     * 4. Fallback language
     *
     * @var array
     */
    protected $userLangs = array();
    protected $appliedLang = NULL;
    protected $langFilePath = NULL;
    protected $cacheFilePath = NULL;
    protected $isInitialized = false;
    /**
     * Constructor
     * The constructor sets all important settings. All params are optional, you can set the options via extra functions too.
     *
     * @param string [$filePath] This is the path for the language files. You must use the '{LANGUAGE}' placeholder for the language.
     * @param string [$cachePath] This is the path for all the cache files. Best is an empty directory with no other files in it. No placeholders.
     * @param string [$fallbackLang] This is the language which is used when there is no language file for all other user languages. It has the lowest priority.
     * @param string [$prefix] The class name of the compiled class that contains the translated texts. Defaults to 'L'.
     */
    public function __construct($filePath = NULL, $cachePath = NULL, $fallbackLang = NULL, $prefix = NULL) {
        // Apply settings
        if ($filePath != NULL) {
            $this->filePath = $filePath;
        }
        if ($cachePath != NULL) {
            $this->cachePath = $cachePath;
        }
        if ($fallbackLang != NULL) {
            $this->fallbackLang = $fallbackLang;
        }
        if ($prefix != NULL) {
            $this->prefix = $prefix;
        }
    }
    public function init() {
        if ($this->isInitialized()) {
            throw new BadMethodCallException('This object from class ' . __CLASS__ . ' is already initialized. It is not possible to init one object twice!');
        }
        $this->isInitialized = true;
        $this->userLangs = $this->getUserLangs();
        // search for language file
        $this->appliedLang = NULL;
        foreach ($this->userLangs as $priority => $langcode) {
            $this->langFilePath = $this->getConfigFilename($langcode);
            if (file_exists($this->langFilePath)) {
                $this->appliedLang = $langcode;
                break;
            }
        }
        if ($this->appliedLang == NULL) {
            throw new RuntimeException('No language file was found.');
        }
        // search for cache file
        $this->cacheFilePath = $this->cachePath . 'php_i18n_' . md5_file(__FILE__) . '_' . $this->prefix . '_' . $this->fallbackLang . '.cache.php';
        // whether we need to create a new cache file

        $outdated = !file_exists($this->cacheFilePath) ||
            filemtime($this->cacheFilePath) < filemtime($this->langFilePath) || // the language config was updated
            ($this->mergeFallback && filemtime($this->cacheFilePath) < filemtime($this->getConfigFilename($this->fallbackLang))); // the fallback language config was updated
        if ($outdated) {
            $config = $this->load($this->langFilePath);
            if ($this->mergeFallback)
                $config = array_replace_recursive($this->load($this->getConfigFilename($this->fallbackLang)), $config);
            $compiled = "<?php class " . $this->prefix . " {\n"
            	. $this->compile($config)
            	. 'public static function __callStatic($string, $args) {' . "\n"
                . '    $s = get_key_string($string);' . "\n"
                . '    $r = constant("self::" . $s) ? vsprintf(constant("self::" . $s), $args) : $s;' . "\n"
                . '    return $r;' . "\n"
            	. "}\n}\n"
            	. "function ".$this->prefix .'($string, $args=NULL) {'."\n"
                . '    $r = @constant("'.$this->prefix.'::".$string);' . "\n"
                . '    $r = $r ? ($args ? vsprintf($r,$args) : $r) : $s ;' . "\n"
                . '    return $args ? vsprintf($r,$args) : $r;' . "\n"
                . "}\n"
                . "// echo __('main','Withdraw request could not be completed.');\n"
                . 'function get_key_string($key) { return preg_match(\'/[^0-9a-zA-Z_]/\',$key) ? md5($key) : preg_replace(\'/\W/\',\'\',preg_replace(\'/\s{1,}/\',\'_\', $key)); }'."\n"
                . 'function ___($prefix, $string, $args=NULL) {   '."\n"
                . '    $s = $prefix."_".get_key_string($string);'."\n"
                . '    $r = Lang($s, $args);'."\n"
                . '    $r = $r ? $r : $string;'."\n"
                . '    return $r;'."\n"
            	. "}";
			if( ! is_dir($this->cachePath))
				mkdir($this->cachePath, 0755, true);

                if (file_put_contents($this->cacheFilePath, $compiled) === FALSE) {
                    throw new Exception("Could not write cache file to path '" . $this->cacheFilePath . "'. Is it writable?");
                }
            $this->gen_js_cache_file();
            chmod($this->cacheFilePath, 0755);
        }
        require_once $this->cacheFilePath;
    }
    private function gen_js_cache_file () {
        $lang = $this->fallbackLang;

        $datafile = $this->langFilePath;
        if (file_exists($datafile)) {
            $data_all = file_get_contents($datafile);
        } else {
            $data_all = '{}';
        }
        $data_all = json_decode($data_all);
        foreach( $data_all as $part => $data_part ) {
            $r = array();
            foreach($data_part as $key => $val) {
                if(trim($key)!='') {
                    $key = $this->get_key_string($key);
                    $r[$key] = $val;
                }
            }
            file_put_contents( $this->cachePath . '/i18n_' . $part . '_' . $lang . '.cache.js', json_encode($r) );
        }
    }
    public function isInitialized() {
        return $this->isInitialized;
    }
    public function getAppliedLang() {
        return $this->appliedLang;
    }
    public function getCachePath() {
        return $this->cachePath;
    }
    public function getFallbackLang() {
        return $this->fallbackLang;
    }
    public function setFilePath($filePath) {
        $this->fail_after_init();
        $this->filePath = $filePath;
    }
    public function setCachePath($cachePath) {
        $this->fail_after_init();
        $this->cachePath = $cachePath;
    }
    public function setFallbackLang($fallbackLang) {
        $this->fail_after_init();
        $this->fallbackLang = $fallbackLang;
    }
    public function setMergeFallback($mergeFallback) {
        $this->fail_after_init();
        $this->mergeFallback = $mergeFallback;
    }
    public function setPrefix($prefix) {
        $this->fail_after_init();
        $this->prefix = $prefix;
    }
    public function setForcedLang($forcedLang) {
        $this->fail_after_init();
        $this->forcedLang = $forcedLang;
    }
    public function setSectionSeparator($sectionSeparator) {
        $this->fail_after_init();
        $this->sectionSeparator = $sectionSeparator;
    }
    /**
     * @deprecated Use setSectionSeparator.
     */
    public function setSectionSeperator($sectionSeparator) {
        $this->setSectionSeparator($sectionSeparator);
    }
    /**
     * getUserLangs()
     * Returns the user languages
     * Normally it returns an array like this:
     * 1. Forced language
     * 2. Language in $_GET['lang']
     * 3. Language in $_SESSION['lang']
     * 4. HTTP_ACCEPT_LANGUAGE
     * 5. Fallback language
     * Note: duplicate values are deleted.
     *
     * @return array with the user languages sorted by priority.
     */
    public function getUserLangs() {
        $userLangs = array();
        // Highest priority: forced language
        if ($this->forcedLang != NULL) {
            $userLangs[] = $this->forcedLang;
        }
        // 2nd highest priority: GET parameter 'lang'
        if (isset($_GET['lang']) && is_string($_GET['lang'])) {
            $userLangs[] = $_GET['lang'];
        }
        // 3rd highest priority: SESSION parameter 'lang'
        if (isset($_SESSION['lang']) && is_string($_SESSION['lang'])) {
            $userLangs[] = $_SESSION['lang'];
        }
        // 4th highest priority: HTTP_ACCEPT_LANGUAGE
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $part) {
                $userLangs[] = strtolower(substr($part, 0, 2));
            }
        }
        // Lowest priority: fallback
        $userLangs[] = $this->fallbackLang;
        // remove duplicate elements
        $userLangs = array_unique($userLangs);
        // remove illegal userLangs
        $userLangs2 = array();
        foreach ($userLangs as $key => $value) {
            // only allow a-z, A-Z and 0-9 and _ and -
            if (preg_match('/^[a-zA-Z0-9_-]*$/', $value) === 1)
                $userLangs2[$key] = $value;
        }
        return $userLangs2;
    }
    protected function getConfigFilename($langcode) {
        return str_replace('{LANGUAGE}', $langcode, $this->filePath);
    }
    protected function load($filename) {
        $ext = substr(strrchr($filename, '.'), 1);
        switch ($ext) {
            case 'properties':
            case 'ini':
                $config = parse_ini_file($filename, true);
                break;
            case 'yml':
            case 'yaml':
                $config = spyc_load_file($filename);
                break;
            case 'json':
                $config = json_decode(file_get_contents($filename), true);
                break;
            default:
                throw new InvalidArgumentException($ext . " is not a valid extension!");
        }
        return $config;
    }
    /**
     * Recursively compile an associative array to PHP code.
     */
    protected function compile($config, $prefix = '') {
        $code = '';
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $code .= $this->compile($value, $prefix . $key . $this->sectionSeparator);
            } else {
                $key = $this->get_key_string($key);
                $fullName = $prefix . $key;
                if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $fullName)) {
                    throw new InvalidArgumentException(__CLASS__ . ": Cannot compile translation key " . $fullName . " because it is not a valid PHP identifier.");
                }
                $code .= 'const ' . $fullName . ' = \'' . str_replace('\'', '\\\'', $value) . "';\n";
            }
        }
        return $code;
    }
    protected function fail_after_init() {
        if ($this->isInitialized()) {
            throw new BadMethodCallException('This ' . __CLASS__ . ' object is already initalized, so you can not change any settings.');
        }
    }
    protected function get_key_string($key) {
        return preg_match('/[^0-9a-zA-Z_]/',$key) ? md5($key) : preg_replace('/\W/','',preg_replace('/\s{1,}/','_', $key));
    }
}
?>