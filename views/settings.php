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
<h1><?php echo __('Banners Plugin Settings');?></h1>
<form action="<?php echo get_url('plugin/banner/settings_save'); ?>" method="post">
	<fieldset style="padding: 0.5em;">
		<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Paths'); ?></legend>
		<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="label"><label for="imgpath"><?php echo __('Image Path:');?> </label></td>
				<td class="field"><input name="imgpath" id="imgpath" type="text" size="35" maxsize="255" value="<?php echo $imgpath;?>"/></td>
				<td class="help"><?php echo __('Image system path relative to CMS_ROOT.'); ?></td>
			</tr>
			<tr>
				<td class="label"><label for="imguri"><?php echo __('Image URI:');?> </label></td>
				<td class="field"><input name="imguri" id="imguri" type="text" size="35" maxsize="255" value="<?php echo $imguri;?>"/></td>
				<td class="help"><?php echo __('Image URI relative to URI_PUBLIC.'); ?></td>
			</tr>
		</table>
	</fieldset>
	<br/>
	<fieldset style="padding: 0.5em;">
		<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Link Properties');?></legend>
		<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="label"><label for="cssclass"><?php echo __('CSS Class:');?> </label></td>
				<td class="field"><input name="cssclass" id="cssclass" type="text" size="35" maxsize="255" value="<?php echo $cssclass;?>"/></td>
				<td class="help"><?php echo __('CSS class applyed to banner elements.');?></td>
			</tr>
			<tr>
				<td class="label"><label for="target"><?php echo __('Link Target:');?> </label></td>
				<td class="field"><input name="target" id="target" type="text" size="35" maxsize="255" value="<?php echo $target;?>"/></td>
				<td class="help"><?php echo __('Target of links; blank for no target (same window).');?></td>
			</tr>
		</table>
	</fieldset>
	<br/>
	<fieldset style="padding: 0.5em;">
		<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('File Creation Defaults'); ?></legend>
		<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="label"><label for="umask"><?php echo __('Umask:');?> </label></td>
				<td class="field"><input name="umask" id="umask" type="text" size="35" maxsize="255" value="<?php echo $umask;?>"/></td>
				<td class="help"><?php echo __('Default PHP umask; see <a href="http://php.net/manual/en/function.umask.php">umask()</a>');?></td>
			</tr>
			<tr>
				<td class="label"><label for="dirmode"><?php echo __('Directory Creation Mode:');?> </label></td>
				<td class="field"><input name="dirmode" id="dirmode" type="text" size="35" maxsize="255" value="<?php echo $dirmode;?>"/></td>
				<td class="help"><?php echo __('Default PHP directory creation mode; see <a href="http://us3.php.net/manual/en/function.chmod.php">chmod()</a>');?></td>
			</tr>
			<tr>
				<td class="label"><label for="filemode"><?php echo __('File Creation Mode:');?> </label></td>
				<td class="field"><input name="filemode" id="filemode" type="text" size="35" maxsize="255" value="<?php echo $filemode;?>"/></td>
				<td class="help"><?php echo __('Default PHP file creation mode; see <a href="http://us3.php.net/manual/en/function.chmod.php">chmod()</a>');?></td>
			</tr>
		</table>
	</fieldset>
	<p class="buttons">
		<input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Save');?>" />
	</p>
</form>

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
        // Prevent accidentally navigating away
        $(':input').bind('change', function() { setConfirmUnload(true); });
        $('form').submit(function() { setConfirmUnload(false); return true; });
    });
// ]]>
</script>