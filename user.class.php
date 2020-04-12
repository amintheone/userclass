<?php
/*
* PHP user class
*/

class User {
    private $user_ID;
    private $username;
    private $user_firstname;
    private $user_lastname;
    private $user_password;
    private $user_register_date;
    private $user_email;
    private $user_phone;
    private $user_picture;
    private $user_access_token;
    private $last_loggedin_date;
    private $user_state;
    private $user_updated_date;
    
    private $DB;
    private $table_name = 'users';
    
    private $userdata = array('username'=>'', 'user_firstname'=>'', 'user_lastname'=>'', 'user_password'=>'', 'user_email'=>'');
    
    public function __construct($userdata=false)
    {
        if ( is_array($userdata) )
            $this->init($userdata);
        elseif ( filter_var($userdata, FILTER_VALIDATE_EMAIL) ) 
            $this->initByEmail($userdata);
    }
    
    public function initDB()
    {
        try {
            $this->DB = new PDO(
            'mysql:host=localhost;dbname=webapp',
            'root',
            '');   
        } catch (Exception $e) {
            echo $this->DB->errorInfo();
        }
    }
    
    /*
    * Intenciate a USER
    * @param associative array $userdata
    */
    public function init(array $userdata)
    {
        foreach ($userdata as $key => $value) 
            if ( !empty($value) ) {
                $this->$key = $value;
            }         
    }
    
    /*
    * Intenciate a USER by Email Address
    * @param string $user_email
    */
    private function initByEmail($user_email)
    {
        if ( $user_email ) {
            if ( $this->userExists( $user_email ) ) {
                
                $this->initDB();
                $q = "SELECT * FROM ".$this->table_name." WHERE ( user_email = '". $user_email ."')";
                try {
                    if ( $query_data = $this->DB->query($q) )
                    {
                        $userdata = $query_data->fetch(PDO::FETCH_ASSOC); 
                        $this->init($userdata);
                        return true;                        
                    }
                    else
                        throw new Exception("Erreur lors du traitement de requete");
                }
                catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            else echo 'User not exists !';
        }        
    }
    
    /*
    * Get User Proprety by name
    */
    public function get_($prop)
    {
        return $this->$prop;
    }
    
    /*
    * Set User Proprety by name
    */
    public function set_($prop)
    {
        $this->$prop = $prop;
    }
    
    /*
    * Create a new user into database
    * @param associative array $userdata
    */
    public function create(array $userdata, $rand_ID)
    {
        if ( $userdata ) {
            if ( !$this->userExists($userdata['user_email']) ) {
                $this->user_ID = random_int(100, 999999);
                
                // DB insertion
                $this->initDB();
                $q = "INSERT INTO ".$this->table_name. " VALUES (".$rand_ID.", ". $this->user_ID .", '". $userdata['username'] ."', '". $userdata['user_firstname']."', '". $userdata['user_lastname']."', '".$userdata['user_email']. "')";
                
                try {
                    if ( $this->DB->exec($q) )
                    {
                        $this->init($userdata);
                        return true;                        
                    }
                    else
                        throw new Exception("Erreur lors de la création du compte");
                }
                catch (Exception $e) {
                    echo $e->getMessage();
                }
                // End DB Insertion
            }
        }
    }
    
    /*
    * Deactivate a user
    * @param user ID @user_ID
    * @return true or error message
    */
    public function deactivate($user_ID=false)
    {
        if ( !$user_ID )
            $user_ID = $this->user_ID;
        
        $this->initDB();
        // DB Query
        $q = "UPDATE ".$this->table_name. " SET user_state = 0
        WHERE user_ID = ".$user_ID;
        
        try {
            if ( $this->DB->exec($q) ) {
                return true;
            }
            else
                throw new Exception("Erreur lors de l'opération");
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    /*
    * Activate a user
    * @param
    * @return true or error message
    */
    public function activate($user_ID=false)
    {
        if ( !$user_ID )
            $user_ID = $this->user_ID;
        
        $this->initDB();
        // DB Query
        $q = "UPDATE ".$this->table_name. " SET user_state = 1
        WHERE user_ID = ".$user_ID;
        
        try {
            if ( $this->DB->exec($q) ) {
                return true;
            }
            else
                throw new Exception("Erreur lors de l'opération");
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function delete($user_ID)
    {
      /* DB Query */  
    }
    
    
    /*
    * Check if a user exists in Database
    */
    public static function userExists($user_email, $username=false)
    {
        // DB Query
        $user = new self();
        $user->initDB();
        if ($user_email && $username)
            $q = "SELECT user_ID FROM ".$user->table_name." WHERE ( username = ". $username .")";
        else
            $q = "SELECT user_ID FROM ".$user->table_name." WHERE ( user_email = '". $user_email ."')";
        
        try {
            if ( $res = $user->DB->query($q) )
                return $res->fetch(PDO::FETCH_OBJ)->user_ID;
            return false;
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    /*
    * Update user property
    */
    public function update(array $userdata)
    {
        if ( $userdata ) {
            if ( $this->userExists($userdata['user_email']) ) {
                
                $this->initByEmail($userdata['user_email']);
                
                $query_fields = array();
                // Composing Query Fields Array
                foreach ($this->userdata as $key => $value) {
                    $query_fields[$key] = isset($userdata[$key]) ? $userdata[$key] : '';
                }
                $query_string = '';
                foreach ( $query_fields as $field_name => $field_value )
                    $query_string .= !empty($field_value) ? $field_name." = '".$field_value."'," : '';
                $query_string = substr($query_string, 0, -1 ); // Remove last comma
                                
                // DB update
                $this->initDB();
                $q = "UPDATE ".$this->table_name. " SET ".$query_string
                ." WHERE user_email = '".$userdata['user_email']."'";
                
                try {
                    if ( $this->DB->exec($q) )
                    {
                        $this->init($userdata);
                        return true;                        
                    }
                    else
                        throw new Exception("Erreur lors de la mise à jour des données");
                }
                catch (Exception $e) {
                    echo $e->getMessage();
                }
                // End DB Insertion
            }
        }
    }
    
    /*
    * Check if a user property can be updated
    */
    private function updateAllowed($user_prop)
    {
        $update_allowed = 0;
        switch ($user_prop) {
            case 'user_id' : $update_allowed = 0;
            case 'user_register_date' : $update_allowed = 0;
        }
        return $update_allowed;
    }
}

?>