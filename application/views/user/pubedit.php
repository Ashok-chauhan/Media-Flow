
<div class="container">



<form id="registerForm" action="<?php echo base_url()?>user/pubedit" method="post" enctype="multipart/form-data">

<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Publisher registration </legend>

<div class="form-row align-items-center">
<input type="hidden" name="type" value="publisher"/>
<input type="hidden" name="id" value="<?php echo $pubedit['id'];?>"/>
		
<div class="form-group col-md-6">
    <label for="first_name">First name</label>
    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $pubedit['first_name'];?>" >
    
  </div>
<div class="form-group col-md-6">
    <label for="last_name">Last name</label>
    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $pubedit['last_name'];?>" >
    
  </div>
  <div class="form-group col-md-6">
    <label for="email">Email address</label>
    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" value="<?php echo $pubedit['email'];?>">
    
  </div>
  <div class="form-group col-md-6">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" value="<?php echo $pubedit['password'];?>">
  </div>

  

  <div class="form-group form-check col-md-6">
    <?php if($pubedit['analytics']):?>
    <input type="checkbox" checked class="form-check-input" id="analytics" name="analytics" value="analytics">
    <?php else:?>
        <input type="checkbox"  class="form-check-input" id="analytics" name="analytics" value="analytics">
    <?php endif;?>
    <label class="form-check-label" for="analytics">Analytics</label>
  </div>
  <div class="form-group form-check col-md-6">
  <?php if($pubedit['cms']):?>
    <input type="checkbox" checked value="cms" class="form-check-input" id="cms" name="cms">
  <?php else:?>
    <input type="checkbox" value="cms" class="form-check-input" id="cms" name="cms">
  <?php endif;?>
    <label class="form-check-label" for="cms">CMS</label>
  </div>
  <div class="form-group form-check col-md-6">
  <?php if($pubedit['isay']):?>
    <input type="checkbox" checked value="isay" class="form-check-input" id="isay" name="isay">
  <?php else:?>
    <input type="checkbox"  value="isay" class="form-check-input" id="isay" name="isay">
  <?php endif;?>

    <label class="form-check-label" for="isay">iSay</label>
  </div>
  <div class="form-group form-check col-md-6">
  <?php if($pubedit['apn']):?>
    <input type="checkbox" checked value="apn" class="form-check-input" id="apn" name="apn">
  <?php else:?>
    <input type="checkbox"  value="apn" class="form-check-input" id="apn" name="apn">
  <?php endif;?>
    <label class="form-check-label" for="apn">APN</label>
  </div>
  <div class="form-group form-check col-md-6">
  <?php if($pubedit['subscription']):?>
    <input type="checkbox" checked value="subscription" class="form-check-input" id="subscription" name="subscription">
  <?php else:?>
    <input type="checkbox" value="subscription" class="form-check-input" id="subscription" name="subscription">
  <?php endif;?>

    <label class="form-check-label" for="subscription">Subscription</label>
  </div>

  <div class="form-group col-md-6">
    <label for="pub_logo">Publisher logo</label>
    <?php //if($pubedit['pub_logo']):?>
    <img src="<?php //$pubedit['pub_logo'];?>"/>
    <?php //endif;?>
    <input type="file" class="form-control-file" id="pub_logo" name="pub_logo" value="<?php //echo $pubedit['pub_logo'];?>">
  </div>





  <div class="form-group col-md-6">
        
        <label for="status">Status</label>
        <?php
            $statusOption = [
                'active' => 'Active',
                'inactive' => 'Inactive'
            ];

            $statusAttrib = [
                'id'    => 'status',
                'class' => 'form-control'
            ];
            echo form_dropdown('status', $statusOption, $pubedit['status'], $statusAttrib);

        ?>
        </div>




  <div class="form-group col-md-6">
    <label for="pub_id">Publication ID</label>
    
    <input type="text" class="form-control" id="pub_id" name="pub_id" value="<?php echo $pubedit['pub_id'];?>" >
    </div>
  
<!-- </div> -->

<div class="form-group col-md-6">
<button type="submit" class="btn btn-primary btn-lg btn-block">Save</button>
</div>
<div class="form-group col-md-6">
<a href="<?php echo base_url().'user/publist';?>"><button type="button" class="btn btn-primary btn-lg btn-block">Cancel</button></a>
</div>


</fieldset>
</form>



</div>

