<?php
    if( !defined( 'ABSPATH' ) ){
        exit;
    }

	// Register Post Type
	function free_tp_custom_tabultimate_shortcode_post_register() {
		$labels = array(
			'name'               => _x('Tabs Free', 'post type general name'),
			'singular_name'      => _x('Tab', 'post type singular name'),
			'add_new'            => _x('Add New Tab', 'tp_tabs_pro'),
			'add_new_item'       => __('Add New Tab'),
			'edit_item'          => __('Edit Tab'),
			'new_item'           => __('New Tab'),
			'view_item'          => __('View Tab'),
			'search_items'       => __('Search Tab'),
			'not_found'          =>  __('Nothing found'),
			'not_found_in_trash' => __('Nothing found in Trash'),
			'parent_item_colon'  => ''
		);
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'query_var'          => true,
			'menu_icon'          => null,
			'rewrite'            => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array('title'),
			'menu_icon'          => 'dashicons-welcome-add-page',
		  );
		register_post_type( 'tp_tab_pro' , $args );
	}
	add_action('init', 'free_tp_custom_tabultimate_shortcode_post_register');
	
	// Manage Shortcode Column
	function free_tp_custom_tabultimate_add_shortcode_column( $columns ) {
		return array_merge(
			$columns, 
			array( 'shortcode' => __( 'Shortcode', 'tpaccordions' ) )
		);
	}
	add_filter( 'manage_tp_tab_pro_posts_columns' , 'free_tp_custom_tabultimate_add_shortcode_column' );

	// Update Publish Button Text
	function free_tp_custom_tabultimate_add_change_publish_button( $translation, $text ) {
		if ( 'tp_tab_pro' == get_post_type()){
			if ( $text == 'Publish' ){
				return 'Publish Tab';
			}
		}
		return $translation;
	}
	add_filter( 'gettext', 'free_tp_custom_tabultimate_add_change_publish_button', 10, 2 );

	// Manage Shortcode Column
	function free_tp_custom_tabultimate_add_posts_shortcode_display( $column, $post_id ) {
		if ($column == 'shortcode'){
			?>
			<input style="background:#ddd" type="text" onClick="this.select();" value="[tabsprofree <?php echo 'id=&quot;'.$post_id.'&quot;';?>]" />
			<?php
		}
	}
	add_action( 'manage_tp_tab_pro_posts_custom_column' , 'free_tp_custom_tabultimate_add_posts_shortcode_display', 10, 2 );
	
	// Adds a box to the main column on the Post and Page edit screens
	function free_tp_custom_tabultimate_shortcode_add_custom_box() {
		$screens = array( 'tp_tab_pro' );
		foreach ( $screens as $screen ) {
			add_meta_box('tabultimate_sectionid', __( 'Tab Settings','tp_tabs_pro' ),'free_tp_custom_tabultimate_shortcode_inner_custom_m_box', $screen);
		}
	}
	add_action( 'add_meta_boxes', 'free_tp_custom_tabultimate_shortcode_add_custom_box' );

	// Prints the box content 
	function free_tp_custom_tabultimate_shortcode_inner_custom_m_box() {
		global $post;
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'free_tp_custom_tabultimate_shortcode_inner_m_boxes' );

		//get the saved meta as an arry
		$tp_custom_tabultimate_shortcode_tabs_themes    = get_post_meta( $post->ID, 'tp_custom_tabultimate_shortcode_tabs_themes', true );
		$tp_custom_tabultimate_shortcode_tabs_activated = get_post_meta( $post->ID, 'tp_custom_tabultimate_shortcode_tabs_activated', true );
		$tp_custom_tabultimate_shortcode_tabs_positions = get_post_meta( $post->ID, 'tp_custom_tabultimate_shortcode_tabs_positions', true );
		$tp_custom_tabultimate_shortcode_tabs_openhover = get_post_meta( $post->ID, 'tp_custom_tabultimate_shortcode_tabs_openhover', true );
		$custom_tabultimate_shortcode_in_transition     = get_post_meta( $post->ID, 'custom_tabultimate_shortcode_in_transition', true );
		$custom_tabultimate_shortcode_out_transition    = get_post_meta( $post->ID, 'custom_tabultimate_shortcode_out_transition', true );
		$custom_tabultimate_shortcode_title_font_color  = get_post_meta( $post->ID, 'custom_tabultimate_shortcode_title_font_color', true );
		$custom_tabultimate_shortcode_active_font_color = get_post_meta( $post->ID, 'custom_tabultimate_shortcode_active_font_color', true );
		$custom_tabultimate_shortcode_title_font_size   = get_post_meta( $post->ID, 'custom_tabultimate_shortcode_title_font_size', true );
		$custom_tabultimate_shortcode_active_bg_color   = get_post_meta( $post->ID, 'custom_tabultimate_shortcode_active_bg_color', true );
		$custom_tabultimate_shortcode_content_bg_color  = get_post_meta( $post->ID, 'custom_tabultimate_shortcode_content_bg_color', true );
		
		?>
		<div class="tupsetings post-grid-metabox">
			<div class="wrap">
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="tp_custom_tabultimate_shortcode_tabs_themes"><?php echo __('Tab Style:', 'tp_tabs_pro'); ?></label>
						</th>
						<td style="vertical-align: middle;">
							<select class="timezone_string" name="tp_custom_tabultimate_shortcode_tabs_themes">
								<option value="theme1" <?php if($tp_custom_tabultimate_shortcode_tabs_themes=='theme1') echo "selected"; ?> ><?php echo __('Default:', 'tp_tabs_pro'); ?></option>
							</select><br/>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="tp_custom_tabultimate_shortcode_tabs_activated"><?php echo __('Active Tab:', 'tp_tabs_pro'); ?></label>
						</th>
						<td style="vertical-align:middle;">
							<select class="timezone_string" name="tp_custom_tabultimate_shortcode_tabs_activated">
								<option value="1" <?php if($tp_custom_tabultimate_shortcode_tabs_activated=='1') echo "selected"; ?> >1</option>
								<option value="2" <?php if($tp_custom_tabultimate_shortcode_tabs_activated=='2') echo "selected"; ?> >2</option>
								<option value="3" disabled <?php if($tp_custom_tabultimate_shortcode_tabs_activated=='3') echo "selected"; ?> >3 (Only For Premium)</option>
								<option value="4" disabled <?php if($tp_custom_tabultimate_shortcode_tabs_activated=='4') echo "selected"; ?> >4 (Only For Premium)</option>
								<option value="5" disabled <?php if($tp_custom_tabultimate_shortcode_tabs_activated=='5') echo "selected"; ?>>5 (Only For Premium)</option>
								<option value="6" disabled <?php if($tp_custom_tabultimate_shortcode_tabs_activated=='6') echo "selected"; ?>>6 (Only For Premium)</option>
								<option value="7" disabled <?php if($tp_custom_tabultimate_shortcode_tabs_activated=='7') echo "selected"; ?>>7 (Only For Premium)</option>
								<option value="8" disabled <?php if($tp_custom_tabultimate_shortcode_tabs_activated=='8') echo "selected"; ?>>8 (Only For Premium)</option>
								<option value="9" disabled <?php if($tp_custom_tabultimate_shortcode_tabs_activated=='9') echo "selected"; ?>>9 (Only For Premium)</option>
								<option value="10" disabled <?php if($tp_custom_tabultimate_shortcode_tabs_activated=='10') echo "selected"; ?>>10 (Only For Premium)</option>
							</select><br/>
							<span class="tp_accordions_pro_hint"><?php echo __('Choose which tab to display initially. <span style="color:red;">(Upgrade Pro)</span>', 'tp_tabs_pro'); ?></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="tp_custom_tabultimate_shortcode_tabs_positions"><?php echo __('Tab Position:', 'tp_tabs_pro'); ?></label>
						</th>
						<td style="vertical-align:middle;">
							<select class="timezone_string" name="tp_custom_tabultimate_shortcode_tabs_positions">
								<option value="top" <?php if($tp_custom_tabultimate_shortcode_tabs_positions=='top') echo "selected"; ?> >Top</option>
								<option value="bottom" disabled <?php if($tp_custom_tabultimate_shortcode_tabs_positions=='bottom') echo "selected"; ?> >Bottom (Only For Premium)</option>
							</select><br/>
							<span class="tp_accordions_pro_hint"><?php echo __('Tab Menu Position (Top/Bottom). Default : Top  <span style="color:red;">(Upgrade Pro)</span>', 'tp_tabs_pro'); ?></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="tp_custom_tabultimate_shortcode_tabs_openhover"><?php echo __('Tab Open On Hover:', 'tp_tabs_pro'); ?></label>
						</th>
						<td style="vertical-align:middle;">
							<select class="timezone_string" name="tp_custom_tabultimate_shortcode_tabs_openhover">
								<option value="false" <?php if($tp_custom_tabultimate_shortcode_tabs_openhover=='false') echo "selected"; ?> >False</option>
								<option value="true" disabled <?php if($tp_custom_tabultimate_shortcode_tabs_openhover=='true') echo "selected"; ?> >True (Only For Premium)</option>
							</select><br/>
							<span class="tp_accordions_pro_hint"><?php echo __('Tab open on hover (true/false). Default : false  <span style="color:red;">(Upgrade Pro)</span>', 'tp_tabs_pro'); ?></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="custom_tabultimate_shortcode_in_transition"><?php echo __('Tab Transition In:', 'tp_tabs_pro'); ?></label>
						</th>
						<td style="vertical-align:middle;">
							<select class="timezone_string" name="custom_tabultimate_shortcode_in_transition">
								<option value="fadeIn" <?php if($custom_tabultimate_shortcode_in_transition=='fadeIn') echo "selected"; ?> >fadeIn</option>
								<option value="slideDownIn" <?php if($custom_tabultimate_shortcode_in_transition=='slideDownIn') echo "selected"; ?> >slideDownIn</option>
								<option value="slideUpIn" <?php if($custom_tabultimate_shortcode_in_transition=='slideUpIn') echo "selected"; ?> >slideUpIn</option>
								<option value="slideRightScaleDownIn" disabled <?php if($custom_tabultimate_shortcode_in_transition=='slideRightScaleDownIn') echo "selected"; ?> >slideRightScaleDownIn</option>
								<option value="slideLeftIn" disabled <?php if($custom_tabultimate_shortcode_in_transition=='slideLeftIn') echo "selected"; ?> >slideLeftIn</option>
								<option value="slideRightIn" disabled <?php if($custom_tabultimate_shortcode_in_transition=='slideRightIn') echo "selected"; ?> >slideRightIn</option>
								<option value="flipIn" disabled <?php if($custom_tabultimate_shortcode_in_transition=='flipIn') echo "selected"; ?> >flipIn</option>
								<option value="rotateIn" disabled <?php if($custom_tabultimate_shortcode_in_transition=='rotateIn') echo "selected"; ?> >rotateIn</option>
								<option value="swingRightIn" disabled <?php if($custom_tabultimate_shortcode_in_transition=='swingRightIn') echo "selected"; ?> >swingRightIn</option>
								<option value="swingLeftIn" disabled <?php if($custom_tabultimate_shortcode_in_transition=='swingLeftIn') echo "selected"; ?> >swingLeftIn</option>
								<option value="scaleDownIn" disabled <?php if($custom_tabultimate_shortcode_in_transition=='scaleDownIn') echo "selected"; ?> >scaleDownIn</option>
								<option value="scaleUpIn" disabled <?php if($custom_tabultimate_shortcode_in_transition=='scaleUpIn') echo "selected"; ?> >scaleUpIn</option>
							</select><br/>
							<span class="tp_accordions_pro_hint">Tab Transition In. <span style="color:red;">(Upgrade Pro)</span></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="custom_tabultimate_shortcode_out_transition"><?php echo __('Tab Transition Out:', 'tp_tabs_pro'); ?></label>
						</th>
						<td style="vertical-align:middle;">
							<select class="timezone_string" name="custom_tabultimate_shortcode_out_transition">
								<option value="fadeOut" <?php if($custom_tabultimate_shortcode_out_transition=='fadeOut') echo "selected"; ?> >fadeOut</option>
								<option value="slideDownOut" <?php if($custom_tabultimate_shortcode_out_transition=='slideDownOut') echo "selected"; ?> >slideDownOut</option>
								<option value="slideUpOut" <?php if($custom_tabultimate_shortcode_out_transition=='slideUpOut') echo "selected"; ?> >slideUpOut</option>
								<option value="slideRightScaleDownOut" disabled <?php if($custom_tabultimate_shortcode_out_transition=='slideRightScaleDownOut') echo "selected"; ?> >slideRightScaleDownOut</option>
								<option value="slideLeftOut" disabled <?php if($custom_tabultimate_shortcode_out_transition=='slideLeftOut') echo "selected"; ?> >slideLeftOut</option>
								<option value="slideRightOut" disabled <?php if($custom_tabultimate_shortcode_out_transition=='slideRightOut') echo "selected"; ?> >slideRightOut</option>
								<option value="flipOut" disabled <?php if($custom_tabultimate_shortcode_out_transition=='flipOut') echo "selected"; ?> >flipOut</option>
								<option value="rotateOut" disabled <?php if($custom_tabultimate_shortcode_out_transition=='rotateOut') echo "selected"; ?> >rotateOut</option>
								<option value="swingRightOut" disabled <?php if($custom_tabultimate_shortcode_out_transition=='swingRightOut') echo "selected"; ?> >swingRightOut</option>
								<option value="swingLeftOut" disabled <?php if($custom_tabultimate_shortcode_out_transition=='swingLeftOut') echo "selected"; ?> >swingLeftOut</option>
								<option value="scaleDownOut" disabled <?php if($custom_tabultimate_shortcode_out_transition=='scaleDownOut') echo "selected"; ?> >scaleDownOut</option>
								<option value="scaleUpOut" disabled <?php if($custom_tabultimate_shortcode_out_transition=='scaleUpOut') echo "selected"; ?> >scaleUpOut</option>
							</select>
							<br/>
							<span class="tp_accordions_pro_hint">Tab Transition Out. <span style="color:red;">(Upgrade Pro)</span></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="custom_tabultimate_shortcode_title_font_size"><?php _e( 'Tab Title Font Size:', 'tp_tabs_pro' ); ?></label>
						</th>
						<td style="vertical-align: middle;">
							<input type="number" name="custom_tabultimate_shortcode_title_font_size" id="custom_tabultimate_shortcode_title_font_size" min="10" class="timezone_string" required value="<?php  if($custom_tabultimate_shortcode_title_font_size !=''){echo $custom_tabultimate_shortcode_title_font_size; }else{ echo '15';} ?>">
							<br/>
							<span class="tp_accordions_pro_hint toss"><?php echo __('Set Tab Title Font Size.', 'tp_tabs_pro'); ?></span>
						</td>
					</tr><!-- End Tab Title Font Size-->

					<tr valign="top">
						<th scope="row">
							<label for="custom_tabultimate_shortcode_title_font_color"><?php echo __('Tab Title Font Color:', 'tp_tabs_pro'); ?></label>
						</th>
						<td style="vertical-align:middle;">
							<input  size='7' name='custom_tabultimate_shortcode_title_font_color' class='custom-tab-columns-font-color' id="custom-tab-columns-title-fonts-color" type='text' value='<?php echo sanitize_text_field($custom_tabultimate_shortcode_title_font_color) ?>' />
							<br/>
							<span class="tp_accordions_pro_hint"><?php echo __('Select Tab Title Font Color.', 'tp_tabs_pro'); ?></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="custom_tabultimate_shortcode_active_font_color"><?php echo __('Active Tab Font Color:', 'tp_tabs_pro'); ?></label>
						</th>
						<td style="vertical-align:middle;">
							<input  size='7' name='custom_tabultimate_shortcode_active_font_color' class='custom-tab-title-active-font-color' id="custom-tab-title-active-font-color" type='text' value='<?php echo sanitize_text_field($custom_tabultimate_shortcode_active_font_color) ?>' />
							<br/>
							<span class="tp_accordions_pro_hint"><?php echo __('Select Active Tab Font Color.', 'tp_tabs_pro'); ?></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="custom_tabultimate_shortcode_active_bg_color"><?php echo __('Active Tab BG Color:', 'tp_tabs_pro'); ?></label>
						</th>
						<td style="vertical-align:middle;">
							<input  size='7' name='custom_tabultimate_shortcode_active_bg_color' class='custom-tab-active-bg-color' id="custom-tab-active-bg-color" type='text' value='<?php echo sanitize_text_field($custom_tabultimate_shortcode_active_bg_color) ?>' />
							<br/>
							<span class="tp_accordions_pro_hint"><?php echo __('Select Active Tab Background Color.', 'tp_tabs_pro'); ?></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="custom_tabultimate_shortcode_content_bg_color"><?php echo __('Tab Content Bg Color:', 'tp_tabs_pro'); ?></label>
						</th>
						<td style="vertical-align:middle;">
							<input  size='7' name='custom_tabultimate_shortcode_content_bg_color' class='custom-tab-content-bg-color' id="custom-tab-content-bg-color" type='text' value='<?php echo sanitize_text_field($custom_tabultimate_shortcode_content_bg_color) ?>' />
							<br/>
							<span class="tp_accordions_pro_hint"><?php echo __('Select Tab Content Background Color.', 'tp_tabs_pro'); ?></span>
						</td>
					</tr>

				</table>
			</div>
		</div>
		<?php
	}

	// When the post is saved, saves our custom data
	function free_tp_custom_tabultimate_shortcode_inner_save_postdata( $post_id ) {

		// Doing autosave then return.
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return;
	    }

	    // Check if current user has permission to edit the post
	    if ( ! current_user_can( 'edit_post', $post_id ) ) {
	        return;
	    }

		// verify this came from the our screen and with proper authorization,
		if ( ! isset( $_POST['free_tp_custom_tabultimate_shortcode_inner_m_boxes'] ) ) {
		    return;
		}

		if ( ! wp_verify_nonce( $_POST['free_tp_custom_tabultimate_shortcode_inner_m_boxes'], plugin_basename( __FILE__ ) ) ) {
		    return;
		}

		// OK, we're authenticated: we need to find and save the data

		#Checks for input and sanitizes/saves if needed
		if ( isset( $_POST[ 'tp_custom_tabultimate_shortcode_tabs_themes' ] ) ) {
			$tp_custom_tabultimate_shortcode_tabs_themes = sanitize_text_field( $_POST['tp_custom_tabultimate_shortcode_tabs_themes'] );
			update_post_meta( $post_id, 'tp_custom_tabultimate_shortcode_tabs_themes', $tp_custom_tabultimate_shortcode_tabs_themes );
		}

		#Checks for input and sanitizes/saves if needed
		if ( isset( $_POST[ 'tp_custom_tabultimate_shortcode_tabs_activated' ] ) ) {
			$tp_custom_tabultimate_shortcode_tabs_activated = sanitize_text_field( $_POST['tp_custom_tabultimate_shortcode_tabs_activated'] );
			update_post_meta( $post_id, 'tp_custom_tabultimate_shortcode_tabs_activated', $tp_custom_tabultimate_shortcode_tabs_activated );
		}

		#Checks for input and sanitizes/saves if needed
		if ( isset( $_POST[ 'tp_custom_tabultimate_shortcode_tabs_positions' ] ) ) {
			$tp_custom_tabultimate_shortcode_tabs_positions = sanitize_text_field( $_POST['tp_custom_tabultimate_shortcode_tabs_positions'] );
			update_post_meta( $post_id, 'tp_custom_tabultimate_shortcode_tabs_positions', $tp_custom_tabultimate_shortcode_tabs_positions );
		}

		#Checks for input and sanitizes/saves if needed
		if ( isset( $_POST[ 'tp_custom_tabultimate_shortcode_tabs_openhover' ] ) ) {
			$tp_custom_tabultimate_shortcode_tabs_openhover = sanitize_text_field( $_POST['tp_custom_tabultimate_shortcode_tabs_openhover'] );
			update_post_meta( $post_id, 'tp_custom_tabultimate_shortcode_tabs_openhover', $tp_custom_tabultimate_shortcode_tabs_openhover );
		}

		#Checks for input and sanitizes/saves if needed
		if ( isset( $_POST[ 'custom_tabultimate_shortcode_out_transition' ] ) ) {
			$custom_tabultimate_shortcode_out_transition = sanitize_text_field( $_POST['custom_tabultimate_shortcode_out_transition'] );
			update_post_meta( $post_id, 'custom_tabultimate_shortcode_out_transition', $custom_tabultimate_shortcode_out_transition );
		}

		#Checks for input and sanitizes/saves if needed
		if ( isset( $_POST[ 'custom_tabultimate_shortcode_title_font_color' ] ) ) {
			$custom_tabultimate_shortcode_title_font_color = sanitize_hex_color( $_POST['custom_tabultimate_shortcode_title_font_color'] );
			update_post_meta( $post_id, 'custom_tabultimate_shortcode_title_font_color', $custom_tabultimate_shortcode_title_font_color );
		}

		#Checks for input and sanitizes/saves if needed
		if ( isset( $_POST[ 'custom_tabultimate_shortcode_active_font_color' ] ) ) {
			$custom_tabultimate_shortcode_active_font_color = sanitize_hex_color( $_POST['custom_tabultimate_shortcode_active_font_color'] );
			update_post_meta( $post_id, 'custom_tabultimate_shortcode_active_font_color', $custom_tabultimate_shortcode_active_font_color );
		}

		#Checks for input and sanitizes/saves if needed
		if ( isset( $_POST[ 'custom_tabultimate_shortcode_active_bg_color' ] ) ) {
			$custom_tabultimate_shortcode_active_bg_color = sanitize_hex_color( $_POST['custom_tabultimate_shortcode_active_bg_color'] );
			update_post_meta( $post_id, 'custom_tabultimate_shortcode_active_bg_color', $custom_tabultimate_shortcode_active_bg_color );
		}

		#Checks for input and sanitizes/saves if needed
		if ( isset( $_POST[ 'custom_tabultimate_shortcode_content_bg_color' ] ) ) {
			$custom_tabultimate_shortcode_content_bg_color = sanitize_hex_color( $_POST['custom_tabultimate_shortcode_content_bg_color'] );
			update_post_meta( $post_id, 'custom_tabultimate_shortcode_content_bg_color', $custom_tabultimate_shortcode_content_bg_color );
		}

		// Sanitize and save 'custom_tabultimate_shortcode_title_font_size' field
		if ( isset( $_POST['custom_tabultimate_shortcode_title_font_size'] ) ) {
		    $custom_tabultimate_shortcode_title_font_size = intval( $_POST['custom_tabultimate_shortcode_title_font_size'] );
		    update_post_meta( $post_id, 'custom_tabultimate_shortcode_title_font_size', $custom_tabultimate_shortcode_title_font_size );
		}
	}
	// Do something with the data entered
	add_action( 'save_post', 'free_tp_custom_tabultimate_shortcode_inner_save_postdata' );

	function free_tp_tabultimate_shortcode_section($post) {
	    // Show only for 'tp_tab_pro' post type
	    if ($post->post_type !== 'tp_tab_pro') {
	        return;
	    }

	    // Generate the dynamic shortcode
	    $shortcode = "[tabsprofree id='" . $post->ID . "']";
	    $php_code = '<?php echo do_shortcode("[tabsprofree id=' . $post->ID . ']"); ?>';

	    ?>
	    <div style="padding: 15px 15px 25px 15px; border: 1px solid #ddd; background: #f9f9f9; margin-top: 15px;">
		    <div style="display: flex; gap: 20px;">

			    <div style="width: 50%;">
			        <p>
			            <strong><?php _e( 'Shortcode','tp_tabs_pro' ); ?>:</strong>
			            <span id="shortcode-notice" style="color: green; display: none; margin-left: 10px;"><?php _e( 'Shortcode copied!','tp_tabs_pro' ); ?></span>
			        </p>
			        <p class="option-info"><?php _e('Click to copy the shortcode and paste it into a page or post to display Tab.','tp_tabs_pro' ); ?></p>
			        <input type="text" id="shortcode-text" style="width:100%; cursor:pointer; box-shadow: none; border:none;outline:none;border-radius: 0" value="<?php echo esc_attr($shortcode); ?>" readonly onclick="copyToClipboard(this, 'shortcode-notice')">
			    </div>

			    <div style="width: 50%;">
			        <p>
			            <strong><?php _e( 'PHP Code for Theme Files','tp_tabs_pro' ); ?>:</strong>
			            <span id="php-notice" style="color: green; display: none; margin-left: 10px;"><?php _e( 'PHP code copied!','tp_tabs_pro' ); ?></span>
			        </p>
			        <p class="option-info"><?php _e('Click to copy the PHP code and use it in your theme files to display Tab.','tp_tabs_pro' ); ?></p>
			        <input type="text" id="php-code-text" style="width:100%; cursor:pointer; box-shadow: none; border:none;outline:none;border-radius: 0" value="<?php echo esc_attr($php_code); ?>" readonly onclick="copyToClipboard(this, 'php-notice')">
			    </div>

		    </div>
	    </div>

	    <script>
	        function copyToClipboard(inputField, noticeId) {
	            inputField.select();
	            navigator.clipboard.writeText(inputField.value);

	            // Show copied message beside the label
	            var notice = document.getElementById(noticeId);
	            notice.style.display = "inline";

	            // Hide the message after 2 seconds
	            setTimeout(function() {
	                notice.style.display = "none";
	            }, 2000);
	        }
	    </script>
	    <?php
	}
	add_action('edit_form_after_title', 'free_tp_tabultimate_shortcode_section');
