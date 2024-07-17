<?php

//***************** Preparing Amazone Domain variables ******************//
require_once dirname(__FILE__) . '/aws_sdk/aws-autoloader.php';
//require_once dirname(__FILE__) . '/db.php';

use Aws\Common\Aws;

global $vars, $db;
$vars = array();
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PORT'] = 443;
//print_r($_SERVER['HTTP_HOST']);
//die();
if ($_SERVER['HTTP_HOST'] == 'cg.curriki.org') {
  $db_host = 'localhost';
  $db_user = 'root';
  $db_pass = 'currikimonitor!';
  $db_name = 'curriki4';
  $vars['wp_contents'] = '/curriki/wp-content';
} else if ($_SERVER['HTTP_HOST'] == 'localhost') {
  $db_host = 'localhost';
  $db_user = 'root';
  $db_pass = 'password';
  $db_name = 'curriki3';
  $vars['wp_contents'] = '/curriki/wp-content';
} else {
  $db_host = '127.0.0.1';
  $db_user = 'curriki';
  $db_pass = 'C9H21hGUaV2WbBY9';
  $db_name = 'wp_curriki';
  $vars['wp_contents'] = '/wp-content';
}

//$db = new db($db_host, $db_user, $db_pass, $db_name);

//$result = $db->select("SELECT * from cur_options where option_name in ('resSourceVideos' ,'resResourceVideos', 'resResourcePosters',
//'resResourceSwfs','resResourceImgs','resResourceDocs','resResourceFiles' , 
//'resLinkImages','awsCDNUrl','awsTransCodePipeLine','awsBucket','resUploadFolder',
//'resMaxSWFSize','resMaxImageSize' , 'resMaxVideoSize', 'resMaxDocSize','resMaxFileSize',
//'resAllowedImg','resAllowedVideo','resAllowedDocs','siteurl','home','resFiles','awsWorkBucket','awsSearchDomain','awsSearchEndPoint')");


//foreach ($result as $r) {
//  $vars[$r['option_name']] = $r['option_value'];
//}

$vars = [
    'awsBucket'=>'archivecurrikicdn',
    'awsCDNUrl'=>'https://archivecurrikicdn.s3-us-west-2.amazonaws.com/',
    'awsTransCodePipeLine'=>'1421535183552-zbqh2y',
    'home'=>'http://localhost/curriki/',
    'resAllowedDocs'=>'doc,docx,pdf,xls,xlsx,txt,csv,html,psd,sql,log,fla,xml,ade,adp,ppt,pptx',
    'resAllowedImg'=>'jpg,jpeg,pjpeg,png,gif,bmp,tiff,tif',
    'resAllowedVideo'=>'mov,mpeg,mp4,avi,mpg,wma,flv,wmv',
    'resLinkImages'=>'linkimages/',
    'resMaxFileSize'=>500000000,
    'resMaxImageSize'=>5242880,
    'resMaxVideoSize'=>500000000,
    'resResourcePosters'=>'posters/',
    'resResourceFiles'=>'resourcefiles/',
    'resResourceImgs'=>'resourceimgs/',
    'resSourceVideos'=>'sourcevideos/',
    'resUploadFolder'=>'/uploads/currikicdn/',
    'resResourceVideos'=>'videos/',
    'siteurl'=>'http://localhost/curriki/',
];
    
$vars['wp_contents'] = '/usr/share/nginx/html/curriki2/trunk/curriki/wp-content';
$vars['base_url'] = ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $vars['wp_contents'];
$vars['current_path'] = $_SERVER['DOCUMENT_ROOT'] . $vars['wp_contents']; // relative path from filemanager folder to upload files folder
$vars['upload_path'] = $vars['current_path'] . $vars['resUploadFolder']; // relative path from filemanager folder to upload files folder
$vars['aws'] = Aws::factory(__DIR__ . '/aws_sdk/config.php');
$aws = Aws::factory(__DIR__ . '/aws_sdk/config.php');
//unset($result);
//unset($r);
//extract($vars);

/*[awsBucket] => currikicdn
  [awsCDNUrl] => https://archivecurrikicdn.s3-us-west-2.amazonaws.com/
  [awsTransCodePipeLine] => 1421535183552-zbqh2y
  [home] => http://localhost/curriki/
  [resAllowedDocs] => doc,docx,pdf,xls,xlsx,txt,csv,html,psd,sql,log,fla,xml,ade,adp,ppt,pptx
  [resAllowedImg] => jpg,jpeg,pjpeg,png,gif,bmp,tiff,tif
  [resAllowedVideo] => mov,mpeg,mp4,avi,mpg,wma,flv,wmv
  [resLinkImages] => linkimages/
  [resMaxFileSize] => 500000000
  [resMaxImageSize] => 5242880
  [resMaxVideoSize] => 500000000
  [resResourcePosters] => posters/
  [resResourceFiles] => resourcefiles/
  [resResourceImgs] => resourceimgs/
  [resSourceVideos] => sourcevideos/
  [resUploadFolder] => /uploads/currikicdn/
  [resResourceVideos] => videos/
  [siteurl] => http://localhost/curriki/
 * 
 */

global $s3_client;
$s3_client = $aws->get('S3');
//print_r($s3_client);