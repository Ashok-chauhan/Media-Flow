
 
  <script>

// $(document).on({
//     ajaxStart: function(){
//         $("body").addClass("loading"); 
//     },
//     ajaxStop: function(){ 
//         $("body").removeClass("loading"); 
//     }    
// });




function save_order() 
{
	
			
			updateCategories() ;
			publication_id = $('#publication_id').val();
			num_cats = $('#num_cats').val();
			var order = $('#sortable').sortable("serialize") + '&publication_id='+publication_id+'&num_cats='+num_cats; 
			console.log(order);
			$.post("../../category/reorder_ajax", order, function(theResponse){
				if(theResponse === 'success')
				{
				
        
        $( function() {

         
          
          datadiv = document.getElementById("dialog-message");

          datadiv.innerHTML = '<span>Category order changed successfully.</span>';

            $( "#dialog-message" ).dialog({
              modal: true,
              /* position: { my: "center top", at: "center top" },*/
              
              buttons: {
                Ok: function() {
                  $( this ).dialog( "close" );
                  
                 
                }
              }
            });
          } );

					console.log('success');
				}
			}); 
}


</script>
  
  <script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );

  </script>



<!-- bof  FUTURE DEVELOPMENT FOR 2 COL LAYOUT--->
<!-- <section>
<div class="row ">
<div class="col-md-7 col-lg-7 "> -->

<!-- --->
<div class="container">

    <fieldset class="border border-secondary rounded p-3">
                            <legend class="w-auto"> <?php echo $publication['name'];?> - Category & Feed Management </legend>
     
    <div class="form-row">

    <!-- <button type="button" class="btn btn-outline-secondary" value="Add Category" onclick="show_category_type_model();"> Add CAtegory </button> -->
    <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#myModal"> Add CAtegory </button> 
    <button type="button" class="btn btn-outline-secondary" value="Refresh Publication" onClick="grabword('refcat','<?php echo $this->config->item('base_url');?>api/refreshpub/<?php echo $publication['id'];?>','<?php echo $publication['name'];?>');"> Refresh Publication </button>
      
      <?php if($pub_id !='' && $type =='admin'):?>
      
		<button type="button" class="btn btn-outline-secondary" value="Settings" onClick='window.location="<?php echo $this->config->item('base_url');?>publication/settings/<?php echo $publication_id;?>"'> Settings </button>
  	  
		<button type="button" class="btn btn-outline-secondary" value="Test Ads" onClick='window.location="<?php echo $this->config->item('base_url');?>publication/ads/<?php echo $publication_id;?>"'> Test Ads </button>
  	  
      <?php endif;?>

     </div>
     </fieldset>
     <!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
  Launch demo modal
</button> -->


     <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
       
        <h4 class="modal-title" id="myModalLabel">Please select feed type</h4>
       
      </div>
      <div class="modal-body">
          
      <select id="cat_type" class="form-control" name="cat_type" onchange="javascript:window.location = '<?php echo base_url();?>category/add_cat/?publication_id=<?php echo $publication['id'];?>&type='+this.value;return false;" style="width:150px;">
   
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
           
<!-- {* added by ashok *} -->
<option value="whiz_fake_search">Whiz Fake Search</option>
<!-- {* eof added by ashok *} -->
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
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>



<!-- eof Modal -->


    <div id="message"></div>
    <div  id="loader"></div>
    
    <div id="messagex" style="padding-left:5px"><b><?php //echo $flashmsg;?></b></div>
 
 
    <div id="dialog-message" title="Category order"></div>


 
 <?php
// print '<pre>';
// print_r($all_categories);
// exit;
// foreach($all_categories as $key => $categories){
//   echo '<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>PARENT-->'.$categories['label'].'</li>';
//     if(is_array($categories['sub'])){
//       foreach($categories['sub'] as $k => $category){
//        echo '<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$category['label'].'</li>';
//       }
//     }
//   }
$base_url = $this->config->item('base_url');
if($this->session->flashdata('catupdate')){
  print '<div class="alert alert-success" role="alert">'.$this->session->flashdata('catupdate').'</div>';
}

?>
<!-- <div class="row">
<div class="col-md-10 col-lg-10 "> -->

<form action="" method="post" enctype="multipart/form-data" name="catForm" id="catForm">
      <input type="hidden" name="publication_id" id="publication_id" value="<?php echo $publication_id;?>"/>
      <input type="hidden" id="num_cats" name="num_cats" value="<?php echo $num_cats;?>"/>
<ul id="sortable" style="padding-inline-start: 0px;">
<?php

foreach($all_categories as $key => $categories){
  echo '<li class="ui-widget-content parentcategory" id="cat_'.$categories['id'].'" ><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$categories['label'].'
    <div style="float:right; width: 70px;">';
    if($categories['status'] == 'active'){?>

    <form action="/category/changecategorystatus" id="<?php echo $categories['id'].'_'.$publication_id;?>" name="<?php echo $categories['id'].'_'.$publication_id;?>" method="POST"> 
       <input type="hidden" id="categoryid" name="categoryid" value="<?php echo $categories['id'];?>"/>
       <input type="hidden" id="publicationid" name="publicationid" value="<?php echo $publication_id;?>"/>
       <input type="hidden" id="status" name="status" value="inactive"/>
       <input type="hidden" id="label" name="label" value="<?php echo $categories['label'];?>"/>
       <input type="image" src="<?php echo $base_url;?>images/active.png" alt="submit" width="25"/>
    </form>
    
    <?php
    }elseif($categories['status'] =='inactive'){?>

  <form action="/category/changecategorystatus" id="<?php echo $categories['id'].'_'.$publication_id;?>" name="<?php echo $categories['id'].'_'.$publication_id;?>" method="POST"> 
       <input type="hidden" id="categoryid" name="categoryid" value="<?php echo $categories['id'];?>"/>
       <input type="hidden" id="publicationid" name="publicationid" value="<?php echo $publication_id;?>"/>
       <input type="hidden" id="status" name="status" value="active"/>
       <input type="hidden" id="label" name="label" value="<?php echo $categories['label'];?>"/>
       <input type="image" src="<?php echo $base_url;?>images/inactive.png" alt="submit" width="25"/>
    </form>

    <?php  } ?>
    </div>

       <div style="float:right; width: 70px;"><a href="/category/edit_cat/<?php echo $categories['id'];?>"><i style="font-size:24px;color:blue" class="fa fa-edit"></i></a></div>
       <div style="float:right;  width: 70px;" >
    
       <a href="/api/refreshcat/<?php echo $categories['id'];?>" ><i style="font-size:24px;color:green" class="fa fa-refresh"></i>
       </a>
      
     </div>
  </li>


  <?php


    if(is_array($categories['sub'])){
      foreach($categories['sub'] as $k => $category){
        //echo '&nbsp;&nbsp;&nbsp;&nbsp;|_&nbsp;&nbsp;&nbsp;&nbsp;';
        // class="ui-state-default childcategory "
        //ui-state-highlight
        //ui-widget-content
       echo '<li class="ui-widget-content childcategory" id="cat_'.$category['id'].'">&nbsp;&nbsp;&nbsp;&nbsp;|_&nbsp;&nbsp;&nbsp;&nbsp;<span class="ui-icon ui-icon-arrowthick-2-n-s "></span>'.$category['label'].'
       
       <div style="float:right; width: 70px;">';
       if($category['status'] == 'active'){?>

        
        <!-- <a href="#" onClick="grabword('refcat','<?php //echo $base_url;?>category/changeStatus/<?php //echo $category['id'].'/'.$publication_id;?>/inactive','<?php //echo preg_replace("/[^A-Za-z0-9\.-]/", "", $category['label']);?>');"><i id="<?php //echo $category['label'].'_'.$category['id'].'_img';?>" style="font-size:24px;color:green" class="fa fa-check"></i></a> -->
       
       <form action="/category/changecategorystatus" id="<?php echo $category['id'].'_'.$publication_id;?>" name="<?php echo $category['id'].'_'.$publication_id;?>" method="POST"> 
       <input type="hidden" id="categoryid" name="categoryid" value="<?php echo $category['id'];?>"/>
       <input type="hidden" id="publicationid" name="publicationid" value="<?php echo $publication_id;?>"/>
       <input type="hidden" id="status" name="status" value="inactive"/>
       <input type="hidden" id="label" name="label" value="<?php echo $category['label'];?>"/>
       <input type="image" src="<?php echo $base_url;?>images/active.png" alt="submit" width="25"/>
       </form>

       <?php
       }elseif($category['status'] == 'inactive'){ ?>
        
        <!-- <a href="#" onClick="grabword('refcat','<?php //echo $base_url;?>category/changeStatus/<?php //echo $category['id'].'/'.$publication_id;?>/inactive','<?php //echo preg_replace("/[^A-Za-z0-9\.-]/", "", $category['label']);?>');"><i id="<?php //echo $category['label'].'_'.$category['id'].'_img';?>" style="font-size:24px;color:red" class="fa fa-remove"></i></a> -->
       
        <form action="/category/changecategorystatus" id="<?php echo $category['id'].'_'.$publication_id;?>" name="<?php echo $category['id'].'_'.$publication_id;?>" method="POST"> 
       <input type="hidden" id="categoryid" name="categoryid" value="<?php echo $category['id'];?>"/>
       <input type="hidden" id="publicationid" name="publicationid" value="<?php echo $publication_id;?>"/>
       <input type="hidden" id="status" name="status" value="active"/>
       <input type="hidden" id="label" name="label" value="<?php echo $category['label'];?>"/>
       <input type="image" src="<?php echo $base_url;?>images/inactive.png" alt="submit" width="25"/>
       </form>

       <?php }?>
       
       </div><div style="float:right; width: 70px;"><a href="/category/edit_cat/<?php echo $category['id'];?>"><i style="font-size:24px;color:blue" class="fa fa-edit"></i></a></div>
       <div style="float:right;  width: 70px;" >
      

       <a href="#" onClick="refreshcategory('<?php echo $category['id'];?>')"><i style="font-size:24px;color:green" class="fa fa-refresh"></i>
       </a>

       <!-- <a href="/api/refreshcat/<?php echo $category['id'];?>"> <i style="font-size:24px;color:green" class="fa fa-refresh"></i></a> -->


       <?php
     echo '</div>
     </li>';
      
      }
    }
  }
  
?>
</ul>
<!-- <input type="button" value="Save Ordering" class="btn btn-primary" name="saveorder" onclick="save_order();return false;"/> -->




<div class="form-row">
        <div class="form-group col-md-12">
        <button type="button" class="btn btn-secondary btn-lg btn-block" name="saveorder" onclick="save_order();return false;">Save Ordering</button>
           </div>
    </div> 

<!-- <input type="submit" value="Save Ordering" name="saveorder" /> -->

<!-- </div>
</div> -->
 
</div>


<!-- 2 col bof FUTURE DEVELOPMENT TO CREATE SECOND COL-->
<!-- end of 1st col-->

<!-- </div>  -->
<!-- bof 2 col -->
<!-- <div class="col-md-5 col-lg-5 ">  -->
<!-- eof 2nd col -->
<!-- </div> 
</div>
</section> -->

<!-- 2 col eof -->
