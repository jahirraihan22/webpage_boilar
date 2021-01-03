<?php
$expire=24*60*60;
session_set_cookie_params($expire);
session_start();

class DBOP
{
    private $con;

    function __construct()
    {
        include_once("../database/db.php");
        $db = new Database();
        $this->con = $db->connect();
    }

    //    get current date
    private function getDate()
    {
        return date("Y-m-d h:m:s");
    }

    // delete a row
    private function dltRow($table, $col, $value)
    {
        $pre_stmt = $this->con->prepare("DELETE FROM $table WHERE $col = '$value' ");
        $result = $pre_stmt->execute() or die($this->con->error);
        if ($result) {
            return "DELETED_SUCCESSFULLY";
        } else {
            return "ERROR_IN_DELETION";
        }
    }

    // die dump
    public function dd($data)
    {
        echo "<pre>";
        print_r($data);
        die();
    }

    //    check if category already exist or not
    public function isElementExist($col, $value, $table)
    {
        $pre_stmt = $this->con->prepare("SELECT id FROM $table WHERE $col =  '$value'");
        $pre_stmt->execute() or die($this->con->error);
        $result = $pre_stmt->get_result();
        if (mysqli_num_rows($result) > 0) {
            return true;
        } else
            return false;
    }

    //  view all from a table
    public function getData($table,$col,$order)
    {
        $pre_stmt = $this->con->prepare("SELECT * from $table ORDER BY $col $order");
        $pre_stmt->execute() or die($this->con->error);
        $result = $pre_stmt->get_result();
        $rows = array();
        if (mysqli_num_rows($result)>= 1) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        } else {
            return "NO_DATA_FOUND";
        }
    }

    //      view all from a table with where condition
    public function getConditionalData($table, $col, $value)
    {
        $pre_stmt = $this->con->prepare("SELECT * from $table WHERE $col =" . "'$value' ORDER BY created_at DESC");
        $pre_stmt->execute() or die($this->con->error);
        $result = $pre_stmt->get_result();
        $rows = array();
        if (mysqli_num_rows($result) == 1) {
            $row = $result->fetch_assoc();
            return $row;
        } elseif (mysqli_num_rows($result) > 1) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        } else {
            return "NO_DATA_FOUND";
        }
    }
    
    // get column id

    public function getID($table, $col)
    {
        $pre_stmt = $this->con->prepare("SELECT $col from $table");
        $pre_stmt->execute() or die($this->con->error);
        $result = $pre_stmt->get_result();
        $rows = array();
        if (mysqli_num_rows($result)== 1) {
            $row = $result->fetch_assoc();
            return $row;
        } elseif (mysqli_num_rows($result)> 1) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        } else {
            return "NO_DATA_FOUND";
        }
    }
// get name from id
    public function getNameFromID($table, $col,$id)
    {
        $pre_stmt = $this->con->prepare("SELECT $col from $table where id = '$id'");
        $pre_stmt->execute() or die($this->con->error);
        $result = $pre_stmt->get_result();
        $rows = array();
        if (mysqli_num_rows($result)== 1) {
            $row = $result->fetch_assoc();
            return $row;
        } elseif (mysqli_num_rows($result)> 1) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        } else {
            return "NO_DATA_FOUND";
        }
    }

    // count row single condition
    public function countRowSingle($table, $col,$value)
    {
        $pre_stmt = $this->con->prepare("SELECT COUNT(id) as totalRow from $table where $col = '$value'");
        $pre_stmt->execute() or die($this->con->error);
        $result = $pre_stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            $row = 0;
            return $row;
        }
    }
    // count row
    public function countRow($table, $col, $value,$col2,$value2)
    {
        $pre_stmt = $this->con->prepare("SELECT COUNT(id) as totalRow from $table where $col = '$value' AND $col2 = $value2");
        $pre_stmt->execute() or die($this->con->error);
        $result = $pre_stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            $row = 0;
            return $row;
        }
    }

    // check if an array contain an array or not
    public function isMultArray($array)
    {
        $tempArray = $array;
        rsort($tempArray);
        if (isset($tempArray[0]) && is_array($tempArray[0])) {
            return true;
        } else
            return false;
    }

    //      live search
    public function liveSearch($table, $col, $values)
    {
        $pre_stmt = $this->con->prepare("SELECT * from $table WHERE $col LIKE '%" . $values . "%'");
        $pre_stmt->execute() or die($this->con->error);
        $result = $pre_stmt->get_result();
        $rows = array();
        if (isset($result)) {
            if (mysqli_num_rows($result)== 1) {
                $row = $result->fetch_assoc();
                return $row;
            } else if (mysqli_num_rows($result)> 1) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
                return $rows;
            } else {
                return "NO_DATA_FOUND";
            }
        } else {
            return "NO_DATA_FOUND";
        }
    }

    // update a col
    public function updateCol($id,$value,$user_id){
        $created_at = $this->getDate();
        $pre_stmt = $this->con->prepare("UPDATE products SET user_id= ?, qty = ? ,created_at=? WHERE id = ?");
        $pre_stmt->bind_param("iisi",$user_id,$value, $created_at, $id);

        $result = $pre_stmt->execute() or die($this->con->error);
    }

    // delete column
    public function deleteTable($id, $table)
    {
        return $this->dltRow($table, 'id', $id);
    }

    private function checkQty($id,$cart_qty){
        $product = $this->getConditionalData('products', 'id', $id);
        if($product['qty'] < $cart_qty){
            return false;
        }
        else return true;
    }
