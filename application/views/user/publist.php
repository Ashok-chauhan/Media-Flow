<div class="container">
<section>
<div class="row ">
<div class="table-responsive">
<table class="table table-striped">
  <thead class="alert alert-dark">
    <tr>
      <th scope="col">Publisher name</th>
      <th scope="col">email</th>
      <th scope="col">Status</th>
      <th scope="col">Publication id</th>
      <th scope="col">Action</th>
      
    </tr>
  </thead>
  <tbody>
  	<?php foreach( $publisher as $publication):?> 
		<tr>
       
			<td><?php echo $publication['first_name'];?></td>
			<td><?php echo $publication['email'];?></td>
            <td><?php echo $publication['status'];?></td>
            <td><?php echo $publication['id'];?></td>
			<td>
      
                <a href="<?php echo base_url
                ().'user/pubedit/'.$publication['id'];?>" class="btn btn-primary btn-sm">View/Edit</a>&nbsp;&nbsp;&nbsp;
                <a href="<?php echo base_url().'user/pubdel/'.$publication['id'];?>" class="btn btn-danger btn-sm" onClick="return checkconfirm(this);">Delete</a>

			</td>
		</tr>
	<?php endforeach;?>
</tbody>
</table>

</div>

</div>
</section>
</div>

<script type="text/javascript">

  function checkconfirm()
	{
	if(confirm("Are You Sure,You wnat to Delete.?"))
	return true;
	else
	return false;
	}

</script>
