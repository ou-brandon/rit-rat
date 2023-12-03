<?php 
   ini_set('display_errors', 1);
   function getAllPostsNew(){
      global $db;
      $query = "SELECT * FROM Creates NATURAL JOIN Post NATURAL JOIN SiteUser ORDER BY dateEdited DESC";
      $statement = $db->prepare($query);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();
      return $results;
   }

   function getAllPostsHot(){
      global $db;
      $query = "SELECT *, (numUpvotes - numDownvotes) /(1 + TIMESTAMPDIFF(HOUR, dateEdited, now())) AS votesMetric FROM Creates NATURAL JOIN Post NATURAL JOIN SiteUser ORDER BY votesMetric DESC";
      $statement = $db->prepare($query);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();
      return $results;
   }

   function getAllPostsTop(){
      global $db;
      $query = "SELECT *, (numUpvotes - numDownvotes) AS votesMetric FROM Creates NATURAL JOIN Post NATURAL JOIN SiteUser ORDER BY votesMetric DESC";
      $statement = $db->prepare($query);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();
      return $results;
   }

   function validateUser($email){
      global $db;
      $query = "SELECT * FROM SiteUser WHERE (email = :email)";
      $statement = $db->prepare($query);
      $statement->bindValue(':email', $email);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();
      return $results;
   }

   function createNewUser($email, $password) {
      global $db;
      $query = "INSERT INTO SiteUser (email, passwordHash) VALUES (:email, SHA2(:password, 256))";
      $statement = $db->prepare($query);
      $statement->bindValue(':email', $email);
      $statement->bindValue(':password', $password);
      $statement->execute();
      $statement->closeCursor();
   }

   function createNewPost($postBody, $email) {
      global $db; 
      $query = "INSERT INTO Post(dateEdited, body, numUpvotes, numDownvotes) 
                  VALUES (now(),:body, 0, 0)";
      $statement = $db->prepare($query);
      $statement->bindValue(':body', $postBody);
      $statement->execute();
      $postId = $db->lastInsertId();
      $userId = getUserId($email);
      $query = "INSERT INTO Creates(postId, userId) VALUES (:postId, :userId)";
      $statement = $db->prepare($query);
      $statement->bindValue(':postId', $postId);
      $statement->bindValue(':userId', $userId);
      $statement->execute();
      $statement->closeCursor();
   }

   function getUserId($email){
      global $db;
      $query = "SELECT userId FROM SiteUser WHERE email=:email LIMIT 1";
      $statement = $db->prepare($query);
      $statement->bindValue(':email', $email);
      $statement->execute();
      $result = $statement->fetch();
      $statement->closeCursor();
      return $result['userId'];
   }

   function getPostById($postId) {
      global $db;
      $query = "SELECT * FROM Post NATURAL JOIN Creates NATURAL JOIN SiteUser WHERE postId=:postId LIMIT 1";
      $statement = $db->prepare($query);
      $statement->bindValue(':postId', $postId);
      $statement->execute();
      $result = $statement->fetch();
      $statement->closeCursor();
      return $result;
   }

   function getCommentsByPostId($postId){
      global $db;
      $query = "SELECT * FROM (SELECT * FROM PostComment WHERE postId=:postId) pc NATURAL JOIN SiteUser NATURAL JOIN TextComment ORDER BY dateEdited DESC;";
      $statement = $db->prepare($query);
      $statement->bindValue(":postId", $postId);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();
      return $results;
   }

   function deletePost($postId){
      global $db;
      $query = "DELETE FROM Post WHERE postID=:postId;";
      $statement = $db->prepare($query);
      $statement->bindValue(":postId", $postId);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();
      return $results;
   }

   function getAllVotes(){
      global $db;
      $query = "SELECT * FROM Vote WHERE 1";
      $statement = $db->prepare($query);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();
      return $results;
   }

   function addUpvote($postId, $userId){
      global $db;
      $query = "INSERT INTO Vote (type, postId, userId) VALUES ('upvote', :postId, :userId)";
      $statement = $db->prepare($query);
      $statement->bindValue(":postId", $postId);
      $statement->bindValue(":userId", $userId);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();

      $query = "UPDATE Post SET numUpvotes = numUpvotes + 1 WHERE postId=:postId";
      $statement = $db->prepare($query);
      $statement->bindValue(":postId", $postId);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();

   }

   function addDownvote($postId, $userId){
      global $db;
      $query = "INSERT INTO Vote (type, postId, userId) VALUES ('downvote', :postId, :userId)";
      $statement = $db->prepare($query);
      $statement->bindValue(":postId", $postId);
      $statement->bindValue(":userId", $userId);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();

      $query = "UPDATE Post SET numDownvotes = numDownvotes + 1 WHERE postId=:postId";
      $statement = $db->prepare($query);
      $statement->bindValue(":postId", $postId);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();
      
   }

   function removeDownvote($postId, $userId){
      global $db;
      $query = "DELETE FROM Vote WHERE type='downvote' AND userId=:userId AND postId=:postId";
      $statement = $db->prepare($query);
      $statement->bindValue(":postId", $postId);
      $statement->bindValue(":userId", $userId);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();

      $query = "UPDATE Post SET numDownvotes = numDownvotes - 1 WHERE postId=:postId";
      $statement = $db->prepare($query);
      $statement->bindValue(":postId", $postId);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();
   }

   function removeUpvote($postId, $userId){
      global $db;
      $query = "DELETE FROM Vote WHERE type='upvote' AND userId=:userId AND postId=:postId";
      $statement = $db->prepare($query);
      $statement->bindValue(":postId", $postId);
      $statement->bindValue(":userId", $userId);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();

      $query = "UPDATE Post SET numUpvotes = numUpvotes - 1 WHERE postId=:postId";
      $statement = $db->prepare($query);
      $statement->bindValue(":postId", $postId);
      $statement->execute();
      $results = $statement->fetchAll();
      $statement->closeCursor();
   }

   function addComment($postId, $postBody, $email){
      global $db;
      $query = "INSERT INTO TextComment(dateEdited, body, numUpvotes, numDownvotes) 
                  VALUES (now(),:body, 0, 0)";
      $statement = $db->prepare($query);
      $statement->bindValue(':body', $postBody);
      $statement->execute();
      $commentId = $db->lastInsertId();

      $query = "INSERT INTO PostComment (postId, commentId, userId) VALUES (:postId, :commentId, :userId)";
      $statement = $db->prepare($query);
      $statement->bindValue(':postId', $postId);
      $statement->bindValue(':commentId', $commentId);
      $statement->bindValue(':userId', getUserId($email));
      $statement->execute();
      $statement->closeCursor();
   }

   function deleteComment($commentId) {
      global $db;
      $query = "DELETE FROM TextComment WHERE commentId=:commentId";
      $statement = $db->prepare($query);
      $statement->bindValue(':commentId', $commentId);
      $statement->execute();

      $query = "DELETE FROM PostComment WHERE commentId=:commentId";
      $statement = $db->prepare($query);
      $statement->bindValue(':commentId', $commentId);
      $statement->execute();
      $statement->closeCursor();
   }
?>