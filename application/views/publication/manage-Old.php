<script type="text/javascript" src="<?php echo $this->config->item('base_url');?>js/jquery-ui-1.7.1.custom.min.js"></script>
<script>
function save_order() 
{
	$.blockUI({  message: '<img src="../../images/ajax-loader.gif" /> loading...' ,css: {
				border: 'none',
				padding: '15px',
				backgroundColor: '#302E2E',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				opacity: .5,
				color: '#fff' ,
				border: '1px solid #BFBDBD'
			} });
			
			updateCategories() ;
			publication_id = $('#publication_id').val();
			num_cats = $('#num_cats').val();
			var order = $('#sortable').sortable("serialize") + '&publication_id='+publication_id+'&num_cats='+num_cats; 
			$.post("../../category/reorder_ajax", order, function(theResponse){
				if(theResponse == 'success')
				{
					document.getElementById('message').style.display = 'block';
					document.getElementById('message').style.background = 'green';
					detailDiv = document.getElementById("message");
					$.unblockUI()
					detailDiv.innerHTML = 'Category order changed successfully';
					document.location.reload(true);
				}
			}); 
}
$(function() {
		
		$("#sortable").sortable({update: function() {
			/*$.blockUI({  message: '<img src="../../public/img/ajax-loader.gif" /> loading...' ,css: {
				border: 'none',
				padding: '15px',
				backgroundColor: '#302E2E',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				opacity: .5,
				color: '#fff' ,
				border: '1px solid #BFBDBD'
			} });
			updateCategories() ;
			publication_id = $('#publication_id').val();
			num_cats = $('#num_cats').val();
			var order = $(this).sortable("serialize") + '&publication_id='+publication_id+'&num_cats='+num_cats; 
			$.post("../../category/reorder_ajax", order, function(theResponse){
				document.getElementById('message').style.display = 'block';
				document.getElementById('message').style.background = 'green';
				detailDiv = document.getElementById("message");
				$.unblockUI();
				detailDiv.innerHTML = 'Category order changed successfully';
				
			}); 	*/		
			document.getElementById('message').style.display = 'block';
			document.getElementById('message').style.background = 'green';
			detailDiv = document.getElementById("message");
			$.unblockUI();
			detailDiv.innerHTML = 'Please click save button at bottom after you are finished with reordering';												 
		}								  
		});
		
	});

</script>


  <fieldset>
    <legend><?php echo $publication['name'];?> : Category & Feed Management</legend>
    <form action="" method="post" enctype="multipart/form-data" name="catForm" id="catForm">
      <input type="hidden" name="publication_id" id="publication_id" value="<?php echo $publication_id;?>"/>
      <input type="hidden" id="num_cats" name="num_cats" value="<?php echo $num_cats;?>"/>
    <table class="nostyle">
      <tr>
       
        <td style=" float:left;"><input name="search_txt" type="text" value="Search" onblur="if(this.value=='')this.value='Search'" onfocus="if(this.value=='Search')this.value='';" ></td>
        <td style=" float:left;"><input type="submit" class="login_submit_btn tour_1" value="Search" name="Search"></td>
     
     <?php if($pub_id !='' && $type =='admin'):?>
      <td style=" float:right;">  
		<input type="button" value="Settings" onClick='window.location="<?php echo $this->config->item('base_url');?>publication/settings/<?php echo $publication_id;?>"'>
  	  </td>
      <td style=" float:right;">  
		<input type="button" value="Test Ads" onClick='window.location="<?php echo $this->config->item('base_url');?>publication/ads/<?php echo $publication_id;?>"'>
  	  </td>
      <?php endif;?>

        <td style=" float:right;"><input type="button" class="login_submit_btn tour_2" value="Add Category" onclick="show_category_type_model();"></td>
        <td style=" float:right;"><input type="button" class="login_submit_btn tour_3" value="Refresh Publication" onClick="grabword('refcat','<?php echo $this->config->item('base_url');?>api/refreshpub/<?php echo $publication['id'];?>','<?php echo $publication['name'];?>');"></td>
      </tr>
    </table>
    </form>
    <div id="message"><?php echo $flashmsg;?></div><div id="messagex" style="padding-left:5px"><b><?php echo $flashmsg;?></b></div>
    <div style=" padding:5px;"><h4 style="margin: 0;">Caution : Feed type of first category should not be "Webkit".</h4> </div>
    <form action="" method="post" enctype="multipart/form-data" name="catForm" id="catForm">
    <div id="contentLeft">
      <ul>
        <li style="background: #5DADF7;color: white; border:none;">
          <ul>
            <li>Category</li>
            <li>Refresh</li>
            <li>Actions</li>
            <li>Status</li>
          </ul>
        </li>
      </ul>
      <ul id="sortable">
      

        <?php $i = 0;
        	  $value = 1;
        	  $base_url = $this->config->item('base_url');

        	  ?>


        	 <?php  foreach ($all_categories as $key => $all_category):?> 
        	  <?php 	$label = preg_replace("/[^A-Za-z0-9\.-]/", "" , $all_category['label']);?>
        	  
        <li id="cat_<?php $all_category['id'];?>">
          <ul>
            <li><?php echo $all_category['label'];?></li>
            <li id="<?php echo $label;?>_refresh">

            

            <?php if($all_category['status'] == 'active'):?>

            <a href="#" onClick="grabword('refcat','<?php echo $base_url;?>api/refreshcat/<?php echo $all_category['id'];?>','<?php echo $label;?>');" class="tour_4"><img src="<?php echo $base_url;?>images/refresh.png" style="width: 20px;"/></a>

        
        <?php else:?>
        	NA
        	<?php endif;?>
        </li>
            <li>
            	<a href="<?php echo $base_url;?>category/edit_cat/<?php echo $all_category['id'];?>" class="tour_5"><img src="<?php echo $base_url;?>images/edit.png" style="width: 20px;"/></a> 

            </li>
            <li id="<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category['label']).'_'.$all_category['id'].'_'.'img';?>">

            	<a href="#" onClick="grabword('refcat','<?php echo $base_url;?>category/changeStatus/<?php echo $all_category['id'];?>/<?php echo $publication['id'];?>/<?php 
            	if($all_category['status'] == 'active'){
            		echo 'inactive';
            		}elseif($all_category['status'] == 'inactive'){
            			echo 'active';

            			};?>/<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category['label']);?>','<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category['label']);?>');" class="tour_6"><img src="<?php echo $base_url;?>images/<?php 
            			if( $all_category['status'] == 'active'){
            				echo 'active.png';
            				}elseif ($all_category['status'] == 'inactive'){
            					echo 'inactive.png';
            					};?>" style="width: 25px;" /></a></li>
          </ul>
        </li>
        
        <?php $i = 0;
        	  $value = $i+1;
        	  ?>

        
        <?php foreach ($all_category['sub'] as $key => $all_category_sub):?>

        <li id="cat_<?php echo $all_category_sub['id'];?>">
          <ul>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;|_&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $all_category_sub['label'];?></li>

            <li id="<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category_sub['label']);?>_refresh">

            	
            	<?php if($all_category_sub['status'] == 'active'):?>

            	<a href="#" onClick="grabword('refcat','<?php echo $base_url;?>api/refreshcat/<?php echo $all_category_sub['id'];?>','<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category_sub['label']);?>');"><img src="<?php echo $base_url;?>images/refresh.png" style="width: 20px;"/></a>
            	<?php else:?>NA
            	<?php endif;?>
            </li>
            <li>
            	<a href="<?php echo $base_url;?>category/edit_cat/<?php echo $all_category_sub['id'];?>">
            		<img src="<?php echo $base_url;?>images/edit.png" style="width: 20px;"/>
            	</a>
            </li>
            <li id="<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category_sub['label']);?>_<?php echo $all_category_sub['id'];?>_img">
            	<a href="#" onClick="grabword('refcat','<?php echo $base_url;?>category/changeStatus/<?php echo $all_category_sub['id'];?>/<?php echo $publication['id'];?>/
            		<?php if($all_category_sub['status'] == 'active'):?>
            			inactive
            			<?php elseif($all_category_sub['status'] == 'inactive'):?>active
            		<?php endif;?>

            			/<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category_sub['label']);?>','<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category_sub['label']);?>');" class="tour_6"><img src="<?php echo $base_url;?>images/
            			<?php if($all_category_sub['status'] == 'active'){
            				echo 'active.png';
            			}elseif($all_category_sub['status'] == 'inactive'){
            				echo 'inactive.png';
            			};?> style="width: 25px;" />
            		</a>
            	</li>
          </ul>
        </li>
        
        <?php $i = 0;
        	  $value = $i+1;
        	  ?>

       

        <?php foreach ($all_category_sub['sub'] as $key => $all_category_sub_sub):?>
        <li id="cat_<?php echo $all_category_sub_sub['id'];?>">
          <ul>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|_&nbsp;&nbsp;&nbsp;&nbsp;<?phop echo $all_category_sub_sub['label'];?>
        </li>
            <li id="<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category_sub_sub['label']);?>_refresh">

            	
            	<?php if($all_category_sub_sub['status'] == 'active'):?>

            	<a href="#" onClick="grabword('refcat','<?php echo $base_url;?>api/refreshcat/<?php echo $all_category_sub_sub['id'];?>','<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category_sub_sub['label']);?>');">
            		<img src="<?php echo $base_url;?>images/refresh.png" style="width: 20px;"/>
            	</a>
           	
            <?php else:?>NA
	        <?php endif;?>
    		</li>

            <li>
            	<a href="<?php echo $base_url;?>category/edit_cat/<?php echo $all_category_sub_sub['id'];?>">
            		<img src="<?php echo $base_url;?>images/edit.png" style="width: 20px;"/>
            	</a>
            </li>

            <li id="<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category_sub_sub['label']);?>_<?php echo $all_category_sub['id'];?>_img">
            	<a href="#" onClick="grabword('refcat','<?php echo $base_url;?>category/changeStatus/<?php echo $all_category_sub_sub['id'];?>/<?php echo $publication['id'];?>/

            		{if $all_category_sub_sub.status eq 'active'}inactive{elseif $all_category_sub_sub.status eq 'inactive'}active{/if}
            		<?php if($all_category_sub_sub['status'] == 'active'){
            			echo 'inactive';
            		}elseif($all_category_sub_sub['status'] == 'inactive'){
            			echo 'active';
            		};?>
            		/<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category_sub_sub['label']);?>','<?php echo preg_replace("/[^A-Za-z0-9\.-]/", "", $all_category_sub_sub['label']);?>');" class="tour_6">
            		<img src="<?php echo $base_url;?>images/
            		<?php if($all_category_sub_sub['status'] == 'active'){
            			echo 'active.png';

            		}elseif($all_category_sub_sub['status'] == 'inactive'){
            			echo 'inactive.png';
            		};?>" style="width: 25px;" />
            	</a>
            </li>
          </ul>
        </li>

        <?php 
        $i='';
        $value = $i+1;
    endforeach;
endforeach;
endforeach;
?>
      </ul>
    </div>
   
    <?php //if($searchform !='yes'):?>
    <input type="button" value="Save Ordering" name="saveorder" onclick="save_order();return false;"/>
    <?php //endif;?>
  </fieldset>

<div id="category_type_model" style="display:none;cursor: default;">
  <div style="position: absolute;top: -12px;right: -11px;" onclick="close_category_type_model();"><img src="<?php echo $base_url;?>images/close_modal.png" style="float: right; width:35px;" />
  </div>
  <div style="font-weight: bold;">Please select feed type</div>
  <div style="float: right; margin:10px 0 0 0;">

    <select id="cat_type" name="cat_type" onchange="javascript:window.location = '<?php echo $base_url;?>ategory/add_cat/?publication_id=<?php echo $publication['id'];?>&type='+this.value;return false;" style="width:150px;">
   
				<option value="">Select</option>
				<option value="whiz_rss_stories">Whiz RSS Stories</option>
				<option value="whiz_rss_photos2">Whiz RSS Photos V2</option>
				<option value="whiz_mrss">Video Media RSS (MRSS)</option>
				<option value="whiz_rss_videosv2">Whiz RSS Videos V2</option>
				<option value="grahamdigital_video_json">OTT Grahamdigital video json</option>

				<option value="legacyobits">Legacy Obits</option>
				<option value="vimeo">Vimeo Video</option>
				<option value="vimeo_salesfuel">Vimeo SalesFuel Video</option>
				<option value="whiz_rss_syndicaster">Syndicaster Media</option>
				<option value="webkit">Webkit</option>
				<option value="isay">iSay</option>
				<option value="whiz_custom">Whiz Custom</option>
				<option value="tigerhuddle_rss_stories">Tiger Huddle RSS Stories</option>
				<option value="cdapress_rss_stories">CDA Press RSS Stories</option>
				       
				
       
       <?php if($type == 'admin'):?>
      			<option value="whiz_mrss_photos">Calkins Whiz Media RSS (Photo)</option>
                
                <option value="whiz_rss_photos">Whiz RSS Photos</option>
                <option value="whiz_rss_videos">Whiz RSS Videos</option>
		<option value="worldnow_rss_stories">Worldnow RSS Stories</option>
		<option value="whiz_ott">OTT Media RSS</option>
		<option value="grahamdigital_video_json">OTT Grahamdigital video json</option>
		<option value="landon_demand">Land on demand (audio)</option>
		<option value="worldnow_rss_videos">Worldnow RSS Videos(MRSS)</option>

		<option value="griffin_ott">Griffin Ott</option>

		<option value="worldnow_mrss_photos">Worldnow Photos </option>
		<option value="whiz_rss_syndicaster">Syndicaster Media</option>
		<option value="newscycle">Newscycle Storeis </option>
		<option value="whole_hot_sports_audio">Whole Hog Sports Audio</option>
		<option value="cumedia_inforum_json">Cumedia Inforum JSON parser</option>
		<option value="cumedia_inforum_video_json">Cumedia Inforum VIDEO parser</option>
               
                <option value="legacyobits">Legacy Obits</option>
				<option value="json">JSON</option>
				<option value="jewishgallery">Jewish Journal Photos</option>
				<option value="jewishvideo">Jewish Journal Videos </option>
				<option value="tout_video_v1">Tout Parser V1 </option>
				<option value="newsy">Newsy Videos </option>
				<option value="pictopia">Pictopia</option>
				<option value="pressofacgallery">Press of AC Gallery</option>
				<option value="mycapture">Mycapture Gallery</option>
				<option value="indiawestgallery">India West Gallery</option>
                <option value="commercialappealgallery">India West Gallery</option>
                <option value="scrippsgallery">Scripps Gallery</option>
                
		<!-- added by ashok -->
		<option value="whiz_fake_search">Whiz Fake Search</option>
		{* eof added by ashok *}
                <option value="vmixvideo">Vmix Videos (JSON)</option>
				<option value="ktncvideo">KTNC Video </option>
				<option value="viddler">Viddler</option>
				<option value="flickr">Flickr</option>
				<option value="atom">Atom feed</option>
				<option value="towngallery">Town Gallery Atom</option>
                <option value="brightcove">Brightcove</option>
                <option value="brightcoveplaylist">Brightcove Playlist</option>
                <option value="brightcovelive">Brightcove Live</option>
          
         <?php endif;?>

			</select>
  </div>
</div>
<script type="text/javascript">
var add_cat_uri = '<?php echo $base_url;?>category/add/?publication_id=<?php echo $publication['id'];?>';

function add_cat_redirect() {
	var selectElem = document.getElementById('cat_type');
	var cat_type = selectElem.options[selectElem.selectedIndex].value;alert(cat_type);
	if(cat_type!='')
	{
		window.location = add_cat_uri+'&type='+cat_type;
	}
}

</script> 
