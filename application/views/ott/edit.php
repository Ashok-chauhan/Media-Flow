<div class="container">

<div class="row border border-secondary border-left-0 border-right-0 m-2"> <!-- bof 2 row -->
<div class="col-mb-6 col-lg-6 col-sm-6 p-2"> 

<!-- <div id="video " > -->
<form action="<?php echo base_url();?>ott/edit" method="post" enctype="multipart/form-data" id="alignForm">

 <div class="form-group">
  <!-- <label for="video_url" class="form-label">Video URL</label> -->
  <input type="text" class="form-control" id="video_url" name="video_url" value="<?php echo $video['uri'];?>">
</div>

<div class="form-group">
  <!-- <label for="video_thumb" class="form-label">Video thumbnail url</label> -->
  <input type="text" class="form-control" id="video_thumb" name="video_thumb" value="<?php echo $video['icon_uri'];?>">
</div>

<div class="form-group">
  <!-- <label for="video_title" class="form-label">Video title</label> -->
  <input type="text" class="form-control" id="video_title" name="video_title" value="<?php echo $video['title'];?>">
</div>
    <input type="hidden" name="priority" value="<?php echo $priority;?>">
	<input type="hidden" name="pub_id" value="<?php echo $pub_id;?>">
    <input type="hidden" name="id" value="<?php echo $video['id'];?>">


    <div class="form-group">
        
        <!-- <label for="format">Format</label> -->
        <?php
            $formatOption = [
                '' => 'Please choose format',
                'mp4' => 'mp4',
                'hls' => 'hls'
            ];

            $formatAttrib = [
                
                'class' => 'form-control'
            ];
           // echo form_dropdown('format', $formatOption, $publication['format'], $formatAttrib);
           echo form_dropdown('format', $formatOption, $formatSelect, $formatAttrib);

        ?>
        </div>


        <div class="form-group">
        
        <!-- <label for="video_group">Please choose type</label> -->
        <?php
            $videoOption = [
                '' => 'Please choose type...',
                'live' => 'Livestream',
                'vod' => 'VOD',
                'sponsored' => 'Sponsored'
            ];

            $videoAttrib = [
                
                'class' => 'form-control'
            ];
            //echo form_dropdown('video_group', $videoOption, $publication['video_group'], $videoAttrib);
            echo form_dropdown('video_group', $videoOption, $typeSelect , $videoAttrib);

        ?>
        </div>
        
</div><!-- eof col 1 -->


<div class="col-mb-6 col-lg-6 col-sm-6 p-2"> <!-- bof col 2 -->
        <div class="form-group">
        
        <!-- <label for="liveframe">Liveframe</label> -->
        <?php
            $liveframeOption = [
                '' => 'Please choose Live frame...',
                '0' => 'Off [0]',
                '1' => 'On [1]',
                '2' => 'Others [2]'
            ];

            $liveframeAttrib = [
                
                'class' => 'form-control'
            ];
           // echo form_dropdown('liveframe', $liveframeOption, $publication['liveframe'], $liveframeAttrib);
           echo form_dropdown('liveframe', $liveframeOption, $lveframeSelect, $liveframeAttrib);

        ?>
        </div>

        
<div class="form-group">
  <!-- <label for="daiAssetKey" class="form-label">dai asset key</label> -->
  <input type="text" class="form-control" id="daiAssetKey" name="daiAssetKey" value="<?php echo $video['daiAssetKey'];?>">
</div>
<div class="form-group">
  <!-- <label for="daiApiKey" class="form-label">dai api key</label> -->
  <input type="text" class="form-control" id="daiApiKey" name="daiApiKey" value="<?php echo $video['daiApiKey'];?>">
</div>
<div class="form-group">
  <!-- <label for="authUrl" class="form-label">Authentication url</label> -->
  <input type="text" class="form-control" id="authUrl" name="authUrl" value="<?php echo $video['authUrl'];?>">
</div>


<div class="  form-check">
<?php if ($video['sticky']):?>
    <input type="checkbox" checked class="form-check-input" id="sticky" name="sticky" value="<?php echo $video['sticky'];?>">
<?php else:?>
    <input type="checkbox"  class="form-check-input" id="sticky" name="sticky" value="1">
<?php endif;?>

    <label class="form-check-label" for="sticky">Autoplay</label>
  </div>
  <button type="submit" class="btn btn-primary btn-block">Submit</button>


</form>
</div> <!--- end of col 2 -->

</div> <!-- eof of 2 row -->




</div>

