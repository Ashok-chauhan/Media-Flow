<script type="text/javascript" src="<?php echo base_url();?>public/js/jquery.qtip-1.0.0.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>public/css/jquery-ui.css" />
<script src="<?php echo base_url();?>public/js/jquery-ui.js"></script>
<script type="text/javascript">
var color_val = '<?php echo $category['catcolor'];?>';
var base_url = '<?php echo base_url();?>';

if(color_val == '')
{
	color_val = '#045fb4';
}
function manageFields(selectvalue,pub_id,cat_id)
{
	
	if(cat_id){action = 'edit_cat';}else{action = 'add_cat';}
	javascript:window.location = base_url+'category/'+action+'/'+cat_id+'?publication_id='+pub_id+'&type='+selectvalue;return false;
}
$().ready(function() {
	
	var availableTags = [
	{
       value: "#045fb4",
       label: "News #045fb4",
	},
	{
       value: " #d9d919",
       label: "Sports #d9d919",
	},
	{
       value: "#dd111",
       label: "Business #dd111",
	},
	{
       value: "#088a08",
       label: "Opinion #088a08",
	},
	{
       value: "#ff00ff",
       label: "Entertainment #ff00ff",
	},
	{
       value: "#f78b1f",
       label: "Lifestyle #f78b1f",
	},
	{
       value: "#aa00ff",
       label: "Videos #aa00ff",
	},
	{
       value: "#8d8d8",
       label: "Obituaries #8d8d8",
	}
    ];

var $input = $("#catcolor").autocomplete({
    source: availableTags,
    minLength: 0,
    select: function( event, ui ) {
                    $( "#catcolor" ).val( ui.item.value );
                    return false;
                }
}).addClass("ui-widget ui-widget-content ui-corner-left");

$("<button type='button'>&nbsp;</button>")                     
    .attr("tabIndex", -1)                     
    .attr("title", "Show All Items")                     
    .insertAfter($input)                     
    .button({                         
        icons: {                             
            primary: "ui-icon-triangle-1-s"                         
        },                         
        text: false                     
    })                     
    .removeClass("ui-corner-all")                     
    .addClass("ui-corner-right ui-button-icon")                   
    .click(function() {                         
        // close if already visible                         
        if ($input.autocomplete("widget").is(":visible")) {                         $input.autocomplete( "close" );
             return;                         
        }                                              
        $(this).blur();                                                 
        $input.autocomplete("search", "" );                         
        $input.focus();                     
    });
	
	
	
	$('.ui-autocomplete-input').attr('title','Select the color that is associated with the category, it is specified in standard hexadecimal code with a leading #. For example, yellow is #FCD116.');
	$('.ui-autocomplete-input').attr('value',color_val);
	$('.ui-autocomplete-input').siblings('a.ui-button-icon-only').attr('title','Select the color that is associated with the category, it is specified in standard hexadecimal code with a leading #. For example, yellow is #FCD116.');
	$('[title]').qtip({ 
	style: { name: 'blue',
	border: {
         width: 1
      }, 
	  tip: { // Now an object instead of a string
         corner: 'bottomMiddle', // We declare our corner within the object using the corner sub-option
         color: '#fff',
         size: {
            x: 20, // Be careful that the x and y values refer to coordinates on screen, not height or width.
            y : 8 // Depending on which corner your tooltip is at, x and y could mean either height or width!
         }
} 
	  } ,
	position: {
      corner: {
         target: 'topMiddle',
         tooltip: 'bottomMiddle'
      }}
	  });
	
	
	$("#addRSS").validate({
		rules: {
			name: "required",
			label: "required",
			/*source_uri: {
				required: true
			}*/
		},
		messages: {
			name: "Please enter name of category",
			label: "Please enter label of category",
			/*source_uri: {
				required: "Please enter URL for source of rss feed"
			}*/
		}
	});
	
$('#validateFeed').live('click',function(e){	
    cat_type = $('#cat_type').val();
	url = $('#source_uri').val();
	if(cat_type == ''){alert('Please select feed type');return false;} 
	if(url == ''){alert('Please add feed URI');return false;} 
	
	$.ajax({
	  url: base_url+'category/validateFeed/',
	  async:false,
	  type: 'post',
	  data:{'type':cat_type, 'url':url},
	  error: function(xhr, statusText, errorThrown){alert(xhr.status);},
	  
	  success: function(data) {
		  if(data == ''){data = 'Perfect!!';}
		  $('#error_container').html(data);
		  }
	});
})

})

</script>

<form action="<?php echo base_url();?>/category/save" method="post" enctype="multipart/form-data" id="addRSS">
  <input type="hidden" name="id" value="<?php echo $category['id'];?>"/>
  <input type="hidden" name="publication_id" value="<?php echo $publication_id;?>"/>
  <input type="hidden" name="atype" value="list"/>
  <h1 style="background:none; color:#000;"><?php echo $action_string;?> Category 
  <?php if ($category['name'] !='') echo ' - '.$category['name'];?></h1>

  <!-- {if $category.name neq ''} - {$category.name}{/if}</h1> -->
  <table class="edit demo" >
    <tr>
      <td colspan="2"><fieldset>
          <legend>Category Information</legend>
          <table class="edit" id="basic_info">
            <tr>
              <td>Feed Type:</td>
              <td><select id="cat_type" name="type" style="width:200px;" onchange="manageFields(this.value,<?php echo $publication_id; if ($category['id'] !='') echo ','.$category['id'];?>);" title="Feed type is type of category you want to add. If you want to add news category you can select categories rss,json,atom as per format of source and for static content use webkit">
                  <option value="">Select</option>
                  <?php 
                  switch($category['type']){
                      case "whiz_rss_stories":
                        print '<option value="whiz_rss_stories" selected="selected">Whiz RSS Stories</option>';
                      break;
                      case "whiz_rss_photos2":
                        print '<option value="whiz_rss_photos2" selected="selected">Whiz RSS Photos</option>';
                      break;
                      case "whiz_mrss":
                        print '<option value="whiz_mrss" selected="selected">Video Media RSS (MRSS) </option>';
                      break;
                      case "whiz_ott":
                        print '<option value="whiz_ott" selected="selected">OTT Media RSS </option>';
                      break;
                      case "grahamdigital_video_json":
                        print '<option value="grahamdigital_video_json" selected="selected">OTT Grahamdigital video json </option>';
                      break;
                      case "griffin_ott":
                        print '<option value="griffin_ott" selected="selected">Griffin OTT </option>';
                      break;
                      case "whiz_rss_videosv2":
                        print '<option value="whiz_rss_videosv2" selected="selected">Whiz RSS Videos V2 </option>';
                      break;
                      case "legacyobits":
                        print '<option value="legacyobits" selected="selected"> Legacy Obits </option>';
                      break;
                      case "vimeo":
                        print '<option value="vimeo" selected="selected"> Vimeo Video </option>';
                      break;
                      case "vimeo_salesfuel":
                        print '<option value="vimeo_salesfuel" selected="selected">Vimeo SalesFuel Video </option>';
                      break;
                      case "whiz_rss_syndicaster":
                        print '<option value="whiz_rss_syndicaster" selected="selected"> Syndicaster Media </option>';
                      break;
                      case "whiz_custom":
                        print '<option value="whiz_custom" selected="selected"> Whiz Custom / Watch now </option>';
                      break;
                      case "webkit":
                        print '<option value="webkit" selected="selected"> Webkit </option>';
                      break;
                      case "isay":
                        print '<option value="isay" selected="selected"> iSay </option>';
                      break;
                      case "tigerhuddle_rss_stories":
                        print '<option value="tigerhuddle_rss_stories" selected="selected">Tiger Huddle RSS Stories</option>';
                      break;
                      case "cdapress_rss_stories":
                        print '<option value="cdapress_rss_stories" selected="selected">CDA Press RSS Stories </option>';
                      break;


                  }
		 
           
          
           if($type =='admin'){
            switch($category['type']){
                case "whiz_mrss_photos":
                  print '<option value="whiz_mrss_photos" selected="selected">Calkins Whiz Media RSS (Photo)</option>';
                break;
                case "whiz_ott":
                    print '<option value="whiz_ott" selected="selected">OTT Media RSS</option>';
                  break;
                  
                  case "grahamdigital_video_json":
                    print '<option value="grahamdigital_video_json" selected="selected">OTT Grahamdigital video json</option>';
                  break;
                  case "feedburner":
                    print '<option value="feedburner" selected="selected">Feed Burner RSS Stories</option>';
                  break;
                  case "whiz_rss_photos":
                    print '<option value="whiz_rss_photos" selected="selected">Whiz RSS Photos</option>';
                  break;
                  case "legacyobits":
                    print '<option value="legacyobits" selected="selected">Legacy Obits</option>';
                  break;
                  case "whiz_rss_videos":
                    print '<option value="whiz_rss_videos" selected="selected">Whiz RSS Videos</option>';
                  break;
                  case "worldnow_rss_stories":
                    print '<option value="worldnow_rss_stories" selected="selected">Worldnow RSS Stories</option>';
                  break;
                  case "worldnow_rss_videos":
                    print '<option value="worldnow_rss_videos" selected="selected">WWorldnow RSS Videos(MRSS)</option>';
                  break;
                  case "landon_demand":
                    print '<option value="landon_demand" selected="selected">Land on demand (audio)</option>';
                  break;
                  case "newscycle":
                    print '<option value="newscycle" selected="selected">Newscycle Stories</option>';
                  break;
                  case "worldnow_mrss_photos":
                    print '<option value="worldnow_mrss_photos" selected="selected">Worldnow Photos</option>';
                  break;
                  case "whiz_rss_syndicaster":
                    print '<option value="whiz_rss_syndicaster" selected="selected">Syndicaster Media</option>';
                  break;
                  case "whole_hog_sports_audio":
                    print '<option value="whole_hog_sports_audio" selected="selected">Whole Hog Sports Audio</option>';
                  break;
                  case "cumedia_inforum_json":
                    print '<option value="cumedia_inforum_json" selected="selected">Cumedia Inforum JSON paraser</option>';
                  break;
                  case "cumedia_inforum_video_json":
                    print '<option value="cumedia_inforum_video_json" selected="selected">Cumedia Inforum VIDEO paraser</option>';
                  break;
                  case "json":
                    print '<option value="json" selected="selected">JSON</option>';
                  break;
                  case "jewishgallery":
                    print '<option value="jewishgallery" selected="selected">Jewish Journal Photos</option>';
                  break;
                  case "jewishvideo":
                    print '<option value="jewishvideo" selected="selected">Jewish Journal Videos</option>';
                  break;
                  case "tout_video_v1":
                    print '<option value="tout_video_v1" selected="selected">Tout Parser V1</option>';
                  break;
                  case "newsy":
                    print '<option value="newsy" selected="selected">Newsy Videos</option>';
                  break;
                  case "pictopia":
                    print '<option value="pictopia" selected="selected">Pictopia</option>';
                  break;
                  case "pressofacgallery":
                    print '<option value="pressofacgallery" selected="selected">Press of AC Gallery</option>';
                  break;

                  case "mycapture":
                    print '<option value="mycapture" selected="selected">Mycapture Gallery</option>';
                  break;
                  case "indiawestgallery":
                    print '<option value="indiawestgallery" selected="selected">India West Gallery<</option>';
                  break;
                  case "commercialappealgallery":
                    print '<option value="commercialappealgallery" selected="selected">Commercial Gallery</option>';
                  break;
                  case "scrippsgallery":
                    print '<option value="scrippsgallery" selected="selected">Scripps Gallery</option>';
                  break;
                  case "whiz_fake_search":
                    print '<option value="whiz_fake_search" selected="selected">Whiz Fake Search</option>';
                  break;
                  case "vmixvideo":
                    print '<option value="vmixvideo" selected="selected">Vmix Videos (JSON)</option>';
                  break;
                  case "ktncvideo":
                    print '<option value="ktncvideo" selected="selected">KTNC Video</option>';
                  break;
                  case "viddler":
                    print '<option value="viddler" selected="selected">Viddler</option>';
                  break;
                  case "flickr":
                    print '<option value="flickr" selected="selected">Flickr</option>';
                  break;
                  case "atom":
                    print '<option value="atom" selected="selected">Atom feed</option>';
                  break;
                  case "towngallery":
                    print '<option value="towngallery" selected="selected">Town Gallery Atom</option>';
                  break;
                  case "brightcove":
                    print '<option value="brightcove" selected="selected">Brightcove</option>';
                  break;
                  case "brightcoveplaylist":
                    print '<option value="brightcoveplaylist" selected="selected">Brightcove Playlist</option>';
                  break;
                  case "brightcovelive":
                    print '<option value="brightcovelive" selected="selected">Brightcove Live</option>';
                  break;
            }
        }

           
	       
              print '</select></td>';

             
              if($category['type'] == 'isay'):?>
              <td class="hideisay">
              
             <?php 
                if($category['type'] == 'brightcove'
                || $category['type']=='brightcoveplaylist'
                || $category['type']=='brightcovelive' 
                ):?>
                Api Id: 
                <?php else:?>
                URI:
                <?php endif;?>
             </td>
             
              <td class="hideisay"><input type="text" name="source_uri" value="<?php echo $category['source_uri'];?>" id="source_uri" size="60" title="URl of source"/>
              
              <?php endif;?> 
                <!--<input type="button" id="validateFeed" value="validate" style=" padding:2px 5px; font-size:12px;"/>-->
                
                <div id="error_container"></div></td>
            </tr>


            <tr>
              
              <?php if($pub_id !='' and $type =='publisher'):?>
              <td style="display:none;">Name:</td>
              <?php endif;?>

             
             
             <?php if($pub_id !='' and $type =='publisher'):?>
             
             <td style="display:none;">
             <input type="text" name="name" value="<?php echo $category['name'];?>" title="Name of category. This will be use internally."/>
             </td>
             <?php endif;?>
              <td>Label:</td>
              <td><input type="text" name="label" value="<?php echo $category['label'];?>" title="Label of category. This will be displayed in apps"/></td>
            </tr>
	     <!-- BOF VIMEO VIDEO ADDED ON 4/11/2016  -->
	      
          <?php if($category['type'] =='vimeo' || $category['type'] =='vimeo_salesfuel'):?>
	      <tr>
	      <td> Access token:</td>
	      <td><input type="text" name="data_store" value="<?php echo $category['data_store'];?>" title=" Vimeo Access token"/></td>
	      </tr>
	      <?php endif;?>
	    <!-- {* EOF VIMEO VIDEO *} -->
            <tr>
            
              <td> Parent Category: </td>
              <td> 
              <!-- {* BOF PARENT CHILD CATEGORY MANIPULATION *} -->
                <select  name="parentcatid" style="width: 180px;" title="In apps there is parent-child menu structure. e.g. SPORTS category can be parent and FOOTBALL and BASEBALL can be its sub categories">
                  <option value="NULL">--NONE--</option>
            <?php foreach($allcategory as $allcategories):?>
                
                <?php if($category['parent_id'] == $allcategories['id']):?>
                  <option  value="<?php echo $allcategories['id'];?>" selected="selected"><?php echo $allcategories['label'];?></option>
               <?php else:?>
                <?php if($category['id'] !== $allcategories['id']):?>
                  
                  <option value="<?php echo $allcategories['id'];?>"><?php echo $allcategories['label'];?> </option>
                <?php endif;?>
                <?php endif;?>
             <?php endforeach;?>

                </select>
                <!-- {* EOF PARENT CHILD CATEGORY MANIPULATION *}  -->
                </td>
                <td class="hidewebkit"> Color: </td>
              <td class="hidewebkit">
              <div style="height:22px; width:22px; float:right;margin: 4px 90px 0 0; background-color:{$category.catcolor}{if $category.catcolor eq ''}#045fb4{/if}"></div>      
                <input type="text" name="catcolor" id="catcolor" value="<?php echo $category['catcolor']; if ($category['catcolor'] == ''):?>#045fb4<?php endif;?>" size="9" style="width:119px;" title="Select the color that is associated with the category, it is specified in standard hexadecimal code with a leading #. For example, yellow is #FCD116."/>
              </div>
              </td>
            </tr>
            <tr>
              <td valign="top" class="hidewebkit"> iPad Template:</td>
              <td class="hidewebkit">
             
               <select name="ipadtemplate" style="width: 180px;" title="This controls the visual look for the section front for the iPad. <ul style='margin:5px 0 5px 12px;'><li>auto: app will pick template based on available images</li><li>Choose ipad1 if all stories will have images.</li><li> Choose ipad2 if at least the top feature story will have an image. </li><li>Choose ipad3 if the top feature story may not have an image.</li>">
                   <?php 
                   switch($category['ipadtemplate']){
                       case "":
                        print '<option  value="" selected="selected">Auto</option>';
                       break;
                       case "ipad1":
                        print '<option  value="ipad1" selected="selected">ipad1 : All images </option>';
                       break;
                       case "ipad2":
                        print '<option value="ipad2" selected="selected">ipad2 : Top story has image</option>';
                       break;
                       case "ipad3":
                        print '<option value="ipad3" selected="selected"> ipad3 : Top story may not have image </option>';
                       break;
                   }
                   ?>
                 </select>
              </td>
              <td class="hidewebkit hideisay"> Feature Image on Smartphone :</td>
              <td class="hidewebkit hideisay">
               
               <?php if($category['feature_story']):?>
                <input type="checkbox" name="feature_story" checked="checked" value="<?php echo $category['feature_story'];?>" />
                
               <?php else:?>
                <input type="checkbox" name="feature_story" value="1" />
               <?php endif;?>
                </td>
            </tr>   
             <tr>
              <td class="hideisay">Product Code:</td>
              <td class="hideisay"><input type="text" name="prod_code" value="<?php echo $category['prod_code'];?>" style="width:100px;" title="This is used for locking the category. If you are not sure leave it as it is."></td>
              <td class="hidewebkit"> Device: </td>
              <td class="hidewebkit"><select  name="device" style="width: 130px;">
                  <option value="phone">--Choose Device--</option>
                  <?php 
                  switch($category['device']){
                      case "phone":
                        print '<option  value="phone" selected="selected"> Phone Only </option>';
                      break;
                      case "tablet":
                        print '<option value="tablet" selected="selected"> Tablet Only </option>';
                      break;
                      case "both" || "":
                        print '<option value="both" selected="selected"> Both </option>';
                      break;
                      
                  }
                  ?>
                  </select></td>
            </tr>    

            <tr>
              <td>Preroll url:&nbsp;</td><td >
	        <textarea name="cat_preroll_url_phone" cols="40" rows="6"><?php echo $category['cat_preroll_url_phone'];?></textarea></td>
              <td>Dfp ad code:&nbsp;</td><td ><input type="text" name="cat_phone_dfp_banner" size="80" value="<?php echo $category['cat_phone_dfp_banner'];?>" /></td>
            </tr>  
              <!-- {* MY_MF *} -->
            <tr>
              <td class="hideisay"> Default Favorite (Applicable for timeline view): </td>
              <td class="hideisay"> 
              {if $category.my_mf}
              <?php if($category['my_mf']):?>
                <input type="checkbox" name="my_mf" checked="checked" value="<?php echo $category['my_mf'];?>" />
              <?php else:?>
                <input type="checkbox" name="my_mf" value="1" />
              <?php endif;?>
               </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              <!-- {* EOF MY_MF *}   -->
	      
          </table>
        </fieldset></td>
    </tr>
<?php if($pub_id !='' && $type =='publisher'):?>
    <tr style="display:none;">
<?php endif;?>
      <td colspan="2"><fieldset>
          <legend>Settings</legend>
          <table class="edit" id="settings">
          <tr>
              <td> Category Type: </td>
              <td><select name="edit_atype" style="width: 180px;" title="Default is List and you can use different types as per category you wan to add">
                  
                  <option value="list">Classified</option>
                  <option value="list">Obituaries</option>
                  <option value="list">Events</option>
                  <option value="list">Horoscope</option>
                  <?php 
                  switch($category['atype']){
                      case "list":
                        print '<option value="list" selected="selected">List</option>';
                      break;
                      case "isay":
                        print '<option value="isay" selected="selected">isay</option>';
                      break;
                      case "webkit":
                        print '<option value="webkit" selected="selected">Webkit</option>';
                      break;
                      case "wsiweather":
                        print '<option value="wsiweather" selected="selected">wsiweather</option>';
                      break;
                      case "nativeweather":
                        print '<option value="nativeweather" selected="selected">nativeweather</option>';
                      break;
                      case "baronweather":
                        print '<option value="baronweather" selected="selected">Baron weather</option>';
                      break;
                      case "wsitraffic":
                        print '<option value="wsitraffic" selected="selected">wsitraffic</option>';
                      break;
                      case "timeline":
                        print '<option value="timeline" selected="selected">timeline</option>';
                      break;
                      case "deeplink":
                        print '<option value="deeplink" selected="selected">deeplink</option>';
                      break;
                      case "360video":
                        print '<option value="360video" selected="selected">360video</option>';
                      break;
                      case "ottnews":
                        print '<option value="ottnews" selected="selected">ottnews</option>';
                      break;
                      case "menu":
                        print '<option value="menu" selected="selected">Menu</option>';
                      break;
                      case "podcast":
                        print '<option value="podcast" selected="selected">Podcast</option>';
                      break;
                      case "liveaudio":
                        print '<option value="liveaudio" selected="selected">Live audio</option>';
                      break;
                  }
                  ?>
                   </select></td>
              <td> Parser Type: </td>
              <td><input type="text" name="parser_type" value="<?php echo $category['parser_type'];?>"></td>
            </tr>
            <tr>
              <td>Icon:</td>
           <td><input type="file" name="icon_file" size="60" title="Please select icon image which will be shown in your apps categories."/>
                &nbsp;&nbsp;&nbsp;<img src="https://prodman.whizti.com/images/icons/<?php echo $current_icon;?>" style="width:20px;"/></td>
	



              <td class="hidewebkit hideisay">iPad Label:</td>
              <td class="hidewebkit hideisay"><input type="text" name="ipad_label" value="<?php echo $category['ipad_label'];?>" title=""/></td>
            </tr>
            
            <tr>
              <td class="hidewebkit hideisay"> 
	      <?php if ($category['type'] == 'whiz_rss_photos' ||
	      $category['type'] == 'whiz_rss_photos2' || 
	      $category['type'] == 'jewishgallery' || 
	      $category['type'] == 'pressofacgallery' || 
	      $category['type'] == 'pressofacgallery' ||
	      $category['type'] == 'mycapture' || 
	      $category['type'] == 'indiawestgallery' ||
	      $category['type'] == 'commercialappealgallery' ||
	      $category['type'] == 'scrippsgallery' ||
	      $category['type'] == 'worldnow_mrss_photos'):?>
                Gallery Limit 
                <?php else:?>
                Story Limit
                <?php endif;?>
                :</td>
              <td class="hidewebkit hideisay">
	      
	<?php if($category['story_limit'] ==''
      || $category['type'] == 'whiz_rss_photos'
      || $category['type'] == 'whiz_rss_photos2'
      || $category['type'] == 'whiz_rss_stories'
      || $category['type'] == 'whiz_mrss'
      || $category['type'] == 'jewishgallery'
      || $category['type'] == 'pressofacgallery'
      || $category['type'] == 'mycapture'
      || $category['type'] == 'indiawestgallery'
      || $category['type'] == 'commercialappealgallery'
      || $category['type'] == 'scrippsgallery'
      ):?>
      <input type="text" size="4" name="story_limit" value="50" style="width:100px;" title="Maximum limit for stories displayed categories."/>
          <?php else:?>
            <input type="text" size="4" name="story_limit" value="<?php echo $category['story_limit'];?>" style="width:100px;" title="Maximum limit for stories displayed categories."/>
          <?php endif;?>
	  </td>

        <?php if (
          $category['type'] == 'whiz_rss_photos' || 
	      $category['type'] == 'whiz_rss_photos2' ||
	      $category['type'] == 'jewishgallery' ||
	      $category['type'] == 'pressofacgallery' ||
	      $category['type'] == 'pressofacgallery' || 
	      $category['type'] == 'mycapture' ||
	      $category['type'] == 'indiawestgallery' || 
	      $category['type'] == 'commercialappealgallery' || 
	      $category['type'] == 'scrippsgallery' ||
	      $category['type'] == 'worldnow_mrss_photos'):?>
             
	      <td class="hideisay">Photo Limit:</td>
              <td class="hideisay">
            <?php if($category['photo_limit'] ==''):?>
                <input type="text" size="4" name="photo_limit" value="50">
            <?php else:?>
                <input type="text" size="4" name="photo_limit" value="<?php echo $category['photo_limit'];?>">
            <?php endif;?>
	      
	      </td>
        <?php endif;?>
              </tr>
            <!-- {* DEVICE SPECIFIC CATEGORIES *} -->
            <tr>
              <td class="hidewebkit"> </td>
              <td class="hidewebkit"></td>
              <td class="hidewebkit hideisay">iPad Category Order:</td>
              <td class="hidewebkit hideisay">
              <?php if($category['ipad_catorder']):?>
              <input type="text" size="4" name="ipad_catorder" value="<?php echo $category['ipad_catorder'];?>" style="width:100px;"/></td>
              <?php else:?>
                <input type="text" size="4" name="ipad_catorder" value="0" style="width:100px;"/></td>
              <?php endif;?>

            </tr>
            <!-- {* EOF DEVICE SPECIFIC CATEGORIES *} -->
            <tr>
              <td class="hidewebkit hideisay"> Cache Expire Time In Minute:</td>
              <?php if($category['cache']):?>
              <td class="hidewebkit hideisay"><input type="text" size="4" name="cache" value="<?php echo $category['cache'];?>" style="width:100px;" title="Device cache time for applications"></td>
              <?php elseif($category['cache'] == ''):?>
                <td class="hidewebkit hideisay"><input type="text" size="4" name="cache" value="10" style="width:100px;" title="Device cache time for applications"></td>
              <?php endif;?>

	      <!-- {* BOF AUTOPLAY *} -->
	      <td class="hideisay"> Autoplay (OTT video only): </td>
              <td class="hideisay"> 
              {if $category.autoplay}
              <?php if($category['autoplay']):?>
                <input type="checkbox" name="autoplay" checked="checked" value="<?php echo $category['autoplay'];?>" />
              <?php else:?>
                <input type="checkbox" name="autoplay" value="1" />
              <?php endif;?> 
                </td>
              <!-- {* EOF AUTOPLAY *} -->

             </tr>
           
            <tr>
              <td class="hideisay"> Wipe Old Data Before Adding New: </td>
              <td class="hideisay">
              
              <?php if($category['delflag']):?>
                <input type="checkbox" name="delflag" checked="checked" value="<?php echo $category['delflag'];?>" />
              <?php else:?>
              <input type="checkbox" name="delflag" value="1" <?php if($category['id']==''):?> checked="checked" <?php endif;?> />
              <?php endif;?> 
                </td>
              <!-- {* BOF PRESERVING STORIES ORDER FROM FEED *} -->
              <td class="hidewebkit hideisay">Keep Stories Order Of Feed:</td>
              <td class="hidewebkit hideisay"> 
              {if $category.orderflag}
              <?php if($category['orderflag']):?>
                <input type="checkbox" name="orderflag" checked="checked" value="<?php echo $category['orderflag'];?>" />
              <?php else:?>
                <input type="checkbox" name="orderflag" value="1" <?php if($category['id']==''):?> checked="checked" <?php endif;?> />
              <?php endif;?>
                 </td>
            </tr>
            <!-- {* EOF PRESERVING STORIES ORDER FROM FEED *} -->
            
          
	      
              <!-- {* FEATURE STORY *} -->
             
              <!-- {* EOF FEATURE STORY *}  -->
              </tr>
          </table>
        </fieldset></td>
    </tr>
    
    <?php if($pub_id !='' && $type == 'publisher'):?>
    <tr style="display:none">
    <?php endif;?>
      <td colspan="2"><fieldset>
          <legend>Advertisement Settings</legend>
          <table  class="edit" id="adsettings">
          
            <tr>
              <td>Banner Ad Script:&nbsp;</td>
              <td colspan="3"><textarea name="adcode" cols="40" rows="6" style="width:600px;" title="Categorywise advertisement code"><?php echo $category['adcode'];?></textarea></td>
            </tr>
            <tr>
              <td>Width:</td>
              <td><input type="text" name="adwidth" value="<?php echo $category['adwidth'];?>" size="5" style="width:100px;" title="width of category advertisement"/></td>
              <td> Height:</td>
              <td><input type="text" name="adheight" value="<?php echo $category['adheight'];?>" size="5" style="width:100px;" title="Height of category advertisment"/></td>
            </tr>

	    <tr>
              <td>Banner Ad :&nbsp;</td>
              <td colspan="3"><textarea name="cat_banner_ads" cols="40" rows="6" style="width:600px;" title="Categorywise advertisement code"><?php echo $category['cat_banner_ads'];?></textarea></td>
            </tr>
	    <tr>
              <td>Interstitial Ad:&nbsp;</td>
              <td colspan="3"><textarea name="cat_interstitial_ads" cols="40" rows="6" style="width:600px;" title="Categorywise advertisement code"><?php echo $category['cat_interstitial_ads'];?></textarea></td>
            </tr>
	    <tr>
              <td>Video Ad :&nbsp;</td>
              <td colspan="3"><textarea name="cat_video_ads" cols="40" rows="6" style="width:600px;" title="Categorywise advertisement code"><?php echo $category['cat_video_ads'];?></textarea></td>
            </tr>
	    <tr>
              <td>Native Ad :&nbsp;</td>
              <td colspan="3"><textarea name="cat_native_ad" cols="40" rows="6" style="width:600px;" title="Categorywise advertisement code"><?php echo $category['cat_native_ad'];?></textarea></td>
            </tr>

             <tr class="hidewebkit hideisay">
              <td valign="top"> Inline Ad Frequency:&nbsp;</td>
              <td><input type="text" name="inline_ad_frequency" size="2" value="<?php echo $category['inline_ad_frequency'];?>" style="width:100px;"/></td>
            </tr>
          </table>
        </fieldset></td>
    </tr>

    <!-- {* EOF ADDED BY ASHOK *} -->
  </table>
  <input type="submit" value="Save" name="<?php echo $action_string;?>"/>
  <a href="<?php echo base_url().'/publication/manage/'.$publication_id;?>">
  <input type="button" name="cancelstory" value="Cancel"/>
  </a>
</form>
