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

?>
<h1><?php echo __('Documentation'); ?></h1>

<h2>Static Usage</h2>
<p>Load a particular banner.</p>
<code>&lt;?php echo bannerById('id_of_div','banner_id'); ?&gt;</code>

<h2>Dynamic Usage (single load)</h2>
<p>Load a dynamic banner once per page load.</p>
<code>&lt;?php echo bannerBySize('id_of_div','banner_width','banner_height'); ?&gt;</code>

<h2>Dynamic Usage (dynamic)</h2>
<p>Load a dynamic banner that refreshes ever X seconds.</p>
<code>&lt;?php echo bannerBySizeDynamic('id_of_div','banner_width','banner_height','timeout'); ?&gt;</code>