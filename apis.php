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
      $query = "SELECT *, (numUpvotes - numDownvotes) /(TIMESTAMPDIFF(DAY, now(), dateEdited)) AS votesMetric FROM Creates NATURAL JOIN Post NATURAL JOIN SiteUser ORDER BY votesMetric DESC";
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
?>