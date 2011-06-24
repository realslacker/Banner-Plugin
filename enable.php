<?php
/*
 * Banner Plugin for WolfCMS <http://www.wolfcms.org>
 * Copyright (C) 2011 Shannon Brooks <shannon@brooksworks.com>
 *
 * This file is part of Banner Plugin. Banner Plugin is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

// Security Measure
if (!defined('IN_CMS')) { exit(); }

//	include the Installer helper
use_helper('Installer');

//	only support MySQL
$driver = Installer::getDriver();
if ( $driver != 'mysql' ) Installer::failInstall( 'banner', __('Only MySQL is supported!') );

//	get plugin version
$version = Plugin::getSetting('version', 'banner');

switch ($version) {

	//	no version found so we do a clean install
	default:
	
		//	sanity check to make sure we are really dealing with a clean install
		if ($version !== false) Installer::failInstall( 'banner', __('Unknown Version!') );
		
		//	create tables
		
		$banner_table = TABLE_PREFIX . 'banner';
		$banner_table_sql =<<<SQL
			CREATE TABLE {$banner_table} (
				`id` int(11) unsigned NOT NULL auto_increment,
				`name` VARCHAR( 255 ) NULL DEFAULT NULL ,
				`alttext` VARCHAR( 255 ) NULL DEFAULT NULL ,
				`url` VARCHAR( 255 ) NULL DEFAULT NULL ,
				`image` VARCHAR( 255 ) NULL DEFAULT NULL ,
				`target` VARCHAR( 25 ) NULL DEFAULT NULL ,
				`width` VARCHAR( 5 ) NULL DEFAULT NULL ,
				`height` VARCHAR( 5 ) NULL DEFAULT NULL ,
				`dcount` int(11) NOT NULL DEFAULT 0,
				`ccount` int(11) NOT NULL DEFAULT 0,
				`active` tinyint(1) NOT NULL DEFAULT 1,
				`created` DATETIME NULL DEFAULT NULL ,
				`expires` DATE NULL DEFAULT NULL,
				`updated` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY ( `id` )
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8
SQL;
		if ( ! Installer::createTable($banner_table,$banner_table_sql) ) Installer::failInstall( 'banner', __('Could not create table 1 of 1.') );
		
		//	create new permissions
		if ( ! Installer::createPermissions('banner_view,banner_new,banner_edit,banner_delete,banner_settings') ) Installer::failInstall('banner');
		
		//	create new roles
		if ( ! Installer::createRoles('banner admin,banner manager') ) Installer::failInstall('banner');
		
		//	assign permissions
		//	note: admin_view is needed in case they don't have any other permissions, otherwise they won't be able to log in to admin interface
		if ( ! Installer::assignPermissions('banner admin','admin_view,banner_view,banner_new,banner_edit,banner_delete,banner_settings') ) Installer::failInstall('banner');
		if ( ! Installer::assignPermissions('banner manager','admin_view,banner_view,banner_new,banner_edit,banner_delete') ) Installer::failInstall('banner');
		if ( ! Installer::assignPermissions('administrator','banner_view,banner_new,banner_edit,banner_delete,banner_settings') ) Installer::failInstall('banner');
		
		//	setup plugin settings
		$settings = array(
			'version'		=>	'0.0.2',
			'imgpath'		=>	'public/banners',
			'imguri'		=>	'public/banners',
			'cssclass'		=>	'banner',
			'umask'			=>	'0',
			'filemode'		=>	'0664',
			'dirmode'		=>	'0775',
			'target'		=>	'_blank'
		);
		if ( ! Plugin::setAllSettings($settings, 'banner') ) Installer::failInstall( 'banner', __('Unable to store plugin settings!') );
		
		Flash::set('success', __('Successfully installed Banner plugin.'));
		
		//	we must exit the switch so upgrades are not applied to new installation (they should already be integrated for new installs)
		break;
		

	//	upgrade 0.0.1 to 0.0.2
	case '0.0.1':

		if ( ! Installer::createRoles('banner admin,banner manager') ) Installer::failInstall('banner');
		if ( ! Installer::assignPermissions('banner admin','admin_view,banner_view,banner_new,banner_edit,banner_delete,banner_settings') ) Installer::failInstall('banner');
		if ( ! Installer::assignPermissions('banner manager','admin_view,banner_view,banner_new,banner_edit,banner_delete') ) Installer::failInstall('banner');
		$settings = array('version'	=> '0.0.2');
		if ( ! Plugin::setAllSettings($settings, 'banner') ) Installer::failInstall( 'banner', __('Unable to store plugin settings!') );
		
		Flash::set('success', __('Successfully upgraded Banner plugin.'));


	//	upgrade 0.0.2 to 0.0.3
	case '0.0.2':
		// nothing here because we're still on 0.0.2, if we were on 0.0.1 and this was 0.0.3 upgrades would process in order
	
}


