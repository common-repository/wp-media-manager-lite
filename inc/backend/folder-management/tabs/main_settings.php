<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );?>
<div class="wpmedia_main_settings wpmdia_manager_wrapper">
<table>
    <tr>
        <td class='apmega-name'>
          <label for="enable_wpmediamanager"><?php _e("Enable WP Media Manager Lite", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Enable or disable WP Media Manager Lite.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <div class="wpmm-switch">
             <input type="checkbox" name="enable_wpmmanager" id="enable_wpmediamanager" value="1" <?php if($enable_wpmmanager  == 1) echo "checked";?>/>
             <label for="enable_wpmediamanager"></label>
           </div>
        </td>
    </tr> 
     <tr>
        <td class='apmega-name'>
          <label for="display_medianum"><?php _e("Display Media Number", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("Display media number on each folders.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <div class="wpmm-switch">
             <input type="checkbox" name="display_medianum" id="display_medianum" value="1" <?php if($display_medianum  == 1) echo "checked";?>/>
             <label for="display_medianum"></label>
           </div>
        </td>
    </tr>
    <tr>
        <td class='apmega-name'>
          <label for="enable_removeall"><?php _e("Remove All Folders At Once?", WPMManagerLite_TD); ?></label>
            <p class='description'>
                <?php _e("If you clicked on each folder delete options from media library"
                        . " remove a folder and its sub folders with all media inside will also be removed if this option is activated.", WPMManagerLite_TD); ?>
            </p>
        </td>
        <td class='apmega-value'>
           <div class="wpmm-switch">
             <input type="checkbox" name="enable_removeall" id="enable_removeall" value="1" <?php if($enable_removeall  == 1) echo "checked";?>/>
             <label for="enable_removeall"></label>
           </div>
        </td>
    </tr>          

      
</table>
</div>