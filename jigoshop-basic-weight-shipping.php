<?php
/*  
Plugin Name: Jigoshop Basic Weight Shipping
Plugin URI: http://www.intervisio.ch/webwork/wordpress-plugins/
Description: Plugin to extend the Jigoshop shipping rates with a basic weight based calculation.
Author: gasserol
Version: 1.0.0
Author URI: http://www.intervisio.ch/

Copyright 2013  intervisio gmbh  (email : info@intervisio.ch)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


== Description ==

Extend the Jigoshop shipping methods by adding a basic weight method.

This is a very simple plugin that allows you to set weight based shipping fees. Five levels of weights are alowed.
Example: level1 = 2kg with next field as 10 CHF = the total weight of an order in the cart is between 0kg and 2 kg and has a fee of 10 CHF.
Example: level2 = 5 kg with next field as 20 CHF = the total weight of an order in the cart is between 2kg and 5 kg and has a fee of 20 CHF.
etc.

My thanks goes to Iain at Polevaultweb, who developed a basic bundle shipping plugin for jigoshop. You can find his plugin at http://jigoshop.com/product/basic-bundle-shipping/

*/


add_action( 'plugins_loaded', 'jbbs_jigoshop_basic_weight_shipping_load', 0 );

function jbbs_jigoshop_basic_weight_shipping_load() {
	
	if ( !class_exists( 'jigoshop_shipping_method' ) ) return;
	
	function add_basic_weight_method( $methods ) {
		$methods[] = 'jbbs_jigoshop_basic_weight_shipping'; 
		return $methods;
	}
	add_filter('jigoshop_shipping_methods', 'add_basic_weight_method' );	
	
	class jbbs_jigoshop_basic_weight_shipping extends jigoshop_shipping_method {

		private $jigoshop_verson;
		public function __construct() {
			
			// Get Jigoshop version
			$path = dirname( dirname(__FILE__) ) .'/jigoshop/jigoshop.php';
			$default_headers = array( 'Version' => 'Version',  'Name' => 'Plugin Name');
			$plugin_data = get_file_data( $path, $default_headers, 'plugin' );
			$this->jigoshop_verson = $plugin_data['Version'];
			
			if ( version_compare( $this->jigoshop_verson, '1.4', '<' ) ) {
				$this->id 			= 'basic_weight';
				$this->enabled		= get_option('jigoshop_basic_weight_enabled');
				$this->title 		= get_option('jigoshop_basic_weight_title');
				$this->kg1          = get_option('jigoshop_basic_weight_kg1');
				$this->level1 		= get_option('jigoshop_basic_weight_level1');
				$this->kg2          = get_option('jigoshop_basic_weight_kg2');
				$this->level2 		= get_option('jigoshop_basic_weight_level2');
				$this->kg3          = get_option('jigoshop_basic_weight_kg3');
				$this->level3 		= get_option('jigoshop_basic_weight_level3');
                $this->kg4         = get_option('jigoshop_basic_weight_kg4');
                $this->level4       = get_option('jigoshop_basic_weight_level4');
                $this->level5       = get_option('jigoshop_basic_weight_level5');
                
				if (isset( jigoshop_session::instance()->chosen_shipping_method_id ) && jigoshop_session::instance()->chosen_shipping_method_id==$this->id) $this->chosen = true;
	
				add_action('jigoshop_update_options', array(&$this, 'process_admin_options'));
	            add_option('jigoshop_basic_weight_kg1', '2.00');
				add_option('jigoshop_basic_weight_level1', '10.00');
				add_option('jigoshop_basic_weight_kg2', '5.00');
				add_option('jigoshop_basic_weight_level2', '20.00');
				add_option('jigoshop_basic_weight_kg3', '10.00');
				add_option('jigoshop_basic_weight_level3', '30.00');
                add_option('jigoshop_basic_weight_kg4', '20.00');
                add_option('jigoshop_basic_weight_level4', '40.00');
                add_option('jigoshop_basic_weight_level5', '50.00');
				
				add_option('jigoshop_basic_weight_title', 'Weight Based Rate');
			
			} else {
				// Post 1.4
				parent::__construct();
				$this->id 			= 'basic_weight';
				$this->enabled		= Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_enabled');
				$this->title 		= Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_title');
				$this->kg1          = Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_kg1');
				$this->level1 		= Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_level1');
				$this->kg2          = Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_kg2');
				$this->level2 		= Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_level2');
				$this->kg3          = Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_kg3');
				$this->level3 		= Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_level3');
                $this->kg4          = Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_kg4');
                $this->level4       = Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_level4');
                $this->level5       = Jigoshop_Base::get_options()->get_option('jigoshop_basic_weight_level5');
				
			}
			
		}
	
		/**
		 * Default Option settings for WordPress Settings API using the Jigoshop_Options class
		 *
		 * These should be installed on the Jigoshop_Options 'Shipping' tab
		 *
		 */	
		 
		// Post 1.4 versions
		protected function get_default_options() {
		
			$defaults = array();
			
			// Define the Section name for the Jigoshop_Options
			$defaults[] = array( 'name' => __('Basic Weight Rate', 'jigoshop'), 'type' => 'title', 'desc' => __('Set a weight for the level1 product and another weight for level2 products.', 'jigoshop') );
			
			// List each option in order of appearance with details
			$defaults[] = array(
				'name'		=> __('Enable Basic Weight Rate','jigoshop'),
				'desc' 		=> '',
				'tip' 		=> '',
				'id' 		=> 'jigoshop_basic_weight_enabled',
				'std' 		=> 'yes',
				'type' 		=> 'checkbox',
				'choices'	=> array(
					'no'			=> __('No', 'jigoshop'),
					'yes'			=> __('Yes', 'jigoshop')
				)
			);
			
			$defaults[] = array(
				'name'		=> __('Method Title','jigoshop'),
				'desc' 		=> '',
				'tip' 		=> __('This controls the title which the user sees during checkout.','jigoshop'),
				'id' 		=> 'jigoshop_basic_weight_title',
				'std' 		=> __('Basic Weight Rate','jigoshop'),
				'type' 		=> 'text'
			);
			
            $defaults[] = array(
                'name'      => __('Level1 Weight Limit in kg','jigoshop'),
                'desc'      => '',
                'type'      => 'decimal',
                'tip'       => __('Enter the first kg limit for level1.','jigoshop'),
                'id'        => 'jigoshop_basic_weight_kg1',
                'std'       => '2.00',
            );
            
			$defaults[] = array(
				'name'		=> __('Shipping Fee for Level1','jigoshop'),
				'desc' 		=> '',
				'type' 		=> 'decimal',
				'tip' 		=> __('Cost excluding tax. Enter an amount, e.g. 2.50.','jigoshop'),
				'id' 		=> 'jigoshop_basic_weight_level1',
				'std' 		=> '10.00',
			);
			
            $defaults[] = array(
                'name'      => __('Level2 Weight Limit in kg','jigoshop'),
                'desc'      => '',
                'type'      => 'decimal',
                'tip'       => __('Enter the kg limit for level2.','jigoshop'),
                'id'        => 'jigoshop_basic_weight_kg2',
                'std'       => '5.00',
            );
            
			$defaults[] = array(
				'name'		=> __('Shipping Fee for Level2','jigoshop'),
				'desc' 		=> '',
				'type' 		=> 'decimal',
				'tip' 		=> __('Cost excluding tax. Enter an amount, e.g. 2.50.','jigoshop'),
				'id' 		=> 'jigoshop_basic_weight_level2',
				'std' 		=> '20.OO',
			);
            
            $defaults[] = array(
                'name'      => __('Level3 Weight Limit in kg','jigoshop'),
                'desc'      => '',
                'type'      => 'decimal',
                'tip'       => __('Enter the kg limit for level3.','jigoshop'),
                'id'        => 'jigoshop_basic_weight_kg3',
                'std'       => '10.00',
            );
            
			$defaults[] = array(
				'name'		=> __('Shipping Fee for Level3','jigoshop'),
				'desc' 		=> '',
				'type' 		=> 'decimal',
				'tip' 		=> __('Cost excluding tax. Enter an amount, e.g. 2.50.','jigoshop'),
				'id' 		=> 'jigoshop_basic_weight_level3',
				'std' 		=> '30.00',
			);
            
            $defaults[] = array(
                'name'      => __('Level4 Weight Limit in kg','jigoshop'),
                'desc'      => '',
                'type'      => 'decimal',
                'tip'       => __('Enter the kg limit for level4.','jigoshop'),
                'id'        => 'jigoshop_basic_weight_kg4',
                'std'       => '20.00',
            );
            
            $defaults[] = array(
                'name'      => __('Shipping Fee for Level4','jigoshop'),
                'desc'      => '',
                'type'      => 'decimal',
                'tip'       => __('Cost excluding tax. Enter an amount, e.g. 2.50.','jigoshop'),
                'id'        => 'jigoshop_basic_weight_level4',
                'std'       => '40.00',
            );
            $defaults[] = array(
                'name'      => __('Shipping Fee for Level5 (more than level4 kg limit)','jigoshop'),
                'desc'      => '',
                'type'      => 'decimal',
                'tip'       => __('Cost excluding tax. Enter an amount, e.g. 2.50.','jigoshop'),
                'id'        => 'jigoshop_basic_weight_level5',
                'std'       => '50.00',
            );
            
			return $defaults;
		}
		
		// Pre 1.4 versions
		public function admin_options() {
			if ( version_compare( $this->jigoshop_verson, '1.4', '>=' ) ) return;
			?>
			<thead><tr><th scope="col" colspan="2"><h3 class="title"><?php _e('Basic Weight Rate', 'jigoshop'); ?></h3></th></tr></thead>
			<tr>
				<th scope="row"><?php _e('Enable basic weight rate', 'jigoshop') ?></th>
				<td class="forminp">
					<select name="jigoshop_basic_weight_enabled" id="jigoshop_basic_weight_enabled" style="min-width:100px;">
						<option value="yes" <?php if (get_option('jigoshop_basic_weight_enabled') == 'yes') echo 'selected="selected"'; ?>><?php _e('Yes', 'jigoshop'); ?></option>
						<option value="no" <?php if (get_option('jigoshop_basic_weight_enabled') == 'no') echo 'selected="selected"'; ?>><?php _e('No', 'jigoshop'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><a href="#" tip="<?php _e('This controls the title which the user sees during checkout.','jigoshop') ?>" class="tips" tabindex="99"></a><?php _e('Method Title', 'jigoshop') ?></th>
				<td class="forminp">
					<input type="text" name="jigoshop_basic_weight_title" id="jigoshop_basic_weight_title" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_basic_weight_title')) echo $value; else echo 'Basic Weight Rate'; ?>" />
				</td>
			</tr>
			
			<tr>
                <th scope="row"><?php _e('Level1 kg limit', 'jigoshop') ?></th>
                <td class="forminp">
                 <input type="text" name="jigoshop_basic_weight_kg1" id="jigoshop_basic_weight_kg1" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_basic_weight_kg1')) echo $value; else echo '2.00'; ?>" />
                </td>
            </tr>
			<tr>
				<th scope="row"><?php _e('Price for Level1 product', 'jigoshop') ?></th>
				<td class="forminp">
				 <input type="text" name="jigoshop_basic_weight_level1" id="jigoshop_basic_weight_level1" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_basic_weight_level1')) echo $value; else echo '10.00'; ?>" />
				</td>
			</tr>
			<tr>
                <th scope="row"><?php _e('Level2 kg limit', 'jigoshop') ?></th>
                <td class="forminp">
                 <input type="text" name="jigoshop_basic_weight_kg2" id="jigoshop_basic_weight_kg2" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_basic_weight_kg2')) echo $value; else echo '5.00'; ?>" />
                </td>
            </tr>
			
			<tr>
				<th scope="row"><?php _e('Price for Level2 products', 'jigoshop') ?></th>
				<td class="forminp">
				 <input type="text" name="jigoshop_basic_weight_level2" id="jigoshop_basic_weight_level2" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_basic_weight_level2')) echo $value; else echo '20.00'; ?>" />
				</td>
			</tr>
			<tr>
                <th scope="row"><?php _e('Level3 kg limit', 'jigoshop') ?></th>
                <td class="forminp">
                 <input type="text" name="jigoshop_basic_weight_kg3" id="jigoshop_basic_weight_kg3" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_basic_weight_kg3')) echo $value; else echo '10.00'; ?>" />
                </td>
            </tr>
			
			<tr>
				<th scope="row"><?php _e('Price for Level3 products', 'jigoshop') ?></th>
				<td class="forminp">
				 <input type="text" name="jigoshop_basic_weight_level3" id="jigoshop_basic_weight_level3" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_basic_weight_level3')) echo $value; else echo '30.00'; ?>" />
				</td>
			</tr>
			
			<tr>
                <th scope="row"><?php _e('Level4 kg limit', 'jigoshop') ?></th>
                <td class="forminp">
                 <input type="text" name="jigoshop_basic_weight_kg4" id="jigoshop_basic_weight_kg4" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_basic_weight_kg4')) echo $value; else echo '20.00'; ?>" />
                </td>
            </tr>
			<tr>
                <th scope="row"><?php _e('Price for Level4 products', 'jigoshop') ?></th>
                <td class="forminp">
                 <input type="text" name="jigoshop_basic_weight_level4" id="jigoshop_basic_weight_level4" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_basic_weight_level4')) echo $value; else echo '40.00'; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Price for Level5 products (more than level4 kg limit)', 'jigoshop') ?></th>
                <td class="forminp">
                 <input type="text" name="jigoshop_basic_weight_level5" id="jigoshop_basic_weight_level5" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_basic_weight_level5')) echo $value; else echo '50.00'; ?>" />
                </td>
            </tr>
			
			<?php
		}

		// Pre 1.4 versions
		public function process_admin_options() {

			if(isset($_POST['jigoshop_basic_weight_enabled'])) update_option('jigoshop_basic_weight_enabled', jigowatt_clean($_POST['jigoshop_basic_weight_enabled'])); else @delete_option('jigoshop_basic_weight_enabled');
			if(isset($_POST['jigoshop_basic_weight_title'])) update_option('jigoshop_basic_weight_title', jigowatt_clean($_POST['jigoshop_basic_weight_title'])); else @delete_option('jigoshop_basic_weight_title');
			if(isset($_POST['jigoshop_basic_weight_kg1'])) update_option('jigoshop_basic_weight_kg1', jigowatt_clean($_POST['jigoshop_basic_weight_kg1'])); else @delete_option('jigoshop_basic_weight_kg1');
			if(isset($_POST['jigoshop_basic_weight_level1'])) update_option('jigoshop_basic_weight_level1', jigowatt_clean($_POST['jigoshop_basic_weight_level1'])); else @delete_option('jigoshop_basic_weight_level1');
			if(isset($_POST['jigoshop_basic_weight_kg2'])) update_option('jigoshop_basic_weight_kg2', jigowatt_clean($_POST['jigoshop_basic_weight_kg2'])); else @delete_option('jigoshop_basic_weight_kg2');
			if(isset($_POST['jigoshop_basic_weight_level2'])) update_option('jigoshop_basic_weight_level2', jigowatt_clean($_POST['jigoshop_basic_weight_level2'])); else @delete_option('jigoshop_basic_weight_level2');
			if(isset($_POST['jigoshop_basic_weight_kg3'])) update_option('jigoshop_basic_weight_kg3', jigowatt_clean($_POST['jigoshop_basic_weight_kg3'])); else @delete_option('jigoshop_basic_weight_kg3');
			if(isset($_POST['jigoshop_basic_weight_level3'])) update_option('jigoshop_basic_weight_level3', jigowatt_clean($_POST['jigoshop_basic_weight_level3'])); else @delete_option('jigoshop_basic_weight_level3');
            if(isset($_POST['jigoshop_basic_weight_kg4'])) update_option('jigoshop_basic_weight_kg4', jigowatt_clean($_POST['jigoshop_basic_weight_kg4'])); else @delete_option('jigoshop_basic_weight_kg4');
            if(isset($_POST['jigoshop_basic_weight_level4'])) update_option('jigoshop_basic_weight_level4', jigowatt_clean($_POST['jigoshop_basic_weight_level4'])); else @delete_option('jigoshop_basic_weight_level4');
            if(isset($_POST['jigoshop_basic_weight_level5'])) update_option('jigoshop_basic_weight_level5', jigowatt_clean($_POST['jigoshop_basic_weight_level5'])); else @delete_option('jigoshop_basic_weight_level5');
		}

		
	    
	    //foga for all versions
	    public function calculate_shipping() {
            $this->shipping_total   = 0;
            $this->shipping_tax     = 0;
            $this->shipping_label   = $this->title;
            
            $fogakg1 = $this->kg1;
            $fogakg2 = $this->kg2;
            $fogakg3 = $this->kg3;
            $fogakg4 = $this->kg4;
                    
            $foga_weight = (jigoshop_cart::$cart_contents_weight);    
               
                      
                if ($foga_weight < $fogakg1) {
                        
                    $this->shipping_total = $this->level1;
                
                } 
                
                    elseif ($foga_weight > $fogakg1 && $foga_weight < $fogakg2) {
                    
                        $this->shipping_total = $this->level2;
                    
                        }
                    
                        elseif ($foga_weight > $fogakg2 && $foga_weight < $fogakg3) {
                    
                        $this->shipping_total = $this->level3;
                    
                        }
                        
                            elseif ($foga_weight > $fogakg3 && $foga_weight < $fogakg4) {
                    
                            $this->shipping_total = $this->level4;
                    
                            }
                            
                      else {
                         
                            $this->shipping_total = $this->level5;
                      }
            }
        
	
	}
}
