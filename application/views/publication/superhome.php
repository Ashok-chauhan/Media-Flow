
 <style>
  #dialog-message{
  /* background: #9cf;*/
 }

 .dialog-text{
   color: green;
 }
 </style>

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
	
			
		//	updateCategories() ;
			publication_id = $('#publication_id').val();
			num_cats = $('#num_cats').val();
			var order = $('#sortable').sortable("serialize") + '&publication_id='+publication_id+'&num_cats='+num_cats; 
			
      console.log(order);
			$.post("../../category/reorder_ajax_superhome", order, function(theResponse){
				if(theResponse === 'success')
				{
				
        
        $( function() {

         
          
          datadiv = document.getElementById("dialog-message");

          datadiv.innerHTML = '<span class="dialog-text">Category order changed successfully.</span>';

            $( "#dialog-message" ).dialog({
              modal: true,
               position: { my: "center top", at: "center top+15%" },
              
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
<?php
  //print '<pre>';
  //print_r($categories);
//  print_r(json_decode($myhome->myhome));
?>
<div class="container">

<section>
<div class="row">
<div class="text-warning bg-dark p-3">You must Save Order then click on Save Settings to complete super home setting process. </div>
</div>

<div class="row ">
<div class="col-md-7 col-lg-7 ">



     


    <div id="message"></div>
    <div  id="loader"></div>
    
    <div id="messagex" style="padding-left:5px"><b><?php //echo $flashmsg;?></b></div>
 
 
    <div id="dialog-message" title="Category order"></div>


 
 <?php

$base_url = $this->config->item('base_url');
if($this->session->flashdata('catupdate')){
  print '<div class="alert alert-success" role="alert">'.$this->session->flashdata('catupdate').'</div>';
}

?>


<form action="" method="post" enctype="multipart/form-data" name="catForm" id="catForm">
      <input type="hidden" name="publication_id" id="publication_id" value="<?php echo $publication_id;?>"/>
      <input type="hidden" id="num_cats" name="num_cats" value="<?php echo $num_cats;?>"/>
</form>


<div id="sortable" >
<?php

foreach($superhome as $key => $categories){
   

  echo '<div id="cat_'.$categories['id'].'" class="row  bg-white p-1 m-1 border border-top-0 border-left-0 border-right-0 border-secondary">';
  echo '<div class="col-9"  ><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$categories['label'].'
    </div><div class="col-3">';
   ?>

    <form action="/category/deletesuperhome" id="<?php echo $categories['id'];?>" name="<?php echo $categories['id'];?>" method="POST"> 
       <input type="hidden" id="id" name="id" value="<?php echo $categories['id'];?>"/>
       
       <input type="hidden" id="publicationid" name="publicationid" value="<?php echo $categories['publication_id'];?>"/>
       <input type="hidden" id="label" name="label" value="<?php echo $categories['label'];?>"/>
       <input type="image" src="<?php echo $base_url;?>images/inactive.png" alt="submit" width="25"/>
    </form>
    
   
    </div>

      
  </div>



  <?php } ?>


</div>


<!-- <input type="button" value="Save Ordering" class="btn btn-primary" name="saveorder" onclick="save_order();return false;"/> -->




<div class="form-row">
        <div class="form-group col-md-12">
        <button type="button" class="btn btn-secondary btn-lg btn-block" name="saveorder" onclick="save_order();return false;">Save Ordering</button>
           </div>
    </div> 

<!-- <input type="submit" value="Save Ordering" name="saveorder" /> -->

<!-- </div>
</div> -->
 
</div> <!-- end of first col -->






<!-- 2 col bof FUTURE DEVELOPMENT TO CREATE SECOND COL-->


<!-- bof 2 col -->
<div class="col-md-5 col-lg-5 ">  
<h3>Configuration </h3>

<form class="form-signin" action="<?php echo $this->config->item('base_url');?>publication/setSuperhome" method="post" enctype="multipart/form-data">
    	
   	<div class="form-group">
     <?php //$setting = json_decode($myhome->myhome);
     //echo $setting->hide_alerts;
     ?> 
     <input type="hidden" id="publicationid" name="publicationid" value="<?php echo $publication_id;?>" />
   <label for="min_items">Minimum items </label>
  
   <select id="min_items" name ="min_items" class="form-control">
	 <option value="0" >Select minimum items </option>
	
  <?php

  $setting = json_decode($myhome->myhome);
 
  $itmes = range(0,10);
  foreach ($itmes as $key => $value):?>
  <?php if($key == $setting->min_items):?>
		<option value="<?php echo $key ;?> " selected><?php echo $value;?></option>
  <?php elseif($key == 4):?>
    <option value="<?php echo $key ;?> " selected><?php echo $value;?></option>
  <?php else:?>
    <option value="<?php echo $key ;?> " ><?php echo $value;?></option>
  <?php endif; ?>

	<?php endforeach;?>

   </select>
   
   </div>
 

   <div class="form-group">
   <label for="min_items_tablet">Minimum items Tablet </label>
   <select id="min_items_tablet" name ="min_items_tablet" class="form-control">
	<option value="0" >Select minimum items tab</option>
	
  <?php
  $itmes = range(0,10);
  foreach ($itmes as $key => $value):?>
  <?php if($key == $setting->min_items_tablet):?>
		<option value="<?php echo $key ;?> " selected><?php echo $value;?></option>
  <?php elseif($key == 6):?>
    <option value="<?php echo $key ;?> " selected><?php echo $value;?></option>
  <?php else:?>
    <option value="<?php echo $key ;?> "><?php echo $value;?></option>
  <?php endif; ?>

	<?php endforeach;?>

   </select>
   
   </div>

   <div class="form-check">
   <?php if(isset($setting->hide_alerts)&& $setting->hide_alerts !=0):?>
    <input type="checkbox" checked class="form-check-input" id="hide_alerts" name="hide_alerts">
   <?php else:?>
    <input type="checkbox"  class="form-check-input" id="hide_alerts" name="hide_alerts">
   <?php endif;?>
    <label class="form-check-label" for="hide_alerts">Hide alerts</label>
  </div>
</br>

   <div class="form-group">
	<button class="btn btn-lg btn-primary btn-block" type="submit"> Save Settings</button>
</div>

    </form>


<!-- eof 2nd col -->
</div> 

</section>

</div> <!-- end of row -->

</div> <!-- end of container -->

<!-- 2 col eof -->
