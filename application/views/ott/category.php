<?php 
//print'<pare>';
//print_r($data);
//error_reporting(0);
?>


<style>
  #sortable { list-style-type: none; margin: 0; padding: 0; width: 800px; }
  #sortable div {  margin: 3px 3px 3px 0; padding: 1px; float: left; width: 255px; height: 300px; font-size: 1em; text-align: center; }
 
 .highlight {
    border: 1px dotted red;
    font-weight: bold;
    background-color: #FFFA90;
}
</style>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>


<script>



$( function (){
$( "#sortable" ).sortable({
    placeholder: "highlight",
    /*axis: 'y',*/
    update: function (event, ui) {
	//div.item.toggleClass("highlight");
        var data = $(this).sortable('serialize');

        // POST to server using $.post or $.ajax
        $.ajax({
            data: data,
            type: 'POST',
            url: 'category'
        });
    }
});
});



</script>

<div class="container">



<div class="row border border-secondary border-left-0 border-right-0 border-top-0 m-2" >
    <div class="col-md-12">
    <h2 >Add OTT Video </h2>
    </div>
</div> <!-- end row -->

<div class="row  m-2" >


<div class="col-md-2 m-3"><?php echo $label;?> is : </div> 



            <form class="form-inline" action="<?php echo base_url();?>ott/catStatus" method="post" enctype="multipart/form-data" id="catstatus">
<?php if($status =='inactive'):?>
    
            <input type="hidden" name="catid" value="<?php echo $catid;?>">
			<input type="hidden" name="pub_id" value="<?php echo $pub_id;?>">
			<input type="hidden" name="status" value="<?php echo $status;?>">
			<button type="submit" class="btn btn-outline-primary" name="submit_catstat">Enabled </button>
    
            </form>

<?php else: ?>

            <form class="form-inline" action="<?php echo base_url();?>ott/catStatus" method="post" enctype="multipart/form-data" id="catstatus">
       
			<input type="hidden" name="catid" value="<?php echo $catid;?>">
			<input type="hidden" name="pub_id" value="<?php echo $pub_id;?>">
			<input type="hidden" name="status" value="<?php echo $status;?>">
			<button type="submit" class="btn btn-outline-primary m-3" name="submit_catstat"> Disabled </button>
        
			</form>

<?php endif;?>

            <form class="form-inline" action="<?php echo base_url();?>ott/catlabel" method="post" enctype="multipart/form-data" id="catstatus">

			<input type="text"  class= "form-control m-3" name="label" id="label" value="<?php echo $label;?>" />


			<input type="hidden" name="pub_id" value="<?php echo $pub_id;?>">
			<input type="hidden" name="catid" value="<?php echo $catid;?>">
			<input type="hidden" name="status" value="<?php echo $status;?>">

	        <button type="submit" class="btn btn-outline-primary m-3" name="submit_label"> Edit label </button>

        
			</form>


</div> <!--end of row -->

<div class="row border border-secondary border-left-0 border-right-0 m-2"> <!-- bof 2 row -->
<div class="col-mb-6 col-lg-6 col-sm-6 p-2"> 

<!-- <div id="video " > -->
<form action="<?php echo base_url();?>ott/category" method="post" enctype="multipart/form-data" id="alignForm">

 <div class="form-group">
  <!-- <label for="video_url" class="form-label">Video URL</label> -->
  <input type="text" class="form-control" id="video_url" name="video_url" placeholder="Video URL">
</div>

<div class="form-group">
  <!-- <label for="video_thumb" class="form-label">Video thumbnail url</label> -->
  <input type="text" class="form-control" id="video_thumb" name="video_thumb" placeholder="Video thumbnail url">
</div>

<div class="form-group">
  <!-- <label for="video_title" class="form-label">Video title</label> -->
  <input type="text" class="form-control" id="video_title" name="video_title" placeholder="Video title">
</div>
    <input type="hidden" name="priority" value="<?php echo $priority;?>">
	<input type="hidden" name="pub_id" value="<?php echo $pub_id;?>">


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
           echo form_dropdown('format', $formatOption, '', $formatAttrib);

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
            echo form_dropdown('video_group', $videoOption, '', $videoAttrib);

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
           echo form_dropdown('liveframe', $liveframeOption, '', $liveframeAttrib);

        ?>
        </div>

        
<div class="form-group">
  <!-- <label for="daiAssetKey" class="form-label">dai asset key</label> -->
  <input type="text" class="form-control" id="daiAssetKey" name="daiAssetKey" placeholder="dai asset key">
</div>
<div class="form-group">
  <!-- <label for="daiApiKey" class="form-label">dai api key</label> -->
  <input type="text" class="form-control" id="daiApiKey" name="daiApiKey" placeholder="dai api key">
</div>
<div class="form-group">
  <!-- <label for="authUrl" class="form-label">Authentication url</label> -->
  <input type="text" class="form-control" id="authUrl" name="authUrl" placeholder="authUrl">
</div>


<div class="  form-check">
    <input type="checkbox" class="form-check-input" id="sticky" name="sticky" value="1">
    <label class="form-check-label" for="sticky">Autoplay</label>
  </div>
  <button type="submit" class="btn btn-primary btn-block">Submit</button>


</form>
</div> <!--- end of col 2 -->

</div> <!-- eof of 2 row -->



<div class="row border border-secondary border-left-0 border-right-0 m-2" >
    <div id="sortable">
	<?php foreach($videos as $vid => $video):?>
	<div id= "item-<?php echo $video['id'];?>" class="item"><img src="<?php echo $video['icon_uri'];?>" width="250" >
		<div  ><b><?php echo $video['title'];?></b><br/>
		<a href="<?php echo base_url();?>ott/edit/<?php echo $video['id'].'/'.$pub_id;?>"><img src="<?php echo base_url();?>images/b_edit.png"> </a> &nbsp;&nbsp; | &nbsp;&nbsp;<a href="<?php echo base_url().'ott/delete/'.$video['id'].'/'.$pub_id;?>"> <img src="<?php echo base_url();?>images/b_drop.png"></a>
        <?php if ($video['sticky']):?>
            &nbsp;&nbsp; | &nbsp;&nbsp; <i>Autoplay</i>
        <?php endif;?>
		</div>
	</div>
	<?php endforeach;?>
	</div>
</div>




</div>

