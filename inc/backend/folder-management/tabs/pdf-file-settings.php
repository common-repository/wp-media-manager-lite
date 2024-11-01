<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );?>
<div class="wpmedia_pdf_settings wpmdia_manager_wrapper">
<table>
    <tr>
        <td class='apmega-name'>
          <label for="enable_pdf_file_design"><?php _e("Enable Single PDF File Design", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Enable or disable single pdf file designing layout with custom options.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <div class="wpmm-switch">
             <input type="checkbox" name="enable_pdf_file_design" id="enable_pdf_file_design" value="1" <?php if($enable_pdf_file_design  == 1) echo "checked";?>/>
             <label for="enable_pdf_file_design"></label>
           </div>
        </td>
    </tr> 
     <tr>
        <td class='apmega-name'>
          <label for="show_size_amount"><?php _e("Display Total File Size", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Display total size of file on frontend within single pdf file design.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <div class="wpmm-switch">
             <input type="checkbox" name="show_size_amount" id="show_size_amount" value="1" <?php if($show_size_amount  == 1) echo "checked";?>/>
             <label for="show_size_amount"></label>
           </div>
        </td>
    </tr> 
       <tr>
        <td class='apmega-name'>
          <label for="show_format_type"><?php _e("Display File Format", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Display file format type on frontend within single pdf file design.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <div class="wpmm-switch">
             <input type="checkbox" name="show_format_type" id="show_format_type" value="1" <?php if($show_format_type  == 1) echo "checked";?>/>
             <label for="show_format_type"></label>
           </div>
        </td>
    </tr> 
</table>

  </div> 
    <div class="second_title_mdia"><h3><?php _e('Custom Settings For Single File',WPMManagerLite_TD);?></h3></div>
    <table>
      <tr>
        <td class='apmega-name'>
          <label for="pdffile_bg_color"><?php _e("Background Color", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Set background color for pdf single file.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <input type="text" class="colorpicker" name="pdffile_bg_color" id="pdffile_bg_color" value="<?php echo esc_attr($pdffile_bg_color);?>"/>
        </td>
    </tr>
     <tr>
        <td class='apmega-name'>
          <label for="pdffile_bg_hcolor"><?php _e("Background Hover Color", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Set background hover color for pdf single file.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <input type="text" class="colorpicker" name="pdffile_bg_hcolor" id="pdffile_bg_hcolor" value="<?php echo esc_attr($pdffile_bg_hcolor);?>"/>
        </td>
    </tr>

      <tr>
        <td class='apmega-name'>
          <label for="pdffile_font_color"><?php _e("Font Color", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Set font color for pdf single file.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <input type="text" class="colorpicker" name="pdffile_font_color" id="pdffile_font_color" value="<?php echo esc_attr($pdffile_font_color);?>"/>
        </td>
    </tr>

      <tr>
        <td class='apmega-name'>
          <label for="pdffile_font_hcolor"><?php _e("Font Hover Color", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Set font hover color for pdf single file.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <input type="text" class="colorpicker" name="pdffile_font_hcolor" id="pdffile_font_hcolor" value="<?php echo esc_attr($pdffile_font_hcolor);?>"/>
        </td>
    </tr>

     <tr>
        <td class='apmega-name'>
          <label for="pdffile_font_size"><?php _e("Font Size", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Set font size for pdf single file content.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
          <input type="number" name="pdffile_font_size" id="pdffile_font_size" value="<?php echo esc_attr($pdffile_font_size);?>"/>
          <em>px</em>
        </td>
    </tr>

       <tr>
        <td class='apmega-name'>
          <label for="file_icon_color"><?php _e("Font Icon Color", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Set font icon color for pdf single file.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <input type="text" class="colorpicker" name="file_icon_color" id="file_icon_color" value="<?php echo esc_attr($file_icon_color);?>"/>
        </td>
    </tr>

     <tr>
        <td class='apmega-name'>
          <label for="file_icon_size"><?php _e("Font Icon Size", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Set font icon size for pdf single file content.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
          <input type="number" name="file_icon_size" id="file_icon_size" value="<?php echo esc_attr($file_icon_size);?>"/>
          <em>px</em>
        </td>
    </tr>




    </table>
 </div>
