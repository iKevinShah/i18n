# Simple i18n

A simplistic PHP Internationalization and localization class.
 
The class helps achieve internationalization (i18n) via separate ini files for separate languages.

### Installation

Installation via composer
````
composer require ikevinshah/i18n
````

Installation without composer
1. Download the class file (src/i18n.php).
2. Include it in your code
````php
require_once '/path/to/i18n.php';
````

## Usage
1. Install using either `composer` or `including` the class

2. Set the variables as per your app, initialize and use `L::word` to display.
````php
$i18n = new ikevinshah\i18n($language,$ini_directory,$cache_directory,$fallback_language);
$i18n->init();
````
**Note:** Ensure that `$language.ini` file exists in the `$ini_directory`. So example if  I want to use russian (ru) on the current page and the `$ini_directory` is `/var/www/lang/`, class will search for ini file at the location of `/var/www/lang/ru.ini`. If the language file (ru) is not found, class will fallback to use the fallback language. If the `$fallback_language.ini` file is also not found, it will throw an error and **exit**. 

## Example 1 

File: **/var/www/lang/en.ini**
````ini
CURRENT_LANG = 'en'
HELLO = 'Hello'
LOGIN = 'Login';
SEARCH = 'Search'
CATEGORIES = 'Categories';
SETTINGS = 'Settings';
PREVIOUS = 'Previous';
NEXT = 'Next';
X='Y';
````

PHP: 
````php
<?php

require 'vendor/autoload.php';

$i18n = new ikevinshah\i18n('en','/var/www/lang/','/tmp/php_cache/');
$i18n->init();

echo L::HELLO; // Hello

echo L::CATEGORIES; //Categories
````

---

## Example 2

File: **/var/www/lang/fr.ini**
````ini
CURRENT_LANG = 'fr'
HELLO = 'Bonjour'
LOGIN = 'Connexion';
SEARCH = 'Recherche'
BACK = 'Retour à la page précédente';
CATEGORIES = 'Catégories';
SETTINGS = 'Paramètres';
PREVIOUS = 'Précédent';
NEXT = 'Suivant';
X='Y';
````

PHP: 
````php
<?php

require 'vendor/autoload.php';

$i18n = new ikevinshah\i18n('fr','/var/www/lang/','/tmp/php_cache/','en');
$i18n->init();

echo L::HELLO; // Bonjour

echo L::CATEGORIES; //Catégories
````

---

## Example 3
File: **/var/www/lang/fr.ini** -> Does not exist

File: **/var/www/lang/en.ini**
````ini
CURRENT_LANG = 'en'
HELLO = 'Hello'
LOGIN = 'Login';
SEARCH = 'Search'
CATEGORIES = 'Categories';
SETTINGS = 'Settings';
PREVIOUS = 'Previous';
NEXT = 'Next';
X='Y';
````

PHP: 
````php
<?php

require 'vendor/autoload.php';

$i18n = new ikevinshah\i18n('fr','/var/www/lang/','/tmp/php_cache/','en');
$i18n->init();

echo L::HELLO; // Hello

echo L::CATEGORIES; //Categories
````

## Example 4
File: **/var/www/lang/fr.ini** -> Does not exist

File: **/var/www/lang/en.ini** -> Does not exist

PHP: 
````php
<?php

require 'vendor/autoload.php';

$i18n = new ikevinshah\i18n('fr','/var/www/lang/','/tmp/php_cache/','en');
$i18n->init(); // Error: RuntimeException: Lang file for fr does not exist. in /path/to/src/i18n.php:78

echo L::HELLO;

echo L::CATEGORIES;
````

