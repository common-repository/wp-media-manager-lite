<?php defined('ABSPATH') or die('No script kiddies please!');
$wpmdia_mobile_detector = new WPMDIAL_Mobile_Detect();
$wpmediamanager_settings     = get_option('wpmediamanager_settings');

$display_title = (isset($attr['display_title']) && $attr['display_title'] == 'true')?$attr['display_title']:'false';
$display_caption = (isset($attr['display_caption']) && $attr['display_caption'] == 'true')?$attr['display_caption']:'false';


$desktop_columns = 6;
$tablet_columns =  3;
$mobile_columns =  1;

if ( $wpmdia_mobile_detector->isMobile() && ! $wpmdia_mobile_detector->isTablet() ) {
    $column_class = 'wpmdia-columns-' . $mobile_columns;
    $browser_type = 'wpmdia-mobile_view';
} else if ( $wpmdia_mobile_detector->isTablet() ) {
    $column_class = 'wpmdia-columns-' . $tablet_columns;
    $browser_type = 'wpmdia-tablet_view';
} else {
  if($columns == ''){
     $column_class = 'wpmdia-columns-' . $desktop_columns;
     $browser_type = 'wpmdia-desktop_view';
   }else{
     $column_class = 'wpmdia-columns-' . $columns;
     $browser_type = 'wpmdia-desktop_view';
   }
   
}


$output  = '<div class="wpmdia_gallery_wrapper wpmdia_grid_gallery">';
$output  .= "<div class='wpmedia-galleries-wrapper wpmedia_grid_template clearfix ".$browser_type ." ".$column_class." id='galleries-$idselector'>";
// WPMManagerLite_Libary::displayArr($attachments );
$i = 0;
$pos = 1;


if ( $attachments )
    {


        $aheight = WPMManagerLite_Libary::get_actual_sHeight($attachments , $size);
        foreach ( $attachments as $id => $attach )
        {     

             $url = get_attachment_link($id);
             $id = intval($id);
             $_post = get_post($id);
             if (empty($_post) || ( 'attachment' != $_post->post_type ) || !$url = wp_get_attachment_url($id))
             return __('Missing Attachment');

             // $eachwidth = 100 / $columns . '%';
         
              $image_title = htmlentities($attach->post_title);
              $image_caption = htmlentities($attach->post_excerpt);
              $image_ID = (int) $attach->ID;
              $image_date = $attach->post_date;
              $url_link = get_post_meta($image_ID, '_cg_img_link', true);
              $link_target = get_post_meta($image_ID, '_cg_link_target', true);
             
              $alt = trim(strip_tags(get_post_meta($id, '_wp_attachment_image_alt', true))); // Use Alt field first
                $sizes = image_get_intermediate_size($image_ID, $size);
               if (!$sizes) {
                   $sizes = wp_get_attachment_metadata($image_ID);
               }
               if (is_numeric($sizes['height']) && $sizes['height'] != 0) {
                    $ratio = $sizes['width'] / $sizes['height'];
               } else {
                    $ratio = 1;
               }

                if (!$image = wp_get_attachment_image_src($id, $size))
                continue; 
                $source        = $image[0];
                $actual_width  = $image[1];
                $actual_height = $image[2];

             

             $output .= "<figure class='wpmdia-gallery wpmdia-gallery-grid-each-box'>";
         
              if ($size && 'none' != $size)
                  $img_src = wp_get_attachment_image($id, $size, false);
              else
                   $img_src = '';

              if (trim($img_src) == '')
                   $img_src =  $image_title;


               $l = 0;
                // link = lightbox, post or none
                if (!empty($link)) {
                  if ($clink == 1) {
                    if ($url_link == ''){
                        $output .= '<a class="wpmdia_each_default_gallery" data-lightbox="'.$l.'" href="' . $url . '" target="' . $link_target . '">' . $img_src . '</a>';
                     }else{
                        $output .= '<a class="wpmdia_each_default_gallery" data-lightbox="'.$l.'" href="' . $url_link . '" target="' . $link_target . '">' . $img_src . '</a>';
                     }
                } else if ( $link  == 'post') {
                    $url = get_attachment_link($id);
                     $output .= '<a class="wpmdia_each_default_gallery" data-lightbox="'.$l.'" href="' . $url . '" target="' . $link_target . '">' . $img_src . '</a>';

                }else{

                    if ($url_link != '') {
                        $url = $url_link;
                        $output .= '<a class="wpmdia-each-gallery"  href="' . $url . '" target="' . $link_target . '" title="' . $image_title . '">' . $img_src . '</a>';
                        } else {
                            if ($columns == 1) {
                                $output .= "<img src='".$source."' width='".$actual_width."' height='".$actual_height."' alt='".$alt."' />";
                            } else {
                                $output .= "<img src='".$source."' data-ratio='$ratio' width='" . max($aheight) * $ratio . "' alt='".$alt."' style='width:" . max($aheight) * $ratio . "px;' />";
                            }
                        }
                }
               }


           $image_meta = wp_get_attachment_metadata($id);

            $orientation = '';
            if (isset($image_meta['height'], $image_meta['width']))
                $orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'wpmdia-portrait' : 'wpmdia-landscape';
             
             if($link  == 'lightbox' && $url_link != ''){
                 $adcCl = "wpmdia_show_both";
             }else{
                  $adcCl = "";
             } 
             
            
                  $output .= "<figcaption class='wp-caption-text gallery-caption ". $adcCl."'>";
                     $output .= '<div class="wpmdia-button-holder">';
                         if ($url_link != '') {
                         $output .= '<a href="'.$url_link.'" class="wpmdia-link" target="'.$link_target.'">
                         <i class="fa fa-link" aria-hidden="true"></i></a>';
                        }
                        $output .= '</div>';
                     if($display_title == "true" && $image_title != ''){
                         $output .= '<span class="wpmdia-title wpmdia-main-title">' . ucfirst(wptexturize(esc_attr($image_title))) . '</span>';
                        }
                      if($display_caption == "true" && $image_caption != ''){
                          $output .= '<div class="wpmdia-caption"><p>'. wptexturize(esc_attr($image_caption)) .'</p></div>';
                         }

                           
                   $output .= "</figcaption>";
                          $output .= '<div class="wpmdia-show-overlay"></div>';

              $output .= "</figure>";    
              $pos++;
        }
  }


$output .= "</div></div>";