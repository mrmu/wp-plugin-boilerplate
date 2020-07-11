<?php
/**
 * Wppb_Doer
 *
 * @package Wppb_Doer
 * @author  Audi Lu <mrmu@mrmu.com.tw>
 */

/* Check if Class Exists. */
if ( ! class_exists( 'Wppb_Doer' ) ) {
	/**
	 * Wppb_Doer class.
	 */

	class Wppb_Doer {
		private static $wppb_doer_instance = null;
		private $plugin_slug;
		private $plugin_name;
		private $plugin_uri;
		private $author_name;
		private $author_email;
		private $author_uri;
		private $upload_dir;
		private $src_base_dir;
		private $src_dirname;
		private $dist_base_dir;
		private $dist_dirname;
		private $zip_to_dir;
		private $zip_filename;
		private $zip_dirname;

		private function __construct($args) {
			$defaults = array(
				'plugin_slug' => 'my-plugin-slug',
				'plugin_name' => 'My Plugin Name',
				'plugin_uri' => 'https://my-plugin.com',
				'author_name' => 'Author Name',
				'author_email' => 'author@my-plugin.com',
				'author_uri' => 'https://my-plugin.com/author',
			);
			$args = wp_parse_args( $args, $defaults );

			$obj_upload_dir = wp_upload_dir();
			$this->upload_dir = $obj_upload_dir['basedir'];

			// args
			$this->plugin_slug =  $args['plugin_slug'];
			$this->plugin_name =  $args['plugin_name'];
			$this->plugin_uri =  $args['plugin_uri'];
			$this->author_name =  $args['author_name'];
			$this->author_email =  $args['author_email'];
			$this->author_uri =  $args['author_uri'];

			// source code (templates)
			// $this->src_base_dir = get_template_directory();
			$this->src_base_dir = plugin_dir_path(dirname(__FILE__));
			$this->src_dirname = 'src_tmls/';

			// replaced code (dist code)
			$this->dist_base_dir = $obj_upload_dir['basedir'];
			$this->dist_dirname = $this->plugin_slug.'/';

			// zip file
			$this->zip_to_dir = $obj_upload_dir['basedir'];
			$this->zip_filename = $this->plugin_slug.'.zip';
			$this->zip_dirname = $this->plugin_slug.'/';
		}

		// recursively remove dir
		private function rrmdir($src) {
			if (file_exists($src)) {
				$dir = opendir($src);
				while (false !== ($file = readdir($dir))) {
					if (($file != '.') && ($file != '..')) {
						$full = $src . '/' . $file;
						if (is_dir($full)) {
							$this->rrmdir($full);
						} else {
							unlink($full);
						}
					}
				}
				closedir($dir);
				rmdir($src);
			}
		}

		// recursively copy src (dir) to dist (dir)
		private function rcopy($src, $dst) {
			$dir = opendir($src);
			if (is_dir($dst)){
				$this->rrmdir($dst);
			}
			mkdir($dst);
			while(false !== ( $file = readdir($dir)) ) {
				if (( $file != '.' ) && ( $file != '..' )) {
					if ( is_dir($src.'/'.$file) ) {
						$this->rcopy($src.'/'.$file, $dst.'/'.$file);
					}else {
						copy($src.'/'.$file, $dst.'/'.$file);
					}
				}
			}
			closedir($dir);
		}

		private function find_n_replace($dir, $replace_args, $str='', $count = 0){
			if (is_dir($dir)) {
				if ('/' !== substr($dir, -1)){
					$dir .= '/';
				}
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if( is_dir( $dir . $file ) ) {
							if( ($file !== ".") && ($file !== "..")) {
								foreach ($replace_args as $find => $replace) {
									// dir rename
									if ($find == '[plugin_slug]' && false !== strpos($file, '[plugin_slug]')) {
										$new_file = str_replace($find, $replace, $file);
										// echo 'dir rename: '.$dir.$file.' to '.$dir.$new_file."\n";
										rename($dir.$file, $dir.$new_file);
										$file = $new_file;
									}
								}
								$this->find_n_replace($dir . $file, $replace_args, $str, $count);
							}
						}else{
							// echo 'is file: '.$dir . $file."\n";
							if ( $str == '' ){
								if( ($file !== ".") && ($file !== "..")) {
									foreach ($replace_args as $find => $replace) {
										// file rename
										if ($find == '[plugin_slug]' && false !== strpos($file, '[plugin_slug]')) {
											$new_file = str_replace($find, $replace, $file);
											// echo 'file rename: '.$dir.$file.' to '.$dir.$new_file."\n";
											rename($dir.$file, $dir.$new_file);
											$file = $new_file;
										}
									}
									$temp = file_get_contents( $dir . $file );
									foreach ($replace_args as $find => $replace) {
										$temp = str_replace($find, $replace, $temp);
										if( !file_put_contents( $dir . $file, $temp ) ){
											// echo "There was a problem (permissions?) replacing the file " . $dir . $file;
										}else{
											// echo "File " . $dir . $file . " replaced!";
											$count++;
										}
									}
								}else{
									// echo 'file == . ..'."\n";
								}
							}else{
								if (strpos($file, $str)){
									$temp = file_get_contents( $dir . $file );
									foreach ($replace_args as $find => $replace) {
										$temp = str_replace($find, $replace, $temp);
										if( !file_put_contents( $dir . $file, $temp ) ){
											// echo "There was a problem (permissions?) replacing the file " . $dir . $file;
										}
										else{
											// echo "File " . $dir . $file . " replaced!";	
											$count++;
										}
									}
								}else{
									// echo 'strpos false';
								}
							}
						}
					}
					closedir($dh);
				}else{
					// echo "There was a problem opening the directory " . $dir . " (permissions maybe?)";	
				}
			}else{
				// echo "You gave us a file instead of a directory, we could check that instead, but this is only designed for recursing really; use vim or something!";
			}
			// echo "Completed recursing" . $dir . "! ";
			// echo "Count: $count";
		}

		private function create_zip($dir, $zip_archive, $zipdir = '') { 
			if (is_dir($dir)) { 
				if ($dh = opendir($dir)) { 
					//Add the directory 
					if(!empty($zipdir)) {
						$zip_archive->addEmptyDir($zipdir); 
					}
					// Loop through all the files 
					while (($file = readdir($dh)) !== false) { 
				
						//If it's a folder, run the function again! 
						if(!is_file($dir . $file)){ 
							// Skip parent and root directories 
							if( ($file !== ".") && ($file !== "..")){ 
								$this->create_zip($dir . $file . "/", $zip_archive, $zipdir . $file . "/");
							} 
						}else{ 
							// Add the files 
							$zip_archive->addFile($dir . $file, $zipdir . $file); 
						} 
					} 
				} 
			} 
		}

		public static function get_doer($args) {
			if (!self::$wppb_doer_instance) {
				self::$wppb_doer_instance = new Wppb_Doer($args);
			}
			return self::$wppb_doer_instance;
		}

		private function slug_format($slug, $mode = 'ucwords') {
			$esc_chars = array('-', '－', '_', '＿');
			for ($i = 0; $i < count($esc_chars); $i++) {
				$slug = str_replace($esc_chars[$i], " ", $slug);
			}
			$slugs = explode(" ", ucwords($slug));
			$slug = implode('_', $slugs);
			if ($mode == 'upper') {
				return strtoupper($slug);
			}elseif ($mode == 'lower') {
				return strtolower($slug);
			}
			return $slug;
		}

		public function run() {
			$src_dir = $this->src_base_dir.$this->src_dirname;
			$dist_dir = $this->dist_base_dir.'/'.$this->dist_dirname;

			// step 1. copy src (dir) to dist (dir)
			$this->rcopy($src_dir, $dist_dir);
		
			// step2. strings replacement
			$replace_args = array(
				'[plugin_slug_classname]' => $this->slug_format($this->plugin_slug),
				'[plugin_slug_funcname]' => $this->slug_format($this->plugin_slug, 'lower'),
				'[plugin_slug]' => strtolower($this->plugin_slug),
				'[PLUGIN_SLUG]' => $this->slug_format($this->plugin_slug, 'upper'), 
				'[PLUGIN_NAME]' => $this->plugin_name,
				'[PLUGIN_URI]' => $this->plugin_uri,
				'[AUTHOR_NAME]' => $this->author_name,
				'[AUTHOR_EMAIL]' => $this->author_email,
				'[AUTHOR_URI]' => $this->author_uri,
			);
			$this->find_n_replace($dist_dir, $replace_args);
		
			$zip = new ZipArchive();
			$filename = $this->zip_to_dir.'/'.$this->zip_filename;
			if ($zip->open($filename, ZipArchive::CREATE) !== true) {
				exit("cannot open <$filename>\n");
			}
			// step3. create zip
			$this->create_zip($dist_dir, $zip, $this->zip_dirname);
			$zip->close();
		
			// step4. del dist dir
			if (is_dir($dist_dir)){
				$this->rrmdir($dist_dir);
			}

			// step5. return zip filename
			return $filename;
		}
	}

}