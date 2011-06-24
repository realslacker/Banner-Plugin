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

class BannerController extends PluginController {

/* INIT *******************************************************************************************
 **************************************************************************************************/

	const VALID_SETTINGS	= 'imgpath,imguri,cssclass,umask,0644,filemode,dirmode,target';
	const VALID_INPUT		= 'id,name,url,alttext,target,width,height,expires,active';
	
	public $settings;

	/**
	 * Init
	 */
	public function __construct() {
		self::__checkPermission();
		
		$this->setLayout('backend');
		$this->assignToLayout('sidebar', new View('../../plugins/banner/views/sidebar'));

		$this->__settings();
		
	}// Init */
	
	/**
	 * Redirects to main index of plugin.
	 */
	public function index() {
		$this->banner_list();
	}//*/

	/**
	 * Displays documentation of plugin.
	 */
	public function documentation() {
		$this->display('banner/views/documentation');
	}//*/

	/**
	 * Displays settings of plugin. Requires role have permission 'banner_settings'.
	 */
	public function settings() {
		$this->__checkPermission('banner_settings');
		$this->display('banner/views/settings', $this->settings);
	}//*/
	
	/**
	 * Display the banner edit page for adding a new banner
	 */
	public function banner_add() {
		$this->display('banner/views/edit',array('target'=>$this->settings['target'],'active'=>1));
	}//*/
	
	/**
	 * Display the banner edit page
	 *
	 * @param int $id
	 */
	public function banner_edit($id=null) {
		if (is_null($id)) {
			Flash::set('error','Banners - '.__('No ID specified!'));
			redirect(get_url('plugin/banner/banner_list'));
		}
		if (!$banner = Banner::findById($id)) {
			Flash::set('error','Banners - '.__('Could not find banner!'));
			redirect(get_url('plugin/banner/banner_list'));
		}
		$banner = (array)$banner;
		$banner['expires'] = !empty($banner['expires']) ? date('m-d-Y',strtotime($banner['expires'])) : '';
		$this->display('banner/views/edit',$banner);
	}//*/
	
	/**
	 * Display the banner list page
	 */
	public function banner_list() {
		$this->display('banner/views/index', array(
			'banners' => Banner::findAll()
		));
	}//*/
	
/* DATA MANIPULATION FUNCTIONS ********************************************************************
 **************************************************************************************************/

	/**
	 * Deletes a banner and redirects to the banner list
	 *
	 * @param int $id
	 */
	public function banner_delete($id=null) {
		if (is_null($id)) {
			Flash::set('error','Banners - '.__('No ID specified!'));
			redirect(get_url('plugin/banner/banner_list'));
		}
		if (!$banner = Banner::findById($id)) {
			Flash::set('error','Banners - '.__('Could not find banner!'));
			redirect(get_url('plugin/banner/banner_list'));
		}
		$fullpath = CMS_ROOT."/{$this->settings['imgpath']}/{$banner->image}";
		if (is_file($fullpath)) if (!unlink($fullpath)) {
			Flash::set('error','Banners - '.__('Permission denied!'));
			redirect(get_url('plugin/banner/banner_list'));
		}
		$banner->delete();
		Flash::set('success','Banners - '.__('Deleted Successfully!'));
		redirect(get_url('plugin/banner/banner_list'));
	}//*/
	
	/**
	 * Processes an update to the banner and redirects back to the edit page
	 *
	 * @param int $id
	 */
	public function banner_update($id=null) {
		if (is_null($id)){
				Flash::set('error','Banners - '.__('No ID specified!'));
				redirect(get_url('plugin/banner/banner_list'));
			}
		
		//	retrieve the current banner settings from the database
		if (!$current = Banner::findById($id)) {
				Flash::set('error','Banners - '.__('Could not find banner!'));
				redirect(get_url('plugin/banner/banner_list'));
			}
		
		//	retrieve the new banner settings from $_POST
		$_POST['id'] = $id;
		$input = $this->__validate($_POST);
		
		//	rename the current image with the new settings
		$newname = $this->__imgname($input,'.'.pathinfo($current->image,PATHINFO_EXTENSION));
		$oldname = $current->image;
		if ($newname !== $oldname) {
			if (!$this->__rename($oldname,$newname)) {
				Flash::set('error','Banners - '.__('Could not rename image!'));
				redirect(get_url('plugin/banner/banner_edit/'.$id));
			}
			$current->image = $newname;
			if (!$current->save()) {
				$this->__rename($newname,$oldname);
				Flash::set('error','Banners - '.__('Could not save new image name in database!'));
				redirect(get_url('plugin/banner/banner_edit/'.$id));
			}
		}
		
		//	attempt to upload a new banner image
		if ($newname = $this->__upload('banner',$input,$current->image)) $current->image = $newname;
		
		//	update banner in database
		$current = Banner::findById($id);
		$current->updated = date('Y-m-d H:i:s');
		foreach ($input as $key => $value) {
			$current->$key = $value;
		}
		
		if (!$current->save()) {
			Flash::set('error','Banners - '.__('Could not update the banner in the database.'));
			redirect(get_url('plugin/banner/banner_edit/'.$id));
		}
		
		Flash::set('success','Banners - '.__('banner saved!'));
		redirect(get_url('plugin/banner/banner_edit/'.$id));
	}//*/

	/**
	 * Processes the new banner and redirects to the banner list
	 */
	public function banner_new() {
		
		//	get the validated input
		$input = $this->__validate($_POST);
		
		//	attempt to upload the image
		if (!$input['image'] = $this->__upload('banner',$input)) $this->display('banner/views/edit',$input);
		
		//	set the created date
		$input['created'] = date('Y-m-d H:i:s');
		$input['updated'] = date('Y-m-d H:i:s');
		
		//	save the new banner, if there is an issue delete the uploaded image
		$record = new Banner($input);
		if (!$record->save()) {
			$fullpath = CMS_ROOT."/{$this->settings['imgpath']}/{$input['image']}";
			if (!unlink($fullpath)) {
				Flash::set('error','Banners - '.__('Could not save banner in database and permission denied deleting image!'));
				$this->display('banner/views/edit',$input);
			}
			Flash::set('error','Banners - '.__('Could not save banner in database!'));
			$this->display('banner/views/edit',$input);
		}
		
		//	pat on the back and send back to banner list
		Flash::set('success','Banners - '.__('banner saved!'));
		redirect(get_url('plugin/banner/banner_list'));

	}//*/

	/**
	 * Saves settings of plugin. Requires role have permission 'banner_settings'.
	 *
	 * @param string $permission - possible values banner_view, banner_new, banner_edit, banner_delete, banner_settings
	 * @return redirects the user if they do not have permission
	 */
	public function settings_save() {
		$this->__checkPermission('banner_settings');
		
		//	clean any keys from the $_POST array that aren't valid
		$settings = $this->__clean($_POST,self::VALID_SETTINGS);
		
		//	sanitize the input path
		$settings['imgpath'] = $this->__sanitize($settings['imgpath']);
		$settings['imguri'] = $this->__sanitize($settings['imguri']);
		
		
		//	make sure classes and targets only have a limited set of characters in them
		$settings['cssclass'] = preg_replace('/[^a-z0-9\s_-]/i','',$settings['cssclass']);
		$settings['target'] = preg_replace('/[^a-z0-9_-]/i','',$settings['target']);
		
		//	cleanup the masks
		$settings['umask'] = (int)$settings['umask'] == 0 ? 0 : sprintf("%04s",(int)$settings['umask']<=777 ? (int)$settings['umask'] : 0);
		$settings['dirmode'] = sprintf("%04s",(int)$settings['dirmode']<=777 && 111 <= (int)$settings['dirmode'] ? (int)$settings['dirmode'] : 755);
		$settings['filemode'] = sprintf("%04s",(int)$settings['filemode']<=777 && 111 <= (int)$settings['filemode'] ? (int)$settings['filemode'] : 644);
		
		if (Plugin::setAllSettings($settings, 'banner')) Flash::set('success', 'Banners - '.__('plugin settings saved.'));
		else Flash::set('error', 'Banners - '.__('plugin settings not saved!'));

		redirect(get_url('plugin/banner/settings'));

	}//*/
	
/* UTILITY FUNCTIONS ******************************************************************************
 **************************************************************************************************/

	/**
	 * Check a users permission against the role they have been assigned.
	 *
	 * @param string $permission - possible values banner_view, banner_new, banner_edit, banner_delete, banner_settings
	 * @return redirects the user if they do not have permission
	 */
	private static function __checkPermission($permission='banner_view') {
		AuthUser::load();
		if ( ! AuthUser::isLoggedIn()) {
			redirect(get_url('login'));
		}
		if ( ! AuthUser::hasPermission($permission) ) {
			Flash::set('error', __('You do not have permission to access the requested page!'));
			if (! AuthUser::hasPermission('banner_view') ) redirect(get_url());
			else redirect(get_url('plugin/banner'));
		}
	}//*/

	/**
	 * Removes invalid keys from a settings array.
	 *
	 * @param array $settings
	 * @param array $keys or string $keys (comma seperated)
	 * @return array
	 */
	private static function __clean($settings,$keys) {
		if (!is_array($settings)) return array();
		$valid = is_array($keys) ? $keys : explode(',',$keys);
		$valid = array_combine($valid,$valid);
		return array_intersect_key($settings, $valid);
	}//*/
	
	/**
	 * Generates the image name based on $input
	 *
	 * @param array $input
	 * @param string $ext
	 * @return string filename
	 */
	private static function __imgname($input,$ext) {
		return date('Y-m-d').'_'.preg_replace("/\s/","-",substr($input['name'],0,100))."_{$input['width']}x{$input['height']}{$ext}";
	}//*/
	
	/**
	 * Rename a file in the upload destination directory
	 *
	 * @param string $old_name
	 * @param string $new_name
	 * @return bool result of rename
	 */
	private function __rename($old_name,$new_name) {
		$dir = CMS_ROOT."/{$this->settings['imgpath']}/";
		if (!file_exists($dir.$old_name)) return false;
		return @rename($dir.$old_name,$dir.$new_name);
	}//*/
	
	/**
	 * Sanitizes a path.
	 *
	 * @param string $path
	 * @return clean string $path
	 */
	private static function __sanitize($path) {
		$path = explode('/',$path);
		foreach ($path as $k => $v) $path[$k] = trim($v," \t.");
		return implode('/',array_filter($path,'strlen'));
	}//*/
	
	/**
	 * Puts all settings into $this->settings
	 */
	private function __settings() {
		if (!$this->settings = Plugin::getAllSettings('banner')) {
			Flash::set('error', 'Banners - '.__('unable to retrieve plugin settings.'));
			redirect(get_url('setting'));
			return;
		}
	}//*/
	
	/**
	 * Uploads the banner image and return the filename
	 *
	 * @param string $tagname
	 * @param array $input
	 * @param string $oldfile
	 * @return string $imgfile
	 */
	private function __upload($tagname,$input,$oldfile=null) {
	
		//	if there is no uploaded file return false
		if (!is_uploaded_file($_FILES[$tagname]['tmp_name'])) return false;
		
		//	determine the uploaded file extension
		$ext = pathinfo($_FILES[$tagname]['name'],PATHINFO_EXTENSION);
		switch($ext) {
			case 'jpg':
			case 'jpeg':
				$ext = '.jpg';
				break;
			case 'gif':
				$ext = '.gif';
				break;
			case 'png':
				$ext = '.png';
				break;
			default:
				Flash::set('error','Banners - '.__('Invalid file type.'));
				return false;
				break;
		}
		
		//	get the image name based on the $input array and upload extension
		$filename = $this->__imgname($input,$ext);
		
		//	determine the destination directory
		$dstdir = CMS_ROOT."/{$this->settings['imgpath']}/";
		
		//	if the $oldfile is set and it exists rename it just in case we
		//	issues with the upload
		if (!empty($oldfile) && file_exists($dstdir.$oldfile)) $this->__rename($oldfile,'__'.$oldfile);
		
		//	if the new name is already taken put the old file back, set an error
		//	and put the $oldfile back
		if (file_exists($dstdir.$filename)) {
			Flash::set('error','Banners - '.__('Banner file already exists with filename')." {$filename}");
			if (file_exists($dstdir.'__'.$oldfile)) $this->__rename('__'.$oldfile,$oldfile);
			return false;
		}
		
		//	set the umask and move the uploaded file
		//	if there is a problem set the error, put the old file back and set an error
		umask(octdec($this->settings['umask']));
		if (!@move_uploaded_file($_FILES[$tagname]['tmp_name'],$dstdir.$filename)) {
			Flash::set('error','Banners - '.__('Could not move uploaded banner file!'));
			if (file_exists($dstdir.'__'.$oldfile)) $this->__rename('__'.$oldfile,$oldfile);
			return false;
		}
		
		//	check to see if the file was uploaded and try to chmod the file
		//	if there is a problem with chmod output an error but don't stop since the
		//	file was already uploaded
		if (@!chmod($dstdir.$filename, octdec($this->settings['filemode']))) Flash::set('error', __('Could not change uploaded file permissions!'));
		
		//	if the temporary old file exists try to delete it, if there is a problem show an error
		if (is_file($dstdir.'__'.$oldfile)) if (!unlink($dstdir.'__'.$oldfile)) Flash::set('error', __('Could not delete temporary file! You need manually delete the file '.'__'.$filename.' and fix the permissions on the directory.'));

		//	return the image name
		return $filename;

	}//*/
	
	/**
	 * Validates input for saving a new banner
	 *
	 * @param array $input
	 * @return validated array $input
	 */
	private function __validate($input) {
		
		//	remove invalid keys from input array
		$input = $this->__clean($input,self::VALID_INPUT);
		
		//	setup path for redirect
		$redirect = 'plugin/banner/banner_edit'.(isset($input['id']) ? '/'.$input['id'] : '');
		
		//	clean the name and leave just the basics
		$input['name'] = preg_replace('/[^a-z0-9\s_-]/i','',$input['name']);
		if (empty($input['name'])) {
			Flash::set('error',__('Banner Name is Required'));
			redirect(get_url($redirect));
		}
		
		//	clean the alttext, check if alttext and url are set and if not set to null
		$input['alttext'] = isset($input['alttext']) ? preg_replace("/[^a-z0-9\s_-]/i","",$input['alttext']) : '';
		$input['target'] = isset($input['target']) ? preg_replace("/[^a-z0-9_-]/i","",$input['target']) : '';
		$input['url'] = isset($input['url']) ? $input['url'] : '';
		$input['active'] = isset($input['active']) ? 1 : 0;
		
		//	make sure the width and height are ints larger than 1
		$input['width'] = isset($input['width']) ? (int)preg_replace('/[^0-9]/','',$input['width']) : 0;
		$input['height'] = isset($input['height']) ? (int)preg_replace('/[^0-9]/','',$input['height']) : 0;
		if ($input['width'] < 1 || $input['height'] < 1) {
			Flash::set('error',__('Banner Width and Height must be greater than zero'));
			redirect(get_url($redirect));
		}
		
		//	set the expires tag to null if it's empty otherwise reformat for the database
		$input['expires'] = preg_replace('/[^0-9]/','',$input['expires']);
		$input['expires'] = !empty($input['expires']) ? substr($input['expires'],4).'-'.substr($input['expires'],0,2).'-'.substr($input['expires'],2,2) : '';
		
		//return the array
		return $input;

	}//*/
	
}