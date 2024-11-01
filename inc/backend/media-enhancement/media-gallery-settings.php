<?php defined('ABSPATH') or die('No script kiddies please!');    
 // define your backbone template;
  // the "tmpl-" prefix is required,
  // and your input field should have a data-setting attribute
  // matching the shortcode name
    $modes_arr = array(
         'horizontal' => __('Horizontal', WPMManagerLite_TD),
         'vertical' => __('Vertical', WPMManagerLite_TD),
         'fade' => __('Fade', WPMManagerLite_TD)
    );
    $gallery_types = array(
         'default' => __('Default', WPMManagerLite_TD),
         'grid_view' => __('Grid Type', WPMManagerLite_TD)
    );
    $settings = get_option('wpmediamanager_settings');
    $gallery_img_s_sizes = unserialize($settings['gallery_image_size']);  
?>

<script type="text/html" id="tmpl-wpmdia-gallery-settings">
            <span><?php _e('Advanced Gallery Settings', WPMManagerLite_TD); ?></span>
            <p class="description"><?php _e('Choose gallery options with advanced settings.', WPMManagerLite_TD); ?></p>
            <h3 class="wpmdia-gallery_section_divider">___________________________________________________________________________________________</h3>
              <label class="setting">
                  <div class="wpmdia-column1"> 
                      <span><?php _e('Gallery Display Type', WPMManagerLite_TD); ?></span>
                  </div>
                 <div class="wpmdia-column2"> 
                    <select class="gallery_type" name="gallery_type" data-setting="gallery_type">
                        <?php foreach($gallery_types as $gtype => $gt){ ?>
                        <option value="<?php echo esc_attr($gtype); ?>" <?php selected($gtype, 'default'); ?>><?php echo esc_html($gt); ?></option>
                        <?php } ?>
                    </select>
                 </div>
              </label>
            
             <div class="clearfix"></div>
             <label class="setting">
               <div class="wpmdia-column1">
                 <span><?php _e('Link On Click', WPMManagerLite_TD); ?></span>
                 </div> 
                 <div class="wpmdia-column2">
                <select class="link" name="link" data-setting="link">
                    <option value="none" selected><?php _e('None', WPMManagerLite_TD); ?></option>
                    <option value="post"><?php _e('Attachment Page', WPMManagerLite_TD); ?></option>
                </select> </div> 
            </label>

            <label class="setting">
                  <div class="wpmdia-column1"><span><?php _e('Total Columns', WPMManagerLite_TD); ?></span></div> 
                  <div class="wpmdia-column2">
                <select class="columns" name="columns" data-setting="columns">
                    <?php for($i=1;$i<=9;$i++){ ?>
                        <option value="<?php echo $i;?>" <?php if($i==1) echo "selected";?>><?php echo $i;?></option>
                    <?php }?>
                </select></div> 
            </label>
            
             <label class="setting">
                     <div class="wpmdia-column1">
                      <span><?php _e('Image Sizes', WPMManagerLite_TD); ?></span>
                    </div>
                     <div class="wpmdia-column2">
                    <select class="size" name="size" data-setting="size">
                        <?php foreach($gallery_img_s_sizes as $size){ ?>
                        <option value="<?php echo esc_attr($size); ?>" <?php selected($size, 'thumbnail'); ?>><?php echo ucwords(str_replace('-', ' ', $size)); ?></option>
                        <?php } ?>
                    </select>  </div>
              </label>
               <label class="setting">
                <div class="wpmdia-column1">
                    <span><?php _e('Order by', WPMManagerLite_TD); ?></span>
                    </div>
                     <div class="wpmdia-column2">
                    <select class="wpmdia_orderby" name="wpmdia_orderby" data-setting="wpmdia_orderby">
                        <option value="post__in" selected><?php _e('Custom', WPMManagerLite_TD); ?></option>
                        <option value="rand"><?php _e('Random', WPMManagerLite_TD); ?></option>
                        <option value="title"><?php _e('Title', WPMManagerLite_TD); ?></option>
                        <option value="id"><?php _e('ID', WPMManagerLite_TD); ?></option>
                        <option value="date"><?php _e('Date', WPMManagerLite_TD); ?></option>
                    </select> </div>
              </label>
               <label class="setting">
                 <div class="wpmdia-column1">
                    <span><?php _e('Order', WPMManagerLite_TD); ?></span>
                     </div>
                     <div class="wpmdia-column2">
                    <select class="wpmdia_order" name="wpmdia_order" data-setting="wpmdia_order">
                       <option value="ASC" selected><?php _e('Ascending', WPMManagerLite_TD); ?></option>
                       <option value="DESC"><?php _e('Descending',WPMManagerLite_TD); ?></option>
                    </select></div>
              </label>
               <label class="setting">
                <div class="wpmdia-column1">
                    <span><?php _e('Display Image Title', WPMManagerLite_TD); ?>
                       <p class="description"><?php _e('Display Image Title.', WPMManagerLite_TD); ?></p>
                    </span>
                  </div>
                     <div class="wpmdia-column2">
                   <input type="checkbox" class="display_title" name="display_title" data-setting="display_title" value="1"/>
                </div>
             </label>
              <label class="setting">
                 <div class="wpmdia-column1">
                    <span><?php _e('Display Caption', WPMManagerLite_TD); ?>
                       <p class="description"><?php _e('Display Caption on image.', WPMManagerLite_TD); ?></p>
                    </span>
                  </div>
                     <div class="wpmdia-column2">
                   <input type="checkbox" class="display_caption" name="display_caption" data-setting="display_caption" value="1"/>
                </div>
              </label>
</script>