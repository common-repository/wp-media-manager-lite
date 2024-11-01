<?php defined('ABSPATH') or die('No script kiddies please!');?>
<div class="wpmedia_custom_filters wpmdia_manager_wrapper">
    <div class="wpmdia_row clearfix">
	           <div class="wpdmedia_col_medium">
		           <label for="enable_customfilters"><?php _e("Enable all custom filters?", WPMManagerLite_TD); ?></label>
		            <p class='description'>
		                <?php _e("Check to enable all custom filters.", WPMManagerLite_TD); ?>
		            </p>
	            </div>

	            <div class="wpmm-switch">
	             <input type="checkbox" name="enable_customfilters" id="enable_customfilters" value="1" <?php if($enable_customfilters  == 1) echo "checked";?>/>
	             <label for="enable_customfilters"></label>
	           </div>
      </div>

      <div class="wpmdia_row clearfix">
           
           <div class="wpmdia_meta_field"> 
		          <label><?php _e("Default Custom Filter Size Options", WPMManagerLite_TD); ?>
		          </label><p class="description"><?php _e("Note: Choose one or multiple custom filter size width and height to list out on media filter custom size dropdown options.", WPMManagerLite_TD); ?></p>
		          <div class="tbl_meta_data_list">

		          	<table class="wp-list-table widefat fixed striped wpmdia_tbl_custom_size" cellspacing="1" id="data_table_size">
					  <thead>
					        <tr>
					          <th><input type="checkbox" class="cb_all_size"/></th>
						      <th><?php _e('Width (px)',WPMManagerLite_TD);?></th><th></th>
							  <th><?php _e('Height (px)',WPMManagerLite_TD);?></th>
							</tr>
		              </thead>
						<?php if(isset($wpmedia_dimension_size) && !empty($wpmedia_dimension_size)):
						$i=1; foreach ($wpmedia_dimension_size as $key => $value) {
		               $str_explode = explode('x',$value);
		               ?>
							
					   <tr id="dimension_row<?php echo $i;?>" class="wpmdia_row_size_sp">
					            <td id="check_row<?php echo $i;?>">
					            <input type="checkbox" <?php if (in_array($value, $wpmedia_s_dimension_size) == true) echo 'checked' ?> class="cb_specific_row_size" name="cb_specific_size[]" value="<?php echo $value; ?>" />
					            </td>
								<td id="width_row<?php echo $i;?>"><?php echo esc_attr($str_explode[0]) ;?></td>
								<td class="middle_row_sign" id="middle_row<?php echo $i;?>">X</td>
								<td id="height_row<?php echo $i;?>"><?php echo esc_attr($str_explode[1]);?></td>
						</tr>


						<?php $i++; }
						endif;
						?>

						</table>


		          </div>
		   </div>

		   <div class="wpmdia_meta_field"> 
		          <label><?php _e("Default Custom Filter Weight Options", WPMManagerLite_TD); ?>
		          </label><p class="description"><?php _e("Note: Choose one or multiple custom minimum and maximum weight to display on media filter weight dropdown options.", WPMManagerLite_TD); ?></p>
		          <div class="tbl_meta_data_list">
		          	<table class="wp-list-table widefat fixed striped wpmdia_tbl_custom_weight" cellspacing="1" id="data_table_weight"> 
					  <thead>
					        <tr>
					           <th><input type="checkbox" class="cb_all_weight"/></th>
						       <th><?php _e('Min',WPMManagerLite_TD);?></th><th></th>
							   <th><?php _e('Max',WPMManagerLite_TD);?></th>
							   <th  style="width: 80px;"><?php _e('Unit',WPMManagerLite_TD);?></th>
							</tr>
		              </thead>
						<?php if(isset($wpmedia_weight_default) && !empty($wpmedia_weight_default)):
						$i=1; foreach ($wpmedia_weight_default as $key => $value) {
		               $str_explode = explode('-',$value[0]);

		               if ($value[1] == 'kB') {
			                $wt_lbl1 = ($str_explode[0] / 1024);
			                $wt_lbl2 = ($str_explode[1] / 1024);
			                $wt_unit = "kB";
			            } else {
			            	$wt_lbl1 = ($str_explode[0] / (1024 * 1024));
			                $wt_lbl2 =($str_explode[1] / (1024 * 1024));
			                $wt_unit = "MB";
			            }

		               ?>
							
					   <tr id="weight_row<?php echo $i;?>" class="wpmdia_row_wt_sp">
					            <td id="check_wtrow<?php echo $i;?>">
					            <input type="checkbox" class="cb_specific_row_weight" <?php if (in_array($value, $wpmedia_s_weight_default) == true) echo 'checked' ?>  name="cb_specific_weight[][]" value="<?php echo $value[0] . ',' . $value[1]; ?>"/>
					            </td>
								<td id="min_wt_row<?php echo $i;?>"><?php echo esc_attr($wt_lbl1) ;?></td>
								<td class="middle_row_sign" id="middle_wt_row<?php echo $i;?>">-</td>
								<td id="max_wt_row<?php echo $i;?>"><?php echo esc_attr($wt_lbl2);?></td>
                                <td id="unit_row<?php echo $i;?>"><?php echo esc_attr($wt_unit);?></td>
						</tr>


						<?php $i++; }
						endif;
						?>

						</table>

						
                    </div>
                     <p class='description'>
		                <?php _e("Note: Here you can choose only above listed custom size and weight to display on filter, enable or disable filter options on free version.
		                   Whereas, you can add custom weight and size on premium version of this plugin.", WPMManagerLite_TD); ?>
		                 </p>
		      </div>
		</div>
</div>
<div class="clear"></div>