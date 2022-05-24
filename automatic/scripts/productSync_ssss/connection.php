<?php

    function OpenConnection()
    {
        
        $servername = "localhost";
        $database = "colemun_andina_licores";
        $username = "colemun_andina";
        $password = '$And1na021';
    
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $database);
        
        // Check connection
    
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            error_log("Failed Connection \nError" . mysqli_connect_error());
            $msj = "Failed Connection \nError" . mysqli_connect_error();
            //custom_logs_connection($msj);
        }
        else{
            // mysqli_query("SET NAMES 'utf8'");
            error_log("Connected successfully");
            //custom_logs_connection("Connected successfully");
        }

        return $conn;
    }

    function CloseConnection($conn)
    {
        mysqli_close($conn);
        error_log("disconnected successfully");
        custom_logs_connection("disconnected successfully");
    }
    
    function custom_logs_connection($message) { 
        if(is_array($message)) { 
            $message = json_encode($message); 
        } 
        $file = fopen("./connection_log.log","a"); 
        echo fwrite($file, "\n" . date('Y-m-d h:i:s') . " :: " . $message); 
        fclose($file); 
    }
   
    
?>