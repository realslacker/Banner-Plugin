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

use_helper('Installer');

if ( ! Installer::removeTable(TABLE_PREFIX.'banner') ) Installer::failUninstall( 'banner' );

if ( ! Installer::removePermissions('banner_view,banner_new,banner_edit,banner_delete,banner_settings') ) Installer::failUninstall( 'banner' );

if ( ! Installer::removeRoles('banner admin,banner manager') ) Installer::failUninstall( 'banner' );

if ( ! Plugin::deleteAllSettings('banner') ) Installer::failUninstall( 'banner', __('Could not remove plugin settings.') );

Flash::set('success', __('Successfully uninstalled plugin.'));
redirect(get_url('setting'));