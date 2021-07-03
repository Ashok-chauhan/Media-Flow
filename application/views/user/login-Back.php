
<div class="row">
	
    <form  method="POST" action="<?php echo base_url("user/authenticate");?>">
      <div class="row">
        <div class="input-field col s12">
          <input name="email" id="email" type="email" class="validate" required>
          <label for="email">Email</label>
          <span class="helper-text " data-error="wrong" data-success="right">Helper text</span>
        </div>
      </div>
     <div class="row">
        <div class="input-field col s12 ">
          <input  name="password" id="password" type="password" class="validate" required>
          <label for="password">Password</label>
        </div>
      </div>

      <button class="btn waves-effect waves-light col s12  orange accent-2" type="submit" >
   			 <i class="material-icons center">send</i>
  		</button>

    </form>
  </div>
  

   
