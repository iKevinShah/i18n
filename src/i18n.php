<?php

namespace iKevinShah;

class i18n
{
    
    private $is_initialized = false;
    
    private $lang_file = null;
    
    private $cache_dir = null;
    
    private $lang = null;
    
    final public function __construct (string $required_lang, string $lang_dir, string $cache_dir, string $fallback_lang = 'en')
    {
        try
        {
            //Does the cache directory exist?
            if (!$cache_dir || !\is_dir($cache_dir))
            {
                //The cache directory is invalid / does not exist.
                throw new \RuntimeException('Cache directory is invalid.');
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
                    throw new \RuntimeException('No lang file found.');
                }

                //Fallback file exists, using that.
                $this->lang = $fallback_lang;
                $this->lang_file = $lang_dir . $fallback_lang . '.ini';
            }
            else
            {
                //Lang file exists, use it.
                $this->lang = $required_lang;
                $this->lang_file = $lang_dir . $required_lang . '.ini';
            }
        }
        catch (\Exception $e)
        {
            echo 'Error: ' . $e;
            //do something with the exception.
        }
    }
    
    final public function init()
    {
        //Run only if not already initialized
        if(!$this->is_initialized)
        {
            //this is the file where compiled "L" class is stored.
            $compiled_file = $this->cache_dir.'lang_'.$this->lang.'_compiled.php';

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
                $compiled_class .= \PHP_EOL;
                $compiled_class.= '}'.PHP_EOL;
                
                if(empty($strings))
                {
                    error_log("Empty or no content found in lang_file: ". $this->lang_file);
                    echo "Empty or no content found in lang_file";
                }
                if(\file_put_contents($compiled_file, $compiled_class) === false)
                {
                    error_log("Error saving compiled file.");
                    echo "Error saving lang compiled file.";
                }
            }
            require_once $compiled_file;
        }
    }
}