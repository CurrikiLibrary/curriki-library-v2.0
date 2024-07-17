<?php
/**
 * rating - Rating: an example LTI tool provider
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 2.0.0
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3.0
 */

/*
 * This page contains the configuration settings for the application.
 */


###
###  Application settings
###
  define('APP_NAME', 'Curriki');
  define('SESSION_NAME', 'curriki-sess');
  define('VERSION', '3.0.00');

###
###  Database connection settings
###
  define('DB_NAME', 'mysql:dbname=currikilive;host=currikirds-databasecluster-1o4qjj2ck5nnj.cluster-c9th6z1uawak.us-west-2.rds.amazonaws.com');  // e.g. 'mysql:dbname=MyDb;host=localhost' or 'sqlite:php-rating.sqlitedb'
  define('DB_USERNAME', 'currikiuser001');
  define('DB_PASSWORD', 'currikiPass001');
  define('DB_TABLENAME_PREFIX', '');
  //define('DB_TABLENAME_PREFIX', 'curlti');
  define('DB_TABLENAME', 'cur_sessions');
  define('ADMIN_USERNAME', 'admin');
  define('ADMIN_PASSWORD', 'currikiltigo');

?>
