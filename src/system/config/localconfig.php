<?php

### INSTALL SCRIPT START ###
$GLOBALS['TL_CONFIG']['licenseAccepted'] = true;
$GLOBALS['TL_CONFIG']['installPassword'] = '$6$9f4b403941e8446f$/cx8VEWr8RhdTNUBkiMfPjzBiCcu7fsLfJGNNlD/v4n4ow9DsdGMeyp36HdPqdLHNnuwJm5hjw/kNyizvuYeq/';
$GLOBALS['TL_CONFIG']['encryptionKey'] = '7c65a0664a1c0d2e8f6d5bb5717b5268';
$GLOBALS['TL_CONFIG']['dbDriver'] = 'MySQLi';
$GLOBALS['TL_CONFIG']['dbHost'] = ''; // Live data
$GLOBALS['TL_CONFIG']['dbUser'] = ''; // Live data
$GLOBALS['TL_CONFIG']['dbPass'] = ''; // Live data
$GLOBALS['TL_CONFIG']['dbDatabase'] = ''; // Live data
$GLOBALS['TL_CONFIG']['dbPconnect'] = false;
$GLOBALS['TL_CONFIG']['dbCharset'] = 'UTF8';
$GLOBALS['TL_CONFIG']['dbPort'] = 3306;
$GLOBALS['TL_CONFIG']['dbSocket'] = '';
$GLOBALS['TL_CONFIG']['adminEmail'] = 'hallo@slash-works.de';
$GLOBALS['TL_CONFIG']['latestVersion'] = '3.5.16';
$GLOBALS['TL_CONFIG']['displayErrors'] = false;
$GLOBALS['TL_CONFIG']['defaultUser'] = '';
$GLOBALS['TL_CONFIG']['defaultGroup'] = '';
$GLOBALS['TL_CONFIG']['repository_unsafe_catalog'] = true;
$GLOBALS['TL_CONFIG']['liveUpdateId'] = 'daa0-872f-e882-20e0-1317';
$GLOBALS['TL_CONFIG']['coreOnlyMode'] = false;
$GLOBALS['TL_CONFIG']['minifyMarkup'] = true;
$GLOBALS['TL_CONFIG']['debugMode'] = false;
$GLOBALS['TL_CONFIG']['maintenanceMode'] = false;
$GLOBALS['TL_CONFIG']['rewriteURL'] = true;
$GLOBALS['TL_CONFIG']['cacheMode'] = 'both';
$GLOBALS['TL_CONFIG']['websiteTitle'] = 'Team AG Heizölrechner';
$GLOBALS['TL_CONFIG']['dateFormat'] = 'd.m.Y';
$GLOBALS['TL_CONFIG']['datimFormat'] = 'd.m.Y H:i';
$GLOBALS['TL_CONFIG']['primaryColor'] = '';
$GLOBALS['TL_CONFIG']['imageWidth'] = 3000;
$GLOBALS['TL_CONFIG']['imageHeight'] = 3000;
$GLOBALS['TL_CONFIG']['maxFileSize'] = 20000000;
$GLOBALS['TL_CONFIG']['uploadFields'] = 10;
$GLOBALS['TL_CONFIG']['installCount'] = 0;
$GLOBALS['TL_CONFIG']['uploadTypes'] = 'jpg,jpeg,gif,png,ico,svg,svgz,odt,ods,odp,odg,ott,ots,otp,otg,pdf,csv,doc,docx,dot,dotx,xls,xlsx,xlt,xltx,ppt,pptx,pot,potx,mp3,mp4,m4a,m4v,webm,ogg,ogv,wma,wmv,ram,rm,mov,fla,flv,swf,ttf,ttc,otf,eot,woff,woff2,css,scss,less,js,html,htm,txt,zip,rar,7z,cto,csv';
$GLOBALS['TL_CONFIG']['useSMTP'] = true;
$GLOBALS['TL_CONFIG']['smtpHost'] = 'smtp.1und1.de';
$GLOBALS['TL_CONFIG']['smtpUser'] = 'dev@borowiakziehe.de';
$GLOBALS['TL_CONFIG']['smtpPass'] = 'slashworks2015';
### INSTALL SCRIPT STOP ###

## external connection for teammmeber Database
$GLOBALS['TL_CONFIG']['dbDriverExt'] = 'MySQLi';
$GLOBALS['TL_CONFIG']['dbHostExt'] = '192.168.1.33'; // Live data
$GLOBALS['TL_CONFIG']['dbUserExt'] = 'team-ag-rechner'; // Live data
$GLOBALS['TL_CONFIG']['dbPassExt'] = 'team-ag-rechner'; // Live data
$GLOBALS['TL_CONFIG']['dbDatabaseExt'] = 'team-ag-rechnerExt'; // Live data
$GLOBALS['TL_CONFIG']['dbPconnectExt'] = false;
$GLOBALS['TL_CONFIG']['dbCharsetExt'] = 'UTF8';
$GLOBALS['TL_CONFIG']['dbPortExt'] = 3306;
$GLOBALS['TL_CONFIG']['dbSocketExt'] = '';


/**
 * DEV server
 */
if (strpos($_SERVER['HTTP_HOST'],'dev.team-ag-rechner.sw-ia.de') !== false) {
	$GLOBALS['TL_CONFIG']['displayErrors'] = true;
	$GLOBALS['TL_CONFIG']['bypassCache'] = true;
	$GLOBALS['TL_CONFIG']['cacheMode'] = 'none';
	$GLOBALS['TL_CONFIG']['minifyMarkup'] = false;

	$GLOBALS['TL_CONFIG']['dbHost'] = '192.168.1.33';
	$GLOBALS['TL_CONFIG']['dbUser'] = 'team-ag-rechner';
	$GLOBALS['TL_CONFIG']['dbPass'] = 'team-ag-rechner';
	$GLOBALS['TL_CONFIG']['dbDatabase'] = 'team-ag-rechner';
}

/**
 * Local project development
 */
else if ($_SERVER['SERVER_ADDR'] === "127.0.0.1" || $_SERVER['SERVER_ADDR'] === "::1" || preg_match("/^dev./", $_SERVER['SERVER_NAME']) || preg_match("/xip.io$/", $_SERVER['SERVER_NAME'])){
	$GLOBALS['TL_CONFIG']['displayErrors'] = true;
	$GLOBALS['TL_CONFIG']['bypassCache'] = true;
	$GLOBALS['TL_CONFIG']['cacheMode'] = 'none';
	$GLOBALS['TL_CONFIG']['minifyMarkup'] = false;

	$GLOBALS['TL_CONFIG']['dbHost'] = '192.168.1.33';
	$GLOBALS['TL_CONFIG']['dbUser'] = 'team-ag-rechner';
	$GLOBALS['TL_CONFIG']['dbPass'] = 'team-ag-rechner';
	$GLOBALS['TL_CONFIG']['dbDatabase'] = 'team-ag-rechner';
}
