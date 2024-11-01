<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );?>
<div class="wpmedia_gallery_settings wpmdia_manager_wrapper">
<table>
    <tr>
        <td class='apmega-name'>
          <label for="enable_gallery_settings"><?php _e("Enable All Gallery Settings", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Enable or disable all settings related with gallery features.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <div class="wpmm-switch">
             <input type="checkbox" name="enable_gallery_features" id="enable_gallery_settings" value="1" <?php if($enable_gallery_features  == 1) echo "checked";?>/>
             <label for="enable_gallery_settings"></label>
           </div>
        </td>
    </tr> 
    <?php $image_sizes = WPMManagerLite_Libary::wpmdia_get_image_sizes(); ?>
    <tr>
        <td class='apmega-name'>
            <label><?php _e("Gallery Image Size", WPMManagerLite_TD); ?></label>
             <p class='description'>
                 <?php _e("Choose Default image sizes. Any Custom image size added using worpdress filter hook will be added in this list.", WPMManagerLite_TD); ?>
             </p>
         </td>

         <td class='apmega-value'>
             <div class="wpmdia-selected-imgsize">
             <?php if(isset($image_sizes) && !empty($image_sizes)):
              foreach ($image_sizes as $size_name => $key): 
             ?>
            
             <input id="ap_<?php echo esc_attr($size_name);?>_imagesize" class="radio" 
             type="checkbox" <?php if(in_array($size_name, $selected_image_sizes)) echo "checked='checked'";?> 
             value="<?php echo esc_attr($size_name);?>" name="gallery_image_size[]">
            <label for="ap_<?php echo esc_attr($size_name);?>_imagesize" class="image_label"><?php echo ucwords(esc_attr($size_name));?></label>
            <br>
             <p class="description"><?php _e('Registered image size:',WPMManagerLite_TD);?> <?php echo esc_attr($size_name);?> <?php echo $key['width'].' * '.$key['height'];?></p>
            <?php endforeach; 
            endif;
            ?>
             </div>
        </td>
    </tr>
    </table>


</div>