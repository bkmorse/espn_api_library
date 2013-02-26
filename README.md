ESPN-API-PHP-Laravel Library
====================

Laravel library for ESPN API

1. Add espn.php to libraries

2. Add 'Espn' => path('app').'libraries/espn.php', to your start.php file, within the Autoloader::map array

3. Enter your API key within espn.php (libraries/espn.php) protected static $apiKey = "your_key_here";

4. Use in your controller