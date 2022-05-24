<?php

    function OpenConnection()
    {
        
        $servername = "localhost";
        $database = "colemun_andina_licores_actualizada";
        $username = "colemun_andina";
        $password = '$And1na021';
    
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $database);
        
        // Check connection
    
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            error_log("Failed Connection \nError" . mysqli_connect_error());
        }
        else{
            // mysqli_query("SET NAMES 'utf8'");
            error_log("Connected successfully");
        }

        return $conn;
    }

    function CloseConnection($conn)
    {
        mysqli_close($conn);
        error_log("disconnected successfully");
    }
   
    
?>