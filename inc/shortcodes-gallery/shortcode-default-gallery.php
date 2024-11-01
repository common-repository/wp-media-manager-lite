<?php defined('ABSPATH') or die('No script kiddies please!');
$wpmediamanager_settings     = get_option('wpmediamanager_settings');
$enable_lightbox             = (isset($wpmediamanager_settings['enable_lightbox']) && $wpmediamanager_settings['enable_lightbox'] == 1)?1:0;
$choose_pp_theme             =  (isset($wpmediamanager_settings['choose_pp_theme']))?$wpmediamanager_settings['choose_pp_theme']:'pp_default'; 
$animation_speed             = (isset($wpmediamanager_settings['animation_speed']) && $wpmediamanager_settings['animation_speed'] != '')?$wpmediamanager_settings['animation_speed']:'normal'; 
$slideshow_speed             = (isset($wpmediamanager_settings['slideshow_speed']) && $wpmediamanager_settings['slideshow_speed'] != '')?$wpmediamanager_settings['slideshow_speed']:'5000'; 
$lightboxsize = (isset($attr['lightboxsize']) && $attr['lightboxsize'] != '')?$attr['lightboxsize']:'large';
$display_title = (isset($attr['display_title']) && $attr['display_title'] == 'true')?$attr['display_title']:'false';
$display_caption = (isset($attr['display_caption']) && $attr['display_caption'] == 'true')?$attr['display_caption']:'false';

if(isset( $enable_lightbox ) && $enable_lightbox == 1 ) {
$tclass = "enable_lightbox";
}else{
$tclass = "";
}

$output  = '<div class="wpmdia_gallery_wrapper wpmdia_default_gallery">';
$output  .= "<div data-lightbox_status='".$enable_lightbox."' data-slideshow_speed='".$slideshow_speed."' data-animation_speed='".$animation_speed."' data-pptheme='".$choose_pp_theme."'  class='wpmedia-galleries-wrapper wpmedia_default_template ".$tclass." wpmdia-columns".$columns."' id='wpdefault-galleries-$idselector'>";
// WPMManagerLite_Libary::displayArr($attachments );
$i = 0;
$pos = 1;


if ( $attachments )
    {	
        $aheight = WPMManagerLite_Libary::get_actual_sHeight($attachments , $size);
        foreach ( $attachments as $id => $attach )
        {     
              $eachwidth = 100 / $columns . '%';
         
        	    $image_title = htmlentities($attach->post_title);
              $image_caption = htmlentities($attach->post_excerpt);
              $image_ID = (int) $attach->ID;
              $image_date = $attach->post_date;
              $url_link = get_post_meta($image_ID, '_cg_img_link', true);
              $link_target = get_post_meta($image_ID, '_cg_link_target', true);

               $sizes = image_get_intermediate_size($image_ID, $size);
                if (!$sizes) {
                    $sizes = wp_get_attachment_metadata($image_ID);
                }
                if (is_numeric($sizes['height']) && $sizes['height'] != 0) {
                    $ratio = $sizes['width'] / $sizes['height'];
                } else {
                    $ratio = 1;
               }
             
        $alt = trim(strip_tags(get_post_meta($id, '_wp_attachment_image_alt', true))); // Use Alt field first
        $url = get_attachment_link($id);
        $id = intval($id);
        $_post = get_post($id);

         if (!$image = wp_get_attachment_image_src($id, $size))
               continue; 
               $source        = $image[0];
               $actual_width  = $image[1];
               $actual_height = $image[2]; 

       if (empty($_post) || ( 'attachment' != $_post->post_type ) || !$url = wp_get_attachment_url($id))
            return __('Missing Attachment');

          $output .= "<figure class='wpmdia-gallery wpmf-gallery-item gallery-item' style='width:$eachwidth'>";
         
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
            if ($url_link == '')
                $output .= '<a class="wpmdia_each_default_gallery" data-lightbox="'.$l.'" href="' . $url . '" target="' . $link_target . '">' . $img_src . '</a>';
        } else if ($link  == 'post') {
            $url = get_attachment_link($id);
             $output .= '<a class="wpmdia_each_default_gallery" data-lightbox="'.$l.'" href="' . $url . '" target="' . $link_target . '">' . $img_src . '</a>';

        } else if( $link  == 'lightbox'){
              $l = 1;
              $urls = wp_get_attachment_image_src($id, $lightboxsize);
              $url = $urls[0];
              $output .= '<a rel="prettyPhoto[default_gallery_'.$idselector.']" data-lightbox="'.$l.'" class="wpmdia-each-default_gallery" href="' . $url . '" target="' . $link_target . '" title="' . $image_title . '">' . $img_src . '</a>';
        }else{
          //none
                if ($url_link != '') {
                $l = 0;
                $url = $url_link;
                $output .= '<a class="wpmdia-each-gallery"  href="' . $url . '" target="' . $link_target . '" title="' . $image_title . '">' . $img_src . '</a>';
                } else {
                   $output .= '<a class="wpmdia-each-gallery"  href="#" title="' . $image_title . '">';
                    if ($columns == 1) {
                        $output .= "<img src='".$source."' width='".$actual_width."' height='".$actual_height."' alt='".$alt."' />";
                    } else {
                        $output .= "<img src='".$source."' data-ratio='$ratio' width='" . max($aheight) * $ratio . "' alt='".$alt."' style='width:" . max($aheight) * $ratio . "px;' />";
                    }
                     $output .= '</a>';
                }
        }
       }


           $image_meta = wp_get_attachment_metadata($id);


            $orientation = '';
            if (isset($image_meta['height'], $image_meta['width']))
                $orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'wpmdia-portrait' : 'wpmdia-landscape';

                  $output .= "<figcaption class='wp-caption-text gallery-caption'>";
                     $output .= '<div class="wpmdia-button-holder">';
                      if($link  == 'lightbox' && $enable_lightbox == 1){
                         $output .= '<a href="javascript:void(0);" class="wpmdia-zoom">
                          <i class="fa fa-search" aria-hidden="true"></i></a>';
                         }
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
                   if($link  == 'lightbox'){
                          $output .= '<div class="wpmdia-slider-overlay"></div>';
                   }else{
                       $output .= '<div class="wpmdia-default-overlay"></div>';
                   }


              $output .= "</figure>";    
              $pos++;
        }
  }


$output .= "</div></div>";