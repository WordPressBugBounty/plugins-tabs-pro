<?php
/**
 * Shortcode Handler Class.
 *
 * @package TPTabs_Ultimate
 * @version 2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class TPTabs_Shortcode {

	/**
	 * Shortcode tag name.
	 *
	 * @var string
	 */
	const SHORTCODE_TAG = 'tabsprofree';

	/**
	 * Initialize hooks.
	 */
	public function __construct() {
		add_shortcode( self::SHORTCODE_TAG, array( $this, 'render_shortcode' ) );
	}

	/**
	 * Render the [tabsprofree id="123"] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Rendered HTML output.
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			self::SHORTCODE_TAG
		);

		$post_id = absint( $atts['id'] );

		// Validate post ID and verify it is a published tab post.
		if ( ! $post_id || 'tp_tab_pro' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
			return '';
		}

		// Enqueue frontend assets on-demand.
		$this->enqueue_assets();

		// Render tab content securely using output buffering.
		ob_start();

		// Legacy fallback if Renderer class isn't loaded
		if ( function_exists( 'TP_tab_Free_Shortcode_ultimate_body' ) ) {
			return TP_tab_Free_Shortcode_ultimate_body( $post_id );
		}

		return ob_get_clean();
	}

	/**
	 * Enqueue required scripts and styles for shortcode rendering.
	 *
	 * @return void
	 */
	private function enqueue_assets() {

		wp_enqueue_script( 'tptabsfree-tabs-engine' );
		wp_enqueue_style( 'tptabsfree-font-awesome' );
		wp_enqueue_style( 'tptabsfree-tabs-engine' );

	}
}

new TPTabs_Shortcode();