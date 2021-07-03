<!-- <div class="col-md-2 col-lg-2 sidebar fixed-top">

<ul class="nav flex-column">
  <li class="nav-item">
    <a class="nav-link active" href="#">Active</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Link</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Link</a>
  </li>
  <li class="nav-item">
    <a class="nav-link disabled" href="#">Disabled</a>
  </li>
</ul>

</div> -->




<?php if($this->session->userdata('user')){ ?>
<!-- <div class="container"> -->
 <nav class="navbar navbar-expand-md  navbar-dark bg-dark">
 

  <a href="<?php echo base_url();?>" class="navbar-brand"><img class="mb-4" src="/images/whizlogo.png" alt="" width="130" ></a>



      <ul  class="navbar-nav" >

  <li class="nav-item">
    <a class="nav-link active" href="/user/pubregistration">Register publishers</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="/user/publist">Manage publishers</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Create group</a>
  </li>
   <li class="nav-item">
    <a class="nav-link" href="#">List group</a>
  </li>
   <li class="nav-item">
    <a class="nav-link" href="#">Watch now</a>
  </li>
   <li class="nav-item">
    <a class="nav-link" href="#">Sponsored Ad</a>
  </li>
   <li class="nav-item">
    <a class="nav-link" href="/user/logout">Log out</a>
  </li>
  <li class="nav-item">
    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
  </li>

</ul>
</nav>
</div>

<?php }?>




<!-- 
<nav class="navbar fixed-top navbar-dark bg-dark">
  <a class="navbar-brand" href="#">Fixed top</a>
</nav> -->