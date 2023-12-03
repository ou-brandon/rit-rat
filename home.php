<?php
 require("connect-db-ritrat.php");
 require("apis.php");
 require("utils.php");

 session_start();
  /* Display errors (remove once we submit the project)*/
  ini_set('display_errors', 1);

 if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
  header("location: login.php");
  exit;
}

function getAllPosts(){
  if($_SESSION['sortMetric'] == 'new'){
    return getAllPostsNew();
  } else if($_SESSION['sortMetric'] == 'hot'){
    return getAllPostsHot();
  } else {
    return getAllPostsTop();
  }
}
//$postSortMetric = 'new'; //Can be 'new', 'top', or 'hot'
if(!isset($_SESSION['sortMetric'])){
  $_SESSION['sortMetric'] = 'new';
}
$allPosts = getAllPosts(); //Default sort is new

$allVotes = getAllVotes();
$userUpvotes = array();
$userDownvotes = array();

//get lists of currently upvoted and downvoted posts by current user
foreach($allVotes as $vote){

  if($vote["type"] == "upvote"){
    if($vote["userId"] == $_SESSION['email']){
      array_push($userUpvotes, $vote["postId"]);
    }
  } else {
    if($vote["userId"] == $_SESSION['email']){
      array_push($userDownvotes, $vote["postId"]);
    }
  }
}


 if($_SERVER["REQUEST_METHOD"] == "POST"){
  if(!empty($_POST['new'])){
    global $postSortMetric;
    global $allPosts;
    $postSortMetric = 'new';
    $_SESSION['sortMetric'] = 'new';
    $allPosts = getAllPosts();
  }
  else if(!empty($_POST['top'])){
    global $postSortMetric;
    global $allPosts;
    $postSortMetric = 'top';
    $_SESSION['sortMetric'] = 'top';
    $allPosts = getAllPosts();
  }
  else if(!empty($_POST['hot'])){
    global $postSortMetric;
    global $allPosts;
    $postSortMetric = 'hot';
    $_SESSION['sortMetric'] = 'hot';
    $allPosts = getAllPosts();
  }
  else if(!empty($_POST['upvote'])){
    //check if currently downvoted
    $postId = $_POST['postId'];
    if(in_array($postId, $userDownvotes)){
      removeDownvote($postId, $_SESSION['email']);
      $userDownvotes = array_diff($userDownvotes, array($postId));
    }
    addUpvote($_POST['postId'], $_SESSION['email']);
    array_push($userUpvotes, $postId);
    $allPosts = getAllPosts();
  }
  else if(!empty($_POST['downvote'])){
    //check if currently upvoted
    $postId = $_POST['postId'];
    if(in_array($postId, $userUpvotes)){
      removeUpvote($postId, $_SESSION['email']);
      $userUpvotes = array_diff($userUpvotes, array($postId));
    }
    addDownvote($_POST['postId'], $_SESSION['email']);
    array_push($userDownvotes, $postId);
    $allPosts = getAllPosts();
  }
  else {
    session_unset();
    session_destroy();
    header("location: login.php");
    exit;
  }
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
  <link rel="stylesheet" href="styles.css" />
       
</head>

<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand px-4" href="home.php">Rit 🐀 Rat</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="home.php" style="color: white">Home</a>
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
    <div class="mt-4">
      <form method="post"> 
          <input type="submit" name="new"
                  class="btn btn-primary shadow <?php echo ($_SESSION['sortMetric'] == 'new') ? 'active' : '' ?>" value="🐀New" /> 
                  
          <input type="submit" name="hot"
                  class="btn btn-primary shadow <?php echo $_SESSION['sortMetric'] == 'hot' ? 'active' : '' ?>" value="🔥Hot" /> 

          <input type="submit" name="top"
                  class="btn btn-primary shadow <?php echo $_SESSION['sortMetric'] == 'top' ? 'active' : '' ?>" value="🏆Top" /> 
      </form> 
    </div>
    
  <?php foreach ($allPosts as $post): ?>
    <div class="card my-4">
      <div class="card-body">
        <div style="display: inline">
          <h4 class="card-title"><a href="post.php?postId=<?php echo $post['postId']?>" style="text-decoration:none"><?php echo $post['body'] ?></a></h4>
          
          <p class="text-muted" style="display: inline"><?php echo $post['email'] ?> · </p>
          <p class="text-muted" style="display: inline"><?php echo time_elapsed_string($post['dateEdited']) ?></p>
        </div>
        <div style="display: inline; float: right;">
          <form method="post">
            <input type="hidden" name="postId" value=<?php echo $post["postId"]?>>
            <input <?php 
              if (in_array($post["postId"], $userUpvotes)){ ?> disabled <?php   } ?>
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" type="submit" name="upvote" class="btn btn-secondary" style="display: inline" value="👍">
            <h4 style="display: inline;"><?php 
              echo ($post["numUpvotes"] - $post["numDownvotes"]);
            ?></h4>
            <input <?php 
              if (in_array($post["postId"], $userDownvotes)){ ?> disabled <?php   } ?>
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" type="submit" style="display: inline" name="downvote" class="btn btn-secondary" value="👎"/>
          </form>
        </div>
      </div>
    </div>
  </a>
  <?php endforeach; ?>
  
</div>     
</body>
</html>