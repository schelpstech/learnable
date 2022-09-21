<?php

class User
{
    // Refer to database connection
    private $db;

    // Instantiate object with database connection
    public function __construct($db_conn)
    {
        $this->db = $db_conn;
    }
    // Register new users
    public function register($last_name, $first_name, $user_email, $user_password, $user_ip, $log_status, $token)
    {
        try {
            // Hash password
            $user_hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

            // Define query to insert values into the users table
            $sql = "INSERT INTO crunch_user(user_firstname, user_surname, useremail, userpwd) VALUES(:first_name, :last_name, :user_email, :user_password)";

            // Prepare the statement
            $query = $this->db->prepare($sql);

            // Bind parameters
            $query->bindParam(":last_name", $last_name);
            $query->bindParam(":first_name", $first_name);
            $query->bindParam(":user_email", $user_email);
            $query->bindParam(":user_password", $user_hashed_password);

            // Execute the query
            $query->execute();
            $sql = " INSERT INTO `user_log` (user_ip, login_status, access_token, userid) VALUES(:user_ip, :user_status,  :user_token, :user_email )";

                    // Prepare the statement
                    $query = $this->db->prepare($sql);

                    // Bind parameters

                    $query->bindParam(":user_ip", $user_ip);
                    $query->bindParam(":user_status", $log_status);
                    $query->bindParam(":user_token", $token);
                    $query->bindParam(":user_email", $user_email);
                    // Execute the query
                    $query->execute();
                    $_SESSION['token'] = $token;
                    // Define session on successful login
                    $_SESSION['uniqueid'] = $user_email;
                    return true;
        } catch (PDOException $e) {
            array_push($errors, $e->getMessage());
        }
    }
    // Log in registered users with either their username or email and their password
    public function login($user_email, $user_password, $user_ip, $log_status, $token)
    {
        try {
            // Define query to insert values into the users table
            $sql = "SELECT * FROM crunch_user WHERE  useremail=:user_email LIMIT 1";

            // Prepare the statement
            $query = $this->db->prepare($sql);

            // Bind parameters
            $query->bindParam(":user_email", $user_email);

            // Execute the query
            $query->execute();

            // Return row as an array indexed by both column name
            $returned_row = $query->fetch(PDO::FETCH_ASSOC);

            // Check if row is actually returned
            if ($query->rowCount() > 0) {
                // Verify hashed password against entered password
                if (password_verify($user_password, $returned_row['userpwd'])) {

                    $sqll = "SELECT * FROM user_log WHERE  userid =:user_id LIMIT 1";

                    // Prepare the statement
                    $query = $this->db->prepare($sqll);

                    // Bind parameters
                    $query->bindParam(":user_id", $user_email);

                    // Execute the query
                    $query->execute();

                    // Return row as an array indexed by both column name
                    $returned_row = $query->fetch(PDO::FETCH_ASSOC);

                    // Check if row is actually returned
                    if ($query->rowCount() > 0) {
                        $sqlll = "UPDATE user_log SET login_status = 0 WHERE  userid =:user_id";
                        // Prepare the statement
                        $query = $this->db->prepare($sqlll);

                        // Bind parameters
                        $query->bindParam(":user_id", $user_email);

                        // Execute the query
                        $query->execute();
                    }

                    $sql = " INSERT INTO `user_log` (user_ip, login_status, access_token, userid) VALUES(:user_ip, :user_status,  :user_token, :user_email )";

                    // Prepare the statement
                    $query = $this->db->prepare($sql);

                    // Bind parameters

                    $query->bindParam(":user_ip", $user_ip);
                    $query->bindParam(":user_status", $log_status);
                    $query->bindParam(":user_token", $token);
                    $query->bindParam(":user_email", $user_email);
                    // Execute the query
                    $query->execute();
                    $_SESSION['token'] = $token;
                    // Define session on successful login
                    $_SESSION['uniqueid'] = $user_email;
                    return true;
                } else {
                    // Define failure
                    return false;
                }
            }
        } catch (PDOException $e) {
            array_push($errors, $e->getMessage());
        }
    }

    // Check if the admin user is already logged in
    public function admin_is_logged_in()
    {
        // Check if user session has been set
        if (isset($_SESSION['admin_uniqueid']) && isset($_SESSION['admin_token'])) {
            
                $id = $_SESSION['admin_uniqueid'];
                $token = $_SESSION['admin_token'];
                $sql = "SELECT login_status from admin_log where userid =:user_email AND access_token =:token LIMIT 1";

                // Prepare the statement
                $query = $this->db->prepare($sql);
      
                // Bind parameters
                $query->bindParam(":user_email", $id);
                $query->bindParam(":token", $token);
      
                // Execute the query
                $query->execute();
      
                // Return row as an array indexed by both column name
                $returned_row = $query->fetch(PDO::FETCH_ASSOC);
                $current_status = $returned_row['login_status'];
                return $current_status; 
        }
    }

  // Log in registered users with either their username or email and their password
  public function admin_login($user_email, $user_password, $user_ip, $log_status, $token)
  {
      try {
          // Define query to insert values into the users table
          $sql = "SELECT * FROM crunch_manager WHERE  mgr_email=:user_email LIMIT 1";

          // Prepare the statement
          $query = $this->db->prepare($sql);

          // Bind parameters
          $query->bindParam(":user_email", $user_email);

          // Execute the query
          $query->execute();

          // Return row as an array indexed by both column name
          $returned_row = $query->fetch(PDO::FETCH_ASSOC);

          // Check if row is actually returned
          if ($query->rowCount() > 0) {
              // Verify hashed password against entered password
              if (password_verify($user_password, $returned_row['mgr_pwd'])) {

                  $sqll = "SELECT * FROM admin_log WHERE  userid =:user_id LIMIT 1";

                  // Prepare the statement
                  $query = $this->db->prepare($sqll);

                  // Bind parameters
                  $query->bindParam(":user_id", $user_email);

                  // Execute the query
                  $query->execute();

                  // Return row as an array indexed by both column name
                  $returned_row = $query->fetch(PDO::FETCH_ASSOC);

                  // Check if row is actually returned
                  if ($query->rowCount() > 0) {
                      $sqlll = "UPDATE admin_log SET login_status = 0 WHERE  userid =:user_id";
                      // Prepare the statement
                      $query = $this->db->prepare($sqlll);

                      // Bind parameters
                      $query->bindParam(":user_id", $user_email);

                      // Execute the query
                      $query->execute();
                  }

                  $sql = " INSERT INTO `admin_log` (user_ip, login_status, access_token, userid) VALUES(:user_ip, :user_status,  :user_token, :user_email )";

                  // Prepare the statement
                  $query = $this->db->prepare($sql);

                  // Bind parameters

                  $query->bindParam(":user_ip", $user_ip);
                  $query->bindParam(":user_status", $log_status);
                  $query->bindParam(":user_token", $token);
                  $query->bindParam(":user_email", $user_email);
                  // Execute the query
                  $query->execute();
                  $_SESSION['admin_token'] = $token;
                  // Define session on successful login
                  $_SESSION['admin_uniqueid'] = $user_email;
                  return true;
              } else {
                  // Define failure
                  return false;
              }
          }
      } catch (PDOException $e) {
          array_push($errors, $e->getMessage());
      }
  }

  public function is_logged_in()
  {
      // Check if user session has been set
      if (isset($_SESSION['uniqueid']) && isset($_SESSION['token'])) {
          
              $id = $_SESSION['uniqueid'];
              $token = $_SESSION['token'];
              $sql = "SELECT login_status from user_log where userid =:user_email AND access_token =:token LIMIT 1";

              // Prepare the statement
              $query = $this->db->prepare($sql);
    
              // Bind parameters
              $query->bindParam(":user_email", $id);
              $query->bindParam(":token", $token);
    
              // Execute the query
              $query->execute();
    
              // Return row as an array indexed by both column name
              $returned_row = $query->fetch(PDO::FETCH_ASSOC);
              $current_status = $returned_row['login_status'];
              return $current_status; 
      }
  }

  

    
}
