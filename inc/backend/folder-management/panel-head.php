<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<div>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
</div>
    <div class="wpmdiam-header-section">
       <div class="wpmdiam_leftsection">
			<div class="title_menu"><?php _e(WPMManagerLite_TITLE,WPMManagerLite_TD);?></div>
			<div class="wpmdiam-version-wrapper">
			  <span>Version <?php echo WPMManagerLite_VERSION;?></span>
			</div>
		</div>

        <div class="wpmdiam-header-social-link">
            <p class="wpmdiam-follow-us"><?php _e('Follow us for new updates',WPMManagerLite_TD);?></p>
            <div class="fb-like" data-href="https://www.facebook.com/accesspressthemes" data-layout="button" data-action="like" data-show-faces="true" data-share="false"></div>
            <a href="https://twitter.com/accesspressthemes" class="twitter-follow-button" data-show-count="false">Follow @accesspressthemes</a>
        </div>
    </div>