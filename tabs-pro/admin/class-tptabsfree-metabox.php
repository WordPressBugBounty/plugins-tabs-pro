<?php
/**
 * Custom Metabox Class for Accordion Items & Settings.
 * Handles legacy CMB2 multi-row conversion, dynamic field parsing, limits, and styling options.
 *
 * @package TPTabs_Ultimate
 * @version 2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class TPTabs_Metabox {

	/**
	 * Meta key used for storing accordion items in postmeta.
	 *
	 * @var string
	 */
	const META_KEY = 'custom_accordion_wordpresspro_columns';

	/**
	 * Maximum allowed accordion items for Free users.
	 *
	 * @var int
	 */
	const FREE_MAX_ITEMS = 4;

	/**
	 * Maximum allowed accordion items for Pro users.
	 *
	 * @var int
	 */
	const PRO_MAX_ITEMS = 25;

	/**
	 * Constructor to register hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ), 0 );
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post_tp_tab_pro', array( $this, 'save_metabox' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		// Admin Columns & Shortcode Notice
		add_filter( 'manage_tp_tab_pro_posts_columns', array( $this, 'register_admin_columns' ) );
		add_action( 'manage_tp_tab_pro_posts_custom_column', array( $this, 'render_admin_columns' ), 10, 2 );
		add_action( 'edit_form_after_title', array( $this, 'render_shortcode_section' ) );
		add_action( 'wp_ajax_tcaccordion_get_live_preview', array( $this, 'ajax_render_live_preview' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_preview_styles' ) );
		add_filter( 'enter_title_here', array( $this, 'tptabsfree_change_title_placeholder' ) );
	}

	/**
	 * Register Custom Post Type
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Tabs Free', 'Post Type General Name', 'tptabs-ultimate' ),
			'singular_name'      => _x( 'Tabs Free', 'Post Type Singular Name', 'tptabs-ultimate' ),
			'menu_name'          => __( 'Tabs Free', 'tptabs-ultimate' ),
			'name_admin_bar'     => __( 'Tabs Free', 'tptabs-ultimate' ),
			'all_items'          => __( 'All Tabs', 'tptabs-ultimate' ),
			'add_new_item'       => __( 'Add New Tab', 'tptabs-ultimate' ),
			'add_new'            => __( 'Add New Tab', 'tptabs-ultimate' ),
			'new_item'           => __( 'New Item Tab', 'tptabs-ultimate' ),
			'edit_item'          => __( 'Edit Tab', 'tptabs-ultimate' ),
			'update_item'        => __( 'Update Tab', 'tptabs-ultimate' ),
			'view_item'          => __( 'View Tabs', 'tptabs-ultimate' ),
			'search_items'       => __( 'Search Tab', 'tptabs-ultimate' ),
			'not_found'          => __( 'Tabs Free Not found', 'tptabs-ultimate' ),
			'not_found_in_trash' => __( 'Tabs Free Not found in Trash', 'tptabs-ultimate' ),
		);

		$args = array(
			'label'               => __( 'Tabs Free', 'tptabs-ultimate' ),
			'description'         => __( 'Tabs Free Post Type', 'tptabs-ultimate' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'menu_icon'   		  => 'dashicons-list-view',
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);

		register_post_type( 'tp_tab_pro', $args );
	}

	/**
	 * Check if the plugin is in Pro version.
	 *
	 * @return bool
	 */
	private function is_pro() {
		return false;
	}

	/**
	 * Get the maximum item limit based on user license.
	 *
	 * @return int
	 */
	private function get_max_items() {
		return function_exists( 'tc_tabs_is_pro' ) && tc_tabs_is_pro() ? self::PRO_MAX_ITEMS : self::FREE_MAX_ITEMS;
	}

	/**
	 * Register the Accordion metabox wrapper.
	 *
	 * @return void
	 */
	public function register_metabox() {
		add_meta_box(
			'custom_accordion_wordpress_feature',
			__( 'Tabs Control Panel', 'tptabs-ultimate' ),
			array( $this, 'render_metabox_wrapper' ),
			'tp_tab_pro',
			'normal',
			'high'
		);
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		global $post_type;

		if ( 'tp_tab_pro' !== $post_type ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style(
			'tcaccordion-metabox',
			TPTABSFREE_PLUGIN_URL . 'admin/css/tptabsfree-metabox.css',
			array(),
			'3.0.8'
		);

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_editor();

		wp_enqueue_style(
	        'font-awesome',
	        TPTABSFREE_PLUGIN_URL . 'assets/css/font-awesome.css',
	        array(),
	        '6.5.1'
	    );

		wp_enqueue_script(
			'tcaccordion-metabox',
			TPTABSFREE_PLUGIN_URL . 'admin/js/tptabsfree-metabox.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			'3.0.8',
			true
		);

		wp_localize_script(
			'tcaccordion-metabox',
			'tcAccordion',
			array(
				'maxItems'   => $this->get_max_items(),
				'isPro'      => $this->is_pro(),
				'addText'    => __( 'Add Item', 'tcaccordion' ),
				'removeText' => __( 'Remove', 'tcaccordion' ),
				'limitText'  => sprintf( __( 'Free version is limited to %d items.', 'tcaccordion' ), self::FREE_MAX_ITEMS ),
			)
		);
	}

	/**
	 * Main Tabbed Container Wrapper
	 */
	public function render_metabox_wrapper( $post ) {
		wp_nonce_field( 'tptabsfree_save_action', 'tcaccordion_nonce_field' );

		// Retrieve active tab from meta or default to '#tab-accordion-content'
		$active_tab = get_post_meta( $post->ID, '_tc_active_tab', true );
		if ( empty( $active_tab ) ) {
			$active_tab = '#tab-accordion-content';
		}
		?>
		<div id="tc-tabs-container" class="tc-tabs-container">
			<!-- Hidden input to post the current active tab -->
			<input type="hidden" id="tc_active_tab_input" name="tc_active_tab_input" value="<?php echo esc_attr( $active_tab ); ?>" />

			<ul class="tc-tabs-menu">
				<li class="tc-tab-item <?php echo ( '#tab-accordion-content' === $active_tab ) ? 'current' : ''; ?>">
					<a href="#tab-accordion-content" class="tc-tab-link">
						<span class="dashicons dashicons-editor-justify"></span>
						<?php esc_html_e( 'Tab Content', 'tptabs-ultimate' ); ?>
					</a>
				</li>

				<li class="tc-tab-item <?php echo ( '#tab-accordion-settings' === $active_tab ) ? 'current' : ''; ?>">
					<a href="#tab-accordion-settings" class="tc-tab-link">
						<span class="dashicons dashicons-admin-generic"></span>
						<?php esc_html_e( 'Tab Settings', 'tptabs-ultimate' ); ?>
					</a>
				</li>

				<li class="tc-tab-item <?php echo ( '#tab-customization-settings' === $active_tab ) ? 'current' : ''; ?>">
					<a href="#tab-customization-settings" class="tc-tab-link">
						<span class="dashicons dashicons-admin-generic"></span>
						<?php esc_html_e( 'Design Options', 'tptabs-ultimate' ); ?>
					</a>
				</li>

				<li class="tc-tab-item <?php echo ( '#tab-accordion-preview' === $active_tab ) ? 'current' : ''; ?>">
					<a href="#tab-accordion-preview" class="tc-tab-link">
						<span class="dashicons dashicons-visibility"></span>
						<?php esc_html_e( 'Live Preview', 'tptabs-ultimate' ); ?>
					</a>
				</li>
			</ul>

			<div class="tc-tab-wrapper">
				<!-- TAB 1: REPEATER / GROUP FIELDS -->
				<div id="tab-accordion-content" class="tc-tab-content <?php echo ( '#tab-accordion-content' === $active_tab ) ? 'active' : ''; ?>">
					<?php $this->render_accordion_content_tab( $post ); ?>
				</div>

				<!-- TAB 2: STYLING & OPTIONS -->
				<div id="tab-accordion-settings" class="tc-tab-content <?php echo ( '#tab-accordion-settings' === $active_tab ) ? 'active' : ''; ?>">
					<?php $this->render_accordion_settings_tab( $post ); ?>
				</div>

				<!-- TAB 3: DESIGN OPTIONS -->
				<div id="tab-customization-settings" class="tc-tab-content <?php echo ( '#tab-customization-settings' === $active_tab ) ? 'active' : ''; ?>">
					<?php $this->render_display_settings_tab( $post ); ?>
				</div>

				<!-- TAB 4: LIVE PREVIEW TAB -->
				<div id="tab-accordion-preview" class="tc-tab-content <?php echo ( '#tab-accordion-preview' === $active_tab ) ? 'active' : ''; ?>">
				    <div class="tc-preview-tab-container" style="padding: 20px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
				        <div class="tc-preview-header" style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
				            <div>
				                <h3 style="margin: 0; display: inline-block;">
				                    <span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span>
				                    <?php esc_html_e( 'Exact Frontend Live Preview', 'tptabs-ultimate' ); ?>
				                </h3>
				                <span class="description" style="margin-left: 10px;">
				                    <?php esc_html_e( '(Renders full shortcodes, embeds, and actual frontend CSS)', 'tptabs-ultimate' ); ?>
				                </span>
				            </div>
				            <button type="button" id="tc-refresh-preview-btn" class="button button-secondary">
				                <span class="dashicons dashicons-update" style="vertical-align: middle; line-height: 1.3;"></span>
				                <?php esc_html_e( 'Refresh Preview', 'tptabs-ultimate' ); ?>
				            </button>
				        </div>

				        <!-- Preview Loader & Display Canvas -->
				        <div id="tc-preview-loading" style="display: none; padding: 30px; text-align: center;">
				            <span class="spinner is-active" style="float: none; margin: 0 5px 0 0;"></span>
				            <?php esc_html_e( 'Rendering exact frontend preview...', 'tptabs-ultimate' ); ?>
				        </div>

				        <div id="tc-exact-preview-canvas" class="tc-frontend-preview-wrap">
				            <!-- Exact Frontend HTML loaded here via AJAX -->
				        </div>
				    </div>
				</div>
			</div>
		</div>

		<!-- ICON PICKER MODAL POPUP -->
		<div id="tc-icon-picker-modal" class="tc-icon-modal-overlay" style="display:none;">
			<div class="tc-icon-modal-content">
				<div class="tc-icon-modal-header">
					<h3><?php esc_html_e( 'Select Icon', 'tptabs-ultimate' ); ?></h3>
					<button type="button" class="tc-icon-modal-close">&times;</button>
				</div>
				<div class="tc-icon-modal-search">
					<input type="text" id="tc-icon-search-input" placeholder="<?php esc_attr_e( 'Search icons...', 'tptabs-ultimate' ); ?>" />
				</div>
				<div class="tc-icon-grid">
					<?php
					$icons = array(
						'fa fa-home', 'fa fa-user', 'fa fa-cog', 'fa fa-star', 'fa fa-heart',
						'fa fa-check', 'fa fa-times', 'fa fa-search', 'fa fa-envelope', 'fa fa-phone',
						'fa fa-map-marker', 'fa fa-calendar', 'fa fa-clock-o', 'fa fa-camera', 'fa fa-comment',
						'fa fa-folder', 'fa fa-file', 'fa fa-download', 'fa fa-upload', 'fa fa-lock',
						'fa fa-unlock', 'fa fa-shield', 'fa fa-trophy', 'fa fa-bookmark', 'fa fa-tag',
						'fa fa-tags', 'fa fa-shopping-cart', 'fa fa-credit-card', 'fa fa-gift', 'fa fa-bullhorn',
						'fa fa-info-circle', 'fa fa-question-circle', 'fa fa-exclamation-triangle', 'fa fa-thumbs-up', 'fa fa-share',
					);
					foreach ( $icons as $icon_class ) {
						echo '<div class="tc-icon-item" data-icon="' . esc_attr( $icon_class ) . '"><i class="' . esc_attr( $icon_class ) . '"></i></div>';
					}
					?>
				</div>
				<div class="tc-icon-modal-footer">
					<button type="button" class="button button-secondary tc-icon-modal-close"><?php esc_html_e( 'Cancel', 'tptabs-ultimate' ); ?></button>
				</div>
			</div>
		</div>
		<style>
			.tc-icon-modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:100000; display:flex; align-items:center; justify-content:center; }
			.tc-icon-modal-content { background:#fff; width:600px; max-width:90%; border-radius:6px; box-shadow:0 5px 15px rgba(0,0,0,0.3); display:flex; flex-direction:column; max-height:80vh; }
			.tc-icon-modal-header { padding:15px 20px; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center; }
			.tc-icon-modal-header h3 { margin:0; font-size:16px; }
			.tc-icon-modal-close { background:none; border:none; font-size:22px; cursor:pointer; }
			.tc-icon-modal-search { padding:15px; border-bottom:1px solid #eee; }
			.tc-icon-modal-search input { width:100%; padding:8px 12px; }
			.tc-icon-grid { padding:15px; display:grid; grid-template-columns:repeat(6, 1fr); gap:10px; overflow-y:auto; max-height:350px; }
			.tc-icon-item { padding:12px; text-align:center; border:1px solid #ddd; border-radius:4px; cursor:pointer; font-size:18px; transition:all 0.2s; }
			.tc-icon-item:hover, .tc-icon-item.selected { background:#007cba; color:#fff; border-color:#007cba; }
			.tc-icon-modal-footer { padding:12px 20px; border-top:1px solid #ddd; text-align:right; }
		</style>
		<script>
			jQuery(document).ready(function($) {
				var activeInput = null;
				
				// Open Popup
				$(document).on('click', '.tc-open-icon-picker', function(e) {
					e.preventDefault();
					activeInput = $(this).siblings('.tc-icon-input-field');
					$('#tc-icon-picker-modal').fadeIn(200);
				});

				// Close Popup
				$(document).on('click', '.tc-icon-modal-close, .tc-icon-modal-overlay', function(e) {
					if (e.target === this) {
						$('#tc-icon-picker-modal').fadeOut(200);
					}
				});

				// Select Icon
				$(document).on('click', '.tc-icon-item', function() {
					var icon = $(this).data('icon');
					if (activeInput) {
						activeInput.val(icon);
						var previewBox = activeInput.siblings('.tc-selected-icon-preview').find('i');
						if (previewBox.length) {
							previewBox.attr('class', icon);
						} else {
							activeInput.siblings('.tc-selected-icon-preview').html('<i class="' + icon + '"></i>');
						}
					}
					$('#tc-icon-picker-modal').fadeOut(200);
				});

				// Search Icons
				$('#tc-icon-search-input').on('keyup', function() {
					var val = $(this).val().toLowerCase();
					$('.tc-icon-item').each(function() {
						var icon = $(this).data('icon');
						if (icon.indexOf(val) !== -1) {
							$(this).show();
						} else {
							$(this).hide();
						}
					});
				});
			});
		</script>
		<?php
	}

	/**
	 * Tab 1 Render: Repeater Items Markup
	 */
	private function render_accordion_content_tab( $post ) {
		$items     = $this->get_items_from_db( $post->ID );
		$max_limit = $this->get_max_items();
		?>
		<div id="tcaccordion-wrapper">
			<div id="tcaccordion-sortable">
				<?php
					if ( ! empty( $items ) && is_array( $items ) ) {
						$i = 1;
						foreach ( $items as $index => $item ) {
							$this->render_item( $index, $item, $i );
							$i++;
						}
					}
				?>
			</div>

			<p class="tcaccordion-actions">
				<button type="button" class="button button-primary" id="tcaccordion-add" <?php echo count( $items ) >= $max_limit ? 'disabled="disabled"' : ''; ?>>
					<?php esc_html_e( 'Add Tab Item', 'tptabs-ultimate' ); ?>
				</button>
			</p>

			<?php if ( ! ( function_exists( 'tc_tabs_is_pro' ) && tc_tabs_is_pro() ) ) : ?>
				<p class="description tcaccordion-limit-notice">
					<?php
					printf(
						/* translators: %d: maximum number of items */
						esc_html__( 'Free version is limited to %d items. Upgrade to Pro for unlimited items.', 'tptabs-ultimate' ),
						self::FREE_MAX_ITEMS
					);
					?>
				</p>
			<?php endif; ?>
		</div>

		<script type="text/template" id="tcaccordion-template">
			<?php
				ob_start();
				$this->render_item(
					'__INDEX__',
					array(
						'title'       => '',
						'description' => '',
						'icon'        => 'fa fa-home',
					),
					1
				);
				$template_content = ob_get_clean();
				echo str_replace( array( '<script', '</script>' ), array( '&lt;script', '&lt;/script' ), $template_content );
			?>
		</script>
		<?php
	}

	/**
	 * Tab 2 Render: Settings & Theme Picker Controls
	 */
	private function render_accordion_settings_tab( $post ) {
		$tp_custom_tabultimate_shortcode_tabs_themes = get_post_meta( $post->ID, 'tp_custom_tabultimate_shortcode_tabs_themes', true );
		$auto_open_item                              = get_post_meta( $post->ID, 'tp_custom_tabultimate_shortcode_tabs_activated', true );
		$auto_open_item                              = ( '' !== $auto_open_item ) ? absint( $auto_open_item ) : 1; 

		$open_event = get_post_meta( $post->ID, '_tp_custom_tabultimate_shortcode_tabs_openhover', true );
		$open_event = ! empty( $open_event ) ? $open_event : 'click';

		$auto_height = get_post_meta( $post->ID, '_tp_custom_tabultimate_shortcode_tabs_autoheight', true );
		if ( empty( $auto_height ) ) {
		    $auto_height = 'off';
		}

		$in_transition = get_post_meta( $post->ID, 'custom_tabultimate_shortcode_in_transition', true );
		if ( empty( $in_transition ) ) {
		    $in_transition = 'fadeIn';
		}

		$anim_speed = get_post_meta( $post->ID, '_tp_custom_tabultimate_shortcode_tabs_animspeed', true );
		if ( empty( $anim_speed ) ) {
		    $anim_speed = '300';
		}

		$menu_position = get_post_meta( $post->ID, 'tp_custom_tabultimate_shortcode_tabs_positions', true ) ?: 'top';
		$use_hash      = get_post_meta( $post->ID, 'tp_custom_tabultimate_shortcode_use_hash', true ) ?: 'on';

		$tabs_alignment = get_post_meta( $post->ID, 'tp_custom_tabultimate_shortcode_tabs_alignment', true );
		if ( empty( $tabs_alignment ) ) {
		    $tabs_alignment = 'left';
		}

		$tab_style = get_post_meta( $post->ID, 'tp_custom_tabultimate_shortcode_tabs_style', true );
		if ( empty( $tab_style ) ) {
		    $tab_style = 'style-6';
		}

		$is_pro_active = function_exists( 'tc_tabs_is_pro' ) && tc_tabs_is_pro();

		if ( empty( $tp_custom_tabultimate_shortcode_tabs_themes ) ) {
			$tp_custom_tabultimate_shortcode_tabs_themes = 'horizontal';
		}

		$themes = array(
			'horizontal' => array( 'label' => __( 'Horizontal', 'tptabs-ultimate' ), 'img' => 'theme1.png' ),
			'vertical'   => array( 'label' => __( 'Vertical', 'tptabs-ultimate' ),     'img' => 'theme2.png' ),
		);
		?>
		<table class="form-table tc-settings-table">
			<tr valign="top">
				<th scope="row"><label><?php esc_html_e( 'Tab Layouts', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<div class="tc-theme-picker">
						<?php foreach ( $themes as $key => $theme ) : ?>
							<?php 
								$is_theme_pro = ! empty( $theme['is_pro'] );
								$disabled     = ( $is_theme_pro && ! $is_pro_active ); 
								$img_url      = defined( 'TPTABSFREE_PLUGIN_URL' ) && isset( $theme['img'] ) ? TPTABSFREE_PLUGIN_URL . 'admin/images/' . $theme['img'] : ''; 
							?>
							<label class="tc-theme-option <?php echo $disabled ? 'tc-theme-disabled' : ''; ?>">
								<input type="radio" 
									   name="tp_custom_tabultimate_shortcode_tabs_themes" 
									   value="<?php echo esc_attr( $key ); ?>" 
									   <?php checked( $tp_custom_tabultimate_shortcode_tabs_themes, $key ); ?> 
									   <?php disabled( $disabled ); ?> />
								<div class="tc-theme-card">
									<?php if ( $img_url ) : ?>
										<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( isset( $theme['label'] ) ? $theme['label'] : '' ); ?>" />
									<?php endif; ?>
									<span class="tc-theme-label">
										<?php echo esc_html( isset( $theme['label'] ) ? $theme['label'] : $key ); ?>
									</span>
								</div>
								<?php if ( $disabled ) : ?>
									<span class="tc-pro-badge-overlay"><small class="tc-pro-badge"><?php esc_html_e( 'PRO', 'tptabs-ultimate' ); ?></small></span>
								<?php endif; ?>
							</label>
						<?php endforeach; ?>
					</div>
			        <p class="description"><?php esc_html_e( 'Choose a tabs layout', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
			    <th scope="row"><label><?php esc_html_e( 'Tabs Alignment', 'tptabs-ultimate' ); ?></label></th>
			    <td>
			        <fieldset class="tp-alignment-selector">
			            <?php
			            $alignments = array(
			                'left'      => __( 'Left', 'tptabs-ultimate' ),
			                'center'    => __( 'Center', 'tptabs-ultimate' ),
			                'right'     => __( 'Right', 'tptabs-ultimate' ),
			                'justified' => __( 'Justified', 'tptabs-ultimate' ),
			            );

			            foreach ( $alignments as $key => $label ) :
			                $checked = checked( $tabs_alignment, $key, false );
			                $active_class = ( $tabs_alignment === $key ) ? ' tp-align-active' : '';
			                ?>
			                <label class="tp-align-option<?php echo esc_attr( $active_class ); ?>">
			                    <input type="radio" name="tp_custom_tabultimate_shortcode_tabs_alignment" value="<?php echo esc_attr( $key ); ?>" <?php echo $checked; ?> style="display:none;">
			                    <span class="tp-align-box">
			                        <span class="dashicons dashicons-editor-align<?php echo ( $key === 'justified' ) ? 'justify' : esc_attr( $key ); ?>"></span>
			                        <span class="tp-align-label"><?php echo esc_html( $label ); ?></span>
			                    </span>
			                </label>
			            <?php endforeach; ?>
			        </fieldset>
			        <p class="description"><?php esc_html_e( 'Select the alignment of your horizontal tab menu items.', 'tptabs-ultimate' ); ?></p>
			    </td>
			</tr>

			<tr valign="top">
			    <th scope="row"><label for="tp_custom_tabultimate_shortcode_tabs_style"><?php esc_html_e( 'Tab Menu Style', 'tptabs-ultimate' ); ?></label></th>
			    <td>
			        <select name="tp_custom_tabultimate_shortcode_tabs_style" id="tp_custom_tabultimate_shortcode_tabs_style">
						<option value="style-1" <?php selected( $tab_style, 'style-1' ); ?>><?php esc_html_e( 'Style 1: Classic Bordered', 'tptabs-ultimate' ); ?></option>
						<option value="style-2" <?php selected( $tab_style, 'style-2' ); ?>><?php esc_html_e( 'Style 2: Modern Pill / Rounded', 'tptabs-ultimate' ); ?></option>
						<option value="style-3" <?php selected( $tab_style, 'style-3' ); ?>><?php esc_html_e( 'Style 3: Minimal Bottom Line', 'tptabs-ultimate' ); ?></option>
						<option value="style-4" <?php selected( $tab_style, 'style-4' ); ?>><?php esc_html_e( 'Style 4: Soft Floating Cards', 'tptabs-ultimate' ); ?></option>
						<option value="style-5" <?php selected( $tab_style, 'style-5' ); ?>><?php esc_html_e( 'Style 5: Dark Mode Bar', 'tptabs-ultimate' ); ?></option>
						<option value="style-6" <?php selected( $tab_style, 'style-6' ); ?>><?php esc_html_e( 'Style 6: Clean Bordered Grid Default', 'tptabs-ultimate' ); ?></option>
			        </select>
			    </td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="tp_custom_tabultimate_shortcode_tabs_positions"><?php esc_html_e( 'Menu Position', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<select name="tp_custom_tabultimate_shortcode_tabs_positions" id="tp_custom_tabultimate_shortcode_tabs_positions" class="widefat" style="max-width: 250px;">
						<option value="top" <?php selected( $menu_position, 'top' ); ?>><?php esc_html_e( 'Top', 'tptabs-ultimate' ); ?></option>
						<option value="bottom" <?php selected( $menu_position, 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'tptabs-ultimate' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Choose where the tab navigation menu will appear relative to the content.', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="tp_custom_tabultimate_shortcode_tabs_activated"><?php esc_html_e( 'Auto Open Tab Index', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<input type="number" name="tp_custom_tabultimate_shortcode_tabs_activated" id="tp_custom_tabultimate_shortcode_tabs_activated" min="0" max="25" value="<?php echo esc_attr( $auto_open_item ); ?>" style="max-width: 100px;" />
					<p class="description"><?php esc_html_e( 'Specify which tab index should be opened automatically by default (e.g., 1 for the first tab).', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Activator Event', 'tptabs-ultimate' ); ?></th>
				<td>
					<fieldset>
						<label style="margin-right: 20px; display: inline-block;">
							<input type="radio" name="_tp_custom_tabultimate_shortcode_tabs_openhover" value="click" <?php checked( $open_event, 'click' ); ?>>
							<?php esc_html_e( 'On Click', 'tptabs-ultimate' ); ?>
						</label>
						<label style="display: inline-block;">
							<input type="radio" name="_tp_custom_tabultimate_shortcode_tabs_openhover" value="hover" <?php checked( $open_event, 'hover' ); ?>>
							<?php esc_html_e( 'On Mouse Hover', 'tptabs-ultimate' ); ?>
						</label>
					</fieldset>
					<p class="description" style="margin-top: 5px;"><?php esc_html_e( 'Select the user interaction required to switch between tabs.', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="_tp_custom_tabultimate_shortcode_tabs_autoheight"><?php esc_html_e( 'Auto Height', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<select name="_tp_custom_tabultimate_shortcode_tabs_autoheight" id="_tp_custom_tabultimate_shortcode_tabs_autoheight" class="widefat" style="max-width: 250px;">
						<option value="on" <?php selected( $auto_height, 'on' ); ?>><?php esc_html_e( 'Enable (On)', 'tptabs-ultimate' ); ?></option>
						<option value="off" <?php selected( $auto_height, 'off' ); ?>><?php esc_html_e( 'Disable (Off)', 'tptabs-ultimate' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Automatically adjust the content area height based on the active tab content.', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabultimate_shortcode_in_transition"><?php esc_html_e( 'In Transition Effect', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<select name="custom_tabultimate_shortcode_in_transition" id="custom_tabultimate_shortcode_in_transition" class="widefat" style="max-width: 250px;">
						<option value="fadeIn" <?php selected( $in_transition, 'fadeIn' ); ?>><?php esc_html_e( 'Fade In', 'tptabs-ultimate' ); ?></option>
						<option value="slideDown" <?php selected( $in_transition, 'slideDown' ); ?>><?php esc_html_e( 'Slide Down', 'tptabs-ultimate' ); ?></option>
						<option value="zoomIn" <?php selected( $in_transition, 'zoomIn' ); ?>><?php esc_html_e( 'Zoom In', 'tptabs-ultimate' ); ?></option>
						<option value="none" <?php selected( $in_transition, 'none' ); ?>><?php esc_html_e( 'None (Instant)', 'tptabs-ultimate' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select the animation effect applied when switching tab contents.', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="_tp_custom_tabultimate_shortcode_tabs_animspeed"><?php esc_html_e( 'Animation Speed', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<input type="number" name="_tp_custom_tabultimate_shortcode_tabs_animspeed" id="_tp_custom_tabultimate_shortcode_tabs_animspeed" value="<?php echo esc_attr( $anim_speed ); ?>" step="50" min="0" style="max-width: 100px;" /> ms
					<p class="description"><?php esc_html_e( 'Set the transition animation speed in milliseconds (e.g., 300ms).', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="tp_custom_tabultimate_shortcode_use_hash"><?php esc_html_e( 'URL Hash Links', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<select name="tp_custom_tabultimate_shortcode_use_hash" id="tp_custom_tabultimate_shortcode_use_hash" class="widefat" style="max-width: 250px;">
						<option value="on" <?php selected( $use_hash, 'on' ); ?>><?php esc_html_e( 'Enable (Update URL Hash)', 'tptabs-ultimate' ); ?></option>
						<option value="off" <?php selected( $use_hash, 'off' ); ?>><?php esc_html_e( 'Disable', 'tptabs-ultimate' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Update the browser URL hash when switching tabs to allow direct linking to specific tabs.', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

		</table>
		<?php
	}

	/**
	 * Tab 3 Render: Design Options & Controls
	 */
	private function render_display_settings_tab( $post ) {
		$custom_tabultimate_shortcode_title_font_color  = get_post_meta( $post->ID, 'custom_tabultimate_shortcode_title_font_color', true );
		$custom_tabsfree_title_active_hv_color          = get_post_meta( $post->ID, 'custom_tabsfree_title_active_hv_color', true );
		$custom_tabsfree_title_active_hvtext_color      = get_post_meta( $post->ID, 'custom_tabsfree_title_active_hvtext_color', true );
		$custom_tabsfree_tab_border_color               = get_post_meta( $post->ID, 'custom_tabsfree_tab_border_color', true );
		$custom_tabsfree_title_active_bg_color          = get_post_meta( $post->ID, 'custom_tabsfree_title_active_bg_color', true );
		$custom_tabsfree_title_active_color             = get_post_meta( $post->ID, 'custom_tabsfree_title_active_color', true );
		$custom_tabultimate_shortcode_title_font_size   = get_post_meta( $post->ID, 'custom_tabultimate_shortcode_title_font_size', true );
		$custom_tabsfree_title_line_height              = get_post_meta( $post->ID, 'custom_tabsfree_title_line_height', true );
		$custom_accordion_content_bg_color              = get_post_meta( $post->ID, 'custom_accordion_content_bg_color', true );
		$custom_accordion_content_font_color            = get_post_meta( $post->ID, 'custom_accordion_content_font_color', true );
		$custom_tabsfree_content_font_size              = get_post_meta( $post->ID, 'custom_tabsfree_content_font_size', true );
		$custom_tabsfree_content_line_height            = get_post_meta( $post->ID, 'custom_tabsfree_content_line_height', true );
		$custom_accordion_content_padding               = get_post_meta( $post->ID, 'custom_accordion_content_padding', true );
		$custom_tabsfree_show_hide_icons                = get_post_meta( $post->ID, 'custom_tabsfree_show_hide_icons', true );
		$custom_tabsfree_tab_icon_position              = get_post_meta( $post->ID, 'custom_tabsfree_tab_icon_position', true );
		$custom_tabsfree_icon_color                     = get_post_meta( $post->ID, 'custom_tabsfree_icon_color', true );
		$custom_tabsfree_icon_font_size                 = get_post_meta( $post->ID, 'custom_tabsfree_icon_font_size', true );
		?>
		<table class="form-table tc-settings-table">

			<!-- Section Header -->
			<tr valign="top">
				<th colspan="2" style="background: #f6f7f7; padding: 12px 15px; border-top: 1px solid #ccd0d4; border-bottom: 1px solid #ccd0d4;">
					<strong style="font-size: 13px; text-transform: uppercase; color: #1d2327;"><?php esc_html_e( 'Tab Title Settings', 'tptabs-ultimate' ); ?></strong>
				</th>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom-accordion-title-font-color"><?php esc_html_e( 'Title Font Color', 'tptabs-ultimate' ); ?></label></th>
				<td><input name="custom_tabultimate_shortcode_title_font_color" class="tc-color-field" id="custom-accordion-title-font-color" type="text" value="<?php echo esc_attr( $custom_tabultimate_shortcode_title_font_color ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabultimate_shortcode_title_font_size"><?php esc_html_e( 'Title Font Size', 'tptabs-ultimate' ); ?></label></th>
				<td><input type="number" name="custom_tabultimate_shortcode_title_font_size" id="custom_tabultimate_shortcode_title_font_size" value="<?php echo ! empty( $custom_tabultimate_shortcode_title_font_size ) ? esc_attr( $custom_tabultimate_shortcode_title_font_size ) : '18'; ?>"> px</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_title_line_height"><?php esc_html_e( 'Title Line Height', 'tptabs-ultimate' ); ?></label></th>
				<td><input type="number" step="0.1" name="custom_tabsfree_title_line_height" id="custom_tabsfree_title_line_height" min="0.8" max="3" value="<?php echo ! empty( $custom_tabsfree_title_line_height ) ? esc_attr( $custom_tabsfree_title_line_height ) : '1.4'; ?>"></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_title_active_color"><?php esc_html_e( 'Active Tab Font Color', 'tptabs-ultimate' ); ?></label></th>
				<td><input name="custom_tabsfree_title_active_color" class="tc-color-field" id="custom_tabsfree_title_active_color" type="text" value="<?php echo esc_attr( $custom_tabsfree_title_active_color ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_title_active_bg_color"><?php esc_html_e( 'Active Tab BG Color', 'tptabs-ultimate' ); ?></label></th>
				<td><input name="custom_tabsfree_title_active_bg_color" class="tc-color-field" id="custom_tabsfree_title_active_bg_color" type="text" value="<?php echo esc_attr( $custom_tabsfree_title_active_bg_color ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_title_active_hv_color"><?php esc_html_e( 'Active Tab Hover Bg Color', 'tptabs-ultimate' ); ?></label></th>
				<td><input name="custom_tabsfree_title_active_hv_color" class="tc-color-field" id="custom_tabsfree_title_active_hv_color" type="text" value="<?php echo esc_attr( $custom_tabsfree_title_active_hv_color ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_title_active_hvtext_color"><?php esc_html_e( 'Active Tab Hover Text Color', 'tptabs-ultimate' ); ?></label></th>
				<td><input name="custom_tabsfree_title_active_hvtext_color" class="tc-color-field" id="custom_tabsfree_title_active_hvtext_color" type="text" value="<?php echo esc_attr( $custom_tabsfree_title_active_hvtext_color ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_tab_border_color"><?php esc_html_e( 'Tab Border Color', 'tptabs-ultimate' ); ?></label></th>
				<td><input name="custom_tabsfree_tab_border_color" class="tc-color-field" id="custom_tabsfree_tab_border_color" type="text" value="<?php echo esc_attr( $custom_tabsfree_tab_border_color ); ?>" /></td>
			</tr>

			<!-- Section Header -->
			<tr valign="top">
				<th colspan="2" style="background: #f6f7f7; padding: 12px 15px; border-top: 1px solid #ccd0d4; border-bottom: 1px solid #ccd0d4;">
					<strong style="font-size: 13px; text-transform: uppercase; color: #1d2327;"><?php esc_html_e( 'Tab Content Settings', 'tptabs-ultimate' ); ?></strong>
				</th>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom-accordion-content-bg-color"><?php esc_html_e( 'Content BG Color', 'tptabs-ultimate' ); ?></label></th>
				<td><input name="custom_accordion_content_bg_color" class="tc-color-field" id="custom-accordion-content-bg-color" type="text" value="<?php echo esc_attr( $custom_accordion_content_bg_color ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom-accordion-content-font-color"><?php esc_html_e( 'Content Font Color', 'tptabs-ultimate' ); ?></label></th>
				<td><input name="custom_accordion_content_font_color" class="tc-color-field" id="custom-accordion-content-font-color" type="text" value="<?php echo esc_attr( $custom_accordion_content_font_color ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_content_font_size"><?php esc_html_e( 'Content Font Size', 'tptabs-ultimate' ); ?></label></th>
				<td><input type="number" name="custom_tabsfree_content_font_size" id="custom_tabsfree_content_font_size" value="<?php echo ! empty( $custom_tabsfree_content_font_size ) ? esc_attr( $custom_tabsfree_content_font_size ) : '16'; ?>"> px</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_content_line_height"><?php esc_html_e( 'Content Line Height', 'tptabs-ultimate' ); ?></label></th>
				<td><input type="number" step="0.1" name="custom_tabsfree_content_line_height" id="custom_tabsfree_content_line_height" min="0.8" max="3" value="<?php echo ! empty( $custom_tabsfree_content_line_height ) ? esc_attr( $custom_tabsfree_content_line_height ) : '1.6'; ?>"></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_accordion_content_padding"><?php esc_html_e( 'Content Padding', 'tptabs-ultimate' ); ?></label></th>
				<td><input type="number" name="custom_accordion_content_padding" id="custom_accordion_content_padding" value="<?php echo ! empty( $custom_accordion_content_padding ) ? esc_attr( $custom_accordion_content_padding ) : '12'; ?>"> px</td>
			</tr>

			<!-- Section Header -->
			<tr valign="top">
				<th colspan="2" style="background: #f6f7f7; padding: 12px 15px; border-top: 1px solid #ccd0d4; border-bottom: 1px solid #ccd0d4;">
					<strong style="font-size: 13px; text-transform: uppercase; color: #1d2327;"><?php esc_html_e( 'Tab Icon Settings', 'tptabs-ultimate' ); ?></strong>
				</th>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_show_hide_icons"><?php esc_html_e( 'Tab Icon Visibility', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<select name="custom_tabsfree_show_hide_icons" id="custom_tabsfree_show_hide_icons" class="widefat" style="max-width: 250px;">
						<option value="1" <?php selected( $custom_tabsfree_show_hide_icons, '1' ); ?>><?php esc_html_e( 'Show Icons', 'tptabs-ultimate' ); ?></option>
						<option value="2" <?php selected( $custom_tabsfree_show_hide_icons, '2' ); ?>><?php esc_html_e( 'Hide Icons', 'tptabs-ultimate' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Choose whether to display or hide the icons on the tab titles.', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_tab_icon_position"><?php esc_html_e( 'Tab Icon Position', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<select name="custom_tabsfree_tab_icon_position" id="custom_tabsfree_tab_icon_position" class="widefat" style="max-width: 250px;">
						<option value="before" <?php selected( $custom_tabsfree_tab_icon_position, 'before' ); ?>><?php esc_html_e( 'Before Title (Left)', 'tptabs-ultimate' ); ?></option>
						<option value="after" <?php selected( $custom_tabsfree_tab_icon_position, 'after' ); ?>><?php esc_html_e( 'After Title (Right)', 'tptabs-ultimate' ); ?></option>
						<option value="top" <?php selected( $custom_tabsfree_tab_icon_position, 'top' ); ?>><?php esc_html_e( 'Above Title (Top)', 'tptabs-ultimate' ); ?></option>
						<option value="bottom" <?php selected( $custom_tabsfree_tab_icon_position, 'bottom' ); ?>><?php esc_html_e( 'Below Title (Bottom)', 'tptabs-ultimate' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select where the icon should be positioned relative to the tab title text.', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_icon_color"><?php esc_html_e( 'Icon Color', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<input name="custom_tabsfree_icon_color" class="tc-color-field" id="custom_tabsfree_icon_color" type="text" value="<?php echo esc_attr( $custom_tabsfree_icon_color ); ?>" />
					<p class="description"><?php esc_html_e( 'Select a custom color for the tab icons.', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="custom_tabsfree_icon_font_size"><?php esc_html_e( 'Icon Font Size', 'tptabs-ultimate' ); ?></label></th>
				<td>
					<input type="number" name="custom_tabsfree_icon_font_size" id="custom_tabsfree_icon_font_size" value="<?php echo ! empty( $custom_tabsfree_icon_font_size ) ? esc_attr( $custom_tabsfree_icon_font_size ) : '16'; ?>" style="max-width: 100px;"> px
					<p class="description"><?php esc_html_e( 'Enter the font size for the tab icons in pixels.', 'tptabs-ultimate' ); ?></p>
				</td>
			</tr>

		</table>
		<?php
	}

	/**
	 * Universal Meta Parser: Fetches all postmeta rows and flattens both CMB2 legacy entries and array formats.
	 *
	 * @param int $post_id Post ID.
	 * @return array Standardized array of items.
	 */
	private function get_items_from_db( $post_id ) {
		$all_meta_rows = get_post_meta( $post_id, self::META_KEY, false );
		$items         = array();

		if ( empty( $all_meta_rows ) || ! is_array( $all_meta_rows ) ) {
			return $items;
		}

		foreach ( $all_meta_rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			if ( isset( $row[0] ) && is_array( $row[0] ) ) {
				foreach ( $row as $sub_item ) {
					if ( is_array( $sub_item ) ) {
						$item = $this->extract_item_fields( $sub_item );
						if ( $item ) {
							$items[] = $item;
						}
					}
				}
			} else {
				$item = $this->extract_item_fields( $row );
				if ( $item ) {
					$items[] = $item;
				}
			}
		}

		return $items;
	}

	/**
	 * Extract title, description, and icon from multiple legacy field key variants.
	 *
	 * @param array $data Raw array row.
	 * @return array|false Formatted item or false if empty.
	 */
	private function extract_item_fields( $data ) {
		$title = '';
		if ( isset( $data['title'] ) ) {
			$title = $data['title'];
		} elseif ( isset( $data['custom_accordions_pro_title'] ) ) {
			$title = $data['custom_accordions_pro_title'];
		}

		$details = '';
		if ( isset( $data['description'] ) ) {
			$details = $data['description'];
		} elseif ( isset( $data['custom_accordions_pro_details'] ) ) {
			$details = $data['custom_accordions_pro_details'];
		}

		// Backward compatible support for old dropdown/icon fields
		$icon = 'fa fa-home';
		if ( isset( $data['icon'] ) ) {
			$icon = $data['icon'];
		} elseif ( isset( $data['field-14'] ) ) {
			$icon = $data['field-14'];
		}

		if ( '' !== $title || '' !== $details ) {
			return array(
				'title'       => $title,
				'description' => $details,
				'icon'        => $icon,
			);
		}

		return false;
	}

	/**
	 * Render Individual Item Markup.
	 *
	 * @param string|int $index          Index key.
	 * @param array      $item           Item details.
	 * @param int        $display_number Display position (1-based index).
	 * @return void
	 */
	private function render_item( $index, $item, $display_number = 1 ) {
		$title   = isset( $item['title'] ) ? $item['title'] : '';
		$content = isset( $item['description'] ) ? $item['description'] : '';
		$icon    = isset( $item['icon'] ) ? $item['icon'] : ( isset( $item['field-14'] ) ? $item['field-14'] : 'fas fa-home' );

		$editor_id = 'tcaccordion_editor_' . sanitize_html_class( $index );
		$display_title = 'Tab ' . (int) $display_number . ': ' . ( $title ? $title : __( 'New Accordion', 'tptabs-ultimate' ) );
		?>
		<div class="tcaccordion-item">
			<div class="tcaccordion-header">
				<span class="dashicons dashicons-menu"></span>
				<strong class="tcaccordion-label">
					<?php echo esc_html( $display_title ); ?>
				</strong>
				<!-- New Duplicate Icon Button -->

				<button type="button" class="button-link tcaccordion-toggle">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
				</button>
			</div>

			<div class="tcaccordion-body">
				<!-- Title & Icon Side-by-Side Wrapper -->
				<div class="tc-title-icon-row" style="display: flex; gap: 15px; align-items: flex-end; margin-bottom: 12px;">
					<!-- Tab Title (Takes available space) -->
					<div style="flex-grow: 1; margin-bottom: 0;">
						<label><strong><?php esc_html_e( 'Tab Title', 'tptabs-ultimate' ); ?></strong></label>
						<input
							type="text"
							class="widefat tcaccordion-title"
							name="<?php echo esc_attr( self::META_KEY ); ?>[<?php echo esc_attr( $index ); ?>][title]"
							value="<?php echo esc_attr( $title ); ?>"
							style="margin-top: 5px;"
						>
					</div>

					<!-- Icon Picker (Small compact area on the right side) -->
					<div style="width: 150px; margin-bottom: 0; text-align: right;">
						<label style="display: block; text-align: left;"><strong><?php esc_html_e( 'Tab Icon', 'tptabs-ultimate' ); ?></strong></label>
						<div style="display: flex; align-items: center; gap: 15px; margin-top: 5px; background: #f0f0f1; padding: 8px 8px; border: 1px solid #ccd0d4; border-radius: 4px;">
							<span class="tc-selected-icon-preview" style="font-size: 16px; min-width: 20px; text-align: center;">
								<i class="<?php echo esc_attr( $icon ); ?>"></i>
							</span>
							<input type="hidden" class="tc-icon-input-field" name="<?php echo esc_attr( self::META_KEY ); ?>[<?php echo esc_attr( $index ); ?>][icon]" value="<?php echo esc_attr( $icon ); ?>">
							<input type="hidden" name="<?php echo esc_attr( self::META_KEY ); ?>[<?php echo esc_attr( $index ); ?>][field-14]" value="<?php echo esc_attr( $icon ); ?>">
							<button type="button" class="button button-small tc-open-icon-picker" style="width: 100%; margin: 0;"><?php esc_html_e( 'Select', 'tptabs-ultimate' ); ?></button>
						</div>
					</div>
				</div>

				<p>
					<label><strong><?php esc_html_e( 'Description', 'tptabs-ultimate' ); ?></strong></label>
					<?php
						wp_editor(
							$content,
							$editor_id,
							array(
								'textarea_name' => self::META_KEY . '[' . $index . '][description]',
								'textarea_rows' => 6,
								'media_buttons' => false,
								'teeny'         => true,
								'quicktags'     => true,
							)
						);
					?>
				</p>

				<p>
					<button type="button" class="button tcaccordion-remove">
						<?php esc_html_e( 'Remove', 'tptabs-ultimate' ); ?>
					</button>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Combined Save Handler (Group Fields + Style Settings)
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post Object.
	 * @return void
	 */
	public function save_metabox( $post_id, $post ) {
		if (
			! isset( $_POST['tcaccordion_nonce_field'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tcaccordion_nonce_field'] ) ), 'tptabsfree_save_action' )
		) {
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// 1. SAVE REPEATER / GROUP ITEMS
		if ( empty( $_POST[ self::META_KEY ] ) || ! is_array( $_POST[ self::META_KEY ] ) ) {
			delete_post_meta( $post_id, self::META_KEY );
		} else {
			$items            = array();
			$max_limit        = $this->get_max_items();
			$raw_posted_items = wp_unslash( $_POST[ self::META_KEY ] );

			foreach ( array_values( $raw_posted_items ) as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}

				if ( count( $items ) >= $max_limit ) {
					break;
				}

				$title   = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
				$details = isset( $item['description'] ) ? wp_kses_post( $item['description'] ) : '';
				
				// Save both modern icon and legacy field-14 key to secure all historical values
				$icon    = isset( $item['icon'] ) ? sanitize_text_field( $item['icon'] ) : ( isset( $item['field-14'] ) ? sanitize_text_field( $item['field-14'] ) : 'fa fa-home' );

				if ( '' !== $title || '' !== $details ) {
					$items[] = array(
						'title'       => $title,
						'description' => $details,
						'icon'        => $icon,
						'field-14'    => $icon,
					);
				}
			}

			delete_post_meta( $post_id, self::META_KEY );
			if ( ! empty( $items ) ) {
				update_post_meta( $post_id, self::META_KEY, $items );
			}
		}

		// Save Active Tab State
		if ( isset( $_POST['tc_active_tab_input'] ) ) {
			$active_tab = sanitize_text_field( wp_unslash( $_POST['tc_active_tab_input'] ) );
			update_post_meta( $post_id, '_tc_active_tab', $active_tab );
		}

		// 2. SAVE ACCORDION STYLE & OPTION METAS
		$style_fields = array(
			'tp_custom_tabultimate_shortcode_tabs_themes'    => 'sanitize_text_field',
			'custom_tabultimate_shortcode_title_font_size'   => 'sanitize_text_field',
			'custom_tabultimate_shortcode_title_font_color'  => 'sanitize_hex_color',
			'custom_tabsfree_title_active_color'             => 'sanitize_hex_color',
			'custom_tabsfree_title_active_bg_color'          => 'sanitize_hex_color',
			'custom_tabsfree_title_active_hv_color'          => 'sanitize_hex_color',
			'custom_tabsfree_title_active_hvtext_color'      => 'sanitize_hex_color',
			'custom_tabsfree_tab_border_color'               => 'sanitize_hex_color',
			'custom_tabsfree_content_font_size'              => 'sanitize_text_field',
			'custom_accordion_content_padding'               => 'sanitize_text_field',
			'custom_tabsfree_show_hide_icons'                => 'sanitize_text_field',
			'custom_tabsfree_icon_color'                     => 'sanitize_text_field',
			'custom_tabsfree_icon_font_size'                 => 'sanitize_text_field',
			'custom_tabsfree_title_line_height'              => 'sanitize_text_field',
			'custom_tabsfree_content_line_height'            => 'sanitize_text_field',
			'custom_accordion_content_bg_color'              => 'sanitize_hex_color',
			'custom_accordion_content_font_color'            => 'sanitize_hex_color',
		);

		foreach ( $style_fields as $field => $sanitize_callback ) {
			if ( isset( $_POST[ $field ] ) ) {
				$val = call_user_func( $sanitize_callback, $_POST[ $field ] );
				update_post_meta( $post_id, $field, $val );
			}
		}

		if ( isset( $_POST['tp_custom_tabultimate_shortcode_tabs_alignment'] ) ) {
		    update_post_meta( $post_id, 'tp_custom_tabultimate_shortcode_tabs_alignment', sanitize_text_field( $_POST['tp_custom_tabultimate_shortcode_tabs_alignment'] ) );
		}

		if ( isset( $_POST['_tp_custom_tabultimate_shortcode_tabs_animspeed'] ) ) {
	        update_post_meta( $post_id, '_tp_custom_tabultimate_shortcode_tabs_animspeed', absint( $_POST['_tp_custom_tabultimate_shortcode_tabs_animspeed'] ) );
	    }

	    if ( isset( $_POST['custom_tabultimate_shortcode_in_transition'] ) ) {
	        update_post_meta( $post_id, 'custom_tabultimate_shortcode_in_transition', sanitize_text_field( $_POST['custom_tabultimate_shortcode_in_transition'] ) );
	    }

		if ( isset( $_POST['_tp_custom_tabultimate_shortcode_tabs_autoheight'] ) ) {
	        $sanitized_value = sanitize_text_field( $_POST['_tp_custom_tabultimate_shortcode_tabs_autoheight'] );
	        update_post_meta( $post_id, '_tp_custom_tabultimate_shortcode_tabs_autoheight', $sanitized_value );
	    }

		if ( isset( $_POST['tp_custom_tabultimate_shortcode_tabs_positions'] ) ) {
	        update_post_meta( $post_id, 'tp_custom_tabultimate_shortcode_tabs_positions', sanitize_text_field( $_POST['tp_custom_tabultimate_shortcode_tabs_positions'] ) );
	    }
	    
	    if ( isset( $_POST['tp_custom_tabultimate_shortcode_use_hash'] ) ) {
	        update_post_meta( $post_id, 'tp_custom_tabultimate_shortcode_use_hash', sanitize_text_field( $_POST['tp_custom_tabultimate_shortcode_use_hash'] ) );
	    }

        if ( isset( $_POST['tp_custom_tabultimate_shortcode_tabs_activated'] ) ) {
            $auto_open = absint( $_POST['tp_custom_tabultimate_shortcode_tabs_activated'] );
            update_post_meta( $post_id, 'tp_custom_tabultimate_shortcode_tabs_activated', $auto_open );
        }

		if ( isset( $_POST['_tp_custom_tabultimate_shortcode_tabs_openhover'] ) ) {
		    $event = sanitize_text_field( wp_unslash( $_POST['_tp_custom_tabultimate_shortcode_tabs_openhover'] ) );
		    update_post_meta( $post_id, '_tp_custom_tabultimate_shortcode_tabs_openhover', in_array( $event, array( 'click', 'hover' ), true ) ? $event : 'click' );
		}

		if ( isset( $_POST['tp_custom_tabultimate_shortcode_tabs_style'] ) ) {
		    update_post_meta( $post_id, 'tp_custom_tabultimate_shortcode_tabs_style', sanitize_text_field( $_POST['tp_custom_tabultimate_shortcode_tabs_style'] ) );
		}

		if ( isset( $_POST['custom_tabsfree_tab_icon_position'] ) ) {
		    update_post_meta( $post_id, 'custom_tabsfree_tab_icon_position', sanitize_text_field( $_POST['custom_tabsfree_tab_icon_position'] ) );
		}
	}

	/**
	 * Custom Admin Post Columns
	 */
	public function register_admin_columns( $columns ) {
		return array(
			'cb'          => '<input type="checkbox" />',
			'title'       => __( 'Shortcode Name', 'tptabs-ultimate' ),
			'shortcode'   => __( 'Shortcode', 'tptabs-ultimate' ),
			'doshortcode' => __( 'Template Shortcode', 'tptabs-ultimate' ),
			'date'        => __( 'Date', 'tptabs-ultimate' ),
		);
	}

	/**
	 * Display Admin Post Columns Content
	 */
	public function render_admin_columns( $column, $post_id ) {
		if ( 'shortcode' === $column ) {
			echo '<input style="background:#ddd" type="text" readonly onClick="this.select();" value="[tabsprofree id=&quot;' . esc_attr( $post_id ) . '&quot;]" />';
		}
		if ( 'doshortcode' === $column ) {
			echo '<textarea cols="40" rows="2" style="background:#ddd;" readonly onClick="this.select();">&lt;?php echo do_shortcode("[tabsprofree id=\'' . esc_attr( $post_id ) . '\']"); ?&gt;</textarea>';
		}
	}

	/**
	 * Display Shortcode Notice Below Title Input
	 */
	public function render_shortcode_section( $post ) {
		if ( 'tp_tab_pro' !== $post->post_type ) {
			return;
		}

		$shortcode = "[tabsprofree id='" . $post->ID . "']";
		$php_code  = '<?php echo do_shortcode("[tabsprofree id=' . $post->ID . ']"); ?>';
		?>
		<div style="padding: 15px; border: 1px solid #ddd; background: #f9f9f9; margin-top: 15px;">
			<div style="display: flex; gap: 20px;">
				<div style="width: 50%;">
					<p><strong><?php esc_html_e( 'Shortcode', 'tptabs-ultimate' ); ?>:</strong> <span id="tc-sc-notice" style="color: green; display: none; margin-left: 10px;"><?php esc_html_e( 'Copied!', 'tptabs-ultimate' ); ?></span></p>
					<input type="text" style="width:100%; cursor:pointer;" value="<?php echo esc_attr( $shortcode ); ?>" readonly onclick="tcCopyNotice(this, 'tc-sc-notice')" />
				</div>
				<div style="width: 50%;">
					<p><strong><?php esc_html_e( 'PHP Code', 'tptabs-ultimate' ); ?>:</strong> <span id="tc-php-notice" style="color: green; display: none; margin-left: 10px;"><?php esc_html_e( 'Copied!', 'tptabs-ultimate' ); ?></span></p>
					<input type="text" style="width:100%; cursor:pointer;" value="<?php echo esc_attr( $php_code ); ?>" readonly onclick="tcCopyNotice(this, 'tc-php-notice')" />
				</div>
			</div>
		</div>
		<script>
			function tcCopyNotice(input, noticeId) {
				input.select();
				navigator.clipboard.writeText(input.value);
				var notice = document.getElementById(noticeId);
				notice.style.display = "inline";
				setTimeout(function() { notice.style.display = "none"; }, 2000);
			}
		</script>
		<?php
	}

	/**
	 * Change Post Title Placeholder
	 */
	public function tptabsfree_change_title_placeholder( $title ) {
		$screen = get_current_screen();
		if ( $screen && 'tp_tab_pro' === $screen->post_type ) {
			return __( 'Tabs Group Title', 'tptabs-ultimate' );
		}
		return $title;
	}

	/**
	 * AJAX Handler to generate exact live frontend markup from unsaved form data.
	 */
	public function ajax_render_live_preview() {
	    check_ajax_referer( 'tptabsfree_save_action', 'security' );

	    if ( ! current_user_can( 'edit_posts' ) ) {
	        wp_send_json_error( array( 'message' => __( 'Permission denied.', 'tptabs-ultimate' ) ) );
	    }

	    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

	    $posted_meta = array();
	    if ( ! empty( $_POST['form_data'] ) ) {
	        wp_parse_str( $_POST['form_data'], $posted_meta );
	    }

	    $renderer = new TPTabs_Renderer( $post_id );

	    if ( ! empty( $posted_meta ) && method_exists( $renderer, 'set_preview_data' ) ) {
	        $renderer->set_preview_data( $posted_meta );
	    }

	    $html = $renderer->render();

	    wp_send_json_success( array( 'html' => $html ) );
	}

	/**
     * Enqueue Admin Scripts & Styles for Preview
     */
    public function enqueue_admin_preview_styles( $hook ) {
        if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
            return;
        }

        $screen = get_current_screen();
        if ( is_object( $screen ) && 'tp_tab_pro' === $screen->post_type ) {
			wp_enqueue_style(
				'tptabsfree-tabs-engine',
				TPTABSFREE_PLUGIN_URL . 'assets/css/tp-tabs-engine.css',
				array(),
				defined( 'TPTABSFREE_VERSION' ) ? TPTABSFREE_VERSION : '3.0.8'
			);

			wp_enqueue_script(
				'tptabsfree-tabs-engine',
				TPTABSFREE_PLUGIN_URL . 'assets/js/tp-tabs-engine.js',
				array( 'jquery' ),
				defined( 'TPTABSFREE_VERSION' ) ? TPTABSFREE_VERSION : '3.0.8',
				true
			);
        }
    }
}

new TPTabs_Metabox();