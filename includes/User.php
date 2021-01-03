<?php
class User
{
    private $con;
    function __construct()
    {
        include_once("../database/db.php");
        $db = new Database();
        $this->con = $db->connect();
    }

    //    to check if user available or not
    private function isElementExist($col,$val)
    {
        $pre_stmt = $this->con->prepare("SELECT * FROM users WHERE $col = ?");
        $pre_stmt->bind_param("s", $val);
        $pre_stmt->execute() or die($this->con->error);
        $result = $pre_stmt->get_result();
        if ($result->num_rows > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    //    hash password
    private function hashPass($pass)
    {
        return md5($pass);
    }

    function createUser($name, $phone, $pass)
    {
        if (!$this->isElementExist('phone',$phone)) {
            $lastLogin = date("Y-m-d h:m:s");
            $pass_hash = $this->hashPass($pass);
            $pre_stmt = $this->con->prepare("INSERT INTO users (name, phone, passwords, last_login) VALUES (?,?,?,?)");
            $pre_stmt->bind_param("ssss", $name, $phone, $pass_hash, $lastLogin);
            $result = $pre_stmt->execute() or die($this->con->error);
            if ($result) {
                if (!isset($_SESSION["userId"])) {
                    $_SESSION["userId"] = $this->con->insert_id;
                    $_SESSION["userName"] = $name;
                    $_SESSION["userPhone"] = $phone;
                    $_SESSION["userLastLogIn"] = $lastLogin;
                }
                return $this->con->insert_id;
            } else {
                return "ERROR";
            }
        } else {
            return "PHONE_NUMBER_ALREADY_EXISTS";
        }
    }
    function updateUser($name, $phone, $email, $id)
    {
        if (!$this->isElementExist('phone',$phone) && !$this->isElementExist('id',$id)) {
            return "PHONE_NUMBER_ALREADY_EXISTS";
        } else {
            $pre_stmt = $this->con->prepare("UPDATE users SET name = ? , phone = ?, email = ? where id = ? ");
            $pre_stmt->bind_param("sssi", $name, $phone, $email,$id);
            $result = $pre_stmt->execute() or die($this->con->error);
            return $result;
        }
    }

    function userLogin($phone, $pass)
    {
        // first check if phone number is registered or not
        $pre_stmt = $this->con->prepare("SELECT * FROM users WHERE phone = ?");
        $pre_stmt->bind_param("s", $phone);
        $pre_stmt->execute() or die($this->con->error);
        $result = $pre_stmt->get_result();
        if ($result->num_rows < 1) {
            return "USER_IS_NOT_REGISTERED";
        } else {
            //this will execute if phone number is registered
            $user_obj = $result->fetch_object();
            // to verify password
            if ($this->hashPass($pass) == $user_obj->passwords) {
                $_SESSION["userId"] = $user_obj->id;
                $_SESSION["userName"] = $user_obj->name;
                $_SESSION["userPhone"] = $user_obj->phone;
                $_SESSION["userLastLogIn"] = $user_obj->last_login;

                //for updating last login
                $last_login =  date("Y-m-d h:m:s");
                $pre_stmt = $this->con->prepare("UPDATE users SET last_login = ? WHERE phone = ? ");
                $pre_stmt->bind_param("ss", $last_login, $phone);
                $result = $pre_stmt->execute() or die($this->con->error);
                if ($result) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return "PASSWORD_DOES_NOT_MATCH";
            }
        }
    }

    function userLogout()
    {
        unset($_SESSION["userId"]);
        unset($_SESSION["userName"]);
        unset($_SESSION["userLastLogIn"]);
    }
}

$user = new User();
//echo $user->createUser("Jahir","01789699076","123456");
//echo $user->userLogin("01789699076","123456");
//echo $_SESSION["userName"];
