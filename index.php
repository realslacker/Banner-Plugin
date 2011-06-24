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

Plugin::setInfos(array(
	'id'          => 'banner',
	'title'       => __('Banners'),
	'description' => __('Provides interface to manage banners.'),
	'version'     => '0.0.2',
	'license'     => 'GPL',
	'author'      => 'Shannon Brooks',
	'website'     => 'http://www.dogdoo.net',
	'require_wolf_version' => '0.7.2'
));

/**
 * Root location where Banner plugin lives.
 */
define('BANNER_ROOT', URI_PUBLIC.'wolf/plugins/banner');

//	watch for banner requests
Observer::observe('page_requested', 'banner_catch_click');
Observer::observe('page_requested', 'banner_count_display');
Observer::observe('page_requested', 'banner_catch_json_request');


// Add the plugin's tab and controller
Plugin::addController('banner', __('Banners'),'banner_view,banner_new,banner_edit,banner_delete,banner_settings');

// Load the Comment class into the system.
AutoLoader::addFile('Banner', CORE_ROOT.'/plugins/banner/Banner.php');

//Plugin::addJavascript('banner', 'banner/js/jquery.maskedinput-1.2.2.min.js');

function bannerBySize($div,$width,$height) {

	$imguri = Plugin::getSetting('imguri','banner');
	$cssclass = Plugin::getSetting('cssclass','banner');
	

	if (!$banner = Banner::findBySize($width,$height)) return "<!-- could not load banner -->";
	$banner->dcount++;
	$banner->updated = date('Y-m-d H:i:s');
	$banner->save();
	$banner->image = "/{$imguri}/{$banner->image}";
	
	return '<div id="'.$div.'" class="'.$cssclass.$banner->width.'x'.$banner->height.'">'.(!empty($banner->url) ? '<a class="'.$cssclass.$banner->width.'x'.$banner->height.'" target="'.$banner->target.'" href="/banner-click/'.$banner->id.'" rel="nofollow">':'').'<img src="'.$banner->image.'" alt="'.$banner->alt.'" width="'.$banner->width.'" height="'.$banner->height.'" class="'.$cssclass.$banner->width.'x'.$banner->height.'" />'.(!empty($banner->url) ? '</a>':'').'</div>';

}

function bannerBySizeDynamic($div,$width,$height,$timeout=30) {
	$id = "banner".rand();
	return <<<HTML
<div id="{$div}" class="banner{$width}x{$height}">
	<div id="{$id}"></div>
</div>
<br style="clear: both;">
<script type="text/javascript">
<!--
$(function(){

	function {$id}_init() {
	
		var {$id}_container = $('#{$id}');
		
		function {$id}_run() {
			{$id}_container.children('a').removeClass('new').addClass('old').css('z-index','1');
			$.getJSON('/banner-json-request/{$width}x{$height}', function(b) {
				{$id}_container.append('<a class="banner{$width}x{$height} new" target="" href="/banner-click/'+b.id+'" rel="nofollow" style="display:none;z-index:2;"><img src="'+b.image+'" alt="'+b.alttext+'" class="{$width}x{$height}" height="{$height}" width="{$width}"></a>');
				{$id}_container.children('a.new').fadeIn(1000);
				{$id}_container.children('a.old').remove();
			});
		}
		{$id}_run();
		setInterval({$id}_run, {$timeout}*1000);
	}
	{$id}_init();

});
//-->
</script>
HTML;
}

function bannerById($div,$id) {

	$imguri = Plugin::getSetting('imguri','banner');
	$cssclass = Plugin::getSetting('cssclass','banner');
	

	$banner = Banner::findById($id);
	//$banner->dcount++;
	$banner->updated = date('Y-m-d H:i:s');
	$banner->save();
	$banner->image = "/{$imguri}/{$banner->image}";
	
	return '<div id="'.$id.'" class="'.$cssclass.$banner->width.'x'.$banner->height.'">'.(!empty($banner->url) ? '<a class="'.$cssclass.$banner->width.'x'.$banner->height.'" target="'.$banner->target.'" href="/banner-click/'.$banner->id.'" rel="nofollow">':'').'<img src="'.$banner->image.'" alt="'.$banner->alt.'" width="'.$banner->width.'" height="'.$banner->height.'" class="'.$cssclass.$banner->width.'x'.$banner->height.'" />'.(!empty($banner->url) ? '</a>':'').'</div>';

}

// redirect urls already set up
function banner_catch_click($args) {

	//	check for banner click
	if (preg_match('#^/banner-click/(\d+)$#i',$args,$matches)) {
		
		//	update the click count of the banner
		$id = (int)$matches[1];
		$banner = Banner::findById($id);
		$banner->ccount++;
		$banner->save();
		
		//	redirect to the requested url
		header ('HTTP/1.1 301 Moved Permanently', true);
		header ('Location: '.$banner->url);

		exit;
	}
	
	//	no click so keep going
	return $args;
}

// redirect urls already set up
function banner_count_display($args) {

	//	check for banner click
	if (preg_match('#^/banner-show/(\d+)$#i',$args,$matches)) {
		
		//	update the click count of the banner
		$id = (int)$matches[1];
		$banner = Banner::findById($id);
		$banner->ccount++;
		$banner->save();
		
		//	get image uri
		$imguri = Plugin::getSetting('imguri','banner');
		
		//	redirect to the requested url
		header ('HTTP/1.1 301 Moved Permanently', true);
		header ("Location: /{$imguri}/{$banner->image}");

		exit;
	}
	
	//	no click so keep going
	return $args;
}

// get new banner JSON
function banner_catch_json_request($args) {

	//	check for banner json request
	if (preg_match('#^/banner-json-request/(\d+)x(\d+)$#i',$args,$matches)) {
	
		$width = (int)$matches[1];
		$height = (int)$matches[2];
		
		$imguri = Plugin::getSetting('imguri','banner');
		$cssclass = Plugin::getSetting('cssclass','banner');
		

		if (!$banner = Banner::findBySize($width,$height)) {
			echo json_encode(array('error'=>'could not find banner'));
			exit;
		}
		$banner->dcount++;
		$banner->updated = date('Y-m-d H:i:s');
		$banner->save();
		$banner->image = "/{$imguri}/{$banner->image}";
		echo json_encode((array)$banner);
		exit;

	}
	
	//	no click so keep going
	return $args;

}


?>