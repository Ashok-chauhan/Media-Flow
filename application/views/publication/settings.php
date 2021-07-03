<div class="container">

<form id="registerForm" action="<?php echo base_url()?>publication/save" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo $publication['id'];?>"/>  
<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Basic Information </legend>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $publication['name'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="publisher_website">Publisher website</label>
            <input type="text" class="form-control" id="publisher_website" name="publisher_website" value="<?php echo $publication['publisher_website'];?>">
    
        </div>
    </div>   


    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="photo_per_ad">Interstitial Ad Frequency</label>
            <input type="text" class="form-control" id="photo_per_ad" name="photo_per_ad" value="<?php echo $publication['photo_per_ad'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="email">iSay Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $publication['email'];?>">
    
        </div>
    </div>   


    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="feedback_email">Feedback Email</label>
            <input type="email" class="form-control" id="feedback_email" name="feedback_email" value="<?php echo $publication['feedback_email'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="paper_name">Paper Name</label>
            <input type="text" class="form-control" id="paper_name" name="paper_name" value="<?php echo $publication['paper_name'];?>">
    
        </div>
    </div>   


    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="terms_conditions">Terms & Conditions URL</label>
            <input type="text" class="form-control" id="terms_conditions" name="terms_conditions" value="<?php echo $publication['terms_conditions'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="privacy_policy">Privacy Policy URL</label>
            <input type="text" class="form-control" id="privacy_policy" name="privacy_policy" value="<?php echo $publication['privacy_policy'];?>">
    
        </div>
    </div>   


    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="copyright">Copyright</label>
            <textarea class="form-control" id="copyright" name="copyright" rows="3"><?php echo $publication['copyright'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="about">About Content</label>
            <textarea class="form-control" id="about" name="about" rows="3"><?php echo $publication['about'];?></textarea>
        </div>
    </div>  


    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="preroll_url_phone">Preroll url</label>
            <textarea class="form-control" id="preroll_url_phone" name="preroll_url_phone" rows="3"><?php echo $publication['preroll_url_phone'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="phone_dfp_banner">Dfp Ad Code</label>
            <input type="text" class="form-control" id="phone_dfp_banner" name="phone_dfp_banner" value="<?php echo $publication['phone_dfp_banner'];?>">
        </div>
    </div>  

        
    <div class="form-row">
        <div class="form-group col-md-12">
        <label for="paywall">Paywall</label>
        <?php
            $paywallOption = [
                '0' => 'Off',
                '1' => 'On'
            ];

            $paywallAttrib = [
                'id'    => 'paywall',
                'class' => 'form-control'
            ];
            echo form_dropdown('paywall', $paywallOption, $publication['paywall'], $paywallAttrib);

        ?>
        
        </div>

    </div>




</fieldset>

<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Live Frame Ad setting </legend>
    <div class="form-row">
    <div class="form-group col-md-12">
    <label for="liveframeads">Live frame ads</label>
    <textarea class="form-control" id="liveframeads" name="liveframeads" rows="3"><?php echo $publication['liveframeads'];?></textarea>
    </div>
    </div>

</fieldset>

<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Publication Information </legend>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="search_url">Search url</label>
            <input type="text" class="form-control" id="search_url" name="search_url" value="<?php echo $publication['search_url'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="search_product_code">Search Product Code</label>
            <input type="text" class="form-control" id="search_product_code" name="search_product_code" value="<?php echo $publication['search_product_code'];?>">
    
        </div>
    </div>   


    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="breakingnews_url">Breaking News url</label>
            <input type="text" class="form-control" id="breakingnews_url" name="breakingnews_url" value="<?php echo $publication['breakingnews_url'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="breakingnews_product_code">Breaking News Product Code</label>
            <input type="text" class="form-control" id="breakingnews_product_code" name="breakingnews_product_code" value="<?php echo $publication['breakingnews_product_code'];?>">
    
        </div>
    </div>  

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="pub_city">City</label>
            <input type="text" class="form-control" id="pub_city" name="pub_city" value="<?php echo $publication['pub_city'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="pub_state">State</label>
            <input type="text" class="form-control" id="pub_state" name="pub_state" value="<?php echo $publication['pub_state'];?>">
    
        </div>
    </div>    

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="zipcode">Zip Code</label>
            <input type="text" class="form-control" id="zipcode" name="zipcode" value="<?php echo $publication['zipcode'];?>">
        </div>

        <div class="form-group col-md-6">
        
        <label for="paywall">Android In-App Parsing</label>
        <?php
            $androidParsing = [
                '0' => 'Falls',
                '1' => 'True'
            ];

            $androidAttrib = [
                'id'    => 'android_inapp_parsing',
                'class' => 'form-control'
            ];
            echo form_dropdown('android_inapp_parsing', $androidParsing, $publication['android_inapp_parsing'], $androidAttrib);

        ?>
        </div>
    </div>    

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="latitude">Latitude</label>
            <input type="text" class="form-control" id="latitude" name="latitude" value="<?php echo $publication['latitude'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="longitude">Longitude</label>
            <input type="text" class="form-control" id="longitude" name="longitude" value="<?php echo $publication['longitude'];?>">
    
        </div>
    </div>  

    <div class="form-row">
    <div class="form-group col-md-6">
        
        <label for="featured_height">Featured Story Height</label>
        <?php
            $storyOption = [
                '0' => 'Normal',
                '1' => 'Medium (xx)',
                '2' => 'Large (xxx)'
            ];

            $storyAttrib = [
                'id'    => 'featured_height',
                'class' => 'form-control'
            ];
            echo form_dropdown('featured_height', $storyOption, $publication['featured_height'], $storyAttrib);

        ?>
        </div>
        <div class="form-group col-md-6">
        
        <label for="group_id">Group Name</label>
        <?php
            $groupOption = [
                '0' => 'Select Group',
                '1' => 'Town News',
                '2' => 'Telygraph',
                '3' => 'Schurz',
                '4' => 'Post and Courier',
                '5' => 'Cecil Whig Newspaper',
                '6' => 'Anniston Star',
                '7' => 'Cordillera',
                '8' => 'npgco',
                '9' => 'Shaw Media',
                '10' => 'Raycom',
                '11' => 'Heartland',
                '12' => 'Rust communications',
                '13' => 'East Oregonian',
                '14' => 'cowles group'
            ];

            $groupAttrib = [
                'id'    => 'group_id',
                'class' => 'form-control'
            ];
            echo form_dropdown('group_id', $groupOption, $publication['group_id'], $groupAttrib);

        ?>
        </div>
    </div>



</fieldset>

<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> UI Graphics </legend>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="splash_screen">Splash Screen</label>
            <input type="text" class="form-control" id="splash_screen" name="splash_screen" value="<?php echo $publication['splash_screen'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="logo">Logo Image</label>
            <input type="text" class="form-control" id="logo" name="logo" value="<?php echo $publication['logo'];?>">
    
        </div>
    </div>   

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="company_icon">Company Icon</label>
            <input type="text" class="form-control" id="company_icon" name="company_icon" value="<?php echo $publication['company_icon'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="banner_color">Banner Color</label>
            <input type="text" class="form-control" id="banner_color" name="banner_color" value="<?php echo $publication['banner_color'];?>">
    
        </div>
    </div>  

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="banner_font_color">Banner Font Color</label>
            <input type="text" class="form-control" id="banner_font_color" name="banner_font_color" value="<?php echo $publication['banner_font_color'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="list_v_color">List View Color</label>
            <input type="text" class="form-control" id="list_v_color" name="list_v_color" value="<?php echo $publication['list_v_color'];?>">
    
        </div>
    </div>   

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="list_view_font_color">List View Font Color</label>
            <input type="text" class="form-control" id="list_view_font_color" name="list_view_font_color" value="<?php echo $publication['list_view_font_color'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="list_view_detail_color">List View Details Color</label>
            <input type="text" class="form-control" id="list_view_detail_color" name="list_view_detail_color" value="<?php echo $publication['list_view_detail_color'];?>">
    
        </div>
    </div>   

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="title_color">Title Color</label>
            <input type="text" class="form-control" id="title_color" name="title_color" value="<?php echo $publication['title_color'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="title_font_color">Title Font Color</label>
            <input type="text" class="form-control" id="title_font_color" name="title_font_color" value="<?php echo $publication['title_font_color'];?>">
    
        </div>
    </div>   


    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="ios_colorscheme">IOS Colorscheme</label>
            <input type="text" class="form-control" id="ios_colorscheme" name="ios_colorscheme" value="<?php echo $publication['ios_colorscheme'];?>">
        </div>
    </div>   



</fieldset>
<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Advertisement </legend>
    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="video_per_ad">Videos Per Ad</label>
            <input type="text" class="form-control" id="video_per_ad" name="video_per_ad" value="<?php echo $publication['video_per_ad'];?>">
        </div>
    </div> 

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="full_page_ad_width">Full Page Ad Width</label>
            <input type="text" class="form-control" id="full_page_ad_width" name="full_page_ad_width" value="<?php echo $publication['full_page_ad_width'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="full_page_ad_height">Full Page Ad Height</label>
            <input type="text" class="form-control" id="full_page_ad_height" name="full_page_ad_height" value="<?php echo $publication['full_page_ad_height'];?>">
    
        </div>
    </div>  

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="video_ad_key">Video Ad Key</label>
            <input type="text" class="form-control" id="video_ad_key" name="video_ad_key" value="<?php echo $publication['video_ad_key'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="full_page_ad_code">Full Page Ad Code</label>
            <textarea class="form-control" id="full_page_ad_code" name="full_page_ad_code" rows="3"><?php echo $publication['full_page_ad_code'];?></textarea>
    
        </div>
    </div>  

    <div class="form-row">
        
        <div class="form-group col-md-12">
        
            <label for="ipad_banner_adcode">iPad Banner Ad Code</label>
            <textarea class="form-control" id="ipad_banner_adcode" name="ipad_banner_adcode" rows="3"><?php echo $publication['ipad_banner_adcode'];?></textarea>
    
        </div>
    </div>  

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="ipad_banner_width">iPad Banner Ad Width</label>
            <input type="text" class="form-control" id="ipad_banner_width" name="ipad_banner_width" value="<?php echo $publication['ipad_banner_width'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="ipad_banner_height">iPad Banner Ad Height</label>
            <input type="text" class="form-control" id="ipad_banner_height" name="ipad_banner_height" value="<?php echo $publication['ipad_banner_height'];?>">
    
        </div>
    </div>  

    <div class="form-row">
        
        <div class="form-group col-md-12">
        
            <label for="ipad_skyscraper_adcode">iPad Skyscraper Ad Code</label>
            <textarea class="form-control" id="ipad_skyscraper_adcode" name="ipad_skyscraper_adcode" rows="3"><?php echo $publication['ipad_skyscraper_adcode'];?></textarea>
    
        </div>
    </div>  

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="ipad_skyscraper_width">iPad Skyscraper Ad Width</label>
            <input type="text" class="form-control" id="ipad_skyscraper_width" name="ipad_skyscraper_width" value="<?php echo $publication['ipad_skyscraper_width'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="ipad_skyscraper_height">iPad Skyscraper Ad Height</label>
            <input type="text" class="form-control" id="ipad_skyscraper_height" name="ipad_skyscraper_height" value="<?php echo $publication['ipad_skyscraper_height'];?>">
    
        </div>
    </div>  

    <div class="form-row">
        
        <div class="form-group col-md-12">
        
            <label for="ipad_fullpage_landscape_adcode">iPad Fullpage Landscape Adcode</label>
            <textarea class="form-control" id="ipad_fullpage_landscape_adcode" name="ipad_fullpage_landscape_adcode" rows="3"><?php echo $publication['ipad_fullpage_landscape_adcode'];?></textarea>
    
        </div>
    </div> 

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="ipad_fullpage_landscape_width">iPad Fullpage Landscape Ad Width</label>
            <input type="text" class="form-control" id="ipad_fullpage_landscape_width" name="ipad_fullpage_landscape_width" value="<?php echo $publication['ipad_fullpage_landscape_width'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="ipad_fullpage_landscape_height">iPad Fullpage Landscape Ad Height</label>
            <input type="text" class="form-control" id="ipad_fullpage_landscape_height" name="ipad_fullpage_landscape_height" value="<?php echo $publication['ipad_fullpage_landscape_height'];?>">
    
        </div>
    </div>   

    <div class="form-row">
        
        <div class="form-group col-md-12">
        
            <label for="ipad_fullpage_portrait_adcode">iPad Fullpage Portrait Adcode</label>
            <textarea class="form-control" id="ipad_fullpage_portrait_adcode" name="ipad_fullpage_portrait_adcode" rows="3"><?php echo $publication['ipad_fullpage_portrait_adcode'];?></textarea>
    
        </div>
    </div> 


    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="ipad_fullpage_portrait_width">iPad Fullpage Portrait Ad Width</label>
            <input type="text" class="form-control" id="ipad_fullpage_portrait_width" name="ipad_fullpage_portrait_width" value="<?php echo $publication['ipad_fullpage_portrait_width'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="ipad_fullpage_portrait_height">iPad Fullpage Portrait Ad Height</label>
            <input type="text" class="form-control" id="ipad_fullpage_portrait_height" name="ipad_fullpage_portrait_height" value="<?php echo $publication['ipad_fullpage_portrait_height'];?>">
    
        </div>
    </div>   

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="fire_banner_ad_width">Kindle Fire Banner Ad Width</label>
            <input type="text" class="form-control" id="fire_banner_ad_width" name="fire_banner_ad_width" value="<?php echo $publication['fire_banner_ad_width'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="fire_banner_ad_height">Kindle Fire Banner Ad Height</label>
            <input type="text" class="form-control" id="fire_banner_ad_height" name="fire_banner_ad_height" value="<?php echo $publication['fire_banner_ad_height'];?>">
    
        </div>
    </div>   


    <div class="form-row">
        
        <div class="form-group col-md-12">
        
            <label for="fire_fullpage_ad">Kindle Fire Fullpage Ad Code</label>
            <textarea class="form-control" id="fire_fullpage_ad" name="fire_fullpage_ad" rows="3"><?php echo $publication['fire_fullpage_ad'];?></textarea>
    
        </div>
    </div> 

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="fire_fullpage_ad_width">Kindle Fire Fullpage Ad Width</label>
            <input type="text" class="form-control" id="fire_fullpage_ad_width" name="fire_fullpage_ad_width" value="<?php echo $publication['fire_fullpage_ad_width'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="fire_fullpage_ad_height">Kindle Fire Fullpage Ad Height</label>
            <input type="text" class="form-control" id="fire_fullpage_ad_height" name="fire_fullpage_ad_height" value="<?php echo $publication['fire_fullpage_ad_height'];?>">
    
        </div>
    </div> 

    <div class="form-row">
        
        <div class="form-group col-md-12">
        
            <label for="fire_fullpage_landscape_ad">Kindle Fire Fullpage Landscape Ad Code</label>
            <textarea class="form-control" id="fire_fullpage_landscape_ad" name="fire_fullpage_landscape_ad" rows="3"><?php echo $publication['fire_fullpage_landscape_ad'];?></textarea>
    
        </div>
    </div>  

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="fire_fullpage_landscape_ad_width">Kindle Fire Fullpage Landscape Ad Width</label>
            <input type="text" class="form-control" id="fire_fullpage_landscape_ad_width" name="fire_fullpage_landscape_ad_width" value="<?php echo $publication['fire_fullpage_landscape_ad_width'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="fire_fullpage_landscape_ad_height">Kindle Fire Fullpage Landscape Ad Height</label>
            <input type="text" class="form-control" id="fire_fullpage_landscape_ad_height" name="fire_fullpage_landscape_ad_height" value="<?php echo $publication['fire_fullpage_landscape_ad_height'];?>">
    
        </div>
    </div>  


    <div class="form-row">
        
        <div class="form-group col-md-12">
        
            <label for="cat_common_adcode">Phone Banner Ad Code</label>
            <textarea class="form-control" id="cat_common_adcode" name="cat_common_adcode" rows="3"><?php echo $publication['cat_common_adcode'];?></textarea>
    
        </div>
    </div>  

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="cat_common_ad_width">Phone Banner Ad Width</label>
            <input type="text" class="form-control" id="cat_common_ad_width" name="cat_common_ad_width" value="<?php echo $publication['cat_common_ad_width'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="cat_common_ad_height">Phone Banner Ad Height</label>
            <input type="text" class="form-control" id="cat_common_ad_height" name="cat_common_ad_height" value="<?php echo $publication['cat_common_ad_height'];?>">
    
        </div>
    </div>  
    
    <div class="form-row">
        <div class="form-group col-md-6">
        <label for="article_ads">Article Ads</label>
        <?php
            $articleOption = [
                '0' => 'Falls',
                '1' => 'True'
            ];

            $articleAttrib = [
                'id'    => 'article_ads',
                'class' => 'form-control'
            ];
            echo form_dropdown('article_ads', $articleOption, $publication['article_ads'], $articleAttrib);

        ?>
        </div>
        <div class="form-group col-md-6">
            <label for="offline_mode_enabled">Offline Mode Enabled</label>
            <?php
            $offlineModeOption = [
                '0' => 'Falls',
                '1' => 'True'
            ];

            $offlineModeAttrib = [
                'id'    => 'offline_mode_enabled',
                'class' => 'form-control'
            ];
            echo form_dropdown('offline_mode_enabled', $offlineModeOption, $publication['offline_mode_enabled'], $offlineModeAttrib);

        ?>
        </div>
    </div>    

    <div class="form-row">
    <div class="form-group col-md-12">
            <label for="dfp_ads">DFP Ads</label>
            <?php
            $dfpadModeOption = [
                '0' => 'Falls',
                '1' => 'True'
            ];

            $dfpadModeAttrib = [
                'id'    => 'dfp_ads',
                'class' => 'form-control'
            ];
            echo form_dropdown('dfp_ads', $dfpadModeOption, $publication['dfp_ads'], $dfpadModeAttrib);

        ?>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="dfp_foreground">Startup Ad DFP</label>
            <input type="text" class="form-control" id="dfp_foreground" name="dfp_foreground" value="<?php echo $publication['dfp_foreground'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="foreground_time_limit">Startup Ad Repeat(minutes)</label>
            <input type="text" class="form-control" id="foreground_time_limit" name="foreground_time_limit" value="<?php echo $publication['foreground_time_limit'];?>">
    
        </div>
    </div>  
</fieldset>

<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Analytics </legend>

    
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="flurrykey">Flurry Key For Android</label>
            <input type="text" class="form-control" id="flurrykey" name="flurrykey" value="<?php echo $publication['flurrykey'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="flurrykey_fire">Flurry Key for Kindle Fire</label>
            <input type="text" class="form-control" id="flurrykey_fire" name="flurrykey_fire" value="<?php echo $publication['flurrykey_fire'];?>">
    
        </div>
    </div> 

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="flurrykey_iphone">Flurry Key For iPhone</label>
            <input type="text" class="form-control" id="flurrykey_iphone" name="flurrykey_iphone" value="<?php echo $publication['flurrykey_iphone'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="flurrykey_ipad">Flurry Key For iPad</label>
            <input type="text" class="form-control" id="flurrykey_ipad" name="flurrykey_ipad" value="<?php echo $publication['flurrykey_ipad'];?>">
    
        </div>
    </div>  

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="google_analytics_key_android">Google Analytics key Android</label>
            <input type="text" class="form-control" id="google_analytics_key_android" name="google_analytics_key_android" value="<?php echo $publication['google_analytics_key_android'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="google_analytics_key_fire">Google Analytics key Fire</label>
            <input type="text" class="form-control" id="google_analytics_key_fire" name="google_analytics_key_fire" value="<?php echo $publication['google_analytics_key_fire'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="google_analytics_key_iphone">Google Analytics key iPhone</label>
            <input type="text" class="form-control" id="google_analytics_key_iphone" name="google_analytics_key_iphone" value="<?php echo $publication['google_analytics_key_iphone'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="google_analytics_key_ipad">Google Analytics key iPad</label>
            <input type="text" class="form-control" id="google_analytics_key_ipad" name="google_analytics_key_ipad" value="<?php echo $publication['google_analytics_key_ipad'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="iphone_omniture_account_name">iPhone Omniture Account Name</label>
            <input type="text" class="form-control" id="iphone_omniture_account_name" name="iphone_omniture_account_name" value="<?php echo $publication['iphone_omniture_account_name'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="ipad_omniture_account_name">iPad Omniture Account Name</label>
            <input type="text" class="form-control" id="ipad_omniture_account_name" name="ipad_omniture_account_name" value="<?php echo $publication['ipad_omniture_account_name'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="fire_omniture_account_name">Fire Omniture Account Name</label>
            <input type="text" class="form-control" id="fire_omniture_account_name" name="fire_omniture_account_name" value="<?php echo $publication['fire_omniture_account_name'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="iphone_omniture_tracking_server">iPhone Omniture Tracking Server</label>
            <input type="text" class="form-control" id="iphone_omniture_tracking_server" name="iphone_omniture_tracking_server" value="<?php echo $publication['iphone_omniture_tracking_server'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="ipad_omniture_tracking_server">iPad Omniture Tracking Server</label>
            <input type="text" class="form-control" id="ipad_omniture_tracking_server" name="ipad_omniture_tracking_server" value="<?php echo $publication['ipad_omniture_tracking_server'];?>">
        </div>

    </div>

</fieldset>

<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Social Media </legend>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="facebook_app_id">Facebook App Id</label>
            <input type="text" class="form-control" id="facebook_app_id" name="facebook_app_id" value="<?php echo $publication['facebook_app_id'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="facebook_secret_code">Facebook Secret Code</label>
            <input type="text" class="form-control" id="facebook_secret_code" name="facebook_secret_code" value="<?php echo $publication['facebook_secret_code'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="twitterkey">Twitter Consumer Key</label>
            <input type="text" class="form-control" id="twitterkey" name="twitterkey" value="<?php echo $publication['twitterkey'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="twitter_secret_code">Twitter Consumer Secret Code</label>
            <input type="text" class="form-control" id="twitter_secret_code" name="twitter_secret_code" value="<?php echo $publication['twitter_secret_code'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="twitter_callback_url">Twitter Callback URL</label>
            <input type="text" class="form-control" id="twitter_callback_url" name="twitter_callback_url" value="<?php echo $publication['twitter_callback_url'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="twitter_handle">Twitter Handle</label>
            <input type="text" class="form-control" id="twitter_handle" name="twitter_handle" value="<?php echo $publication['twitter_handle'];?>">
    
        </div>
    </div>
    
</fieldset>

<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Authentication </legend>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="publication_wsm_name">Publication WSM Name</label>
            <input type="text" class="form-control" id="publication_wsm_name" name="publication_wsm_name" value="<?php echo $publication['publication_wsm_name'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="login_url">Login URL</label>
            <input type="text" class="form-control" id="login_url" name="login_url" value="<?php echo $publication['login_url'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="login">Login JSON</label>
            <textarea class="form-control" id="login" name="login" rows="3"><?php echo $publication['login'];?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="login_stub_code">Login Stub Code</label>
            <textarea class="form-control" id="login_stub_code" name="login_stub_code" rows="3"><?php echo $publication['login_stub_code'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="auth_stub_code">Auth Stub Code</label>
            <textarea class="form-control" id="auth_stub_code" name="auth_stub_code" rows="3"><?php echo $publication['auth_stub_code'];?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="login_stub_code_android">Login Stub Code For Android</label>
            <textarea class="form-control" id="login_stub_code_android" name="login_stub_code_android" rows="3"><?php echo $publication['login_stub_code_android'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="auth_stub_code_android">Auth Stub Code For Android</label>
            <textarea class="form-control" id="auth_stub_code_android" name="auth_stub_code_android" rows="3"><?php echo $publication['auth_stub_code_android'];?></textarea>
        </div>
    </div>

</fieldset>

<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Miscellaneous </legend>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="forgot_password_url">Forgot password URL</label>
            <input type="text" class="form-control" id="forgot_password_url" name="forgot_password_url" value="<?php echo $publication['forgot_password_url'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="login_help_url">Login Help URL</label>
            <input type="text" class="form-control" id="login_help_url" name="login_help_url" value="<?php echo $publication['login_help_url'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="faq_url">FAQ URL</label>
            <input type="text" class="form-control" id="faq_url" name="faq_url" value="<?php echo $publication['faq_url'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="weather_url">Weather URL</label>
            <input type="text" class="form-control" id="weather_url" name="weather_url" value="<?php echo $publication['weather_url'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="tabbed_categories">Tab Bar Categories</label>
            <textarea class="form-control" id="tabbed_categories" name="tabbed_categories" rows="3"><?php echo $publication['tabbed_categories'];?></textarea>
        </div>

        <div class="form-group col-md-6">
        
            <label for="read_story_url">Read story URL</label>
            <input type="text" class="form-control" id="read_story_url" name="read_story_url" value="<?php echo $publication['read_story_url'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="video_ads">Video ads</label>
            <textarea class="form-control" id="video_ads" name="video_ads" rows="3"><?php echo $publication['video_ads'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="push_notification">Push Notification</label>
            <textarea class="form-control" id="push_notification" name="push_notification" rows="3"><?php echo $publication['push_notification'];?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="banner_ads">Banner ads</label>
            <textarea class="form-control" id="banner_ads" name="banner_ads" rows="3"><?php echo $publication['banner_ads'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="interstitial_ads">Interstitial ads</label>
            <textarea class="form-control" id="interstitial_ads" name="interstitial_ads" rows="3"><?php echo $publication['interstitial_ads'];?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="inapp_params">In App Purchase - Settings</label>
            <textarea class="form-control" id="inapp_params" name="inapp_params" rows="3"><?php echo $publication['inapp_params'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="timeline">Time line</label>
            <textarea class="form-control" id="timeline" name="timeline" rows="3"><?php echo $publication['timeline'];?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="notes">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $publication['notes'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="native_ads">Native ads</label>
            <textarea class="form-control" id="native_ads" name="native_ads" rows="3"><?php echo $publication['native_ads'];?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="paymeter_config_ios">Paymeter config ios</label>
            <textarea class="form-control" id="paymeter_config_ios" name="paymeter_config_ios" rows="3"><?php echo $publication['paymeter_config_ios'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="paymeter_config_android">Paymeter config android</label>
            <textarea class="form-control" id="paymeter_config_android" name="paymeter_config_android" rows="3"><?php echo $publication['paymeter_config_android'];?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="alerts_table_url">Alerts table url</label>
            <textarea class="form-control" id="alerts_table_url" name="alerts_table_url" rows="3"><?php echo $publication['alerts_table_url'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="weather_json">Weather json</label>
            <textarea class="form-control" id="weather_json" name="weather_json" rows="3"><?php echo $publication['weather_json'];?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="live_video_url">Live video url</label>
            <input type="text" class="form-control" id="live_video_url" name="live_video_url" value="<?php echo $publication['live_video_url'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="live_video_preroll_ad">Live video preroll ad</label>
            <input type="text" class="form-control" id="live_video_preroll_ad" name="live_video_preroll_ad" value="<?php echo $publication['live_video_preroll_ad'];?>">
    
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="share_story_email">Share story email</label>
            <textarea class="form-control" id="share_story_email" name="share_story_email" rows="3"><?php echo $publication['share_story_email'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="image_resizer_url">Image resizer url</label>
            
            <input type="text" class="form-control" id="image_resizer_url" name="image_resizer_url" value="<?php echo $publication['image_resizer_url'];?>">
        </div>
    </div>


     <div class="form-row">
        <div class="form-group col-md-6">
            <label for="extra">Extra</label>
            <textarea class="form-control" id="extra" name="extra" rows="3"><?php echo $publication['extra'];?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label for="user_consent_screen">User consent screen</label>
            <textarea class="form-control" id="user_consent_screen" name="user_consent_screen" rows="3"><?php echo $publication['user_consent_screen'];?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="extra">My Home</label>
            <textarea class="form-control" id="myhome" name="myhome" rows="3"><?php echo $publication['myhome'];?></textarea>
        </div>

    </div> 

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="ccpa_email">CCPA Email</label>
            <input type="text" class="form-control" id="ccpa_email" name="ccpa_email" value="<?php echo $publication['ccpa_email'];?>">
        </div>

        <div class="form-group col-md-6">
        
            <label for="live_video_preroll_ad">CCPA Display</label>
            <?php
            $ccpaDisplayOption = [
                '0' => '0',
                '1' => '1'
            ];

            $ccpaDisplayAttrib = [
                'id'    => 'ccpa_display',
                'class' => 'form-control'
            ];
            echo form_dropdown('ccpa_display', $ccpaDisplayOption, $publication['ccpa_display'], $ccpaDisplayAttrib);

        ?>
        </div>
    </div>



    <div class="form-row">
        <div class="form-group col-md-6">
           
        <button type="submit" class="btn btn-primary btn-lg btn-block">Submit</button>
        </div>

        <div class="form-group col-md-6">
        <a href="<?php echo base_url();?>publication/manage/<?php echo $publication['id'];?>" id="cancel" name="cancel" class="btn btn-primary btn-lg btn-block">Cancel</a>
        
        </div>
    </div>

    


</fieldset>

<!-- <button type="submit" class="btn btn-primary">Submit</button> -->
</form>










</div>
