<?php
/*
Plugin Name: Tptabs Ultimate Shortcodes
Plugin URI: https://themepoints.com/
Description: Tab Shortcode Ultimate is yet another simple, responsive, lightweight jQuery tabs plugin for creating responsive tabbed panels with unlimited options 
Version: 1.5
Author: Themepoints
Author URI: https://themepoints.com/
Text Domain: tptabs-ultimate
Domain Path: /languages
License: GPLv2
*/


if ( ! defined( 'ABSPATH' ) ) exit;

	define('TABS_ULTIMATE_SHORTCODE_PLUGIN_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
	add_filter('widget_text', 'do_shortcode');

	// Tabs Ultimate enqueue scripts
	function tp_ultimate_tabs_active_script(){
		wp_register_style( 'tptabs-free-tptabsultimate-css', plugins_url( '/assets/css/tptabsultimate.css' , __FILE__ ) );
		wp_register_style( 'tptabs-free-fontawesome-css', plugins_url( '/assets/css/font-awesome.css' , __FILE__ ) );
		wp_register_style( 'tptabs-free-animate-css', plugins_url( '/assets/css/animation.css' , __FILE__ ) );
		wp_register_style( 'tptabs-free-jQueryTab-css', plugins_url( '/assets/css/jQueryTab.css' , __FILE__ ) );
		wp_enqueue_script('jquery');
		wp_register_script('tptabs-free-tptabsultimate-js', plugins_url( '/assets/js/tptabsultimate.js', __FILE__ ), array('jquery'), '1.0', false);
		wp_register_script('tptabs-free-jQueryTab-js', plugins_url( '/assets/js/jQueryTab.js', __FILE__ ), array('jquery'), '1.0', false);
	}
	add_action('init', 'tp_ultimate_tabs_active_script');

	// Register enqueue script
	function tp_ultimate_tabs_free_admin_enqueue_scripts(){
		global $typenow;
		if(($typenow == 'tp_tab_pro')){
		wp_enqueue_style( 'tabultimate-free-admin-css', plugins_url( 'admin/css/tabultimate-backend-admin.css' , __FILE__ ) );
		wp_enqueue_script('tabultimate-free-admin-js', TABS_ULTIMATE_SHORTCODE_PLUGIN_PATH.'admin/js/tabultimate-backend-admin.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'tabultimate-free-admin-js', plugins_url('admin/js/tabultimate-backend-admin.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
		wp_enqueue_style('wp-color-picker');	
		wp_enqueue_script( 'tabultimate_color_picker', plugins_url('admin/js/color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
		}
	}	
	add_action('admin_enqueue_scripts', 'tp_ultimate_tabs_free_admin_enqueue_scripts');


	// Includes All FIles
	include( plugin_dir_path( __FILE__ ) . 'inc/tab-shortcode-ultimate-free-post-type.php' );
	include( plugin_dir_path( __FILE__ ) . 'metabox/custom-meta-boxes-free.php' );
	include( plugin_dir_path( __FILE__ ) . 'theme/tab-shortcode-ultimate-themes.php' );


	// Tab Shortcode Ultimate Register Meta Boxes Version 1.1
	function tp_custom_tabultimate_shortcode_filter_meta_box_free( $meta_boxes ) {
	  $meta_boxes[] = array(

		'id'          => 'custom_accordion_wordpress_feature',
		'title'       => 'Tab Free',
		'pages'       => array('tp_tab_pro'),
		'context'     => 'normal',
		'priority'    => 'high',
		'show_names'  => true, 
		'fields' => array(

		  array(
			'id'   => 'custom_accordion_wordpresspro_columns',
			'name'    => 'Tab Free Details',
			'type' => 'group',
			'repeatable'     => true,
			'sortable'       => true,			
			'repeatable_max' => 4,
			
			'fields' => array(

			array(
			'id'              => 'custom_accordions_pro_title',
			'name'            => 'Title',                
			'type'            => 'text',
			'cols'            => 4
			),
				
			array(
				'id' => 'field-14',
				'name' => 'Select Icons',
				'type' => 'select',
				'options' => array(
					'' => 'No Icon',
					'fa-adjust' => 'fa-adjust',
					'fa-anchor' => 'anchor',
					'fa-archive' => 'archive',
					'fa-arrows' => 'arrows',
					'fa-arrows-h' => 'arrows-h',
					'fa-arrows-v' => 'arrows-v',
					'fa-asterisk' => 'asterisk',
					'fa-automobile' => 'automobile',
					'fa-ban' => 'ban',
					'fa-bank' => 'bank',
					'fa-bar-chart-o' => 'bar-chart-o',
					'fa-barcode' => 'barcode',
					'fa-bars' => 'bars',
					'fa-beer' => 'beer',
					'fa-bell' => 'bell',
					'fa-bell-o' => 'bell-o',
					'fa-bolt' => 'bolt',
					'fa-bomb' => 'bomb',
					'fa-book' => 'book',
					'fa-bookmark' => 'bookmark',
					'fa-bookmark-o' => 'bookmark-o',
					'fa-briefcase' => 'briefcase',
					'fa-bug' => 'bug',
					'fa-building' => 'building',
					'fa-building-o' => 'building-o',
					'fa-bullhorn' => 'bullhorn',
					'fa-bullseye' => 'bullseye',
					'fa-cab' => 'cab',
					'fa-calendar' => 'calendar',
					'fa-calendar-o' => 'calendar-o',
					'fa-camera' => 'camera',
					'fa-camera-retro' => 'camera-retro',
					'fa-car' => 'car',
					'fa-caret-square-o-down' => 'caret-square-o-down',
					'fa-caret-square-o-left' => 'caret-square-o-left',
					'fa-caret-square-o-right' => 'caret-square-o-right',
					'fa-caret-square-o-up' => 'caret-square-o-up',
					'fa-certificate' => 'certificate',
					'fa-check' => 'check',
					'fa-check-circle' => 'check-circle',
					'fa-check-circle-o' => 'check-circle-o',
					'fa-check-square' => 'check-square',
					'fa-check-square-o' => 'check-square-o',
					'fa-child' => 'child',
					'fa-circle' => 'circle',
					'fa-circle-o' => 'circle-o',
					'fa-circle-o-notch' => 'circle-o-notch',
					'fa-circle-thin' => 'circle-thin',
					'fa-clock-o' => 'clock-o',
					'fa-cloud' => 'cloud',
					'fa-cloud-download' => 'cloud-download',
					'fa-cloud-upload' => 'cloud-upload',
					'fa-code' => 'code',
					'fa-code-fork' => 'code-fork',
					'fa-coffee' => 'coffee',
					'fa-cog' => 'cog',
					'fa-cogs' => 'cogs',
					'fa-comment' => 'comment',
					'fa-comment-o' => 'comment-o',
					'fa-comments' => 'comments',
					'fa-comments-o' => 'comments-o',
					'fa-compass' => 'compass',
					'fa-credit-card' => 'credit-card',
					'fa-crop' => 'crop',
					'fa-crosshairs' => 'crosshairs',
					'fa-cube' => 'cube',
					'fa-cubes' => 'cubes',
					'fa-cutlery' => 'cutlery',
					'fa-dashboard' => 'dashboard',
					'fa-database' => 'database',
					'fa-desktop' => 'desktop',
					'fa-dot-circle-o' => 'dot-circle-o',
					'fa-download' => 'download',
					'fa-edit' => 'edit',
					'fa-ellipsis-h' => 'ellipsis-h',
					'fa-ellipsis-v' => 'ellipsis-v',
					'fa-envelope' => 'envelope',
					'fa-envelope-o' => 'envelope-o',
					'fa-envelope-square' => 'envelope-square',
					'fa-eraser' => 'eraser',
					'fa-exchange' => 'exchange',
					'fa-exclamation' => 'exclamation',
					'fa-exclamation-circle' => 'exclamation-circle',
					'fa-exclamation-triangle' => 'exclamation-triangle',
					'fa-external-link' => 'external-link',
					'fa-external-link-square' => 'external-link-square',
					'fa-eye' => 'eye',
					'fa-eye-slash' => 'eye-slash',
					'fa-fax' => 'fax',
					'fa-female' => 'female',
					'fa-fighter-jet' => 'fighter-jet',
					'fa-file-archive-o' => 'file-archive-o',
					'fa-file-audio-o' => 'file-audio-o',
					'fa-file-code-o' => 'file-code-o',
					'fa-file-excel-o' => 'file-excel-o',
					'fa-file-image-o' => 'file-image-o',
					'fa-file-movie-o' => 'file-movie-o',
					'fa-file-pdf-o' => 'file-pdf-o',
					'fa-file-photo-o' => 'file-photo-o',
					'fa-file-picture-o' => 'file-picture-o',
					'fa-file-powerpoint-o' => 'file-powerpoint-o',
					'fa-file-sound-o' => 'file-sound-o',
					'fa-file-video-o' => 'file-video-o',
					'fa-file-word-o' => 'file-word-o',
					'fa-file-zip-o' => 'file-zip-o',
					'fa-film' => 'film',
					'fa-filter' => 'filter',
					'fa-fire' => 'fire',
					'fa-fire-extinguisher' => 'fire-extinguisher',
					'fa-flag' => 'flag',
					'fa-flag-checkered' => 'flag-checkered',
					'fa-flag-o' => 'flag-o',
					'fa-flash' => 'flash',
					'fa-flask' => 'flask',
					'fa-folder' => 'folder',
					'fa-folder-o' => 'folder-o',
					'fa-folder-open' => 'folder-open',
					'fa-folder-open-o' => 'folder-open-o',
					'fa-frown-o' => 'frown-o',
					'fa-gamepad' => 'gamepad',
					'fa-gavel' => 'gavel',
					'fa-gear' => 'gear',
					'fa-gears' => 'gears',
					'fa-gift' => 'gift',
					'fa-glass' => 'glass',
					'fa-globe' => 'globe',
					'fa-graduation-cap' => 'graduation-cap',
					'fa-group' => 'group',
					'fa-hdd-o' => 'hdd-o',
					'fa-headphones' => 'headphones',
					'fa-heart' => 'heart',
					'fa-heart-o' => 'heart-o',
					'fa-history' => 'history',
					'fa-home' => 'home',
					'fa-image' => 'image',
					'fa-inbox' => 'inbox',
					'fa-info' => 'info',
					'fa-info-circle' => 'info-circle',
					'fa-institution' => 'institution',
					'fa-key' => 'key',
					'fa-keyboard-o' => 'keyboard-o',
					'fa-language' => 'language',
					'fa-laptop' => 'laptop',
					'fa-leaf' => 'leaf',
					'fa-legal' => 'legal',
					'fa-lemon-o' => 'lemon-o',
					'fa-level-down' => 'level-down',
					'fa-level-up' => 'level-up',
					'fa-life-bouy' => 'life-bouy',
					'fa-life-ring' => 'life-ring',
					'fa-life-saver' => 'life-saver',
					'fa-lightbulb-o' => 'lightbulb-o',
					'fa-location-arrow' => 'location-arrow',
					'fa-lock' => 'lock',
					'fa-magic' => 'magic',
					'fa-magnet' => 'magnet',
					'fa-mail-forward' => 'mail-forward',
					'fa-mail-reply' => 'mail-reply',
					'fa-mail-reply-all' => 'mail-reply-all',
					'fa-male' => 'male',
					'fa-map-marker' => 'map-marker',
					'fa-meh-o' => 'meh-o',
					'fa-microphone' => 'microphone',
					'fa-microphone-slash' => 'microphone-slash',
					'fa-minus' => 'minus',
					'fa-minus-circle' => 'minus-circle',
					'fa-minus-square' => 'minus-square',
					'fa-minus-square-o' => 'minus-square-o',
					'fa-mobile' => 'mobile',
					'fa-mobile-phone' => 'mobile-phone',
					'fa-money' => 'money',
					'fa-moon-o' => 'moon-o',
					'fa-mortar-board' => 'mortar-board',
					'fa-music' => 'music',
					'fa-navicon' => 'navicon',
					'fa-paper-plane' => 'paper-plane',
					'fa-paper-plane-o' => 'paper-plane-o',
					'fa-paw' => 'paw',
					'fa-pencil' => 'pencil',
					'fa-pencil-square' => 'pencil-square',
					'fa-pencil-square-o' => 'pencil-square-o',
					'fa-phone' => 'phone',
					'fa-phone-square' => 'phone-square',
					'fa-photo' => 'photo',
					'fa-picture-o' => 'picture-o',
					'fa-plane' => 'plane',
					'fa-plus' => 'plus',
					'fa-plus-circle' => 'plus-circle',
					'fa-plus-square' => 'plus-square',
					'fa-plus-square-o' => 'plus-square-o',
					'fa-power-off' => 'power-off',
					'fa-print' => 'print',
					'fa-puzzle-piece' => 'puzzle-piece',
					'fa-qrcode' => 'qrcode',
					'fa-question' => 'question',
					'fa-question-circle' => 'question-circle',
					'fa-quote-left' => 'quote-left',
					'fa-quote-right' => 'quote-right',
					'fa-random' => 'random',
					'fa-recycle' => 'recycle',
					'fa-refresh' => 'refresh',
					'fa-reorder' => 'reorder',
					'fa-reply' => 'reply',
					'fa-reply-all' => 'reply-all',
					'fa-retweet' => 'retweet',
					'fa-road' => 'road',
					'fa-rocket' => 'rocket',
					'fa-rss' => 'rss',
					'fa-rss-square' => 'rss-square',
					'fa-search' => 'search',
					'fa-search-minus' => 'search-minus',
					'fa-search-plus' => 'search-plus',
					'fa-send' => 'send',
					'fa-send-o' => 'send-o',
					'fa-share' => 'share',
					'fa-share-alt' => 'share-alt',
					'fa-share-alt-square' => 'share-alt-square',
					'fa-share-square' => 'share-square',
					'fa-share-square-o' => 'share-square-o',
					'fa-shield' => 'shield',
					'fa-shopping-cart' => 'shopping-cart',
					'fa-sign-in' => 'sign-in',
					'fa-sign-out' => 'sign-out',
					'fa-signal' => 'signal',
					'fa-sitemap' => 'sitemap',
					'fa-sliders' => 'sliders',
					'fa-smile-o' => 'smile-o',
					'fa-sort' => 'sort',
					'fa-sort-alpha-asc' => 'sort-alpha-asc',
					'fa-sort-alpha-desc' => 'sort-alpha-desc',
					'fa-sort-amount-asc' => 'sort-amount-asc',
					'fa-sort-amount-desc' => 'sort-amount-desc',
					'fa-sort-asc' => 'sort-asc',
					'fa-sort-desc' => 'sort-desc',
					'fa-sort-down' => 'sort-down',
					'fa-sort-numeric-asc' => 'sort-numeric-asc',
					'fa-sort-numeric-desc' => 'sort-numeric-desc',
					'fa-sort-up' => 'sort-up',
					'fa-space-shuttle' => 'space-shuttle',
					'fa-spinner' => 'spinner',
					'fa-spoon' => 'spoon',
					'fa-square' => 'square',
					'fa-square-o' => 'square-o',
					'fa-star' => 'star',
					'fa-star-half' => 'star-half',
					'fa-star-half-empty' => 'star-half-empty',
					'fa-star-half-full' => 'star-half-full',
					'fa-star-half-o' => 'star-half-o',
					'fa-star-o' => 'star-o',
					'fa-suitcase' => 'suitcase',
					'fa-sun-o' => 'sun-o',
					'fa-support' => 'support',
					'fa-tablet' => 'tablet',
					'fa-tachometer' => 'tachometer',
					'fa-tag' => 'tag',
					'fa-tags' => 'tags',
					'fa-tasks' => 'tasks',
					'fa-taxi' => 'taxi',
					'fa-terminal' => 'terminal',
					'fa-thumb-tack' => 'thumb-tack',
					'fa-thumbs-down' => 'thumbs-down',
					'fa-thumbs-o-down' => 'thumbs-o-down',
					'fa-thumbs-o-up' => 'thumbs-o-up',
					'fa-thumbs-up' => 'thumbs-up',
					'fa-ticket' => 'ticket',
					'fa-times' => 'times',
					'fa-times-circle' => 'times-circle',
					'fa-times-circle-o' => 'times-circle-o',
					'fa-tint' => 'tint',
					'fa-toggle-down' => 'toggle-down',
					'fa-toggle-left' => 'toggle-left',
					'fa-toggle-right' => 'toggle-right',
					'fa-toggle-up' => 'toggle-up',
					'fa-trash-o' => 'trash-o',
					'fa-tree' => 'tree',
					'fa-trophy' => 'trophy',
					'fa-truck' => 'truck',
					'fa-umbrella' => 'umbrella',
					'fa-university' => 'university',
					'fa-unlock' => 'unlock',
					'fa-unlock-alt' => 'unlock-alt',
					'fa-unsorted' => 'unsorted',
					'fa-upload' => 'upload',
					'fa-user' => 'user',
					'fa-users' => 'users',
					'fa-video-camera' => 'video-camera',
					'fa-volume-down' => 'volume-down',
					'fa-volume-off' => 'volume-off',
					'fa-volume-up' => 'volume-up',
					'fa-warning' => 'warning',
					'fa-wheelchair' => 'wheelchair',
					'fa-wrench' => 'wrench',
					'fa-file' => 'file',
					'fa-file-archive-o' => 'file-archive-o',
					'fa-file-audio-o' => 'file-audio-o',
					'fa-file-code-o' => 'file-code-o',
					'fa-file-excel-o' => 'file-excel-o',
					'fa-file-image-o' => 'file-image-o',
					'fa-file-movie-o' => 'file-movie-o',
					'fa-file-o' => 'file-o',
					'fa-file-pdf-o' => 'file-pdf-o',
					'fa-file-photo-o' => 'file-photo-o',
					'fa-file-picture-o' => 'file-picture-o',
					'fa-file-powerpoint-o' => 'file-powerpoint-o',
					'fa-file-sound-o' => 'file-sound-o',
					'fa-file-text' => 'file-text',
					'fa-file-text-o' => 'file-text-o',
					'fa-file-video-o' => 'file-video-o',
					'fa-file-word-o' => 'file-word-o',
					'fa-file-zip-o' => 'file-zip-o',
					'fa-circle-o-notch' => 'circle-o-notch',
					'fa-cog' => 'cog',
					'fa-gear' => 'gear',
					'fa-refresh' => 'refresh',
					'fa-spinner' => 'spinner',
					'fa-check-square' => 'check-square',
					'fa-check-square-o' => 'check-square-o',
					'fa-circle' => 'circle',
					'fa-circle-o' => 'circle-o',
					'fa-dot-circle-o' => 'dot-circle-o',
					'fa-minus-square' => 'minus-square',
					'fa-minus-square-o' => 'minus-square-o',
					'fa-plus-square' => 'plus-square',
					'fa-plus-square-o' => 'plus-square-o',
					'fa-square' => 'square',
					'fa-square-o' => 'square-o',
					'fa-bitcoin' => 'bitcoin',
					'fa-btc' => 'btc',
					'fa-cny' => 'cny',
					'fa-dollar' => 'dollar',
					'fa-eur' => 'eur',
					'fa-euro' => 'euro',
					'fa-gbp' => 'gbp',
					'fa-inr' => 'inr',
					'fa-jpy' => 'jpy',
					'fa-krw' => 'krw',
					'fa-money' => 'money',
					'fa-rmb' => 'rmb',
					'fa-rouble' => 'rouble',
					'fa-rub' => 'rub',
					'fa-ruble' => 'ruble',
					'fa-rupee' => 'rupee',
					'fa-try' => 'try',
					'fa-turkish-lira' => 'turkish-lira',
					'fa-usd' => 'usd',
					'fa-won' => 'won',
					'fa-yen' => 'yen',
					'fa-align-center' => 'align-center',
					'fa-align-justify' => 'align-justify',
					'fa-align-left' => 'align-left',
					'fa-align-right' => 'align-right',
					'fa-bold' => 'bold',
					'fa-chain' => 'chain',
					'fa-chain-broken' => 'chain-broken',
					'fa-clipboard' => 'clipboard',
					'fa-columns' => 'columns',
					'fa-copy' => 'copy',
					'fa-cut' => 'cut',
					'fa-dedent' => 'dedent',
					'fa-eraser' => 'eraser',
					'fa-file' => 'file',
					'fa-file-o' => 'file-o',
					'fa-file-text' => 'file-text',
					'fa-file-text-o' => 'file-text-o',
					'fa-files-o' => 'files-o',
					'fa-floppy-o' => 'floppy-o',
					'fa-font' => 'font',
					'fa-header' => 'header',
					'fa-indent' => 'indent',
					'fa-italic' => 'italic',
					'fa-link' => 'link',
					'fa-list' => 'list',
					'fa-list-alt' => 'list-alt',
					'fa-list-ol' => 'list-ol',
					'fa-list-ul' => 'list-ul',
					'fa-outdent' => 'outdent',
					'fa-paperclip' => 'paperclip',
					'fa-paragraph' => 'paragraph',
					'fa-paste' => 'paste',
					'fa-repeat' => 'repeat',
					'fa-rotate-left' => 'rotate-left',
					'fa-rotate-right' => 'rotate-right',
					'fa-save' => 'save',
					'fa-scissors' => 'scissors',
					'fa-strikethrough' => 'strikethrough',
					'fa-subscript' => 'subscript',
					'fa-superscript' => 'superscript',
					'fa-table' => 'table',
					'fa-text-height' => 'text-height',
					'fa-text-width' => 'text-width',
					'fa-th' => 'th',
					'fa-th-large' => 'th-large',
					'fa-th-list' => 'th-list',
					'fa-underline' => 'underline',
					'fa-undo' => 'undo',
					'fa-unlink' => 'unlink',
					'fa-angle-double-down' => 'angle-double-down',
					'fa-angle-double-left' => 'angle-double-left',
					'fa-angle-double-right' => 'angle-double-right',
					'fa-angle-double-up' => 'angle-double-up',
					'fa-angle-down' => 'angle-down',
					'fa-angle-left' => 'angle-left',
					'fa-angle-right' => 'angle-right',
					'fa-angle-up' => 'angle-up',
					'fa-arrow-circle-down' => 'arrow-circle-down',
					'fa-arrow-circle-left' => 'arrow-circle-left',
					'fa-arrow-circle-o-down' => 'arrow-circle-o-down',
					'fa-arrow-circle-o-left' => 'arrow-circle-o-left',
					'fa-arrow-circle-o-right' => 'arrow-circle-o-right',
					'fa-arrow-circle-o-up' => 'arrow-circle-o-up',
					'fa-arrow-circle-right' => 'arrow-circle-right',
					'fa-arrow-circle-up' => 'arrow-circle-up',
					'fa-arrow-down' => 'arrow-down',
					'fa-arrow-left' => 'arrow-left',
					'fa-arrow-right' => 'arrow-right',
					'fa-arrow-up' => 'arrow-up',
					'fa-arrows' => 'arrows',
					'fa-arrows-alt' => 'arrows-alt',
					'fa-arrows-h' => 'arrows-h',
					'fa-arrows-v' => 'arrows-v',
					'fa-caret-down' => 'caret-down',
					'fa-caret-left' => 'caret-left',
					'fa-caret-right' => 'caret-right',
					'fa-caret-square-o-down' => 'caret-square-o-down',
					'fa-caret-square-o-left' => 'caret-square-o-left',
					'fa-caret-square-o-right' => 'caret-square-o-right',
					'fa-caret-square-o-up' => 'caret-square-o-up',
					'fa-caret-up' => 'caret-up',
					'fa-chevron-circle-down' => 'chevron-circle-down',
					'fa-chevron-circle-left' => 'chevron-circle-left',
					'fa-chevron-circle-right' => 'chevron-circle-right',
					'fa-chevron-circle-up' => 'chevron-circle-up',
					'fa-chevron-down' => 'chevron-down',
					'fa-chevron-left' => 'chevron-left',
					'fa-chevron-right' => 'chevron-right',
					'fa-chevron-up' => 'chevron-up',
					'fa-hand-o-down' => 'hand-o-down',
					'fa-hand-o-left' => 'hand-o-left',
					'fa-hand-o-right' => 'hand-o-right',
					'fa-hand-o-up' => 'hand-o-up',
					'fa-long-arrow-down' => 'long-arrow-down',
					'fa-long-arrow-left' => 'long-arrow-left',
					'fa-long-arrow-right' => 'long-arrow-right',
					'fa-long-arrow-up' => 'long-arrow-up',
					'fa-toggle-down' => 'toggle-down',
					'fa-toggle-left' => 'toggle-left',
					'fa-toggle-right' => 'toggle-right',
					'fa-toggle-up' => 'toggle-up',
					'fa-arrows-alt' => 'arrows-alt',
					'fa-backward' => 'backward',
					'fa-compress' => 'compress',
					'fa-eject' => 'eject',
					'fa-expand' => 'expand',
					'fa-fast-backward' => 'fast-backward',
					'fa-fast-forward' => 'fast-forward',
					'fa-forward' => 'forward',
					'fa-pause' => 'pause',
					'fa-play' => 'play',
					'fa-play-circle' => 'play-circle',
					'fa-play-circle-o' => 'play-circle-o',
					'fa-step-backward' => 'step-backward',
					'fa-step-forward' => 'step-forward',
					'fa-stop' => 'stop',
					'fa-youtube-play' => 'youtube-play',
					'fa-adn' => 'adn',
					'fa-android' => 'android',
					'fa-apple' => 'apple',
					'fa-behance' => 'behance',
					'fa-behance-square' => 'behance-square',
					'fa-bitbucket' => 'bitbucket',
					'fa-bitbucket-square' => 'bitbucket-square',
					'fa-bitcoin' => 'bitcoin',
					'fa-btc' => 'btc',
					'fa-codepen' => 'codepen',
					'fa-css3' => 'css3',
					'fa-delicious' => 'delicious',
					'fa-deviantart' => 'deviantart',
					'fa-digg' => 'digg',
					'fa-dribbble' => 'dribbble',
					'fa-dropbox' => 'dropbox',
					'fa-drupal' => 'drupal',
					'fa-empire' => 'empire',
					'fa-facebook' => 'facebook',
					'fa-facebook-square' => 'facebook-square',
					'fa-flickr' => 'flickr',
					'fa-foursquare' => 'foursquare',
					'fa-ge' => 'ge',
					'fa-git' => 'git',
					'fa-git-square' => 'git-square',
					'fa-github' => 'github',
					'fa-github-alt' => 'github-alt',
					'fa-github-square' => 'github-square',
					'fa-gittip' => 'gittip',
					'fa-google' => 'google',
					'fa-google-plus' => 'google-plus',
					'fa-google-plus-square' => 'google-plus-square',
					'fa-hacker-news' => 'hacker-news',
					'fa-html5' => 'html5',
					'fa-instagram' => 'instagram',
					'fa-joomla' => 'joomla',
					'fa-jsfiddle' => 'jsfiddle',
					'fa-linkedin' => 'linkedin',
					'fa-linkedin-square' => 'linkedin-square',
					'fa-linux' => 'linux',
					'fa-maxcdn' => 'maxcdn',
					'fa-openid' => 'openid',
					'fa-pagelines' => 'pagelines',
					'fa-pied-piper' => 'pied-piper',
					'fa-pied-piper-alt' => 'pied-piper-alt',
					'fa-pied-piper-square' => 'pied-piper-square',
					'fa-pinterest' => 'pinterest',
					'fa-pinterest-square' => 'pinterest-square',
					'fa-qq' => 'qq',
					'fa-ra' => 'ra',
					'fa-rebel' => 'rebel',
					'fa-reddit' => 'reddit',
					'fa-reddit-square' => 'reddit-square',
					'fa-renren' => 'renren',
					'fa-share-alt' => 'share-alt',
					'fa-share-alt-square' => 'share-alt-square',
					'fa-skype' => 'skype',
					'fa-slack' => 'slack',
					'fa-soundcloud' => 'soundcloud',
					'fa-spotify' => 'spotify',
					'fa-stack-exchange' => 'stack-exchange',
					'fa-stack-overflow' => 'stack-overflow',
					'fa-steam' => 'steam',
					'fa-steam-square' => 'steam-square',
					'fa-stumbleupon' => 'stumbleupon',
					'fa-stumbleupon-circle' => 'stumbleupon-circle',
					'fa-tencent-weibo' => 'tencent-weibo',
					'fa-trello' => 'trello',
					'fa-tumblr' => 'tumblr',
					'fa-tumblr-square' => 'tumblr-square',
					'fa-twitter' => 'twitter',
					'fa-twitter-square' => 'twitter-square',
					'fa-vimeo-square' => 'vimeo-square',
					'fa-vine' => 'vine',
					'fa-vk' => 'vk',
					'fa-wechat' => 'wechat',
					'fa-weibo' => 'weibo',
					'fa-weixin' => 'weixin',
					'fa-windows' => 'windows',
					'fa-wordpress' => 'wordpress',
					'fa-xing' => 'xing',
					'fa-xing-square' => 'xing-square',
					'fa-yahoo' => 'yahoo',
					'fa-youtube' => 'youtube',
					'fa-youtube-play' => 'youtube-play',
					'fa-youtube-square' => 'youtube-square',
					'fa-ambulance' => 'ambulance',
					'fa-h-square' => 'h-square',
					'fa-hospital-o' => 'hospital-o',
					'fa-medkit' => 'medkit',
					'fa-plus-square' => 'plus-square',
					'fa-stethoscope' => 'stethoscope',
					'fa-user-md' => 'user-md',
					'fa-wheelchair' => 'wheelchair',

					),
					'multiple' => false,
					'cols' => 4 ),

					array(
						'id'              => 'custom_accordions_pro_details',
						'name'            => 'Description',                
						'type'            => 'wysiwyg',
						'sanitization_cb' => false,
						'options' => array( 'textarea_rows' => 8, ),
						'default'         => 'Insert Your Description Here?',
					),
			  )
		  )
	  )
	);

	return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'tp_custom_tabultimate_shortcode_filter_meta_box_free' );


/*
=================================
 Tp Tabs Ultimate Content Prefix
=================================
*/

function tptabsfix_p($content) {
	$array = array(
		'<p>[' => '[',
		']</p>' => ']',
		']<br />' => ']'
	);

	$content = strtr($content, $array);

	return $content;
}


/*
=====================================
 Tabs Ultimate Shortcode Register
=====================================
*/

function tptabs_ultimate_shortcodes( $atts, $content = null ) {
	$atts = ( shortcode_atts( array(
		'width' => '',
		'initialtab' => 1,
		'autoplayinterval' => 0,
		'color' => 'dark'
	), $atts ) );

	$colors_available = array('dark', 'blue');

	if(is_numeric($atts['width'])){
		$width = 'width:' . esc_attr( $atts['width'] ) . 'px';
	}else{
		$width = '';
	}
	if(!in_array($atts['color'], $colors_available)) $atts['color'] = 'dark';

	$tptabsclass = esc_attr( $atts['color'] ) . ' initialTab-' . ( esc_attr( $atts['initialtab']-1 ) )  . ' autoplayInterval-' . esc_attr( $atts['autoplayinterval'] );

	$output = '<div class="tp_tabs tp_menu '. esc_attr( $tptabsclass ) .'" style="'. esc_attr( $width ) .'">';
	$output .= do_shortcode(tptabsfix_p($content));
	$output .= '</div>';

	return $output;
}
add_shortcode('tptabs_ultimate', 'tptabs_ultimate_shortcodes');


function tptabs_tab_container( $atts, $content = null ) {
	$output  = '<ul class="tptabs_content_container_main">';
	$output .= (do_shortcode($content));
	$output .= '</ul>';

	return $output;
}
add_shortcode('tptabs_tab_container', 'tptabs_tab_container');


function tptabs_tab( $atts, $content = null ) {
	$output  = '<li><a>';
	$output .= (do_shortcode($content));
	$output .= '</a></li>';

	return $output;
}
add_shortcode('tptabs_tab', 'tptabs_tab');


function tptabs_content_container( $atts, $content = null ) {
	$output  = '<div class="tptabs_content_container">';
	$output .= '<div class="tptabs_content_container_inner">';
	$output .= (do_shortcode($content));
	$output .= '</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode('tptabs_content_container', 'tptabs_content_container');


function tptabs_content( $atts, $content = null ) {
	$output  = '<div class="tptabs_content_main">';
	$output .= (do_shortcode($content));
	$output .= '</div>';

	return $output;
}
add_shortcode('tptabs_content', 'tptabs_content');


/*
================================================
 Register Shortcode Button on Post Visual Editor
================================================
*/

function tptabs_ultimate_button_function() {
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
   if ( get_user_option('rich_editing') == 'true') {
	add_filter ("mce_external_plugins", "tptabs_ultimate_button_js");
	add_filter ("mce_buttons", "tptabs_ultimate_button");
   }
}


function tptabs_ultimate_button_js($plugin_array) {
	$plugin_array['TptabsUltimate'] = plugins_url('inc/editor_plugin_button.js', __FILE__);
	return $plugin_array;
}


function tptabs_ultimate_button($buttons) {
	array_push ($buttons, 'tptabs_ultimate');
	return $buttons;
}
add_action ('init', 'tptabs_ultimate_button_function'); 


// Added version 1.1
function free_tp_custom_tabultimate_buy_action_links( $links ) {
	$links[] = '<a href="https://themepoints.com/product/tabs-ultimate-shortcodes/" style="color: red; font-weight: bold;" target="_blank">Buy Pro!</a>';
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'free_tp_custom_tabultimate_buy_action_links' );


// Added Version 1.1
function free_tp_custom_tabultimate_shortcode_register($atts, $content = null){
	wp_enqueue_script( 'tptabs-free-tptabsultimate-js' );
	wp_enqueue_script( 'tptabs-free-jQueryTab-js' );
    wp_enqueue_style( 'tptabs-free-tptabsultimate-css' );
    wp_enqueue_style( 'tptabs-free-fontawesome-css' );
    wp_enqueue_style( 'tptabs-free-animate-css' );
    wp_enqueue_style( 'tptabs-free-jQueryTab-css' );
	$atts = shortcode_atts(
		array(
			'id' => '',
		), $atts);
		global $post;
		$post_id = $atts['id'];
		
		$content = '';
		$content.= TP_tab_Free_Shortcode_ultimate_body($post_id);
		return $content;
}

// shortcode hook
add_shortcode('tabsprofree', 'free_tp_custom_tabultimate_shortcode_register');

?>