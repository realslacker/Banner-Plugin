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
<p class="button"><a href="<?=get_url('plugin/banner'); ?>"><img src="<?php echo URL_PUBLIC; ?>wolf/plugins/banner/images/list.png" align="middle" /><?php echo __('List Banners'); ?></a></p>
<p class="button"><a href="<?=get_url('plugin/banner/banner_add'); ?>"><img src="<?php echo URL_PUBLIC; ?>wolf/plugins/banner/images/new.png" align="middle" /><?php echo __('Add Banner'); ?></a></p>
<p class="button"><a href="<?=get_url('plugin/banner/documentation'); ?>"><img src="<?php echo URL_PUBLIC; ?>wolf/plugins/banner/images/documentation.png" align="middle" /><?php echo __('Documentation'); ?></a></p>
<div class="box">
<h2><?php echo __('Banners Plugin');?></h2>
<p>
<?php echo __('Plugin Version').': '.Plugin::getSetting('version', 'banner'); ?>
</p>
</div>
