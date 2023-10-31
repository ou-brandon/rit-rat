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


?>