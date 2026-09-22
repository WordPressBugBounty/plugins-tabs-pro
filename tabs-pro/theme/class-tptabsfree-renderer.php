<?php
/**
 * Frontend Renderer Engine Class for TPTabs Ultimate.
 * Full backward compatibility with legacy CMB/CMB2 group data structures & modern schemas.
 *
 * @package TPTabs_Ultimate
 * @version 2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class TPTabs_Renderer {

	/**
	 * Post ID for rendering.
	 *
	 * @var int
	 */
	private $post_id = 0;

	/**
	 * Whether rendering in live preview mode.
	 *
	 * @var bool
	 */
	private $is_preview = false;

	/**
	 * Unsaved preview data array sent from AJAX.
	 *
	 * @var array
	 */
	private $preview_data = array();

	/**
	 * Constructor.
	 *
	 * @param int $post_id Post ID to render.
	 */
	public function __construct( $post_id = 0 ) {
		$this->post_id = absint( $post_id );
	}

	/**
	 * Inject live unsaved form data for instant preview rendering.
	 *
	 * @param array $data Serialized form values from AJAX.
	 * @return void
	 */
	public function set_preview_data( array $data ) {
		$this->preview_data = $data;
		$this->is_preview   = true;
	}

	/**
	 * Safe string extraction helper to prevent "Array to string conversion" warnings.
	 *
	 * @param mixed $value Field value from database.
	 * @return string
	 */
	private function safe_string( $value ) {
		if ( is_array( $value ) ) {
			$value = reset( $value );
		}
		
		if ( is_scalar( $value ) ) {
			return trim( (string) $value );
		}

		return '';
	}

	/**
	 * Retrieve meta value checking preview data, primary key, and fallback meta keys.
	 *
	 * @param string $key           Primary meta key.
	 * @param mixed  $default       Default fallback value.
	 * @param array  $fallback_keys Alternative meta keys across versions.
	 * @return mixed
	 */
	private function get_meta_value( $key, $default = '', $fallback_keys = array() ) {
		// 1. Check Live Preview Data (AJAX)
		if ( $this->is_preview ) {
			if ( isset( $this->preview_data[ $key ] ) && '' !== $this->preview_data[ $key ] ) {
				return $this->preview_data[ $key ];
			}
			foreach ( $fallback_keys as $alt_key ) {
				if ( isset( $this->preview_data[ $alt_key ] ) && '' !== $this->preview_data[ $alt_key ] ) {
					return $this->preview_data[ $alt_key ];
				}
			}
		}

		// 2. Check Saved Post Meta
		if ( $this->post_id ) {
			$meta = get_post_meta( $this->post_id, $key, true );
			if ( '' !== $meta && false !== $meta && null !== $meta ) {
				return $meta;
			}

			foreach ( $fallback_keys as $alt_key ) {
				$alt_meta = get_post_meta( $this->post_id, $alt_key, true );
				if ( '' !== $alt_meta && false !== $alt_meta && null !== $alt_meta ) {
					return $alt_meta;
				}
			}
		}

		return $default;
	}

	/**
	 * Retrieve and normalize tab rows for SINGLE associative, MULTI-ROW legacy, and MODERN array formats.
	 *
	 * @return array Normalized items with title, description, and icon.
	 */
	private function get_tab_items() {
		$raw_data = array();

		// Priority list of meta keys used across versions
		$meta_keys = array(
			'custom_accordion_wordpresspro_columns',
			'tp_custom_tabultimate_shortcode_repeater_data',
			'tptabs_free_tab_data',
			'tp_tab_pro_data',
		);

		if ( $this->is_preview ) {
			foreach ( $meta_keys as $key ) {
				if ( ! empty( $this->preview_data[ $key ] ) ) {
					$raw_data = $this->preview_data[ $key ];
					break;
				}
			}
		} elseif ( $this->post_id ) {
			foreach ( $meta_keys as $key ) {
				$single_data = get_post_meta( $this->post_id, $key, true );
				$multi_data  = get_post_meta( $this->post_id, $key, false );

				if ( ! empty( $multi_data ) && is_array( $multi_data ) && count( $multi_data ) > 1 ) {
					$raw_data = $multi_data;
					break;
				} elseif ( ! empty( $single_data ) ) {
					$raw_data = $single_data;
					break;
				}
			}
		}

		if ( is_string( $raw_data ) ) {
			$raw_data = maybe_unserialize( $raw_data );
		}

		if ( empty( $raw_data ) || ! is_array( $raw_data ) ) {
			return array();
		}

		$items = array();

		// Recursive closure to extract tab items across all nesting levels
		$parse_rows = function( $data_structure ) use ( &$items, &$parse_rows ) {
			if ( ! is_array( $data_structure ) ) {
				return;
			}

			$single_extracted = $this->extract_tab_data( $data_structure );

			if ( ! empty( $single_extracted ) && ! isset( $data_structure[0] ) ) {
				$items[] = $single_extracted;
				return;
			}

			foreach ( $data_structure as $row ) {
				if ( is_string( $row ) ) {
					$row = maybe_unserialize( $row );
				}

				if ( is_array( $row ) ) {
					$extracted = $this->extract_tab_data( $row );
					if ( ! empty( $extracted ) ) {
						$items[] = $extracted;
					} else {
						$parse_rows( $row );
					}
				}
			}
		};

		$parse_rows( $raw_data );

		return array_filter( $items );
	}

	/**
	 * Extract field values matching legacy CMB keys and new key formats safely.
	 *
	 * @param array $item Raw item array.
	 * @return array Standardized array.
	 */
	private function extract_tab_data( $item ) {
		$title       = '';
		$description = '';
		$icon        = '';

		// 1. EXTRACT TITLE
		$title_keys = array( 'custom_accordions_pro_title', 'title', 'tab_title', 'tptabs_title', 'header_title' );
		foreach ( $title_keys as $key ) {
			if ( isset( $item[ $key ] ) ) {
				$val = $this->safe_string( $item[ $key ] );
				if ( '' !== $val ) {
					$title = $val;
					break;
				}
			}
		}
		if ( empty( $title ) && isset( $item[0] ) ) {
			$title = $this->safe_string( $item[0] );
		}

		// 2. EXTRACT DESCRIPTION
		$desc_keys = array( 'custom_accordions_pro_details', 'description', 'tab_content', 'tptabs_details', 'content', 'header_details' );
		foreach ( $desc_keys as $key ) {
			if ( isset( $item[ $key ] ) ) {
				$val = $this->safe_string( $item[ $key ] );
				if ( '' !== $val ) {
					$description = $val;
					break;
				}
			}
		}
		if ( empty( $description ) && isset( $item[1] ) ) {
			$description = $this->safe_string( $item[1] );
		}

		// 3. EXTRACT ICON
		$icon_keys = array( 'field-14', 'icon', 'tab_icon', 'tptabs_icon' );
		foreach ( $icon_keys as $key ) {
			if ( isset( $item[ $key ] ) ) {
				$val = $this->safe_string( $item[ $key ] );
				if ( '' !== $val ) {
					$icon = $val;
					break;
				}
			}
		}
		if ( empty( $icon ) && isset( $item[2] ) ) {
			$icon = $this->safe_string( $item[2] );
		}

		if ( empty( $title ) && empty( $description ) ) {
			return array();
		}

		return array(
			'title'       => $title,
			'description' => $description,
			'icon'        => $icon,
		);
	}

	/**
	 * Main Render Engine Execution.
	 *
	 * @return string Rendered tab HTML output.
	 */
	public function render() {
		if ( ! $this->post_id && ! $this->is_preview ) {
			return '';
		}

		$items = $this->get_tab_items();

		if ( empty( $items ) ) {
			return '';
		}

		// Styles & Settings Fallbacks
		$direction              = $this->get_meta_value( 'tp_custom_tabultimate_shortcode_tabs_themes', 'horizontal', array( 'custom_accordion_columns_post_themes' ) );
		$activated              = $this->get_meta_value( 'tp_custom_tabultimate_shortcode_tabs_activated', '1', array( 'custom_accordion_auto_open' ) );
		$menu_position          = $this->get_meta_value( 'tp_custom_tabultimate_shortcode_tabs_positions', 'top' ); // top or bottom
		$tab_style              = $this->get_meta_value( 'tp_custom_tabultimate_shortcode_tabs_style', 'style-1' );
		$tabs_alignment         = $this->get_meta_value( 'tp_custom_tabultimate_shortcode_tabs_alignment', 'left' );
		$open_event             = $this->get_meta_value( '_tp_custom_tabultimate_shortcode_tabs_openhover', 'click' );
		// $direction           = $this->get_meta_value( '_tp_custom_tabultimate_shortcode_tabs_direction', 'horizontal' );
		$auto_height            = $this->get_meta_value( '_tp_custom_tabultimate_shortcode_tabs_autoheight', 'on' ) === 'off' ? 'false' : 'true';
		$anim_speed             = $this->get_meta_value( '_tp_custom_tabultimate_shortcode_tabs_animspeed', '300' );
		$in_transition          = $this->get_meta_value( 'custom_tabultimate_shortcode_in_transition', 'fadeIn' );
		$use_hash               = $this->get_meta_value( 'tp_custom_tabultimate_shortcode_use_hash', 'on' ) === 'off' ? 'false' : 'true';
		
		// Custom Meta Settings for Styling
		$title_font_size        = $this->get_meta_value( 'custom_tabultimate_shortcode_title_font_size', '15', array( 'custom_accordion_title_font_size' ) );
		$title_font_color       = $this->get_meta_value( 'custom_tabultimate_shortcode_title_font_color', '#21759b', array( 'custom_accordion_title_font_color' ) );
		$active_font_color      = $this->get_meta_value( 'custom_tabsfree_title_active_color', '#000000' );
		$active_bg_color        = $this->get_meta_value( 'custom_tabsfree_title_active_bg_color', '#ffffff' );
		$title_active_hv        = $this->get_meta_value( 'custom_tabsfree_title_active_hv_color', '' );
		$title_active_hvtext    = $this->get_meta_value( 'custom_tabsfree_title_active_hvtext_color', '' );
		$title_tab_border_color = $this->get_meta_value( 'custom_tabsfree_tab_border_color', '#e2e8f0' );
		
		$content_bg_color       = $this->get_meta_value( 'custom_tabultimate_shortcode_content_bg_color', '#f6f6f6', array( 'custom_accordion_content_bg_color' ) );
		$content_font_color     = $this->get_meta_value( 'custom_accordion_content_font_color', '' );
		$content_font_size      = $this->get_meta_value( 'custom_tabsfree_content_font_size', '16' );
		$content_line_height    = $this->get_meta_value( 'custom_tabsfree_content_line_height', '1.6' );
		$content_padding        = $this->get_meta_value( 'custom_accordion_content_padding', '12' );
		
		$show_hide_icon         = $this->get_meta_value( 'custom_tabsfree_show_hide_icons', '1' ); // Default 1 (Show)
		$icon_position          = $this->get_meta_value( 'custom_tabsfree_tab_icon_position', 'before' );
		$icon_color             = $this->get_meta_value( 'custom_tabsfree_icon_color', '' );
		$icon_font_size         = $this->get_meta_value( 'custom_tabsfree_icon_font_size', '16' );
		
		$tabmultiid             = rand( 1, 9999 );
		$container_id           = 'tpultimatestabs-active-' . esc_attr( $tabmultiid );

		// Generate Inline CSS Variables inside container style attribute
		$container_inline_style = sprintf(
			'style="--tp-title-size: %spx; --tp-title-color: %s; --tp-active-color: %s; --tp-active-bg: %s; --tp-hover-bg: %s; --tp-border-color: %s; --tp-hover-text: %s; --tp-content-bg: %s; --tp-content-color: %s; --tp-content-size: %spx; --tp-content-line: %s; --tp-content-padding: %spx; --tp-icon-color: %s; --tp-icon-size: %spx;"',
			esc_attr( $title_font_size ),
			esc_attr( $title_font_color ),
			esc_attr( $active_font_color ),
			esc_attr( $active_bg_color ),
			esc_attr( $title_active_hv ),
			esc_attr( $title_active_hvtext ),
			esc_attr( $title_tab_border_color ),
			esc_attr( $content_bg_color ),
			esc_attr( $content_font_color ),
			esc_attr( $content_font_size ),
			esc_attr( $content_line_height ),
			esc_attr( $content_padding ),
			esc_attr( $icon_color ),
			esc_attr( $icon_font_size )
		);

		$logologcreate = '';


		// Container with data attributes for tpTabsEngine
		$logologcreate .= '<div id="' . esc_attr( $container_id ) . '" ' . $container_inline_style . ' class="tptabs-container tp-tabs-' . esc_attr( $menu_position ) . ' tp-tabs-' . esc_attr( $direction ) . ' tp-align-' . esc_attr( $tabs_alignment ) . ' tp-style-' . esc_attr( $tab_style ) . '"';
		$logologcreate .= ' data-initial-tab="' . absint( $activated ) . '"';
		$logologcreate .= ' data-event="' . esc_attr( $open_event ) . '"';
		$logologcreate .= ' data-direction="' . esc_attr( $direction ) . '"';
		$logologcreate .= ' data-auto-height="' . esc_attr( $auto_height ) . '"';
		$logologcreate .= ' data-anim-speed="' . absint( $anim_speed ) . '"';
		$logologcreate .= ' data-in-animation="' . esc_attr( $in_transition ) . '"';
		$logologcreate .= ' data-use-hash="' . esc_attr( $use_hash ) . '">';

		$logologcreate .= '<ul class="tptabs-nav">';
		$count = 0;

		foreach ( $items as $item ) {
			$logologcreate .= '<li><a href="#tab1-' . esc_attr( $count ) . '">';

			// Check if Icon is set to Show (value '1')
			$icon_html = '';
			if ( '1' === (string) $show_hide_icon && ! empty( $item['icon'] ) ) {
				$icon_html = '<i class="fa ' . esc_attr( $item['icon'] ) . ' tc-tab-icon"></i>';
			}

			$title_html = '<span class="tc-tab-title-text">' . esc_html( $item['title'] ) . '</span>';

			// Render based on Icon Position
			switch ( $icon_position ) {
				case 'after':
					$logologcreate .= $title_html . ( $icon_html ? ' ' . $icon_html : '' );
					break;
				case 'top':
					if ( $icon_html ) {
						$logologcreate .= '<span class="tc-icon-col-wrap" style="display:inline-flex; flex-direction:column; align-items:center; text-align:center;">' . $icon_html . $title_html . '</span>';
					} else {
						$logologcreate .= $title_html;
					}
					break;
				case 'bottom':
					if ( $icon_html ) {
						$logologcreate .= '<span class="tc-icon-col-wrap" style="display:inline-flex; flex-direction:column; align-items:center; text-align:center;">' . $title_html . $icon_html . '</span>';
					} else {
						$logologcreate .= $title_html;
					}
					break;
				case 'before':
				default:
					$logologcreate .= ( $icon_html ? $icon_html . ' ' : '' ) . $title_html;
					break;
			}

			$logologcreate .= '</a></li>';
			$count++;
		}

		$logologcreate .= '</ul>';
		$logologcreate .= '<div class="tptabs-content-wrapper">';
		$count = 0;

		foreach ( $items as $item ) {
			$logologcreate .= '<div class="tab_content" id="tab1-' . esc_attr( $count ) . '">';
			
			$content = $item['description'];
			$content = do_shortcode( $content );
			$logologcreate .= wpautop( wp_kses_post( $content ) );
			
			$logologcreate .= '</div>';
			$count++;
		}

		$logologcreate .= '</div>';
		$logologcreate .= '</div>';

		// Inline Script to initialize tpTabsEngine for this instance
		$logologcreate .= '
		<script type="text/javascript">
			jQuery(document).ready(function($){
				var $container = $("#' . esc_js( $container_id ) . '");
				if (typeof $container.tpTabsEngine === "function") {
					$container.tpTabsEngine({
						initialTab:        parseInt($container.data("initial-tab"), 10) || 1,
						event:             $container.data("event") || "click",
						direction:         $container.data("direction") || "horizontal",
						autoHeight:        $container.data("auto-height") !== false && $container.data("auto-height") !== "false",
						animSpeed:         parseInt($container.data("anim-speed"), 10) || 300,
						inAnimation:       $container.data("in-animation") || "fadeIn",
						useHash:           $container.data("use-hash") !== false && $container.data("use-hash") !== "false",
						autoOrder:         true
					});
				}
			});
		</script>';
		
		return $logologcreate;
	}
}

// Global Shortcode / Legacy Function Bridge
if ( ! function_exists( 'TP_tab_Free_Shortcode_ultimate_body' ) ) {
	function TP_tab_Free_Shortcode_ultimate_body( $post_id ) {
		$renderer = new TPTabs_Renderer( $post_id );
		return $renderer->render();
	}
}