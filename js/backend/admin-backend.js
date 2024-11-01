/**
 * Wordpress Media Manager Plugin Script
*/
(function ($) {
  $(document).ready(function () {
      
      var media_page = null;
      var gap = '&nbsp;&nbsp;';
      var wpmediabreadcrumb,wpmedia_folderbrowser,sid,sParentID;
      var default_breadcrumb_txt = wpmmanager_admin_ajax.default_breadcrumb_txt;         
      var main_home_breadcrumb  = wpmmanager_admin_ajax.main_home_breadcrumb;
      var AjaxURL               = wpmmanager_admin_ajax.ajax_url;
      var AjaxNonce             = wpmmanager_admin_ajax.ajax_nonce;
      var saved_success_message = wpmmanager_admin_ajax.success_message;
      var error_message      = wpmmanager_admin_ajax.error_message;
      var current_blog_id    = wpmmanager_admin_ajax.current_blog_id;
      var empty_msg          = wpmmanager_admin_ajax.empty_message;
      
      var deletemsg          = wpmmanager_admin_ajax.delete_message;
      var checkdeleteall     = wpmmanager_admin_ajax.check_to_delete_all;

      //Alert Message
      var folder_exists      = wpmmanager_admin_ajax.msg_folder_exists;
      var folder_delete      = wpmmanager_admin_ajax.msg_folder_delete;
      var folder_delete_all  = wpmmanager_admin_ajax.msg_folder_delete_all; // ask for permission to delete all folders/sub folders at once after checking enable remove all once is enabled.
      var folders_id         = wpmmanager_admin_ajax.folders_id;
      var allfolders         = wpmmanager_admin_ajax.allfolders;
      var allmimetypes       = wpmmanager_admin_ajax.allmimetypes;
      var filtermimeText     = wpmmanager_admin_ajax.filtermimeText;
      var uploadedToThisPost = wpmmanager_admin_ajax.uploadedToThisPost;
      var current_post_type  = wpmmanager_admin_ajax.current_post_type;
      var wpdia_unattached   = wpmmanager_admin_ajax.wpdia_unattached;
      var displaymedia_num   = wpmmanager_admin_ajax.displaymedia_num;

      var enable_customfilters   = wpmmanager_admin_ajax.enable_customfilters; // enable custom filter check
      var GetUploadedPostype = uploadedToThisPost + current_post_type;
      var pgnow = wpmmanager_admin_ajax.pagenow;
      var j = 0; 
      var CurFolder = 0;
      var tbody_content = "";
      var tbody_content1 = "";
      var listsFoldersFilter = {}, listsFilterFolders = {};
      var wpmediamanager_move_file;

      var custom_selected_size = wpmmanager_admin_ajax.custom_selected_size;
      var custom_selected_weight = wpmmanager_admin_ajax.custom_selected_weight;
      var size_default_lbl = wpmmanager_admin_ajax.size_default_lbl;
      var wt_default_lbl = wpmmanager_admin_ajax.wt_default_lbl;
      var urole = wpmmanager_admin_ajax.userrole;

     var wrappers = $('.wpmedia-manager-attachments-wrapper');
     var $wrapperdiv = $('.attachments-browser');

       /* check if media library page */ 
       BrowserDragFn = function () {
            if ($('.wpmediamanager-browser-folders .wpmmanager-attachmedia').length > 0) {
                $('.wpmediamanager-browser-folders .wpmmanager-attachmedia:not(.mediaback)').draggable({
                    revert: true,
                    cursorAt: {top: 10, left: -10},
               });
            }
        };
       /*
       *  Draggable Media Files
       */
       var media = window.wp.media;
       var media2 = wp.media; 
       startMediaAttachments = function () {
            if ($('ul.attachments .wpmmanager-uidraggable').length > 0) {
               $('ul.attachments .attachment.wpmmanager-uidraggable:not(.attachment.uploading)').draggable({
                    revert: true,
                    cursorAt: {top: 10, left: 0},
                    helper: draghelperfn,
                    appendTo: ".wpmediamanager-lists-wrap",
                    drag: handleFilesDrag,
                    start: startDrag,
                    stop: stopDrag
               });
            }
        };
        
        startDrag = function (event, ui) {
           $('.ui-draggable-dragging').addClass('wpmdia_dragsmall').removeClass('wpmediam_draglarge');
            var mediaelemIDs = ui.helper.data('mediaelemIDs').split(',');
            $(mediaelemIDs).each(function (i, val) {
                $('.wpmediamanager-lists-wrap .attachments .attachment[data-id="' + val + '"]').css('visibility', 'hidden');
            });
        };
        
         stopDrag = function (event, ui) {
            var mediaelemIDs = ui.helper.data('mediaelemIDs').split(',');
            $(mediaelemIDs).each(function (i, val) {
                 $('.wpmediamanager-lists-wrap .attachments .attachment[data-id="' + val + '"]').css('visibility', 'visible');
            });
        };
        
        draghelperfn = function (e) {
                        var mediaelemIDs = [];
                        var mediaelem = $.merge($(this), $('.wpmediamanager-lists-wrap .attachments .attachment.selected').not(this));
                        mediaelem.each(function () {
                            mediaelemIDs.push($(this).data('id'));
                        });
                        helper = $(this).clone();
                        helper.append('<span class="wpmdia_dCOuntNum">Move ' + mediaelem.length + ' File</<span>');
                        helper.data('mediaelemIDs', mediaelemIDs.join());
                        return helper;
        };
        
        handleFilesDrag = function(){
            // $('.ui-draggable-dragging').addClass('wpmf_dragsmall').removeClass('wpmf_draglarge').width(30).height(30);
       };
        
        /*
       *  Droppable Media Files
       */     
       startDroppableAttachments = function () {
           if (media_page == 'table') {
                var $droparea = $('.wpmediamanager-browser-folders .wpmmanager-attachmedia,.wpmmanager-media-fname a.wpmedia-folder-title,#wpmedialists-0');
           } else {
                var $droparea = $('.wpmediamanager-browser-folders .wpmmanager-attachmedia,.wpmmanager-media-fname a.wpmedia-folder-title,[id^="__wp-uploader-id-"]:visible #wpmedialists-0'); 
           }
           if ($droparea.length > 0) {
               $droparea.droppable({
                   hoverClass: "wpmediafolder-hovered",
                   tolerance: 'pointer',
                   //accept: ".wpmmanager-uidraggable",
                   drop: handleFilesDrop,

                   
               });
            }
        };
      
        
      handleFilesDrop = function(event, ui){
            if ($(ui.draggable).hasClass('wpmmanager-attachmedia')) {
              // folder inside folder drag and drop case
                 var droppablefolderID = $(this).attr('data-id');        
               var droppableParentID = $(this).attr('data-parentid'); 
               CheckEventDrop(event,ui,droppablefolderID,droppableParentID);                 
            }else{
              // media to folder drag and drop case
                var folder_id = $(this).attr('data-id');
                //alert(folder_id);
                var mediaelemIDs = ui.helper.data('mediaelemIDs');
             // console.log(mediaelemIDs);
                if (mediaelemIDs != undefined) {
                    $(mediaelemIDs.split(',')).each(function () {
                        $('li.attachment[data-id="' + this + '"]:not(.ui-draggable-dragging)').hide();
                    });
                }
              setTimeout(function () {
                  $.ajax({
                      url: AjaxURL,
                      type: 'POST',
                      data: {
                          action: "wpmedia_dragsave_file",
                          collectids: mediaelemIDs,
                          folderid: folder_id,
                          wp_nonce: AjaxNonce
                      },
                      success: function(resp) {
                           if (resp == 1) {
                             if(folder_id != 0){
                                    var counttotal = $('.wpmedia-folder-title[data-id="'+ folder_id +'"] .wpmmedia-totalfiles-cnt').html();
                                    var new_countfiles = parseInt(counttotal) + mediaelemIDs.split(',').length;
                                    $('.wpmedia-folder-title[data-id="'+ folder_id +'"] .wpmmedia-totalfiles-cnt').html(new_countfiles);
                               }

                               if (media_page != 'table') {
                                if (wp.media.frame.content.get() !== null) {
                                    wp.media.frame.content.get().collection.props.set({ignore: (+new Date())});
                                    wp.media.frame.content.get().options.selection.reset();
                                } else {
                                    wp.media.frame.library.props.set({ignore: (+new Date())});
                                    if (pgnow == 'upload.php') {
                                        wpmediamanager_move_file.controller.state().get('selection').reset();
                                    }
                                }
                               // $('.wpmedia-headermove').removeClass('selected');
                            }else{
                               // table view
                                $(mediaelemIDs.split(',')).each(function () {
                                $('table.media #the-list #post-' + this).hide();
                                });
                               $('#the-list input[name="media[]"]').prop('checked', false);
                                
                            }
                               $('.wpmedia-headermove').removeClass('selected');
                           }
                      }
                   });
                 }, 500);
                
                
            }
       };
       
               
        CheckEventDrop = function(event,ui,droppablefolderID,droppableParentID){
               var $CurID = $(ui.draggable[0]);
               var draggableFolderID = $CurID.attr('data-id');        
               var draggableFolderParentID = $CurID.attr('data-parentid');        
               var folder_name = $CurID.find('.wpmainfilename a').text();    
              // if (droppablefolderID != draggableFolderID && droppablefolderID != draggableFolderParentID && droppableParentID != draggableFolderID) {
               var parent_id = $('select.wpmedia-all-folders option:selected').attr('data-id');                 
               
              $.ajax({
                    url: AjaxURL,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: "change_folder_arrangement",
                        draggableFolderParentID: draggableFolderParentID,
                        draggableFolderID: draggableFolderID,
                        droppablefolderID: droppablefolderID,
                        droppableParentID: droppableParentID,
                        selectedParentID: parent_id,
                        wp_nonce: AjaxNonce
                    },
                    success: function(response) {
                         //console.log(response);
                         if(response.status == true){   
                            var count_total = response.count_num;
                            allfolders[draggableFolderID].parent_id = droppablefolderID;
                            allfolders[draggableFolderID].folder_order = count_total;
                            IncreaseOrder = function (parent) {
                                   allfolders.each(function (index, value) {
                                       if (allfolders[index].parent_id == parent) {
                                           allfolders[index].folder_order++;
                                           IncreaseOrder(allfolders[index]);
                                       }
                                   });
                               };
                    $('.wpmmanager-media-fname[id="wpmedialists-' + draggableFolderID + '"]').remove();
                   
                   if(droppablefolderID == 0){
                   var html_append = '<li class="wpmmanager-media-fname wpmm-collapsed" data-id="wpmedialists-' + draggableFolderID + '" data-parent_id="' + allfolders[draggableFolderID].parent_id + '" data-type="foldername">';
                   html_append += '<div class="wpmedia-icon-open wpmedia-fchild" data-id="' + draggableFolderID + '" data-parent-id="' + allfolders[draggableFolderID].parent_id + '" data-slug="' + allfolders[draggableFolderID].slug + '" style="opactiy:0;visibility:hidden;">';
                   html_append += '<i class="fa fa-caret-right"></i></div>';
                   html_append += '<i class="fa fa-folder"></i>';
                   html_append += '<a href="javascript:void(0);" class="wpmedia-folder-title wpmedia-manager-files" data-id="' + draggableFolderID + '" data-child="" data-type="foldername">' + allfolders[draggableFolderID].name + '<span class="wpmmedia-totalfiles-cnt allorganized-files">0</span></a>';
                   html_append += '</li>';
                   $('.wpmediamanager-folders-wrap').append(html_append);
                    //remove right section lists name on dropbbale 
                  }
                    var option_val = $('.wpmedia-all-folders option[data-id="' + draggableFolderID + '"]').val();
                    $('.wpmedia-all-folders option[data-id="' + draggableFolderID + '"]').remove();
                    var appendAfterOption = $('.wpmedia-all-folders option[data-id="' + droppablefolderID + '"]');
                    var option_html = "<option value='"+option_val+"' data-id='" + draggableFolderID + "'  data-parentid='" + droppablefolderID + "'>"+gap.repeat(count_total) + folder_name+"</option>"
                    appendAfterOption.after(option_html);
                    //Remove Attachment Folder from top section
                    $('li.wpmmanager-attachmedia[data-id="' + draggableFolderID + '"]').remove();
                    if (media_page != 'table') {
                        wp_media_manager_init();
                        if ($('.media-frame').hasClass('mode-select')) {
                            $('select.wpmedia-all-folders').hide();
                        }
                    }
                       $('select.wpmedia-all-folders option').prop('selected', null);
                       $('select.wpmedia-all-folders option[data-id="' + parent_id + '"]').prop('selected', 'selected');
                      }
                    }
                 });               
             };

         /*
          * Onclick Open Sub folders Events
         */
         setClickOpenEvents = function(selector, clicked_type){   
         
              if (typeof $wpmediamanagertree == "undefined")
                return;
            
               var dataid = selector.attr('data-id');
               var datachild = selector.attr('data-child');
               if(selector.parent().hasClass('wpmedia-current-active')){ 
                 }else{
                  $('.wpmmanager-media-fname').removeClass('wpmedia-current-active');
                  selector.parent().addClass('wpmedia-current-active');
                 }
                  if(selector.parent().hasClass('wpmm-expanded')){
                   selector.parent().removeClass('wpmm-expanded').addClass('wpmm-collapsed');
                  }else{
                    selector.parent().removeClass('wpmm-collapsed').addClass('wpmm-expanded');
                  }
                  if(datachild == '1'){
                       var subhtml_append;
                        $.ajax({
                              url: AjaxURL,
                              type: 'post',
                              dataType: 'json',
                              data: {
                                  action: "wpmmedia_get_subfolders",
                                  folder_id: dataid,
                                  wp_nonce: AjaxNonce
                              },
                              beforeSend: function() {
                                     //$(".wpmedia-hide-icon").css('display', 'block');
                              },
                              complete: function() {
                                     //$(".wpmedia-hide-icon").css('display', 'none');
                              },
                             
                              success: function(response) {
                              // console.log(response);
                              if(response != "empty"){
                                subhtml_append = "<ul class='wpmediamanager-folders-wrap' style='display: none'>";
                                  for (i = 0; i < response.length; i++) {
                                     
                                      subhtml_append += '<li class="wpmmanager-media-fname wpmm-collapsed" id="wpmedialists-' + response[i].id + '" data-parent_id="' + response[i].parent_id + '" data-type="foldername">';
                                           var cnt = response[i].total_media_cnt;
                                        if(typeof displaymedia_num != "undefined" && displaymedia_num == 1){
                                             var countfiles = '<span class="wpmmedia-totalfiles-cnt allorganized-files">'+cnt+'</span>';
                                        }else{
                                             var countfiles = '';
                                         }

                                      if (response[i].child instanceof Array) {
                                         subhtml_append += '<div class="wpmedia-icon-open wpmedia-fchild" data-id="' + response[i].id + '" data-order="' + response[i].folder_order + '" data-parent-id="' + response[i].parent_id + '" data-slug="' + response[i].slug + '"><i class="fa fa-caret-right" aria-hidden="true"></i></div>';
                                      } else {
                                         subhtml_append += '<div class="wpmedia-icon-open wpmedia-fchild" data-id="' + response[i].id + '" data-order="' + response[i].folder_order + '" data-parent-id="' + response[i].parent_id + '" data-slug="' + response[i].slug + '" style="opacity:0;visibility:hidden;"><i class="fa fa-caret-right" aria-hidden="true"></i></div>';
                                      }
                                    if (response[i].child instanceof Array) {
                                       subhtml_append +='<i class="fa fa-folder"></i><a href="javascript:void(0);" class="wpmedia-folder-title wpmedia-manager-files" data-child="'+datachild +'" data-id="'+  response[i].id +'" data-type="foldername" data-slug="'+response[i].slug+'">'+ response[i].name + countfiles + '</a></li>';
                                    }else{
                                       subhtml_append +='<i class="fa fa-folder"></i><a href="javascript:void(0);" class="wpmedia-folder-title wpmedia-manager-files" data-child="0" data-id="'+  response[i].id +'" data-type="foldername" data-slug="'+response[i].slug+'">'+ response[i].name + countfiles + '</a></li>';
                                    }
                                  }
                                  subhtml_append += '</ul>';
                                  }else{
                                     subhtml_append = '';
                                  }
                               
                                  $('ul.wpmediamanager-folders-wrap a[data-id="' + dataid + '"]').parent().removeClass('wpmm-collapsed').addClass('wpmm-expanded');
                                  $('ul.wpmediamanager-folders-wrap a[data-id="' + dataid + '"]').next('.wpmediamanager-folders-wrap').remove();
                                  $('ul.wpmediamanager-folders-wrap a[data-id="' + dataid + '"]').after(subhtml_append);
                                  $('ul.wpmediamanager-folders-wrap a[data-id="' + dataid + '"]').next().show();
                                  startMediaAttachments();
                                  startDroppableAttachments();
                               

                                 if(clicked_type != 'icon_clicked'){
                                  $('select.wpmedia-all-folders [data-id="' + dataid + '"]').prop('selected', 'selected').change();
                                 } 

                              }
                          });
                    }else{
                       
                        if(clicked_type != 'icon_clicked'){
                          $('select.wpmedia-all-folders [data-id="' + dataid + '"]').prop('selected', 'selected').change();
                        } 
                    }
         };
         
         /*
          * Onclick All Media Files
         */
         setClickAllFolders = function(selector){   
                if(selector.parent().hasClass('wpmedia-current-active')){   
                 }else{
                  $('.wpmmanager-media-fname').removeClass('wpmedia-current-active');
                  selector.parent().addClass('wpmedia-current-active');

                 }
         };
                
          /*
          * Onclick Close Sub folders Events
         */
         setClickCloseEvents = function(selector){ 
             
             var dataid = selector.attr('data-id');
             
             if (typeof $wpmediamanagertree == "undefined")
                return;
            
            $wpmediamanagertree.find('a[data-id="' + dataid + '"]').next().slideUp("fast", function () {
                $(this).remove();   
             
            });
            
            $wpmediamanagertree.find('a[data-id="' + dataid + '"]').parent().removeClass('wpmm-expanded').addClass('wpmm-collapsed');
         };
        
         /*
          * Open Folder Events
         */
        openFolderEvents = function(){  
            $wpmediamanagertree = $('.wpmmanager-folder-rtwrapper');   
            $('.wpmmanager-folder-rtwrapper').on('click','li.wpmmanager-media-fname.wpmm-collapsed a.wpmedia-folder-title', function (e) {
                e.preventDefault;
                e.stopPropagation();
                var datatype = $(this).attr('data-type');
                var dataid = $(this).attr('data-id');
               if(datatype !== "allfiles"){
                  setClickOpenEvents($(this),'folder_clicked');
               }else{
                 setClickAllFolders($(this),'folder_clicked');
               }
            });
             
             //allmedia files lists
             $('.wpmmanager-folder-rtwrapper').on('click','li.wpmm-expanded a.wpmm-allfiles', function (e) {
                e.preventDefault;
                e.stopPropagation();
                setClickAllFolders($(this));
                 $('select.wpmedia-all-folders [data-id="0"]').prop('selected', 'selected').change();
            });

             $('.wpmmanager-folder-rtwrapper').on('click','li.wpmmanager-media-fname.wpmm-collapsed .wpmedia-icon-open', function (e) {
                e.preventDefault;
                e.stopPropagation();
                setClickOpenEvents($(this).parent().find('a.wpmedia-folder-title'),'icon_clicked');
             
            });
              $('.wpmmanager-folder-rtwrapper').on('click','li.wpmmanager-media-fname.wpmm-expanded .wpmedia-icon-open', function (e) {
                e.preventDefault;
                e.stopPropagation();
                setClickCloseEvents($(this).parent().find('a.wpmedia-folder-title'));
                 
            });

             $('.wpmmanager-folder-rtwrapper').on('click','li.wpmmanager-media-fname.wpmm-expanded a.wpmedia-folder-title', function (e) {
                e.preventDefault;
                e.stopPropagation();
                 var datatype = $(this).attr('data-type');
                
                 if(datatype !== "allfiles"){
                   setClickCloseEvents($(this));
                 }else{
                   setClickAllFolders($(this));
                 }
            });  
        };   
        

        /*
        * Create New Folder And Sub Folders & append to folder lists
        */
        createNewFolder = function(media_page){
//            console.log(allfolders);
//             console.log(folders_id);
                if ($('.wpmmmanager-main-wrapper .wpmmanager-create-new-folder').length != 0 || $('.wpmmmanager-main-wrapper2 .wpmmanager-create-new-folder').length != 0) {
                 
                   $('body').on('click','.wpmmanager-create-new-folder',function(e){
                       e.preventDefault();
                        $('.wpmmanager_overlay_wrap').slideDown('slow');
                        $('.wpmmanager_overlay_wrap').addClass('wpmedia-popup-show');
                   });

                   $('.wpmmanager_close').click(function(e){
                      e.preventDefault();
                       $(this).find('.wpmm_action_settings').val('add_folder');
                       $(this).find('.wpmmanagersubmitbtn').val('ADD');
                       $(this).find('.wpm_folderDetailsID,.wpmmanagername').val('');
                       $(this).find('.wpmedia-manager-fname').html('');
                       $('.wpmmanager_overlay_wrap').slideUp("slow");
                       $('.wpmmanager_overlay_wrap').removeClass('wpmedia-popup-show');
                     });

            /*
             * Function To Display Popup / Validating Empty Field
            */
            $('.wpmmanagersubmitbtn').click(function(){
                    var foldername = $('.wpmedia-manager-fname').html();
                    var foldnrname = $('#wpm_manager_folder_name').val();
                    var f_action = $('.wpmm_action_settings').val();
                    var countlength = $('.wpmedia-current-active').find('div.wpmedia-icon-open').attr('data-order');
                    var active     = $('.wpmedia-current-active').attr('data-type');   
                if(countlength == "undefined" || countlength == ''){
                    countlength = 0;
                }

                if(f_action == "add_folder"){
                        var fid        = $('.wpmedia-current-active').attr('id');
                        fid            = fid.split("-");
                        fid            = fid[1];
                    }else{
                     var folderid = $('.wpm_folderDetailsID').val();
                     var fid = '';
                    }
                  if (foldername == '' && foldnrname == '' ) {
                       alert(empty_msg);
                       return false;
                  }else{
                       $.ajax({
                        url: AjaxURL,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            action: "wpmmedia_addfolder",
                            foldername: foldername,
                            activefiles : active,
                            parent_folder_id: fid,
                            folderid: folderid,
                            folder_action:f_action,
                            countlength: countlength,
                            wp_nonce: AjaxNonce
                        },
                        beforeSend: function() {
                             //  $(".wpmedia-manager-overlay").css('display', 'block');
                               $(".wpmedia-hide-icon").css('display', 'block');
                               $('.wpmmanager_overlay_wrap').css('display','none');
                        },
                        complete: function() {
                              //$(".wpmedia-manager-overlay").css('display', 'none');
                                $('.wpmm_action_settings').val('add_folder');
                                $('.wpmmanagersubmitbtn').val('ADD');
                                $('.wpm_folderDetailsID,.wpmmanagername').val('');
                                $('.wpmedia-manager-fname').html('');
                                $(".wpmedia-hide-icon").css('display', 'none');
                                $('.wpmmanager_overlay_wrap').css('display','none');
                        },

                        success: function(response) { 
                           // alert(response);
                            //console.log(response);
                          if(response.message == "success"){      
                            var resultt = response.result;
                            var fname = resultt.folder_name;
                            var parentid = resultt.folder_parent;
                            var slug = resultt.folder_slug;
                            var folder_id = resultt.folder_id;
                            var folder_order = resultt.folder_order;
                            var foldertype = resultt.type;
                            if(foldertype == "folder"){
                              var  type = "foldername";
                            }else{
                                 var  type = "categoryname";
                            }
                            var countfiles = response.count;
                          

                        if(typeof displaymedia_num != "undefined" && displaymedia_num == 1){
                            var countfiles = '<span class="wpmmedia-totalfiles-cnt allorganized-files">'+ countfiles +'</span>';
                        }else{
                            var countfiles = '';
                        }
                        fname = String(fname).charAt(0).toUpperCase()+ String(fname).slice(1);
         
                         if(f_action == "add_folder"){
                            if( active == "allfiles"){         
                                 tbody_content += '<li class="wpmmanager-media-fname wpmm-collapsed" id="wpmedialists-'+folder_id+'" data-parent_id="'+parentid+'" data-type="'+type+'" style="display:none;">';
                                 tbody_content += '<div class="wpmedia-icon-open" data-id="'+folder_id+'" data-order="'+folder_order+'" data-parent-id="0" data-slug="'+slug+'" style="opacity:0;visibility:hidden;">';
                                 tbody_content += '<i class="fa fa-caret-right" aria-hidden="true"></i></div>';
                                 tbody_content +=  '<i class="fa fa-folder"></i><a href="javascript:void(0);" class="wpmedia-folder-title wpmedia-manager-files" data-child="0" data-id="'+folder_id+'" data-type="foldername" data-slug="'+slug+'">'+fname+countfiles+'</a></li>';
                                 // ul.abc >li:nth-child(2)).after('added');
                              
                               if($('ul.wpmediamanager-folders-wrap li').length == 0){
                                  $('.wpmedia-current-active').find('ul.wpmediamanager-folders-wrap').append(tbody_content);
                                  $('.wpmedia-current-active').find('ul.wpmediamanager-folders-wrap > li').slideDown('slow'); 
                              }else{
                                 $('.wpmedia-current-active').find('ul.wpmediamanager-folders-wrap > li:last-child').after(tbody_content);
                                 $('.wpmedia-current-active').find('ul.wpmediamanager-folders-wrap > li:last-child').slideDown('slow'); 
                              }

                              tbody_content1 +=  '<li class="wpmmanager-attachmedia ui-droppable ui-draggable ui-draggable-handle" data-parentid="'+parentid+'" data-id="'+folder_id+'">';
                              tbody_content1 += '<div class="wpmega-fonticons"><i title="Edit Folder" class="wpmega-fedit-icons fa fa-pencil" title="Edit Folder"></i>';
                              tbody_content1 += '<i class="wpmega-fdelete-icons fa fa-trash" title="Delete Folder"></i></div>';
                              tbody_content1 += '<div class="wpmmanager-previewattach"><i class="wpmega-ficons fa fa-folder"></i>';
                              tbody_content1 += '<div class="wpmainfilename"><a href="javascript:void(0);" data-id="'+folder_id+'" data-slug="'+slug+'">'+fname+'</a></div></div></li>';
                              if($('ul.wpmediamanager-browser-folders li').length == 0){
                                   $('ul.wpmediamanager-browser-folders').append(tbody_content1);
                              }else{
                                 $('ul.wpmediamanager-browser-folders > li:last-child').after(tbody_content1);
                              }
                             
                            }else{
                                tbody_content += '<ul class="wpmediamanager-folders-wrap" style="display:none;">';
                                tbody_content += '<li class="wpmmanager-media-fname wpmm-collapsed" id="wpmedialists-'+folder_id+'" data-parent_id="'+parentid+'" data-type="'+type+'">';
                                tbody_content += '<div class="wpmedia-icon-open" data-id="'+folder_id+'" data-order="'+folder_order+'" data-parent-id="0" data-slug="'+slug+'" style="opacity:0;visibility:hidden;">';
                                tbody_content += '<i class="fa fa-caret-right" aria-hidden="true"></i></div>';
                                tbody_content += '<i class="fa fa-folder"></i>';
                                tbody_content += '<a href="javascript:void(0);" class="wpmedia-folder-title wpmedia-manager-files" data-child="0" data-id="'+folder_id+'" data-type="foldername" data-slug="'+slug+'">'+fname+countfiles+'</a></li>';
                                tbody_content += '</ul>';
                                $('.wpmedia-current-active').find('.wpmedia-icon-open[data-id='+fid+']').addClass('wpmedia-fchild');
                                $('.wpmedia-current-active').find('.wpmedia-icon-open[data-id='+fid+']').css({
                                  'opacity' : '1',
                                  'visibility':'visible'
                                });
                                $('.wpmedia-current-active').find('a.wpmedia-folder-title[data-id='+fid+']').attr('data-child','1');
                                $('.wpmedia-current-active').find('a.wpmedia-folder-title[data-id='+fid+']').after(tbody_content);
                                $('.wpmedia-current-active').find('a.wpmedia-folder-title[data-id='+fid+'] ul.wpmediamanager-folders-wrap').show().slideDown('slow');                       
                            }
                          }else{
                              //edit folder 
                             if(media_page == "table"){
                                  $('select#wpmedia-select-folder option[data-id="' + folder_id + '"]').html(fname);
                             }else{
                                  $('select.wpmedia-all-folders option[data-id="' + folder_id + '"]').html(fname);
                             }
                             $('.wpmmanager-attachmedia[data-id="' + folder_id + '"]').find('.wpmainfilename > a').html(fname);
                             $('.wpmmanager-media-fname[id="wpmedialists-' + folder_id + '"] a[data-id="' + folder_id + '"]').html(fname);
                          }
                          
                         //  openFolderEvents();
                           //Add element to the select list
                                  
                            var FCount = $('select.wpmedia-all-folders option').length - 1;
                            listsFoldersFilter[folder_id] = String(FCount + 1);
                            listsFilterFolders[FCount + 1] = response.folder_id;
                            allfolders[folder_id] = {id: folder_id, name: fname, parent_id: parentid, slug: slug};
                            folders_id[FCount + 1] = folder_id;
                            
                          if (media_page !== 'table') {
                            $('select.wpmedia-all-folders option:selected').after('<option value="' + (FCount + 1) + '" data-id="' +folder_id + '" data-parentid="' + parentid + '">' + fname + '</option>');
                                wp_media_manager_init();
                                if ($('.media-frame').hasClass('mode-select')) {
                                    $('select.wpmedia-all-folders').hide();
                                }
                                $('select.wpmedia-all-folders option[data-id="' + CurFolder + '"]').prop('selected', 'selected');
                                } else {
                            $('select.wpmedia-all-folders option:selected').after('<option value="' + folder_id + '" data-id="' + folder_id+ '" data-parent_id="' + parentid + '">' + fname + '</option>');
                          }
                               
                          BrowserDragFn();
                          startDroppableAttachments();
                          //startDroppableAttachments2();
                          ClickEventEditFolderName(media_page);
                          
                       }else{
                          alert(error_message);
                       }
                }
              });               
            }
          });

           $('body').on('keyup','.wpmmanagername',function(){
            // alert($(this).val());
             $('.wpmedia-manager-fname').html($(this).val());
            });


           $('body').on('click', '.wpmmanager_overlay_wrap', function () {
              $(this).find('.wpmm_action_settings').val('add_folder');
              $(this).find('.wpmmanagersubmitbtn').val('ADD');
              $(this).find('.wpm_folderDetailsID,.wpmmanagername').val('');
              $(this).find('.wpmedia-manager-fname').html('');
              
              $('.wpmmanager_overlay_wrap').slideUp("slow");
              $('.wpmmanager_overlay_wrap').removeClass('wpmedia-popup-show');
             
           }).find('#wpmmanager-dialog-form').on('click', function (e) {
             e.stopPropagation();
           });

        //  openFolderEvents();
        //alert(pgnow);
         if (pgnow == "upload.php") {
            openFolderEvents();
         }
          if(media_page != "table"){
            ClickEventEditFolderName(media_page);
          }
        } 
       };
       
      
        var repeat = function (num) {
          return new Array(isNaN(num) ? 1 : ++num).join(this);
        };
        
          /*
          * Change attribute for filter dropdown folders
          */
          initFilterOnTable = function () { 
            // console.log(allfolders);
            // console.log(listsFilterFolders);  
            if (media_page != 'table') {
               $('.wpmedia-all-folders').find('option').each(function() {
                    if ($(this).val() !== 0) {
                        if(typeof (listsFilterFolders[$(this).val()]) !== 'undefined' && typeof (allfolders[listsFilterFolders[$(this).val()]]) !== 'undefined'){
                        $(this).attr('data-id', allfolders[listsFilterFolders[$(this).val()]].id);
                        $(this).attr('data-parentid', allfolders[listsFilterFolders[$(this).val()]].parent_id);
                    }
                  }
                });

                  //On change event for filter folder options 
                $('[id^="__wp-uploader-id-"]:visible select.wpmedia-all-folders').on('change', function () {
                     alterFolder.call(this);
                });
                 if ($('ul.attachments').length) {
                    $('ul.attachments').get(0).addEventListener("DOMNodeInserted", function () {
                        $('ul.attachments').trigger('change');
                    });
                 }
              } else {            
                // submit the form on change filter dropdown on table view
                 $('body').on('change','select#wpmedia-select-folder', function () {
                   $('select#wpmedia-select-folder').parents('form').submit();
                 });
                  $('body').on('change','select#wpmedia-folder-size', function () {
                   $('select#wpmedia-folder-size').parents('form').submit();
                 });
                   $('body').on('change','select#wpmedia-folder-weight', function () {
                   $('select#wpmedia-folder-weight').parents('form').submit();
                 });
 
            }
        };
        
        /*
         * Change folder from filter dropdown options
         */
        alterFolder = function () {
          //console.log(allfolders);
           // alert($(this).find('option:selected').data('id'));
             if($(this).find('option:selected').data('id') != '' || $(this).find('option:selected').data('id') != "undefined"){
                  sid = $(this).find('option:selected').data('id');
                  sParentID = $(this).find('option:selected').data('parentid');
             }else{
                  sid = 0;
                  sParentID =  0;
             }  
            
             if (pgnow == 'upload.php' && media_page != 'table') {
                if ($('.media-frame.mode-select .select-mode-toggle-button').length != 0) {
                    $('.media-frame.mode-select .select-mode-toggle-button').click();
                }
            }

            if($('body').hasClass('wp-customizer')){
              $('body').append('<input type="hidden" class="wpmediam_select_folder_id" value="0">');
            }
  
             $('.wpmediam_select_folder_id').val(sid);
             var folderid = $('.wpmediam_select_folder_id').val();
             
              //unselect items
           // if (pgnow == 'upload.php') {
           //     if (typeof (wp.media) !== 'undefined' && typeof (wp.media.frame) !== 'undefined' && wp.media.frame.content.get() !== null) {
           //         wp.media.frame.content.get().options.selection.reset();
           //     }
           // }
            
             if (media_page !== 'table') {
                wpmediabreadcrumb = $('[id^="__wp-uploader-id-"]:visible .wpmediamanager-breadcrumb');
                wpmedia_folderbrowser = $('[id^="__wp-uploader-id-"]:visible .wpmedia-attachments-wrap .wpmediamanager-browser-folders');
            } else {
                wpmediabreadcrumb = $('.wpmediamanager-breadcrumb'); 
                wpmedia_folderbrowser = $('.wpmedia-attachments-wrap .wpmediamanager-browser-folders'); 
            }


            if (CurFolder == null || CurFolder < 0 || CurFolder == "undefined"){
                 CurFolder = 0;
            }else{
                 if(folderid ==  ''){
                 folderid = 0;
                }
                CurFolder = folderid;
            }
           
            var bfolders = allfolders[CurFolder];
              
           // change breadcrumb her
           var breadcrumb = ''; 
           wpmediabreadcrumb.html('');
          // alert(bfolders.parent_id);
           while (bfolders.parent_id != 0) {
                breadcrumb = '<li>&nbsp;&nbsp;>&nbsp;&nbsp;<a href="#" data-id="' + allfolders[bfolders.id].id + '">' + allfolders[bfolders.id].name + '</a></li>' + breadcrumb;
                bfolders = allfolders[allfolders[bfolders.id].parent_id];
            }
            if (bfolders.id != 0) {
                breadcrumb = '<li><a href="#" data-id="' + allfolders[bfolders.id].id + '">' + allfolders[bfolders.id].name + '</a></li>' + breadcrumb;
            }
            var first_breadcrumb = '<li>' + default_breadcrumb_txt + '&nbsp;<a href="#" data-id="0">&nbsp;' + main_home_breadcrumb + '&nbsp;</a>>&nbsp;&nbsp;</li>';   
            breadcrumb = first_breadcrumb + breadcrumb;
            //alert(breadcrumb);
            wpmediabreadcrumb.prepend(breadcrumb);
            $('.wpmediamanager-breadcrumb li a').click(function () {
                if (media_page != 'table') {
                    $('[id^="__wp-uploader-id-"]:visible .wpmedia-all-folders option[data-id="' + allfolders[$(this).data('id')].id + '"]').prop('selected', 'selected').change();
                } else {
                    $('.wpmedia-all-folders option[data-id="' + allfolders[$(this).data('id')].id + '"]').prop('selected', 'selected').change();
                }
            });
           //end change breadcrumb here
           

            if (sid !== 0) {
               var wpmedia_gobackbutton = '<li class="wpmmanager-attachmedia mediaback" data-id="' + sParentID + '" data-folderid="'+sid+'">' +
                    '<div class="wpmediamanager-attachment-preview">' +
                    '<span class="wpmdia-back-btn"></span>' +
                    '</div>' +
                    '</li>';
              wpmedia_folderbrowser.html('');
              wpmedia_folderbrowser.append(wpmedia_gobackbutton);
            }
            
            //save the current folder 
            $.ajax({
                 url: AjaxURL,
                 type: 'POST',
                 dataType: 'json',
                 data: {
                    action: "wpmmedia_alterfolders",
                    parentid: folderid,
                    wp_nonce: AjaxNonce
                },
                success: function (response) {   
                // console.log(response.result);
                 if(response.message == "success"){
                            if(folderid == 0){
                                 wpmedia_folderbrowser.html('');
                              }else{
                                 wpmedia_folderbrowser.find('li').each(function(){
                                  if(!$(this).hasClass('mediaback') ){
                                      $(this).remove();
                                  }   
                            });
                                
                              }
                            $.each(response.result, function(i, item) {
                             
                               var fname = item.folder_name;
                               var fid = item.folder_id;
                               var folder_parent = item.folder_parent;
                               var folder_slug = item.folder_slug;
                               var html_folder = '<li class="wpmmanager-attachmedia" data-parentid="' + folder_parent + '" \n\
                                     data-id="' + fid + '">\n\
                                     <div class="wpmega-fonticons"><i class="wpmega-fedit-icons fa fa-pencil" title="Edit Folder"></i>\n\
                                     <i class="wpmega-fdelete-icons fa fa-trash" title="Delete Folder"></i></div>\n\
                                      <div class="wpmmanager-previewattach">' +
                                     '<i class="wpmega-ficons fa fa-folder"></i>\n\
                                     <div class="wpmainfilename"><a href="javascript:void(0);" data-id="'+fid+'" \n\
                                      data-slug="'+folder_slug+'">'+fname+'</a></div></div></li>';
                                   // alert("second="+wpmedia_folderbrowser.html());
                                    wpmedia_folderbrowser.append(html_folder);
                           });
                             
                           startMediaAttachments();
                           startDroppableAttachments();
                           BrowserDragFn();
                           ClickEventEditFolderName(media_page);
                       }
                }
            });
           
           ClickEventEditFolderName(media_page);
           CheckDraggable(media_page);

           if(enable_customfilters == 1){
              if(pgnow == "upload.php"){
                if(media_page == "table"){
                  // allmimetypes 
                  var pdf_cnt = wpmmanager_admin_ajax.media_filter_pdf;
                  var zip_count = wpmmanager_admin_ajax.media_filter_zip;
                  var documents_cnt = wpmmanager_admin_ajax.media_filter_documents;
                  var selected_filetype = wpmmanager_admin_ajax.selected_filetype;
                  var wpmedia_manager_mime_addon = '<option value="wpmdiamanager_pdf_type">' + pdf_cnt +'</option>';
                   wpmedia_manager_mime_addon += '<option value="wpmdiamanager_zip_type">' + zip_count + '</option>';
                   wpmedia_manager_mime_addon += '<option value="wpmdiamanager_doc-pptx_type">' + documents_cnt + '</option>';
                   $('select[name="attachment-filter"] option[value="detached"]').before(wpmedia_manager_mime_addon);
                  if (selected_filetype != '') {
                      $('select[name="attachment-filter"] option[value="' + selected_filetype + '"]').prop('selected', true);
                  }

                }
              }
           }

           if (media_page != 'table') {
            var menu_left = '<div class="wpmmmanager-main-wrapper2"></div>';
            if ($('[id^="__wp-uploader-id-"]:visible .media-frame.hide-menu').length === 0) {
                  if ($('.media-frame-menu .media-menu .wpmmmanager-main-wrapper2').length == 0) {
                     $('[id^="__wp-uploader-id-"]:visible .media-frame-menu .media-menu:not([id^="__wp-uploader-id-"]:visible .media-frame.hide-menu .media-frame-menu .media-menu)').append(menu_left);
                  }
            }
           }

          // $('.wpmediam_load_flag').val(1);
            
        };
        
        CheckDraggable = function (media_page){
            if (media_page != 'table') {
                $('ul.attachments').unbind('change').bind('change', function () {
                    startMediaAttachments();
                   // startDroppableAttachments();
                });
            }else{
                 var $droptablerow = $('.wpmediamanager-rowmov');
                
                $('input[name="media[]"]').change(function () {
                    if ($(this).is(':checked')) {
                        $(this).parents('tr').find('.wpmediamanager-rowmov').addClass('wpmdia_selected');
                    } else {
                        $(this).parents('tr').find('.wpmediamanager-rowmov').removeClass('wpmdia_selected');
                    }
                });

                 if (typeof jQuery.ui != "undefined" && $droptablerow.length > 0) {
                    $droptablerow.draggable({
                      revert: true,
                      cursor: "move",
                      opacity: 0.6,
                      refreshPositions: true,
                        helper: function (e) {
                            var mediaID = [];
                            var media_elements = $.merge($(this).parents('tr').find('input[name="media[]"]'), $('#the-list input[name="media[]"]:checked').not($(this).parents('tr').find('input[name="media[]"]')));
                            //attach selected media_elements data-id to the helper
                           // console.log(media_elements);
                            media_elements.each(function () {
                                mediaID.push($(this).val());
                            });
                            helper = $(this).clone();
                            helper.append('<span class="wpmdia_dCOuntNum">Move ' + media_elements.length + ' File</<span>');
                            helper.data('mediaelemIDs', mediaID.join());
                            return helper;
                        },
                       appendTo: ".wpmedia-attachments-wrap",
                        start: function (event, ui) {
                           ui.helper.addClass("draggable");
                            var mediaID = ui.helper.data('mediaelemIDs').split(',');
                            $(mediaID).each(function (index, value) {
                                $('#post-' + value + '').css('opacity', '0.3');
                            });
                        },
                        stop: function (event, ui) {
                           ui.helper.removeClass("draggable");
                            var mediaID = ui.helper.data('mediaelemIDs').split(',');
                            $(mediaID).each(function (index, value) {
                                $('#post-' + value + '').css('opacity', '1');
                            });
                        }
                    });
                 }
            }  
        };
        
        
        
         //bind the click event on folders
          ClickEventEditFolderName = function (media_page) {
              
              /*
               * Edit Folder Name 
              */
          $('li.wpmmanager-attachmedia .wpmega-fedit-icons').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var fid = $(this).parent().parent().attr('data-id');
                var fname = $(this).parent().parent().find('.wpmainfilename a').text();
                $(".wpmmanager-create-new-folder").trigger("click");
                $('.wpmmanagername').val(fname);  
                $('.wpmedia-manager-fname').html(fname);
                $('.wpm_folderDetailsID').val(fid);
                $('.wpmm_action_settings').val('edit_folder');
                $('.wpmmanagersubmitbtn').val('EDIT FOLDER');
              });

        /*
         * Delete Folder Details 
        */
      var enable_remove_all = wpmmanager_admin_ajax.enable_remove_all;// check if remove all folders enabled?
      var error_delete_msg = wpmmanager_admin_ajax.error_delete_msg;
        // $('li.wpmmanager-attachmedia .wpmega-fdelete-icons').unbind('click').bind('click', function (e) {
        $('li.wpmmanager-attachmedia .wpmega-fdelete-icons').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();    
                var fid = $(this).parent().parent().attr('data-id');
                if(fid != 'undefined' || fid != 0 ){

                            $.ajax({
                                url: AjaxURL,
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                   action: "wpmmedia_check_sub_folders",
                                   folder_id: fid,
                                   wp_nonce: AjaxNonce
                               },
                               success: function (response) {
                                if(response.status == 1){
                                  //yes it has child
                                //  alert('yes it has a child');
                                  if(enable_remove_all == 1){
                                     //remove all folder and its sub folder at once options
                                     if(confirm(folder_delete_all)){
                                      $.ajax({
                                              url: AjaxURL,
                                              type: 'POST',
                                              dataType: 'json',
                                              data: {
                                                 action: "wpmedia_delete_folders",
                                                 folder_id: fid,
                                                 wp_nonce: AjaxNonce
                                             },
                                             beforeSend: function() {
                                                     $(".wpmedia-manager-overlay").css('display', 'block');
                                              },
                                              complete: function() {
                                                     $(".wpmedia-manager-overlay").css('display', 'none');
                                              },
                                             success: function (dta) {
                                              //console.log(dta);
                                               var status =  dta.status;
                                               var folderchilds = dta.folderchilds;
                                                  if(status == 1){
                                                        $.each(folderchilds, function (index, childid) {
                                                          $('.wpmmanager-attachmedia[data-id="' + childid + '"]').remove();
                                                          $('.wpmmanager-media-fname[id="wpmedialists-' + childid + '"]').remove();
                                                          $('select.wpmedia-all-folders option[data-id="' + childid + '"]').remove();
                                                          delete allfolders[childid];
                                                          var index = folders_id.indexOf(childid.toString());
                                                          folders_id.splice(index, 1);
                                                          if (media_page != 'table') {
                                                              wp.Uploader.queue.reset();
                                                          }

                                                      });

                                                  }
                                              }
                                          });


                                     }
                                   }else{
                                     //only remove each folder wise
                                     alert(checkdeleteall);
                                    }
                                }else{
                                  // no it has no child
                                   if (confirm(deletemsg)) {
                                          $.ajax({
                                              url: AjaxURL,
                                              type: 'POST',
                                              dataType: 'json',
                                              data: {
                                                 action: "wpmedia_delete_single_folders",
                                                 folder_id: fid,
                                                 wp_nonce: AjaxNonce
                                             },
                                             beforeSend: function() {
                                                     $(".wpmedia-manager-overlay").css('display', 'block');
                                              },
                                              complete: function() {
                                                     $(".wpmedia-manager-overlay").css('display', 'none');
                                              },
                                             success: function (resp) {
                                                  if(resp.check_deleted_status == 1){

                                                      $('select.wpmedia-all-folders option[data-id="' + fid + '"]').remove();
                                                       $('li.wpmmanager-attachmedia[data-id="' + fid + '"]').remove();
                                                       $('.wpmmanager-media-fname[id="wpmedialists-' + fid + '"]').remove();
                                                       
                                                       delete allfolders[fid];
                                                       var index = folders_id.indexOf(fid.toString());
                                                       folders_id.splice(index, 1);
                                                      
                                                      if (media_page != 'table') {
                                                          wp.Uploader.queue.reset();
                                                      }
                                                      
                                                  }else{
                                                    alert(error_delete_msg);
                                                  }
                                              }
                                          });

                                  } 
                               }
                              }
                          });
               }

            });
            

               // $('.wpmmanager-attachmedia').unbind('click').bind('click', function (e) {
               $('.wpmmanager-attachmedia').on('click', function (e) {
                  e.preventDefault();
                  e.stopPropagation();
                 
                 if ($(e.target).hasClass('ui-draggable-dragging') || $(e.target).parents('.wpmediamanager-browser-folders').hasClass('ui-draggable-dragging')) {
                    return;
                }
                 var ID = $(this).data('id');
     
                 if (media_page == 'table') {
                     $('select.wpmedia-all-folders option[data-id="' + ID + '"]').prop('selected', 'selected').change();
                } else {
                    $('[id^="__wp-uploader-id-"]:visible .wpmedia-all-folders option[data-id="' + ID + '"]').prop('selected', 'selected').change();               
                }
   
                  if($('li.wpmmanager-media-fname[id="wpmedialists-'+ID+'"]').hasClass('wpmm-collapsed')){
                       $('li.wpmmanager-media-fname.wpmm-collapsed .wpmedia-folder-title[data-id="'+ID+'"]').trigger( "click" );
                  }else{
                    if($(this).hasClass('mediaback')){ 
                     var IDD = $(this).attr('data-id');
                    }else{
                     var IDD = ID; 
                    }
                    $('.wpmmanager-media-fname').removeClass('wpmedia-current-active');
                    $('.wpmmanager-media-fname[id="wpmedialists-'+IDD+'"]').addClass('wpmedia-current-active'); 
                    $('li.wpmmanager-media-fname.wpmm-expanded .wpmedia-folder-title[data-id="'+IDD+'"]').trigger( "click" );
                  }
              });    
          };

        /*
        *  Wrap attachment images with main div 'wpmedia-manager-attachments-wrapper' structure and add right section with all folders lists
        */
        setrefreshmedia_manager = function () {
            // var wrappers = $('.wpmedia-manager-attachments-wrapper');
            if ($(document).find('.wpmedia-manager-attachments-wrapper').length === 0) {
                if ($(document).find('.wpmedia-manager-attachments-wrapper:visible .wpmediamanager-browser-folders').length === 0) {
              // var bcmb = '<li>' + default_breadcrumb_txt + '&nbsp;<a href="#" data-id="0">&nbsp;' + main_home_breadcrumb + '&nbsp;</a>>&nbsp;&nbsp;</li>';
              if(media_page != "table"){
                  appendGridWrapper();
                }else{   
                  appendTableWrapper();
                }
                   // Hide menu once we know its width
                $('body').on('click','.wpmedia-showhide',function() {
                   $( this ).toggleClass( "clicked" );
                   $( this ).parent().toggleClass('folderclosenow');
                   $( this ).next().toggleClass('folderclosenow');
                   $( this ).next().next().toggleClass('folderclosenow');

               });
                appendRightFolderSection();
                initFilterOnTable();
                // $(document).find('[id^="__wp-uploader-id-"]:visible .wpmf-categories').change();
               //trigger the first selection
                $(document).find('[id^="__wp-uploader-id-"]:visible .wpmedia-all-folders option').prop('selected',null);
                $(document).find('[id^="__wp-uploader-id-"]:visible .wpmedia-all-folders option[value="'+listsFoldersFilter[CurFolder]+'"]').prop('selected','selected');
                 if (wpmmanager_admin_ajax.userrole != 'administrator' && media_page != 'table') {
                     $(document).find('[id^="__wp-uploader-id-"]:visible select.wpmedia-all-folders').prop('selected', true).change();
                } else {
                   // loadcount = 0;
                    $(document).find('[id^="__wp-uploader-id-"]:visible select.wpmedia-all-folders').change();
                }
              }
           }
        };
        
        appendRightFolderSection = function(){
               $.ajax({
                    url: AjaxURL,
                    type: 'post',
                    data: {
                        action: "wpmmedia_rightfolderlists",
                        current_blog_id: current_blog_id,
                        wp_nonce: AjaxNonce
                    },
                    success: function(res) {

                    if(media_page != "table"){
                      if($('[id^="__wp-uploader-id-"]:visible .media-menu').find('.wpmmmanager-main-wrapper2').length > 0) {
                        $(document).find('.media-frame .wpmediamanager-lists-wrap .wpmmmanager-main-wrapper').remove();
                         $('[id^="__wp-uploader-id-"]:visible .media-menu').find('.wpmmmanager-main-wrapper2').html(res);
                          $(document).find('.media-frame .wpmediamanager-browser-folders').not(':eq(0)').remove();
                          $(document).find('.media-frame .wpmediamanager-lists-wrap .attachments').not(':eq(0)').remove();
                           openFolderEvents();
                      }else{
                         $(document).find('[id^="__wp-uploader-id-"]:visible .wpmedia-manager-attachments-wrapper .wpmmmanager-main-wrapper').html(res);
                      }  
                     }else{
                       $(document).find('.wpmedia-manager-attachments-wrapper .wpmmmanager-main-wrapper').html(res);
                     }
                       //create folder 
                      createNewFolder(media_page);
                      
                    }
                });
        };
        
        appendGridWrapper = function(){
                //add the folders
              if ($(document).find('.wpmedia-manager-attachments-wrapper:visible .wpmediamanager-browser-folders').length === 0) {
               $(document).find('[id^="__wp-uploader-id-"]:visible ul.attachments').before('<ul class="wpmediamanager-browser-folders clearfix"></ul><div class="wpmediamanager-clear"></div><input type="hidden" class="wpmediam_load_flag" value="0">');
                $(document).find('[id^="__wp-uploader-id-"]:visible .attachments-browser ul.attachments,[id^="__wp-uploader-id-"]:visible .attachments-browser .wpmediamanager-browser-folders,[id^="__wp-uploader-id-"]:visible .attachments-browser .wpmediamanager-clear').wrapAll('<div class="wpmedia-manager-attachments-wrapper"></div>');              
                //add the breadcrumb <li><a href="#" data-id="0">Files</a></li>
                $(document).find('[id^="__wp-uploader-id-"]:visible .wpmedia-manager-attachments-wrapper').prepend('<ul class="wpmediamanager-breadcrumb"><li><a href="#" data-id="0">'+default_breadcrumb_txt+' &nbsp;&nbsp;'+main_home_breadcrumb+ ' </a></li></ul>');
                $(document).find('[id^="__wp-uploader-id-"]:visible .wpmedia-manager-attachments-wrapper').append('<div class="wpmmmanager-main-wrapper"></div>');   
                $('.wpmediamanager-browser-folders,.wpmediamanager-breadcrumb').wrapAll('<div class="wpmedia-attachments-wrap"></div>');              
                $('.wpmmmanager-main-wrapper,ul.attachments').wrapAll('<div class="wpmediamanager-lists-wrap clearfix"></div>');
               if(pgnow == "upload.php"){
               // $('.wpmediamanager-lists-wrap').prepend('<div id="clickme" class="wpmedia-showhide"><i class="fa fa-arrow-circle-right"></i></div>');                     }
              }
            }
        };
        
        appendTableWrapper = function(){
                 var j = 0;
                 $.each(allfolders || {}, function () {
                    listsFoldersFilter[this.id] = j;
                    listsFilterFolders[j] = this.id;
                    j++;
                });
                $('.wp-list-table.media').before('<ul class="wpmediamanager-breadcrumb"></ul>');
                $('.wp-list-table.media').before('<ul class="wpmediamanager-browser-folders clearfix"></ul><div class="wpmediamanager-clear"></div>');
                $('.wp-list-table.media, .wpmediamanager-browser-folders, .wpmediamanager-clear , .wpmediamanager-breadcrumb').wrapAll('<div class="wpmedia-manager-attachments-wrapper"></div>');              
               
                $('.wp-list-table.media').after('<div class="wpmmmanager-main-wrapper"></div>');              
                $('.wpmediamanager-browser-folders,.wpmediamanager-breadcrumb').wrapAll('<div class="wpmedia-attachments-wrap"></div>'); 
                $('.wpmmmanager-main-wrapper,.wp-list-table.media').wrapAll('<div class="wpmediamanager-lists-wrap clearfix"></div>');              
                if(pgnow == "upload.php"){
                // $('.wpmediamanager-lists-wrap').prepend('<div id="clickme" class="wpmedia-showhide"><i class="fa fa-arrow-circle-right"></i></div>'); 
                }
                 //$('body').find('[id^="__wp-uploader-id-"]:visible #wpmedia-select-folder').change();
                //Add the drag column on table
                $('.wp-list-table.media thead tr td#cb,.wp-list-table.media tfoot tr td.column-cb').after('<td class="wpmedia-headermove"></td>');
                $('.wp-list-table.media #the-list tr th.check-column').after('<td class="wpmediamanager-rowmov" title="Drag and Drop me to above folder."><span class="wpmmrowmov wpmmrowmov-more"><i class="fa fa-arrows-alt"></i></span></td>'); 
        };
        
   




      if ( undefined !== media ) {
        //check if media is in table structure mode
         if (media2 && $('body.upload-php table.media').length === 0) {
          var MediaLibraryFoldersFilter,ExtraAttachmentFilters, CustomSizeFilter,CustomWeightFilter;
          var MediaAttachmentPrototype = media.view.AttachmentsBrowser;
          var myModalType = media.view.Modal;

              if (!window.wp || !media) {
                  return;
                }
  
               if (media.view.AttachmentFilters == undefined || MediaAttachmentPrototype == undefined){
                    return;
                }
     
               wp_media_manager_init = function () {
                  /*
                  * Add Class to each li attachment
                  */
                  media.view.Attachment.Library = media.view.Attachment.Library.extend({
                      className: function () { 
                        return 'attachment ' + this.model.get( 'mycustomclass' ); 
                      }
                  });

                /**
                * Create a new MediaLibraryFoldersFilter we later will instantiate
                * https://gist.github.com/danielbachhuber/0e4ff7ad82ffc15ce
                * https://wordpress.stackexchange.com/questions/113264/add-filter-function-in-media-modal-box
                * wp.media.view.AttachmentFilters.MediaLibraryFoldersFilter
                * A filter dropdown for all custom folders lists hierarchy.
                * @class
                * @augments wp.media.view.AttachmentFilters
                */ 
                MediaLibraryFoldersFilter = media.view.AttachmentFilters.extend({
                    id: 'wpmedia-manager-folders',
                    className: 'wpmedia-all-folders attachment-filters',
                    createFilters: function () {
                         var datafilters = {};
                          _.each(folders_id || [], function( value, index ) {
                            folder_val = allfolders[value];
                            if (typeof folder_val.folder_order == 'undefined') {
                                folder_val.folder_order = 0;
                            }
                            datafilters[ j ] = {
                                text: gap.repeat(folder_val.folder_order) + folder_val.name,
                                props:  {
                                    // Change this: key needs to be the WP_Query var
                                    folder_id: parseInt(folder_val.id),
                                    folder_slug: folder_val.slug,
                                  }
                            };
                            listsFoldersFilter[folder_val.id] = j;
                            listsFilterFolders[j] = folder_val.id; 
                            j++;
                        });
                        this.filters = datafilters;
                    }
                  });
                 /**
                    * wp.media.view.AttachmentFilters.ExtraAttachmentFilters
                    * A filter dropdown for all mimetypes.
                    * @class
                    * @augments wp.media.view.AttachmentFilters
                  */ 
                   ExtraAttachmentFilters = media.view.AttachmentFilters.extend({
                        className: 'attachment-filters wpmediam_attach-mimetype',
                         createFilters: function(){
                              var filters = {};
                              _.each( allmimetypes || {}, function( text, key ) {
                                    filters[ key ] = {
                                            text: text[0],
                                            props: {
                                                    status:  null,
                                                    type:    key,
                                                    uploadedTo: null,
                                                    orderby: 'date',
                                                    order:   'DESC'
                                            }
                                    };
                              });

                            filters.all = {
                                text: filtermimeText,
                                props: {
                                    status: null,
                                    type: null,
                                    uploadedTo: null,
                                    orderby: 'date',
                                    order:   'DESC'
                                },
                                priority: 10
                            };

                            filters.unattached = {
                                text:  wpdia_unattached,
                                props: {
                                  status:     null,
                                  uploadedTo: 0,
                                  type:       null,
                                  orderby:    'menuOrder',
                                  order:      'ASC'
                                },
                                priority: 10
                              };

                            this.filters = filters;
                         },
                         /**
                         * When the selected filter changes, update the Attachment Query properties to match.
                         */
                        change: function() {
                          var filter = this.filters[ this.el.value ];
                          if ( filter ) {
                            this.model.set( filter.props );
                          }
                        },
                    });
           
             if(enable_customfilters == 1){
                    //Custom filter size fOr grid view
                    CustomSizeFilter = media.view.AttachmentFilters.extend({
                        className: 'wpmdia-custom-size attachment-filters',
                        id: 'wpmdia-size-cfilter',
                        createFilters: function () {
                            var filters = {};
                            _.each(custom_selected_size || {}, function (key, value) {
                                filters[ key ] = {
                                    text: key,
                                    props: {
                                        wpmdia_custom_size: key
                                    },
                                };
                            });

                            filters.all = {
                                text: size_default_lbl,
                                props: {
                                    wpmdia_custom_size: 'all',
                                },
                                priority: 10
                            };

                            this.filters = filters;
                        }
                    });

                    //Custom filter weight fOr grid view
                    CustomWeightFilter = media.view.AttachmentFilters.extend({
                        className: 'wpmdia-custom-weight attachment-filters',
                        id: 'wpmdia-weight-cfilter',
                         createFilters: function () {
                            var filters = {};
                            _.each(custom_selected_weight || {}, function (key, value) {
                                var custom_key_val = key[0].split('-');
                                if (key[1] == 'MB') {
                                  var label = (custom_key_val[0] / (1024 * 1024)) + '-' + (custom_key_val[1] / (1024 * 1024)) + ' MB';
                                } else {
                                  var label = (custom_key_val[0] / 1024) + '-' + (custom_key_val[1] / 1024) + ' kB';
                                }
                                filters[ key[0] ] = {
                                    text: label,
                                    props: {
                                        wpmdia_custom_weight: key[0]
                                    },
                                };
                            });

                            filters.all = {
                                text: wt_default_lbl,
                                props: {
                                    wpmdia_custom_weight: 'all',
                                },
                                priority: 20
                            };

                            this.filters = filters;
                        }
                    });
                }
            

                    /**
                     * Extend and override wp.media.view.AttachmentsBrowser to include our new filter
                     * Replace the media-toolbar with our own
                     */
                    media.view.AttachmentsBrowser = media.view.AttachmentsBrowser.extend({
                      createToolbar: function() {
                        // Make sure to load the original toolbar
                       // MediaAttachmentPrototype.prototype.createToolbar.call( this );
                        media.model.Query.defaultArgs.filterSource = 'filter-media-folders';
                        MediaAttachmentPrototype.prototype.createToolbar.apply(this, arguments);

                        this.toolbar.set( 'MediaLibraryFoldersFilter', new MediaLibraryFoldersFilter({
                          controller: this.controller,
                          model:      this.collection.props,
                          priority: -80
                        }).render());

                       
                       if(enable_customfilters == 1){
                          // add our custom filter size
                              this.toolbar.set('custom_size_filters', new CustomSizeFilter({
                                  controller: this.controller,
                                  model: this.collection.props,
                                  priority: -80
                              }).render());
                          // add our custom filter weight
                              this.toolbar.set('custom_weight_filters', new CustomWeightFilter({
                                  controller: this.controller,
                                  model: this.collection.props,
                                  priority: -80
                              }).render());
                        }

                        }
                    });


                };


               if (media_page != 'table') {
                 wp_media_manager_init();
               } // end init filter

                MediaAttachmentPrototype.prototype.on('ready', function () {
                  setrefreshmedia_manager();
                });

            myMediaControllerLibrary = wp.media.controller.Library;
                wp.media.controller.Library = wp.media.controller.Library.extend({
                    refreshContent: function () {
                        startMediaAttachments();
                        myMediaControllerLibrary.prototype.refreshContent.apply(this, arguments);
                    },
                });
                
                
                if (typeof pgnow != "undefined" && pgnow == 'upload.php' && media_page !== 'table') {
                    SelectMToggle = media2.view.SelectModeToggleButton;
                    if (typeof SelectMToggle != "undefined") {
                        media2.view.SelectModeToggleButton = media2.view.SelectModeToggleButton.extend({
                            toggleBulkEditHandler: function () {
                                wpmediamanager_move_file = this;
                                SelectMToggle.prototype.toggleBulkEditHandler.apply(this, arguments);
                            }
                        });
                    }
                }


                
                // Hooking on the uploader queue (on reset):
                //http://stackoverflow.com/questions/14279786/how-to-run-some-code-as-soon-as-new-image-gets-uploaded-in-wordpress-3-5-uploade
                if (typeof wp.Uploader !== 'undefined' && typeof wp.Uploader.queue !== 'undefined') {
                    wp.Uploader.queue.on('reset', function() { 
                        if ($('#wpb_visual_composer').is(":visible")) {

                        } else {
                            $('.attachment.uploading').remove();
                            if (media2.frame.content.get() !== null) {
                                if (typeof media2.frame.content.get().collection != "undefined") {
                                    media2.frame.content.get().collection.props.set({ignore: (+new Date())});
                                   media2.frame.content.get().options.selection.reset();
                                }
                            } else {
                               media2.frame.library.props.set({ignore: (+new Date())});
                            }
                        }
                        $('select.wpmedia-all-folders option[data-id="' + CurFolder + '"]').prop('selected', 'selected');
                    });
                }else {
                    return;
                }
          }else{
               if (typeof allfolders == "undefined")
               return;
               //media in table structure mode
                media_page = 'table';
               setrefreshmedia_manager();
               initFilterOnTable();
               createNewFolder();
               alterFolder.call($('select.wpmedia-all-folders'));

          }
        }
  });//$(function () end
}(jQuery));