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
<h1><?=isset($id) ? __('Edit Banner') . " #{$id}: {$name} ({$width}x{$height})" : __('New Banner');?></h1>
<form method="post" enctype="multipart/form-data" action="<?=isset($id) ? get_url('plugin/banner/banner_update/'.$id) : get_url('plugin/banner/banner_new'); ?>">
	<fieldset style="padding: 0.5em;">
		<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Banner Image'); ?></legend>
		<? if (isset($id)) : ?>
		<img src="<?=URI_PUBLIC.Plugin::getSetting('imguri','banner').'/'.$image;?>" alt="<?=$alttext;?>" width="<?=$width;?>" height="<?=$height;?>" /><br />
		<? else : ?>
		<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="label"><label for="banner"><?php echo __('Banner Image:');?> </label></td>
				<td class="field"><input name="banner" id="banner" type="file" /></td>
				<td class="help"><?php echo __('Only jpg, png and gif files are accepted'); ?></td>
			</tr>
		</table>
		<? endif; ?>
	</fieldset>
	<br />
	<fieldset style="padding: 0.5em;">
		<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Banner Information'); ?></legend>
		<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="label"><label for="name"><?php echo __('Name:');?> </label></td>
				<td class="field"><input name="name" id="name" type="text" size="35" maxsize="255" value="<?=isset($name) ? $name : '';?>"/></td>
				<td class="help"><?php echo __('The banner name; be descriptive'); ?></td>
			</tr>
			<tr>
				<td class="label"><label for="url"><?php echo __('Destination URL:');?> </label></td>
				<td class="field"><input name="url" id="url" type="text" size="35" maxsize="255" value="<?=isset($url) ? $url : '';?>"/></td>
				<td class="help"><?php echo __('URL of the website to go to when the banner is clicked'); ?></td>
			</tr>
			<tr>
				<td class="label"><label for="alttext"><?php echo __('Alt Text:');?> </label></td>
				<td class="field"><input name="alttext" id="alttext" type="text" size="35" maxsize="255" value="<?=isset($alttext) ? $alttext : '';?>"/></td>
				<td class="help"><?php echo __('Alternative Text - helps with searchability'); ?></td>
			</tr>
			<tr>
				<td class="label"><label for="target"><?php echo __('Link Target:');?> </label></td>
				<td class="field"><input name="target" id="target" type="text" size="35" maxsize="255" value="<?=isset($target) ? $target : '';?>"/></td>
				<td class="help"><?php echo __('Link Target - use _blank for new window'); ?></td>
			</tr>
			<tr>
				<td class="label"><label for="width"><?php echo __('Width:');?> </label></td>
				<td class="field"><input name="width" id="width" type="text" size="35" maxsize="255" value="<?=isset($width) ? $width : '';?>"/></td>
				<td class="help"><?php echo __('Width in pixels'); ?></td>
			</tr>
			<tr>
				<td class="label"><label for="height"><?php echo __('Height:');?> </label></td>
				<td class="field"><input name="height" id="height" type="text" size="35" maxsize="255" value="<?=isset($height) ? $height : '';?>"/></td>
				<td class="help"><?php echo __('Height in pixels'); ?></td>
			</tr>
			<tr>
				<td class="label"><label for="expires"><?php echo __('Expires Date:');?> </label></td>
				<td class="field"><input name="expires" id="expires" type="text" size="35" maxsize="255" value="<?=isset($expires) ? $expires : '';?>"/></td>
				<td class="help"><?php echo __('Use MM/DD/YYYY format'); ?></td>
			</tr>
			<tr>
				<td class="label"><label for="active"><?php echo __('Active:');?> </label></td>
				<td class="field"><input name="active" id="active" type="checkbox" value="1" <?=$active ? 'checked="checked"' : '';?> /></td>
				<td class="help"><?php echo __('Is the banner active?'); ?></td>
			</tr>
		</table>
	</fieldset>
	<p class="buttons">
		<input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Save');?>" />
	</p>
</form>
<script type="text/javascript" src="<?=PLUGINS_URI;?>/banner/js/jquery.maskedinput-1.2.2.min.js"></script>
<script type="text/javascript">
// <![CDATA[
    function setConfirmUnload(on, msg) {
        window.onbeforeunload = (on) ? unloadMessage : null;
        return true;
    }

    function unloadMessage() {
        return '<?php echo __('You have modified this page.  If you navigate away from this page without first saving your data, the changes will be lost.'); ?>';
    }

    $(document).ready(function() {
		// setup some masks
		$('#expires').mask('99/99/9999');
		$('#width').mask('9?999');
		$('#height').mask('9?999');
		
        // Prevent accidentally navigating away
        $(':input').bind('change', function() { setConfirmUnload(true); });
        $('form').submit(function() { setConfirmUnload(false); return true; });
    });
// ]]>
</script>