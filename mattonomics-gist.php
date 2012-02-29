<?php
/*
Plugin Name: Mattonomics Gist
Plugin URI: https://github.com/mattonomics/gist-shortcode
Description: Simple plugin to handle gists
Version: 1.0
Author: Matt Gross
Author URI: http://mattonomics.com
License: GPLv2, but screw people who care
*/

class mattonomics_gist {
	public function __construct() {
		add_action('wp', array(__CLASS__, 'add_css')); // check if we have a gist
	}
	
	public static function add_css() {
		global $post;
		if (self::has_gist($post))
			add_action('wp_head', array(__CLASS__, 'css_output')); // if we have a gist, add the css to the head
	}
	
	public static function has_gist($post = false) {
		if (! empty($post) && is_object($post) && property_exists($post, 'post_content') && preg_match('/\[gist.+\]/', $post->post_content))
			return true;
		return false;
	}
	
	public static function css_output() {
		echo "\n\n\t\t<!-- github css -->\n"
		. "\t\t<link type=\"text/css\" media=\"screen\" rel=\"stylesheet\" href=\"https://gist.github.com/stylesheets/gist/embed.css\" />\n";
	}
	
	public static function gist($a) {
		if (! preg_match('/^https:\/\/gist\.github\.com\/([0-9]+)$/', $a['src'], $m)) // $m[1] is the identifier
			return false;
		$g = wp_remote_get("http://gist.github.com/{$m[1]}.json");
		if (! is_wp_error($g))
			$code = json_decode($g['body']);
		
		return $code->div;
	}
}

add_shortcode('gist', array('mattonomics_gist', 'gist'));

new mattonomics_gist;