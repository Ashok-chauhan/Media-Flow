<?php if ( $this->session->userdata('user')) : ?>

<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
<a href="<?php echo base_url();?>" class="navbar-brand"><img class="mb-4" src="/images/whizlogo.png" alt="" width="130" ></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
      <!-- <li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li> -->
            <li class="nav-item">
            <a class="nav-link active" href="/user/pubregistration">Register publishers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/user/publist">Manage publishers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/publication/adgroup">Publisher group</a>
        </li>
        
        <!-- <li class="nav-item">
            <a class="nav-link" href="#">Watch now</a>
        </li> -->

    <li class="nav-item">
            <form action="<?php echo base_url();?>ott/category" method="post" enctype="multipart/form-data" name="ottcategory">
              <?php 
              if(isset($pub_id)):?>
              <input type="hidden" name="pub_id" value="<?php echo $pub_id;?>">
              <A href="#" class="nav-link" onClick="javascript:document.forms['ottcategory'].submit();">Watch Now </A>
              <?php else:?>
                <A href="#" class="nav-link disabled" aria-disabled="true" >Watch Now </A>
              <?php endif;?>
            </form>
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
    <span class="navbar-text">
      Navbar text with an inline element
    </span>
  </div>
</nav>

<?php endif ?>