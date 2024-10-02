<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function TP_tab_Free_Shortcode_ultimate_body($postid){
	
	$tcpfeaturess                                   = get_post_meta( $postid, 'custom_accordion_wordpresspro_columns');
	$tp_custom_tabultimate_shortcode_tabs_themes    = get_post_meta( $postid, 'tp_custom_tabultimate_shortcode_tabs_themes', true );
	$tp_custom_tabultimate_shortcode_tabs_activated = get_post_meta( $postid, 'tp_custom_tabultimate_shortcode_tabs_activated', true );
	$tp_custom_tabultimate_shortcode_tabs_positions = get_post_meta( $postid, 'tp_custom_tabultimate_shortcode_tabs_positions', true );
	$tp_custom_tabultimate_shortcode_tabs_openhover = get_post_meta( $postid, 'tp_custom_tabultimate_shortcode_tabs_openhover', true );
	$custom_tabultimate_shortcode_in_transition     = get_post_meta( $postid, 'custom_tabultimate_shortcode_in_transition', true );
	$custom_tabultimate_shortcode_out_transition    = get_post_meta( $postid, 'custom_tabultimate_shortcode_out_transition', true );

	$custom_tabultimate_shortcode_title_font_size   = get_post_meta( $postid, 'custom_tabultimate_shortcode_title_font_size', true );
	if(empty($custom_tabultimate_shortcode_title_font_size)) {
	$custom_tabultimate_shortcode_title_font_size   = "15";
	}
	
	$custom_tabultimate_shortcode_title_font_color  = get_post_meta( $postid, 'custom_tabultimate_shortcode_title_font_color', true );
	if(empty($custom_tabultimate_shortcode_title_font_color)) {
	$custom_tabultimate_shortcode_title_font_color  = "#21759b";
	}
	
	$custom_tabultimate_shortcode_active_font_color = get_post_meta( $postid, 'custom_tabultimate_shortcode_active_font_color', true );
	if(empty($custom_tabultimate_shortcode_active_font_color)) {
	$custom_tabultimate_shortcode_active_font_color = "#000";
	}		
	
	$custom_tabultimate_shortcode_active_bg_color   = get_post_meta( $postid, 'custom_tabultimate_shortcode_active_bg_color', true );
	if(empty($custom_tabultimate_shortcode_active_bg_color)) {
	$custom_tabultimate_shortcode_active_bg_color   = "#fff";
	}
	
	$custom_tabultimate_shortcode_content_bg_color  = get_post_meta( $postid, 'custom_tabultimate_shortcode_content_bg_color', true );
	if(empty($custom_tabultimate_shortcode_content_bg_color)) {
	$custom_tabultimate_shortcode_content_bg_color  = "#f6f6f6";
	}
	
	$tabmultiid = rand(1,1000);
	
	if( esc_attr($tp_custom_tabultimate_shortcode_tabs_themes ) =="theme1") {
		$logologcreate ='';
		$count = 0;
		$logologcreate .='
		<div id="tpultimatestabs-acitve-'.esc_attr( $tabmultiid ).'">
			<ul class="tabs">';
			foreach ($tcpfeaturess as $tcpfeature) {
			$logologcreate .='<li><a href="#tab1-'.esc_attr( $count ).'">';
			if ( !empty( $tcpfeature['field-14'] ) ){
				$logologcreate .='<i class="fa '.esc_attr( $tcpfeature['field-14'] ).'"></i>';
			}else{
				$logologcreate .='';
			}
			
			$logologcreate .=''.esc_html( $tcpfeature['custom_accordions_pro_title'] ).'';
			$logologcreate .='</a></li>';
			$count++;
			};

			$logologcreate .='
			</ul>
			<div class="tab_content_wrapper">';
			$count = 0;
			foreach ($tcpfeaturess as $tcpfeature) {
			$logologcreate .='<div class="tab_content" id="tab1-'.esc_attr( $count ).'">';
			$logologcreate .= do_shortcode(wpautop($tcpfeature['custom_accordions_pro_details']));
			$logologcreate .='</div>';
			$count++;
			};
			$logologcreate .='
			</div>
		</div>';
				
		$logologcreate.='
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$("#tpultimatestabs-acitve-'.esc_attr( $tabmultiid ).'").jQueryTab({
					initialTab:'.esc_attr( $tp_custom_tabultimate_shortcode_tabs_activated ).',
					cookieName: "active-tab-1",
					tabPosition : "top",
					collapsible:true,
					openOnhover: false,
					responsive:true,
					useCookie: false,
					tabInTransition: "'.esc_attr( $custom_tabultimate_shortcode_in_transition ).'",
					tabOutTransition: "'.esc_attr( $custom_tabultimate_shortcode_out_transition).'",
					accordionIntime:500,
					accordionOutTime:400
				});
			});
		</script>
		';

		$logologcreate.='
		<style type="text/css">
			#tpultimatestabs-acitve-'.esc_attr( $tabmultiid ).' .tabs{
			  border: 1px solid #ccc;
			  box-shadow: none;
			  list-style: outside none none;
			  margin: 0;
			  overflow: hidden;
			  padding: 0;
			}
			#tpultimatestabs-acitve-'.esc_attr( $tabmultiid ).' .tabs li.active {
			  float: left;
			  line-height: normal;
			  margin: auto;
			  background-color:'.esc_attr( $custom_tabultimate_shortcode_active_bg_color ).';
			}
			#tpultimatestabs-acitve-'.esc_attr( $tabmultiid ).' .tabs li a {
			  border-left: 1px solid #ccc;
			  box-shadow: none;
			  color: '.esc_attr( $custom_tabultimate_shortcode_title_font_color ).';
			  display: block;
			  font-weight: bold;
			  padding: 15px 20px;
			  text-decoration: none;
			  outline: none;
			  font-size:'.esc_attr( $custom_tabultimate_shortcode_title_font_size ).'px;
			}
			#tpultimatestabs-acitve-'.esc_attr( $tabmultiid ).' .tabs .active a {
			  color: '.esc_attr( $custom_tabultimate_shortcode_active_font_color ).';
			}
			#tpultimatestabs-acitve-'.esc_attr( $tabmultiid ).' .tabs li:first-child a {
			  border-left: medium none;
			}
			#tpultimatestabs-acitve-'.esc_attr( $tabmultiid ).' .tab_content {
			  background: '.esc_attr( $custom_tabultimate_shortcode_content_bg_color ).';
			  padding: 15px;
			  transition: all 0.6s ease-in-out 0s;
			}
			.tab_content_wrapper p {
			    line-height: normal;
			    margin-bottom: 25px;
			}
		</style>
		';
		return $logologcreate;
	}
}

?>