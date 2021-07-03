<div class="container">



<form id="registerForm" action="<?php echo base_url()?>user/pubregistration" method="post" enctype="multipart/form-data">

<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Publisher registration </legend>

<div class="form-row align-items-center">
<input type="hidden" name="type" value="publisher"/>
<div class="form-group col-md-6">
    <label for="first_name">First name</label>
    <input type="text" class="form-control" id="first_name" name="first_name" >
    
  </div>
<div class="form-group col-md-6">
    <label for="last_name">Last name</label>
    <input type="text" class="form-control" id="last_name" name="last_name" >
    
  </div>
  <div class="form-group col-md-6">
    <label for="email">Email address</label>
    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
    
  </div>
  <div class="form-group col-md-6">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password">
  </div>

  

  <div class="form-group form-check col-md-6">
    <input type="checkbox" class="form-check-input" id="analytics" name="analytics">
    <label class="form-check-label" for="analytics">Analytics</label>
  </div>
  <div class="form-group form-check col-md-6">
    <input type="checkbox" class="form-check-input" id="cms" name="cms">
    <label class="form-check-label" for="cms">CMS</label>
  </div>
  <div class="form-group form-check col-md-6">
    <input type="checkbox" class="form-check-input" id="isay" name="isay">
    <label class="form-check-label" for="isay">iSay</label>
  </div>
  <div class="form-group form-check col-md-6">
    <input type="checkbox" class="form-check-input" id="apn" name="apn">
    <label class="form-check-label" for="apn">APN</label>
  </div>
  <div class="form-group form-check col-md-6">
    <input type="checkbox" class="form-check-input" id="subscription" name="subscription">
    <label class="form-check-label" for="subscription">Subscription</label>
  </div>

  <div class="form-group col-md-6">
    <label for="pub_logo">Publisher logo</label>
    <input type="file" class="form-control-file" id="pub_logo" name="pub_logo">
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
            echo form_dropdown('status', $statusOption, 'active', $statusAttrib);

        ?>
        </div>




  <div class="form-group col-md-6">
    <label for="pub_id">Publication ID</label>
    <input type="text" class="form-control" id="pub_id" name="pub_id" >
    </div>
  <button type="submit" class="btn btn-primary">Register</button>
</div>

</fieldset>
</form>



</div>

