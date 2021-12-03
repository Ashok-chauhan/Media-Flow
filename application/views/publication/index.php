<section>
<div class="row ">
<div class="col-md-7 col-lg-7 ">
<div class="table-responsive">
<table class="table table-striped">
  <thead class="alert alert-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name</th>
      <th scope="col">Actions</th>
      
    </tr>
  </thead>
  <tbody> 
  	<?php foreach( $publications as $publication):?>
  	 
		<tr>
			<td><?php echo $publication['id'];?></td>
			<td><?php echo $publication['name'];?></td>
			<td>
				<a href="<?php echo $this->config->item('base_url');?>publication/manage/<?php echo $publication['id'];?>">View</a> <!--| 
				<a href="{$base_url}publication/delete/{$publication.id}">Delete</a>-->
			</td>
		</tr>
	<?php endforeach;?>
</tbody>
</table>

</div>

</div>

<!-- publication create form -->
<div class="col-md-5 col-lg-5 ">
	
	<h3>Add Publication</h3>

    <form class="form-signin" action="<?php echo $this->config->item('base_url');?>save" method="post" enctype="multipart/form-data">
    	<div class="form-group">
    		<label for="name"> Name </label>
   		<input type="text" class="form-control" id="name" placeholder="Publication name" name="name" required />
   	</div>
   	<div class="form-group">

   <label for="group_name">Group Name </label>
   
	<select id="group_id" name ="group_id" class="form-control">
	<option value="0" >Select Group </option>
	
	<?php foreach ($group as $key => $value):?>
		<option value="<?php echo $value['id'];?>"><?php echo $value['group_name'];?></option>
	<?php endforeach;?>

   </select>
   
   </div>
	<div class="form-group">
	<label for="email">iSay Email</label>
	<input class="form-control" type="text" id="email" placeholder="email" name="email"/>
</div>

<div class="form-group">
	<label for="Photos per ad"> Photos Per Ad</label>
   <input class="form-control" type="text" id="photo_per_ad" placeholder="Photos per ad" name="photo_per_ad"/>
</div>
<div class="form-group">
	<label for="videos per ad"> Videos Per Ad </label>
   <input class="form-control" type="text" id="video_per_ad" name="video_per_ad" placeholder="Video per ad" />
</div>
<div class="form-group">
<label for="Full page ad code"> Full Page Ad Code </label>
   <textarea class="form-control" id="ful_page_ad_code" name="full_page_ad_code" rows="6"></textarea>
</div>
<div class="form-group">
	<label for="full page ad Width"> Full Page Ad Width </label>
   <input class="form-control" type="text" id="full_page_ad_width" name="full_page_ad_width" placeholder="Full page ad widht" />
</div>

<div class="form-group">
	<label for="full page ad Height"> Full Page Ad Height </label>
   <input class="form-control" type="text" id="full_page_ad_height" name="full_page_ad_height" placeholder="Full page ad height" />
</div>
<div class="form-group">
	<label for="Video ad key"> Video Ad Key </label>
   <input class="form-control" type="text" id="video_ad_key" name="video_ad_key" placeholder="video ad key" />
</div>
<div class="form-group">
	<button class="btn btn-lg btn-primary btn-block" type="submit"> Add Publication </button>
</div>

    </form>
</div>
<!-- publication create form eof -->


</div>

</section>
