<?php error_reporting(0);?>

<div class="container">
<?php //echo base_url();
print '<pre>';
//print_r($category);
//print_r($allcategory);
print '</pre>';
?>
<form action="<?php echo base_url().'category/save';?>" method="post" enctype="multipart/form-data">

  <input type="hidden" name="id" value="<?php echo  $category['id'];?>"/>
  <input type="hidden" name="publication_id" value="<?php echo $publication_id;?>"/>
  <input type="hidden" name="atype" value="list"/>
<fieldset class="border border-secondary rounded p-3">
                            <legend class="w-auto">Category Information</legend>


<div class="form-row">

        <div class="form-group col-md-6">


            <label for="feedType">Feed Type</label>

            <?php 
            $js = array(
                'id'       => 'feedType',
                /*'onChange' => 'some_function();'*/
                'class'    => 'form-control'
        );
            $options = array(
                'NULL'                      => '--NONE--',
                'whiz_rss_stories'          => 'Whiz rss stories',
                'whiz_mrss_photos'			=> 'Calkins Whiz Media RSS (Photo)',
                 'whiz_ott'					=> 'OTT Media RSS',
               'grahamdigital_video_json' 	=> 'OTT Grahamdigital video json',
               'feedburner' 					=> 'Feed Burner RSS Stories',
               'whiz_rss_photos'				=> 'Whiz RSS Photos',
               'legacyobits'					=> 'Legacy Obits',
               'whiz_rss_videos'				=> 'Whiz RSS Videos',
               'worldnow_rss_stories'		=> 'Worldnow RSS Stories',
               'worldnow_rss_videos'			=> 'WWorldnow RSS Videos(MRSS)',
               'landon_demand'				=> 'Land on demand (audio)',
               'newscycle'					=> 'Newscycle Stories',
               'worldnow_mrss_photos'		=> 'Worldnow Photos',
               'whiz_rss_syndicaster'		=> 'Syndicaster Media',
               'whole_hog_sports_audio'		=> 'Whole Hog Sports Audio',
               'cumedia_inforum_json'		=> 'Cumedia Inforum JSON paraser',
               'cumedia_inforum_video_json'	=> 'Cumedia Inforum VIDEO paraser',
               'json'						=> 'JSON',
               'jewishgallery'				=> 'Jewish Journal Photos',
               'jewishvideo'					=> 'Jewish Journal Videos',
               'tout_video_v1'				=> 'Tout Parser V1',
               'newsy'						=> 'Newsy Videos',
               'pictopia'					=> 'Pictopia',
               'pressofacgallery'			=> 'Press of AC Gallery',
               'mycapture'					=> 'Mycapture Gallery',
               'indiawestgallery'			=> 'India West Gallery',
               'commercialappealgallery'		=> 'Commercial Gallery',
               'scrippsgallery'				=> 'Scripps Gallery',
               'whiz_fake_search'			=> 'Whiz Fake Search',
               'vmixvideo'					=> 'Vmix Videos (JSON)',
               'ktncvideo'					=> 'KTNC Video',
               'viddler'						=> 'Viddler',
               'flickr'						=> 'Flickr',
               'atom'						=> 'Atom feed',
               'towngallery'					=> 'Town Gallery Atom',
               'brightcove'					=> 'Brightcove',
               'brightcoveplaylist'			=> 'Brightcove Playlist',
               'brightcovelive'				=> 'Brightcove Live',
               'tigerhuddle_rss_stories'    => 'Tiger Huddle RSS Stories',
               'whiz_custom'                => 'Whiz Custom'
               );
        
        //$shirts_on_sale = array('small', 'large');
        echo form_dropdown('type', $options, $category['type'], $js);
        
            ?>
            

           
        </div>



        <div class="form-group col-md-6">
            <label for="source_uri" class="">URI</label>
            <!-- <div class=""> -->
            <input type="text" class="form-control" name="source_uri" id="source_uri" value="<?php echo $category['source_uri'];?>">
            <!-- </div> -->
        </div>
    
  </div>
  


<div class="form-row">
    <div class="form-group col-md-6">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $category['name'];?>" >
   
    </div>

  <div class="form-group col-md-6">
    <label for="label">Label</label>
    <input type="text" class="form-control" id="label" name="label" value="<?php echo $category['label'];?>">
 </div>

</div>


<div class="form-row">

        <div class="form-group col-md-6">
            <label for="parentcatid">Parnet category</label>
            <?php
            $categories['NULL'] = '--NONE--';
            foreach($allcategory as $key => $value){
                    $categories[$value['id']]=$value['label'];
            }
            $parentAttrib = array(
                'id'       => 'parentcatid',
                /*'onChange' => 'some_function();'*/
                'class'    => 'form-control'
                 );
            //  print '<li> -----> '.$parentCategory['id'];
            //  print '<li>-----cat -> '. $categories['id'];   
            //  print '<pre>';
            //  print_r($categories);
            //  print '</pre>'; 
                
            if($parentCategory){
                        echo form_dropdown('parentcatid', $categories, $parentCategory['id'], $parentAttrib);
            }else{
                        echo form_dropdown('parentcatid', $categories, 'NULL', $parentAttrib);
            }
            ?>
            
        </div>
        <div class="form-group col-md-6">
        <label for="catcolor">Color</label>
        <?php
        // print '<pre>';
        // print_r($category);
        // print '</pre>';

        $colorOption = [
            '#045fb4'   =>'News #045fb4',
            '#d9d919'   =>'Sports #d9d919',
            '#dd1112'   =>'Business #dd1112',
            '#088a08'   =>'Opinion #088a08',
            '#ff00ff'   =>'Entertainment #ff00ff',
            '#f78b1f'   =>'Lifestyle #f78b1f',
            '#aa00ff'   =>'Videos #aa00ff',
            '#8d8d8d'   =>'Obituaries #8d8d8d'
        ];
        $colorAttrib = [
            'id'    => 'catcolor',
            'class' => 'form-control'
        ];
        echo form_dropdown('catcolor', $colorOption, $category['catcolor'], $colorAttrib);
         ?>
           
        </div>
</div>


<div class="form-row">

        <div class="form-group col-md-6">
        <label for="ipadtemplate">iPad Template</label>
        <?php 
        
            $templateOption = [
                ''        => 'Auto',
                'ipad1'   => 'ipad1 : All images',
                'ipad2'   => 'ipad2 : Top story has image',
                'ipad3'   => 'ipad3 : Top story may not have image'
            ];
            $templateAttrib =[
                'id'      => 'ipadtemplate',
                'class'   => 'form-control'
            ];
            echo form_dropdown('ipadtemplate', $templateOption, $category['ipadtemplate'], $templateAttrib);
            
        ?>
           
        </div>
        <div class="form-group col-md-6">
                    <div class="form-check">
                    <?php if($category['feature_story']):?>
                    <input class="form-check-input" type="checkbox" checked="checked" value="1" id="feature_story" name="feature_story">
                    <?php else:?>
                        <input class="form-check-input" type="checkbox"  value="1" id="feature_story" name="feature_story">
                    <?php endif;?>
                    <label class="form-check-label" for="featureImage">
                        Feature Image on Smartphone
                    </label>
                    </div>


        </div>
</div>

<div class="form-row">
        <div class="form-group col-md-6">
        <?php 
        // print '<pre>';
        // print_r($category);
        // print '</pre>';
        ?>
            <label for="name">Product code</label>
            <input type="text" class="form-control" id="prod_code" name="prod_code" value="<?php echo $category['prod_code'];?>">
    
        </div>

        <div class="form-group col-md-6">
            <label for="device">Device</label>
            <select class="form-control" id="device" name="device">
                  <option value="phone">--Choose Device--</option>
                  <option  value="phone" > Phone Only </option>
                  <option value="tablet" > Tablet Only </option>
                  <option value="both" selected="selected"> Both </option>
            </select>
        </div>
        
</div>

<div class="form-row">

        <div class="form-group col-md-6">
            <label for="cat_preroll_url_phone">Preroll url</label>
            <textarea class="form-control" id="cat_preroll_url_phone" name="cat_preroll_url_phone" rows="3"><?php echo $category['cat_preroll_url_phone'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="cat_phone_dfp_banner">Dfp ad code</label>
            <input type="text" class="form-control" id="cat_phone_dfp_banner" name="cat_phone_dfp_banner" value="<?php echo $category['cat_phone_dfp_banner'];?>">
    
        </div>
</div>

<div class="form-row">
        <div class="form-group col-md-6">
                    <div class="form-check">
                    <?php if($category['my_mf']):?>
                    <input class="form-check-input" type="checkbox" checked="checked" value="1" id="my_mf" name="my_mf">
                    <?php else:?>
                        <input class="form-check-input" type="checkbox"  value="1" id="my_mf" name="my_mf">
                    <?php endif;?>
                    <label class="form-check-label" for="my_mf">
                    Default Favorite (Applicable for timeline view)
                    </label>
                    </div>
        </div>
</div>
</fieldset>

<fieldset class="border border-secondary rounded p-3">
                            <legend class="w-auto">Settings</legend>

    <div class="form-row">
        <div class="form-group col-md-6">
        <label for="device">Category type</label>   
        <?php 
        
        $atypeOption = [
            'list'      =>'List',
            /*'list'      =>'Classified',
            'list'      =>'Events',
            'list'      =>'Horoscope',
            */
            'isay'      =>'isay',
            'webkit'    =>'Webkit',
            'wsiweather'=>'wsiweather',
            'nativeweather'=>'nativeweather',
            'baronweather'=>'Baron weather',
            'wsitraffic'  =>'wsitraffic',
            'timeline'  =>'timeline',
            'deeplink'  =>'deeplink',
            '360video'  =>'360video',
            'ottnews'   =>'ottnews',
            'podcast'   =>'Podcast',
            'liveaudio' =>'Live audio'


        ];
        $atypAttrib =[
            'id'      => 'edit_atype',
            'class'   => 'form-control'
        ];
        // print '<pre>';
        // print_r($category);
        // print '</pre>';

        echo form_dropdown('edit_atype', $atypeOption, $category['atype'], $atypAttrib);
        ?>

            
           
        </div>

        <div class="form-group col-md-6">
        
            <label for="parser_type">Parser type</label>
            <input type="text" class="form-control" id="parser_type" name="parser_type" value="<?php echo $category['parser_type'];?>">
    
        </div>

        
        
    </div>


    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="icon_file">Icon</label>
            <input type="file" class="form-control-file" id="icon_file" name="icon_file">
        </div>

        <div class="form-group col-md-6">
        
            <label for="ipad_label">iPad Label</label>
            <input type="text" class="form-control" id="ipad_label" name="ipad_label" value="<?php echo $category['ipad_label'];?>">
    
        </div>
    </div>


    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="story_limit">Story limit</label>
            <input type="text" class="form-control" id="story_limit" name="story_limit" value="<?php echo $category['story_limit'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="ipad_label">iPad category oreder</label>
            <input type="text" class="form-control" id="ipad_catorder" name="ipad_catorder" value="<?php echo $category['ipad_catorder'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="cache">Cache Expire Time In Minute</label>
            <input type="text" class="form-control" id="cache" name="cache" value="<?php echo $category['cache'];?>">
        </div>

        <div class="form-group col-md-6">
        
                    <div class="form-check">
                    <?php if($category['autoplay']):?>
                    <input class="form-check-input" type="checkbox" checked="checked" value="1" id="autoplay" name="autoplay">
                    <?php else:?>
                        <input class="form-check-input" type="checkbox"  value="1" id="autoplay" name="autoplay">
                    <?php endif;?>
                    <label class="form-check-label" for="autoplay">
                    Autoplay (OTT video only)
                    </label>
                    </div>
        </div>
    </div>


    <div class="form-row">
            <div class="form-group col-md-6">
                
                <div class="form-check">
                <?php if($category['delflag']):?>
                    
                <input class="form-check-input" type="checkbox" checked="checked" value="1" id="delflag" name="delflag">
                <?php else:?>
                
                    <input class="form-check-input" type="checkbox"  value="1" id="delflag" name="delflag">
                <?php endif;?>
                <label class="form-check-label" for="delflag">
                Wipe Old Data Before Adding New
                </label>
                </div>
        </div>

        <div class="form-group col-md-6">
        
                    <div class="form-check">
                    <?php if($category['orderflag']):?>
                    <input class="form-check-input" type="checkbox" checked="checked" value="1" id="orderflag" name="orderflag">
                    <?php else:?>
                        <input class="form-check-input" type="checkbox"  value="1" id="orderflag" name="orderflag">
                    <?php endif;?>
                    <label class="form-check-label" for="orderflag">
                    Keep Stories Order Of Feed
                    </label>
                    </div>
        </div>
    </div>


</fieldset>

<fieldset class="border border-secondary rounded p-3">
                            <legend class="w-auto">Advertisement Settings</legend>
    <div class="form-row">

        <div class="form-group col-md-12">
            <label for="adcode">Banner Ad Script</label>
            <textarea class="form-control" id="adcode" name="adcode" rows="3"><?php echo $category['adcode'];?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="adwidth">Width</label>
            <input type="text" class="form-control" id="adwidth" name="adwidth" value="<?php echo $category['adwidth'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="adheight">Height</label>
            <input type="text" class="form-control" id="adheight" name="adheight" value="<?php echo $category['adheight'];?>">
    
        </div>
    </div>   

    <div class="form-row">

        <div class="form-group col-md-6">
            <label for="cat_banner_ads">Banner Ad </label>
            <textarea class="form-control" id="cat_banner_ads" name="cat_banner_ads"  rows="3"><?php echo $category['cat_banner_ads'];?></textarea>
        </div>
        <div class="form-group col-md-6">
            <label for="cat_interstitial_ads">Interstitial Ad</label>
            <textarea class="form-control" id="cat_interstitial_ads" name="cat_interstitial_ads" rows="3"><?php echo $category['cat_interstitial_ads'];?></textarea>
        </div>
    </div> 

    <div class="form-row">

        <div class="form-group col-md-6">
            <label for="cat_video_ads">Video Ad </label>
            <textarea class="form-control" id="cat_video_ads" name="cat_video_ads" rows="3"><?php echo $category['cat_video_ads'];?></textarea>
        </div>
        <div class="form-group col-md-6">
            <label for="cat_native_ad">Native Ad</label>
            <textarea class="form-control" id="cat_native_ad" name="cat_native_ad" rows="3"><?php echo $category['cat_native_ad'];?></textarea>
        </div>
    </div>  

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="inline_ad_frequency">Inline Ad Frequency</label>
            <input type="text" class="form-control" id="inline_ad_frequency" name="inline_ad_frequency" value="<?php echo $category['inline_ad_frequency'];?>">
        </div>
    </div>  

    <div class="form-row">
        <div class="form-group col-md-6">
        <button type="submit" class="btn btn-primary btn-lg btn-block">Save</button>
           </div>

        <div class="form-group col-md-6">
        <a href="<?php echo base_url().'publication/manage/'.$publication_id;?>" id="cancel" name="cancel" class="btn btn-primary btn-lg btn-block">Cancel</a>

        </div>
    </div> 


    
</fieldset>

  
    




  
</form>




</div><!--container-->
