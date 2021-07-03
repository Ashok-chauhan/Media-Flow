<div class="container">

<section>
<div class="row">

<div class="col-md-5 col-lg-5 "> <!-- create group form -->

<form id="registerForm" action="<?php echo base_url()?>publication/adgroup" method="post" enctype="multipart/form-data">
<fieldset class="border border-secondary rounded p-5">
                            <legend class="w-auto"> Add group </legend>

<div class="form-group">
	<label for="group"> Group name </label>
   <input class="form-control" type="text" id="group" placeholder="name" name="group" required/>
</div>


<div class="form-group">
	<button class="btn btn-lg btn-primary btn-block" type="submit"> Add group </button>
</div>

</fieldset>
</form>
</div> <!-- eof create group form -->






<div class="col-md-7 col-lg-7 ">
<div class="table-responsive">
<table class="table table-striped">
  <thead class="alert alert-dark">
    <tr>
      <th scope="col">Group#</th>
      <th scope="col">Name</th>
      
      
    </tr>
  </thead>
  <tbody>
  	<?php foreach( $groups as $group):?> 
		<tr>
			<td><?php echo $group['id'];?></td>
			<td><?php echo $group['group_name'];?></td>
			
		</tr>
	<?php endforeach;?>
</tbody>
</table>
</div>
</div>

</div> <!-- eof row -->

</section>