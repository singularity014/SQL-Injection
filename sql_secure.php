<?php
/**
 * User: anique
 * Date: 4/22/16
 * Time: 4:41 PM
 */
$sql_server_address = "localhost";
$sql_port = 3306;
$sql_username = "root";
$sql_password = "root";
$sql_database = "cse545";

if(!isset($second_order_inputs)){
    $second_order_inputs = [];
}



function secure_query($query, $database=""){
    global $sql_server_address;
    global $sql_port;
    global $sql_username;
    global $sql_password;
    global $sql_database;
    global $second_order_inputs;

    if($database == ""){
        $database = $sql_database;
    }

    $mysqli = new mysqli($sql_server_address, $sql_username, $sql_password, $database, $sql_port);

    if($mysqli->connect_errno){
        file_put_contents("error.log", "SQL connection failed: ".$mysqli->connect_error."\n", FILE_APPEND);
    }

    $user_strings = [];
    //Get get, post, and cookie parameters
    foreach ($_REQUEST as $key=>$value){
        array_push($user_strings, $key);
        array_push($user_strings, $value);
    }


    foreach ($_SERVER as $key=>$value){
        array_push($user_strings, $key);
        array_push($user_strings, $value);
    }

    //Get request payload
    array_push($user_strings,
        file_get_contents("php://input"));

    //Add uploaded files
    foreach($_FILES as $file){
        array_push($user_strings, $file['name']);
        array_push($user_strings, file_get_contents($file['tmp_name']));
    }


    //Search for second order inputs
    foreach($second_order_inputs as $second_order_input){
        array_push($user_strings, $second_order_input);
    }


    foreach($user_strings as $user_string){
        //Check if the escaped version matches what the user provided
        //if it does we're good in any case
        if($mysqli->escape_string($user_string) != $user_string){
            //Check if this string is used in the query
            if (stripos($query, $user_string)!=false){
                file_put_contents("error.log", "Possible SQL injection: ".$user_string."\n", FILE_APPEND);
                //Escape the sql string
                $query = str_replace($user_string,$mysqli->escape_string($user_string),$query);
            }
        }

    }


    //var_dump($query);

    $results = $mysqli->query($query);



    $rows = $results->fetch_all();

    foreach ($rows as $row){
        foreach($row as $field){
            array_push($second_order_inputs, $field);
        }
    }

    //var_dump($rows);
    $results->data_seek(0);
    return $results;
}