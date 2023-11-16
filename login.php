<?php
 require("connect-db-ritrat.php");
 require("apis.php");

 /* Display errors (remove once we submit the project)*/
 ini_set('display_errors', 1);

  session_start();

  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: home.php");
    exit;
  }

  $email = $password = "";

  if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(trim($_POST["email"]) == "" || trim($_POST["password"]) == ""){
      echo "Please enter both an email and password";
    }
    else {
      $results = validateUser($_POST["email"], $_POST["password"]);
      if (count($results) >= 1){
        $email = $results[0]["email"];
        $password = $results[0]["passwordHash"];

        if(strcmp(trim(hash("sha256", $_POST["password"])),trim($password)) == 0){
          //logged in!
          $_SESSION['loggedin'] = TRUE;
          $_SESSION['email'] = $email;
          header("location: home.php");
          exit;
        }
        else {
          echo "Incorrect password";
        }
      } else {
        createNewUser($_POST["email"], $_POST["password"]);
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['email'] = $email;
        header("location: home.php");
        exit;
      }
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

<body>
<div class="container text-center">
  <h1>Rit 🐀 Rat</h1> 
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="mb-3 mx-3">
      <label class="form-label">Enter your email and password to login or signup:</label>
      <div class="input-group">
          <input type="text" placeholder="Email" class="form-control" name="email">
          <input type="password" placeholder="Password" class="form-control" name="password">
      </div>  
      <input type="submit" value="Login/Signup" class="btn btn-primary">
    </div>
  </form>
</div>    
</body>
</html>