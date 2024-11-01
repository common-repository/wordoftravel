<?php
/*
Plugin Name: wordoftravel
Plugin URI: https://bloggers.wordoftravel.com
Description: Official plugin of wordoftravel.com for travel bloggers
Author: wordoftravel.com
Version: 1.0.5
Author URI: https://wordoftravel.com

We thank and give credit to Omar Kasem of wisersteps.com
 for his work on the initial version of this plugin
 */

class WP_WordOfTravel_Blogger {
	protected $plugin_name;
	protected $version;

	public function __construct() {
		$this->version = '1.0.5';
		$this->plugin_name = 'wordoftravel';

		add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

		// Require Libraries
		$this->require_libs();

		// Option page
		add_action('admin_menu', array($this, 'plugin_option_page'));
		add_action('admin_init', array($this, 'plugin_register_settings'));

		// Unique code
		add_action('wp_head', array($this, 'add_unique_code'));

		// Add link to us to footer/sidebar
		add_action('wp_footer', array($this, 'add_link_to_footer'), 1);
		add_action('get_sidebar', array($this, 'add_link_to_sidebar'), 1, 1);

		// Admin scripts & styles
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts_and_styles'));

		// Metabox
		add_action('add_meta_boxes', array($this, 'register_meta_box'));
		add_action('save_post', array($this, 'save_meta_box'));

		// Insert Topics in post
		add_filter('the_content', array($this, 'insert_into_post_content'), 20);

	}

	/**
	 * Insert topics and places into post
	 * @param  string $content
	 * @return string
	 */
	public function insert_into_post_content($content) {
		$post_id = get_the_ID();
		$topics = get_post_meta($post_id, 'wotb_topics', true);
		$places = get_post_meta($post_id, 'wotb_places', true);
		if (is_array($topics) && !empty($topics)) {
			$content .= '<!––wordoftravel Topics ' . json_encode($topics) . ' -->';
		}
		if (is_array($places) && !empty($places)) {
			$content .= '<!--wordoftravel Places ' . json_encode($places) . ' -->';
		}

		return $content;
	}

	/**
	 * Register meta box
	 * @return null
	 */
	public function register_meta_box() {
		add_meta_box('wordoftravel-metabox', __('Wordoftravel Post Categories', $this->plugin_name), array($this, 'meta_box_callback'), 'post');
	}

	/**
	 * Metabox callback display
	 * @param  integer $meta_id
	 * @return null
	 */
	public function meta_box_callback($meta_id) {
		include plugin_dir_path(__FILE__) . 'partials/metabox-display.php';
	}

	/**
	 * Save metabox values on save
	 * @param  integer $post_id
	 * @return null
	 */
	public function save_meta_box($post_id) {
		if (get_post_type($post_id) === 'post') {
			if (isset($_POST['wotb_topics']) && !empty($_POST['wotb_topics'])) {
				$topics = $_POST['wotb_topics'];
				update_post_meta($post_id, 'wotb_topics', $topics);
			}

			if (isset($_POST['wotb_places']) && !empty($_POST['wotb_places'])) {
				$places = $_POST['wotb_places'];
				update_post_meta($post_id, 'wotb_places', $places);
			}
		}
	}

	/**
	 * Make the plugin translatable
	 * @return null
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->plugin_name,
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}

	/**
	 * Require important libraries
	 * @return null
	 */
	private function require_libs() {
		require plugin_dir_path(__FILE__) . 'libs/vendor/autoload.php';
	}

	/**
	 * Enqueue styles and scripts to the plugin
	 * @return null
	 */
	public function admin_scripts_and_styles() {
		if (get_current_screen()->id === 'settings_page_wordoftravel' || get_current_screen()->id === 'post') {
			// Scripts
			wp_enqueue_script($this->plugin_name . 'select2', plugin_dir_url(__FILE__) . 'assets/admin/js/select2.full.min.js', array('jquery'), $this->version, false);

			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'assets/admin/js/wordoftravel-admin.js', array('jquery'), $this->version, false);

			wp_localize_script($this->plugin_name, 'ajax_object',
				array('ajax_url' => admin_url('admin-ajax.php')));

			// Styles
			wp_enqueue_style($this->plugin_name . 'select2', plugin_dir_url(__FILE__) . 'assets/admin/css/select2.min.css', array(), $this->version, 'all');
		}

	}

	/**
	 * Get wordoftravel topics from API
	 * @return array
	 */
	public function get_topics() {
		$url = 'https://wpplugin-api.wordoftravel.com/topics';
		$client = new \GuzzleHttp\Client(['verify' => false]);
		$res = $client->request('GET', $url);
		$body = json_decode($res->getBody());
		return $body->validTopics;
	}

	/**
	 * Show message on validating unique code
	 * @return null
	 */
	public function show_unique_code_message() {
		if (isset($_POST['submit']) && isset($_POST['wotb_unique_code']) && $_POST['wotb_unique_code'] !== '') {
			$code = sanitize_text_field($_POST['wotb_unique_code']);
			update_option('wotb_unique_code', $code);
			$body = $this->confirm_unique_code();
			if (is_object($body) && property_exists($body, 'error')) {
				echo '<p class="wotb_error">' . $body->error . '</p>';
			} else {
				echo '<p class="wotb_success">' . __('Thank you your blog ownership has been confirmed.', $this->plugin_name) . '</p>';
			}
		}
	}

	/**
	 * Validate uniuqe code from API
	 * @return object
	 */
	public function confirm_unique_code() {
		$blog_url = home_url();
		$blog_url = preg_replace('#^https?://#', '', rtrim($blog_url, '/'));
		$url = 'https://wpplugin-api.wordoftravel.com/confirmownership/' . $blog_url . '';
		$client = new \GuzzleHttp\Client(['verify' => false]);
		try {
			$res = $client->request('GET', $url);
			$body = json_decode($res->getBody());
		} catch (GuzzleHttp\Exception\ClientException $e) {
			$response = $e->getResponse();
			$body = json_decode($response->getBody()->getContents());
		}
		return $body;

	}

	/**
	 * Add option page
	 * @return null
	 */
	public function plugin_option_page() {
		add_options_page('Wordoftravel', 'Wordoftravel', 'manage_options', $this->plugin_name . '.php', array($this, 'plugin_option_page_display'));
	}

	/**
	 * Include option page HTML
	 * @return null
	 */
	public function plugin_option_page_display() {
		include plugin_dir_path(__FILE__) . 'partials/option-page.php';
	}

	/**
	 * Register the plugin settings
	 * @return null
	 */
	public function plugin_register_settings() {
		register_setting('wotb_link_settings', 'wotb_type_of_link');
		register_setting('wotb_link_settings', 'wotb_html_place');
		register_setting('wotb_link_settings', 'wotb_link_style');

	}

	/**
	 * Add the unique code into meta tag in header
	 * @return  null
	 */
	public function add_unique_code() {
		if (get_option('wotb_unique_code') !== '') {
			echo '<meta name="wordoftravel" content="' . sanitize_text_field(get_option('wotb_unique_code')) . '">';
		}
	}

	/**
	 * Get type of link
	 * @param  integer $footer
	 * @return string
	 */
	private function type_of_link($footer = 0) {
		$link = '';
		if (intval(get_option('wotb_type_of_link')) === 1) {
			$link = '<div class="wotb_link_div"><a ' . (($footer === 1 && intval(get_option('wotb_link_style')) === 2) ? 'style="background: #4b4b4b;color:#fff!important;"' : '') . '  class="wotb_link_to_us" href="https://wordoftravel.com">' . __('Find more travel blogs like this on wordoftravel.com', $this->plugin_name) . '</a></div>';
		} elseif (intval(get_option('wotb_type_of_link')) === 2) {
			$link = '<div class="wotb_link_div"><a class="wotb_link_to_us" href="https://wordoftravel.com"><img src="https://wordoftravel.com/images/bloggerlinksmall.png" alt="' . __('Find more travel blogs like this on wordoftravel.com', $this->plugin_name) . '"></a></div>';
		}
		if ($footer == 1) {
			$link .= '<style>
                .wotb_link_div{
                    text-align: center;
                }
                .wotb_link_to_us{
                    position: relative;
                    display: inline-block;
                    margin: 10px 0;
                    padding: 10px;
                    font-size: 13px;
                }
            </style>';
		} else {
			echo '
                <style>
                    .wotb_link_to_us{font-size:13px;}
                </style>
            ';
		}
		return $link;
	}

	/**
	 * Add link to post or home page footers
	 * @return null
	 */
	public function add_link_to_footer() {
		if (is_home() && intval(get_option('wotb_html_place')) === 1) {
			echo $this->type_of_link(1);
		}if (is_singular('post') && intval(get_option('wotb_html_place')) === 3) {
			echo $this->type_of_link(1);
		}
	}

	/**
	 * Add link to post or home page sidebars
	 * @return null
	 */
	public function add_link_to_sidebar() {
		if (is_home() && intval(get_option('wotb_html_place')) === 2) {
			echo $this->type_of_link();
		}if (is_singular('post') && intval(get_option('wotb_html_place')) === 4) {
			echo $this->type_of_link();
		}
	}

}

new WP_WordOfTravel_Blogger();