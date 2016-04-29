<!DOCTYPE html>
<html lang="<?php echo substr($_COOKIE['lng'],0,2); ?>">
  <head>
    <meta charset="utf-8">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate, max-age=0" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />    

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>ToDo</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/jquery-ui.min.css" rel="stylesheet">
    <link href="css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="css/bootstrap-select.min.css" rel="stylesheet">
    <link href="css/todo.css" rel="stylesheet">
    
    <link rel="shortcut icon" sizes="16x16 24x24 32x32 48x48 64x64" href="/favicon.ico">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.min.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

<div class="container">

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="task.php"><i class="fa fa-plus"></i></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li<?php if(PAGE_NAME=='today') {echo ' class="active"';} ?>><a href="index.php"><i class="fa fa-inbox"></i> <?php echo _('Today'); ?></a></li>
            <li<?php if(PAGE_NAME=='tomorrow') {echo ' class="active"';} ?>><a href="tomorrow.php"><i class="fa fa-calendar-o"></i> <?php echo _('Tomorrow'); ?></a></li>
            <li<?php if(PAGE_NAME=='scheduled') {echo ' class="active"';} ?>><a href="scheduled.php"><i class="fa fa-calendar"></i> <?php echo _('Scheduled'); ?></a></li>
            <li<?php if(PAGE_NAME=='next') {echo ' class="active"';} ?>><a href="next.php"><i class="fa fa-server"></i> <?php echo _('Next'); ?></a></li>
            <li class="dropdown<?php if(PAGE_NAME=='tags') {echo ' active';} ?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-tags"></i> <?php echo _('Tags'); ?> <span class="caret"></span></a>
              <ul class="dropdown-menu">
                ##DROPDOWN_TAGS##
              </ul>
            </li>
          </ul>
          <form class="navbar-form navbar-left" role="search" action="search.php" method="GET">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="<?php echo _('Search').'...'; ?>" name="mot">
              <span class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
              </span>            
            </div>        
          </form>          
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown<?php if(PAGE_NAME=='settings') {echo ' active';} ?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cog"></i> <?php echo _('Settings'); ?> <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="tags.php"><i class="fa fa-tag"></i> <?php echo _('Edit tags'); ?></a></li>
                <li><a href="list_done.php"><i class="fa fa-check"></i> <?php echo _('Done'); ?></a></li>
                <li><a href="list_deleted.php"><i class="fa fa-trash"></i> <?php echo _('Deleted'); ?></a></li>
                <li><a href="language.php"><i class="fa fa-globe"></i> <?php echo _('Language'); ?></a></li>
                <li><a href="http://en.jeffprod.com/contact.php"><i class="fa fa-envelope"></i> <?php echo _('Contact us'); ?></a></li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>    
          
    <div class="row">
      <div class="col-md-8">
<?php
// no 'QUICK ADD TASK' on those pages
if(!(PAGE_NAME=='task' || PAGE_NAME=='settings')) {
?>
<form method="POST" action="task.php">
  <div class="input-group">
    <input type="text" name="tache_libelle" class="form-control" aria-label="Add task" placeholder="<?php echo _('Quick add task...'); ?>" autofocus>
    <span class="input-group-btn">
      <button class="btn btn-default" type="submit"><i class="fa fa-plus"></i></button>
    </span>    
  </div>
</form> 
<?php
}
?>          
        <h3>##TITLE##</h3>
        ##CONTENT##
      </div>  
    </div>


</div> <!-- container -->

<footer class="footer">
  <div class="container">
    <p class="text-muted text-center">Web ToDo app by <a href="http://www.jeffprod.com">JeffProd</a></p>
  </div>
</footer>

<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="js/locales-select/defaults-<?php echo $_COOKIE['lng']; ?>.min.js"></script>
<script src="js/locales/bootstrap-datepicker.<?php echo substr($_COOKIE['lng'],0,2); ?>.min.js"></script>
<script>
##SCRIPT##
</script>

  </body>
</html>
