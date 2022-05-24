<?php
/**
 * @since      4.0.2
 * Description: Conversios Onboarding page, It's call while active the plugin
 */
if ( ! class_exists( 'Conversios_Footer' ) ) {
	class Conversios_Footer {	
		public function __construct( ){
			add_action('add_conversios_footer',array($this, 'before_end_footer'));
		}	
		public function before_end_footer(){ 
			?>  <div class="tvc_footer_links">
						
					</div>
				</div>
			<?php
		}
	}
}
new Conversios_Footer();