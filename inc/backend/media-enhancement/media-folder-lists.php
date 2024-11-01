<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$core_class = new Core_Lite_Class();
$count_pages = wp_count_posts('attachment')->inherit;
$get_active_folder_id = (isset($_GET['wpmmanager-folder']) ? (int) $_GET['wpmmanager-folder'] : "");
$list_results = DatabaseLite_Class::tbl_recur_array_builder(WPMManagerLite_FolderLists,$get_active_folder_id); 
$getfolderHTML = $core_class->FolderTreeStructure($list_results,$get_active_folder_id,'');
$unorganized_cnt = WPMManagerLite_Libary::get_unorganized_count_files();
?>
<div class="wpmmanager-folder-rtwrapper wpmmanager-rt-wrap wpmmanager-hide" id="wpmmanager<?php echo esc_attr($current_blog_id); ?>">
	<div class="wpmmanager-header-wrap">
		<h2><?php _e('Media Manager',WPMManagerLite_TD);?></h2>
		<a href="javasript:void(0)" class="wpmmanager-create-new-folder button button-primary button-large">
		   <i class="fa fa-folder-open-o"></i>
		  <?php _e('Create Folder',WPMManagerLite_TD);?>
		</a>
		<img src="<?php echo WPMManagerLite_IMG_DIR;?>/ajax_loader.gif" class="wpmedia-hide-icon"/>
	</div>
	<div class="wpmmanager-folders-lists-wrapper">
           
		<div class="coverfiles folderlists-wrap">
			<ul class="wpmedia-wrapper">
			<!-- class="wpmmanager-media-fname wpmedia-current-active wpmm-expanded" -->
			<li data-id="0" data-group="1" class="media-ofiles">
			 <!-- <a href="<?php echo admin_url('upload.php?wpmmanager-folder=');?>" data-child="0" data-id="0" class="wpmedia-manager-files wpmedia-folder-title wpmm-allfiles" data-type="allfiles"> -->
			   <i class="fa fa-file"></i>
			     <?php _e('All Files',WPMManagerLite_TD); ?>
			   <span class="wpmmedia-totalfiles-cnt allorganized-files"><?php echo $count_pages;?></span>
			 <!-- </a> -->
			 </li>
			 <li data-id="-1" data-group="1" class="media-ofiles">
			<!-- <a href="<?php echo admin_url('upload.php?wpmmanager-folder=-1');?>" data-child="0" data-id="-1" class="wpmedia-manager-files wpmedia-folder-title wpmm-unorganized-files" data-type="unorganized_files"> -->
			   <i class="fa fa-minus-square-o"></i>
			     <?php _e('Unorganized',WPMManagerLite_TD); ?>
			   <span class="wpmmedia-totalfiles-cnt unorganized-files"><?php echo $unorganized_cnt;?></span>
			<!-- </a> -->
			</li>
                     <li data-id="0" class="wpmmanager-media-fname wpmedia-current-active wpmm-expanded" id="wpmedialists-0" data-type="allfiles">
			 <a href="javascript:void(0);" data-child="0" data-id="0" class="wpmedia-manager-files wpmedia-folder-title wpmm-allfiles" data-type="allfiles">
			   <i class="fa fa-file"></i>
			     <?php _e('All Media Files',WPMManagerLite_TD); ?>
			   <span class="wpmmedia-totalfiles-cnt allorganized-files"><?php echo $count_pages;?></span>
			 </a>
			   <?php echo $getfolderHTML;  ?>
			 </li>
			

			</ul>
		</div>
	</div>
</div>

