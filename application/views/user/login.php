
<section >


<div class="row bg-dark justify-content-center align-items-center" style="height: 100vh;">
  <div class="col-md-5 col-lg-5 ">

<?php if($error_messages):?>
  <div class="alert alert-danger" role="alert">
  <?php echo $error_messages ;?>
</div>
<?php endif;?>

<form class="form-signin" method="POST" action="/user/authenticate/">
  <div class="text-center mb-4">
    <img class="mb-4" src="/images/whizlogo.png" alt="" width="130" >
    
    
  </div>

  <div class="form-label-group">
    <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
    <label for="inputEmail"></label>
  </div>

  <div class="form-label-group">
    <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
    <label for="inputPassword"></label>
  </div>

  
  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
  <p class="mt-5 mb-3 text-muted text-center">&copy; <?php echo date('Y');?> <a href="https://whizti.com">Whiz Technologies Inc</a>, All Rights Reserved &reg;</p>
</form>
</div>


</div>
</section>
