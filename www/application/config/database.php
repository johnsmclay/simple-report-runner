<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/
$active_group = 'pglms';
$active_record = TRUE;

$db['pglms']['hostname'] = '10.64.9.71';
$db['pglms']['username'] = 'reporter';
$db['pglms']['password'] = 'pr8tREdUcez2wAyA8PA5';
$db['pglms']['database'] = 'pglms2010';
$db['pglms']['dbdriver'] = 'mysql';
$db['pglms']['dbprefix'] = '';
$db['pglms']['pconnect'] = TRUE;
$db['pglms']['db_debug'] = TRUE;
$db['pglms']['cache_on'] = FALSE;
$db['pglms']['cachedir'] = '';
$db['pglms']['char_set'] = 'utf8';
$db['pglms']['dbcollat'] = 'utf8_general_ci';
$db['pglms']['swap_pre'] = '';
$db['pglms']['autoinit'] = TRUE;
$db['pglms']['stricton'] = FALSE;
$db['pglms']['port'] = 3306;


// Gale database connection
$db['gale']['hostname'] = '10.64.3.83';
$db['gale']['username'] = 'reporter';
$db['gale']['password'] = 'pr8tREdUcez2wAyA8PA5';
$db['gale']['database'] = 'speakez';
$db['gale']['dbdriver'] = 'mysql';
$db['gale']['dbprefix'] = '';
$db['gale']['pconnect'] = TRUE;
$db['gale']['db_debug'] = TRUE;
$db['gale']['cache_on'] = FALSE;
$db['gale']['cachedir'] = '';
$db['gale']['char_set'] = 'utf8';
$db['gale']['dbcollat'] = 'utf8_general_ci';
$db['gale']['swap_pre'] = '';
$db['gale']['autoinit'] = TRUE;
$db['gale']['stricton'] = FALSE;
$db['gale']['port'] = 3306;

// Temporary PGLMS connection until ip problem is resolved
$db['temp']['hostname'] = '10.5.0.126';
$db['temp']['username'] = 'reporter';
$db['temp']['password'] = 'pr8tREdUcez2wAyA8PA5';
$db['temp']['database'] = 'pglms2010';
$db['temp']['dbdriver'] = 'mysql';
$db['temp']['dbprefix'] = '';
$db['temp']['pconnect'] = TRUE;
$db['temp']['db_debug'] = TRUE;
$db['temp']['cache_on'] = FALSE;
$db['temp']['cachedir'] = '';
$db['temp']['char_set'] = 'utf8';
$db['temp']['dbcollat'] = 'utf8_general_ci';
$db['temp']['swap_pre'] = '';
$db['temp']['autoinit'] = TRUE;
$db['temp']['stricton'] = FALSE;
$db['temp']['port'] = 3316;

// Local Machine
$db['local']['hostname'] = 'localhost';
$db['local']['username'] = 'root';
$db['local']['password'] = 'Wh1sk3yT@ng0';
$db['local']['database'] = 'mil_bi';
$db['local']['dbdriver'] = 'mysql';
$db['local']['dbprefix'] = '';
$db['local']['pconnect'] = TRUE;
$db['local']['db_debug'] = TRUE;
$db['local']['cache_on'] = FALSE;
$db['local']['cachedir'] = '';
$db['local']['char_set'] = 'utf8';
$db['local']['dbcollat'] = 'utf8_general_ci';
$db['local']['swap_pre'] = '';
$db['local']['autoinit'] = TRUE;
$db['local']['stricton'] = FALSE;
$db['local']['port'] = 3306;




/* End of file database.php */
/* Location: ./application/config/database.php */