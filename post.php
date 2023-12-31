<?php 
  require("apis.php");
  require("connect-db-ritrat.php");
  require("utils.php");
  session_start();
      /* Display errors (remove once we submit the project)*/
  ini_set('display_errors', 1);
  $postId = $_GET['postId'];
  $post = getPostById($postId);

  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
    header("location: login.php");
    exit;
  }

  $isOwner = strcmp($_SESSION['email'],$post['email']) === 0;
  $comments = getCommentsByPostId($postId);

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['delete'])){
      $deleted = deletePost($postId);
      if($deleted >= 1){
        header("location: home.php");
        exit;
      } else {
        echo '<script>alert("Post deletion failed, please try again.")</script>';
      }
    } else if(isset($_POST['edit'])){
      header("location: edit-post.php?postId=".$postId);
    }
    else if(isset($_POST['comment'])) {
      addComment($postId, $_POST['commentBody'], $_SESSION['email']);
      $comments = getCommentsByPostId($postId);
      header("location: post.php?postId=".$postId);
    }
    else if(!empty($_POST['delete_comment'])){
      deleteComment($_POST['comment_id_to_delete']);
      $comments = getCommentsByPostId($postId);
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
        <a class="nav-link" href="home.php" style="color: white">Home</span></a>
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

<div class="container">
    <div class="card my-4">
        <div class="card-body">
            <h4 class="card-title"><?php echo $post['body'] ?></h4>
            <div style="display: inline">
                <p class="text-muted" style="display: inline"><?php echo $post['email'] ?> · </p>
                <p class="text-muted" style="display: inline"><?php echo time_elapsed_string($post['dateEdited']) ?></p>
            </div>
            <div style="display: inline; float: right;">
              <form method="post">
                <?php if($isOwner) echo "<input action='edit-post.php?postId={$post["postId"]}' method='get' type='submit' name='edit' value='✏️' class='btn btn-secondary'/>"; ?>
                <?php if($isOwner) echo '<input type="submit" name="delete" value="🗑️" class="btn btn-danger"/>' ?>
              </form>
            </div>

        </div>

    </div>

    <form action="post.php?postId=<?php echo $postId?>" method="post">
        <div class="form-group">
        <label for="commentInput">Comment:</label>
        <input type="text" class="form-control mb-2" id="commentInput" name="commentBody" placeholder="Whatchu ritting and ratting about?">
        <input type="submit" type="submit" name="comment" value="Comment" class="btn btn-primary"/>
        </div>
    </form>
    <hr/>
    <div class="container bg-light">
      <?php foreach ($comments as $comment): ?>
        <div class="my-3">
            <h5 style="margin-bottom: 0"><?php echo $comment['body'] ?></h5>
            <div style="display: inline">
            <p class="text-muted small" style="display: inline"><?php echo $comment['email'] ?> · </p>
            <p class="text-muted small" style="display: inline"><?php echo time_elapsed_string($comment['dateEdited']) ?></p>
            </div>
            <?php if(strcmp($comment['email'], $_SESSION['email']) === 0 || !$comment['email']) { ?>
              <form action="post.php?postId=<?php echo $postId?>" method="post">
                <input name="delete_comment" type="submit" id="delete_comment" class="btn btn-danger" style="display: inline" value="🗑️" 
                  onclick="return confirm('Are you sure you want to delete this comment?')"/>
                  
                <input type="hidden" name="comment_id_to_delete" value="<?php echo $comment['commentId']; ?>" />
              </form>
              <?php } ?>
            
        </div>
      <?php endforeach; ?>
      
      <?php if(count($comments) == 0): ?>
        <p>No comments. Be the first to add one!</p>
      <?php endif; ?>

    </div>
</div>
</div>     
</body>
</html>

