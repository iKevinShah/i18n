<?php

namespace ikevinshah;

/**
 * Class i18n
 *
 * @package ikevinshah
 */
class i18n
{
    /**
     * @var bool    To prevent repeated inits
     */
    private $is_initialized = false;
    
    /**
     * @var string  The path of lang file
     */
    private $lang_file = null;
    
    /**
     * @var string  The directory in which the compiled 'L' class will be stored
     */
    private $cache_dir = null;
    
    /**
     * @var string  The lang in use.
     */
    private $lang = null;
    
    /**
     * i18n constructor.
     *
     * @param string $required_lang     The lang to use.
     * @param string $lang_dir          The directory in which the language ini files are stored.
     * @param string $cache_dir         The directory in which to store the compiled file.
     * @param string $fallback_lang     The lang to use in case the $required_lang is not found.
     */
    final public function __construct (string $required_lang, string $lang_dir, string $cache_dir, string $fallback_lang = 'en')
    {
        try
        {
            // Some PHP setups disable parse_ini_file for security purpose. Checking...
            if(!\function_exists('parse_ini_file'))
            {
                //parse_ini_file function does not exist / is disabled.
                throw new \RuntimeException('parse_ini_file function is disabled / unavailable.');
            }
            if(!\function_exists('file_exists'))
            {
                //file_exists function does not exist / is disabled.
                throw new \RuntimeException('file_exists function is disabled / unavailable.');
            }
            if(!\function_exists('file_put_contents'))
            {
                //file_put_contents function does not exist / is disabled.
                throw new \RuntimeException('file_put_contents function is disabled / unavailable.');
            }
            //Does the cache directory exist?
            if (!$cache_dir || !\is_dir($cache_dir))
            {
                //The cache directory is invalid / does not exist.
                throw new \RuntimeException('Cache directory is invalid / does not exist');
            }
            
            //Yes, the cache directory exists.
            $this->cache_dir = $cache_dir;
            
            //Does the lang file exist?
            if (!\file_exists($lang_dir . $required_lang . '.ini'))
            {
                //The lang file does not exist, does the fallback lang file exist?
                if (!\file_exists($lang_dir . $fallback_lang . '.ini'))
                {
                    // Both lang files are not found. Throw error
                    throw new \RuntimeException('Lang file for '.$required_lang.' does not exist.');
                }
                //Fallback file exists, using that.
                $this->lang = $fallback_lang;
                $this->lang_file = $lang_dir . $fallback_lang . '.ini';
            }
            else
            {
                //Lang file exists for $required_lang, use it.
                $this->lang = $required_lang;
                $this->lang_file = $lang_dir . $required_lang . '.ini';
            }
        }
        catch (\Exception $e)
        {
            //do something with the exception.
            echo 'Error: ' . $e;
            \error_log('i18n Error: '.$e);
            exit;
        }
    }
    
    /**
     * Initialize and create the compiled file, if not already created.
     * Also compiles the class again if the ini file for $required_lang is changed.
     *
     */
    final public function init()
    {
        //Run only if not already initialized
        if(!$this->is_initialized)
        {
            //this is the file where compiled 'L' class is stored.
            $compiled_file = $this->cache_dir.'lang_'.$this->lang.'_compiled.php';
            
            try
            {
                //if compiled file does not exist OR if ini file is modified after compile file was modified
                if(!\file_exists($compiled_file) || \filemtime($compiled_file) < \filemtime($this->lang_file))
                {
                    $strings = \parse_ini_file($this->lang_file, false);
                    $compiled_class = '<?php'.PHP_EOL;
                    $compiled_class.= 'class L'.PHP_EOL;
                    $compiled_class.= '{'.PHP_EOL;
                    $compiled_class .= \PHP_EOL;
                    
                    foreach ($strings as $identifier => $value)
                    {
                        $compiled_class .= '    const '.$identifier.' = \''.$value.'\';'.\PHP_EOL;
                    }
                    $compiled_class.= '}'.PHP_EOL;
                    
                    if(empty($strings))
                    {
                        //Empty or corrupt ini file.
                        throw new \RuntimeException('Empty or no content found in lang_file: '. $this->lang_file);
                        
                    }
                    if(\file_put_contents($compiled_file, $compiled_class) === false)
                    {
                        throw new \RuntimeException('Error saving compiled language class.');
                    }
                }
                require_once $compiled_file;
            }
            
            catch (\Exception $e)
            {
                //do something with the exception.
                echo 'Error: ' . $e;
                \error_log('i18n Error: '.$e);
                exit;
            }
        }
    }
}