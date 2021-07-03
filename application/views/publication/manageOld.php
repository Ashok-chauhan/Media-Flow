<script type="text/javascript" src="{$base_url}public/js/jquery-ui-1.7.1.custom.min.js"></script>
<script>
{literal}
function save_order() 
{
	$.blockUI({  message: '<img src="../../public/img/ajax-loader.gif" /> loading...' ,css: {
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
			console.log(order);
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
{/literal}
</script>


  <fieldset>
    <legend>{$publication.name} : Category & Feed Management</legend>
    <form action="" method="post" enctype="multipart/form-data" name="catForm" id="catForm">
      <input type="hidden" name="publication_id" id="publication_id" value="{$publication_id}"/>
      <input type="hidden" id="num_cats" name="num_cats" value="{$num_cats}"/>
    <table class="nostyle">
      <tr>
       
        <td style=" float:left;"><input name="search_txt" type="text" value="Search" onblur="if(this.value=='')this.value='Search'" onfocus="if(this.value=='Search')this.value='';" ></td>
        <td style=" float:left;"><input type="submit" class="login_submit_btn tour_1" value="Search" name="Search"></td>
     {if $pub_id neq '' and $type eq 'admin'}  
      <td style=" float:right;">  
		<input type="button" value="Settings" onClick='window.location="{$base_url}publication/settings/{$publication_id}"'>
  	  </td>
      <td style=" float:right;">  
		<input type="button" value="Test Ads" onClick='window.location="{$base_url}publication/ads/{$publication_id}"'>
  	  </td>
      {/if}
      {*  <td style=" float:right;"><input type="button" class="login_submit_btn tour_2" value="Add Category New" onclick="javascript:window.location = '{$base_url}category/add_cat/?publication_id={$publication.id}';return false;"></td>*}
        <td style=" float:right;"><input type="button" class="login_submit_btn tour_2" value="Add Category" onclick="show_category_type_model();"></td>
        <td style=" float:right;"><input type="button" class="login_submit_btn tour_3" value="Refresh Publication" onClick="grabword('refcat','{$base_url}api/refreshpub/{$publication.id}','{$publication.name}');"></td>
      </tr>
    </table>
    </form>
    <div id="message">{$flashmsg}</div><div id="messagex" style="padding-left:5px"><b>{$flashmsg}</b></div>
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
        {assign var=i value=1}  {foreach from=$all_categories item=all_category}
        <li id="cat_{$all_category.id}">
          <ul>
            <li>{$all_category.label}</li>
            <li id="{$all_category.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}_refresh">{if $all_category.status eq 'active'}<a href="#" onClick="grabword('refcat','{$base_url}api/refreshcat/{$all_category.id}','{$all_category.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}');" class="tour_4"><img src="{$base_url}public/img/refresh.png" style="width: 20px;"/></a>{else}NA{/if}</li>
            <li><a href="{$base_url}category/edit_cat/{$all_category.id}" class="tour_5"><img src="{$base_url}public/img/edit.png" style="width: 20px;"/></a> </li>
            <li id="{$all_category.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}_{$all_category.id}_img"><a href="#" onClick="grabword('refcat','{$base_url}category/changeStatus/{$all_category.id}/{$publication.id}/{if $all_category.status eq 'active'}inactive{elseif $all_category.status eq 'inactive'}active{/if}/{$all_category.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}','{$all_category.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}');" class="tour_6"><img src="{$base_url}public/img/{if $all_category.status eq 'active'}active.png{elseif $all_category.status eq 'inactive'}inactive.png{/if}" style="width: 25px;" /></a></li>
          </ul>
        </li>
        {assign var=i value=$i+1}
        {foreach from=$all_category.sub item=all_category_sub}
        <li id="cat_{$all_category_sub.id}">
          <ul>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;|_&nbsp;&nbsp;&nbsp;&nbsp;{$all_category_sub.label}</li>
            <li id="{$all_category_sub.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}_refresh">{if $all_category_sub.status eq 'active'}<a href="#" onClick="grabword('refcat','{$base_url}api/refreshcat/{$all_category_sub.id}','{$all_category_sub.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}');"><img src="{$base_url}public/img/refresh.png" style="width: 20px;"/></a>{else}NA{/if}</li>
            <li><a href="{$base_url}category/edit_cat/{$all_category_sub.id}"><img src="{$base_url}public/img/edit.png" style="width: 20px;"/></a> </li>
            <li id="{$all_category_sub.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}_{$all_category_sub.id}_img"><a href="#" onClick="grabword('refcat','{$base_url}category/changeStatus/{$all_category_sub.id}/{$publication.id}/{if $all_category_sub.status eq 'active'}inactive{elseif $all_category_sub.status eq 'inactive'}active{/if}/{$all_category_sub.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}','{$all_category_sub.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}');" class="tour_6"><img src="{$base_url}public/img/{if $all_category_sub.status eq 'active'}active.png{elseif $all_category_sub.status eq 'inactive'}inactive.png{/if}" style="width: 25px;" /></a></li>
          </ul>
        </li>
        {assign var=i value=$i+1}
        {foreach from=$all_category_sub.sub item=all_category_sub_sub}
        <li id="cat_{$all_category_sub_sub.id}">
          <ul>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|_&nbsp;&nbsp;&nbsp;&nbsp;{$all_category_sub_sub.label}</li>
            <li id="{$all_category_sub_sub.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}_refresh">{if $all_category_sub_sub.status eq 'active'}<a href="#" onClick="grabword('refcat','{$base_url}api/refreshcat/{$all_category_sub_sub.id}','{$all_category_sub_sub.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}');"><img src="{$base_url}public/img/refresh.png" style="width: 20px;"/></a>{else}NA{/if}</li>
            <li><a href="{$base_url}category/edit_cat/{$all_category_sub_sub.id}"><img src="{$base_url}public/img/edit.png" style="width: 20px;"/></a></li>
            <li id="{$all_category_sub_sub.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}_{$all_category_sub.id}_img"><a href="#" onClick="grabword('refcat','{$base_url}category/changeStatus/{$all_category_sub_sub.id}/{$publication.id}/{if $all_category_sub_sub.status eq 'active'}inactive{elseif $all_category_sub_sub.status eq 'inactive'}active{/if}/{$all_category_sub_sub.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}','{$all_category_sub_sub.label|regex_replace:"/[^A-Za-z0-9\.-]/":""}');" class="tour_6"><img src="{$base_url}public/img/{if $all_category_sub_sub.status eq 'active'}active.png{elseif $all_category_sub_sub.status eq 'inactive'}inactive.png{/if}" style="width: 25px;" /></a></li>
          </ul>
        </li>
        {assign var=i value=$i+1}
        {/foreach}
        {/foreach}
        {/foreach}
      </ul>
    </div>
    {if $searchform neq 'yes'}
    <input type="button" value="Save Ordering" name="saveorder" onclick="save_order();return false;"/>
    {/if}
  </fieldset>

<div id="category_type_model" style="display:none;cursor: default;">
  <div style="position: absolute;top: -12px;right: -11px;" onclick="close_category_type_model();"><img src="{$base_url}public/img/close_modal.png" style="float: right; width:35px;" /></div>
  <div style="font-weight: bold;">Please select feed type</div>
  <div style="float: right; margin:10px 0 0 0;">

   {* <select id="cat_type" name="cat_type" onchange="javascript:window.location = '{$base_url}category/add/?publication_id={$publication.id}&type='+this.value;return false;" style="width:150px;">*}
    <select id="cat_type" name="cat_type" onchange="javascript:window.location = '{$base_url}category/add_cat/?publication_id={$publication.id}&type='+this.value;return false;" style="width:150px;">
   
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
				       
				
       {if $type eq 'admin'}
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
                
		{* added by ashok *}
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
         {/if}       
			</select>
  </div>
</div>
<script type="text/javascript">
var add_cat_uri = '{$base_url}category/add/?publication_id={$publication.id}';
{literal}
function add_cat_redirect() {
	var selectElem = document.getElementById('cat_type');
	var cat_type = selectElem.options[selectElem.selectedIndex].value;alert(cat_type);
	if(cat_type!='')
	{
		window.location = add_cat_uri+'&type='+cat_type;
	}
}
{/literal}
</script> 
