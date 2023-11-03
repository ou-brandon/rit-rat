<?php 

   function getAllPostsNew(){
      global $db;
      $query = "SELECT * FROM Creates NATURAL JOIN Post NATURAL JOIN SiteUser";
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


?>