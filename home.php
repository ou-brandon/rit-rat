<?php
 require("connect-db-ritrat.php");
 require("apis.php");

 session_start();

 if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
  header("location: login.php");
  exit;
}

//  echo $_SESSION['email'];

 $allPostsNew = getAllPostsNew();

 if($_SERVER["REQUEST_METHOD"] == "POST"){
  session_unset();
  session_destroy();
  header("location: login.php");
  exit;
 }

 ?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">  
  
  <!-- 2. include meta tag to ensure proper rendering and touch zooming -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- 
  Bootstrap is designed to be responsive to mobile.
  Mobile-first styles are part of the core framework.
   
  width=device-width sets the width of the page to follow the screen-width
  initial-scale=1 sets the initial zoom level when the page is first loaded   
  -->
  
  <meta name="author" content="your name">
  <meta name="description" content="include some description about your page">  
    
  <title>RitRat</title>
  
  <!-- 3. link bootstrap -->
  <!-- if you choose to use CDN for CSS bootstrap -->  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  
  <!-- you may also use W3's formats -->
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  
  <!-- 
  Use a link tag to link an external resource.
  A rel (relationship) specifies relationship between the current document and the linked resource. 
  -->
  
  <!-- If you choose to use a favicon, specify the destination of the resource in href -->
  <link rel="icon" type="image/png" href="http://www.cs.virginia.edu/~up3f/cs4750/images/db-icon.png" />
  
  <!-- if you choose to download bootstrap and host it locally -->
  <!-- <link rel="stylesheet" href="path-to-your-file/bootstrap.min.css" /> --> 
  
  <!-- include your CSS -->
  <!-- <link rel="stylesheet" href="custom.css" />  -->
       
</head>

<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand px-4" href="#">Rit 🐀 Rat</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="submission.php">Rit a Rat</a>
      </li>
      
    </ul>
    
  </div>
  <p class="nav-item float-right my-auto" style="color: white">
        Logged in as <?php echo $_SESSION['email']?>
  </p>
  <div class="px-4">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="submit" value="Log out" class="btn btn-secondary"></input>
    </form>
  </div>
</nav>
<div class="container bg-light">
  <?php foreach ($allPostsNew as $post): ?>
    <div class="card my-3" style="width:90%; ">
      <div class="card-body">
        <h4 class="card-title"><?php echo $post['body'] ?></h4>
        <p class="text-muted mb-2"><?php echo $post['email'] ?></p>
        <!-- <?php echo("<script>console.log('PHP: " . $post['email'] . "');</script>"); ?> -->
      </div>
    </div>
  <?php endforeach; ?>
  <!-- <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <input type="submit" value="Log out" class="btn btn-secondary" ></input>
  </form> -->
  
</div>     
</body>
</html>