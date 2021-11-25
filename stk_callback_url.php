<?php

    $callback_response = file_get_contents('php://input');
        
    $call_back = json_decode($callback_response, true)["Body"]["stkCallback"];

    $result_code = $call_back["ResultCode"];
    $result_desc = $call_back["ResultDesc"];
    $call_back_metadata = null;
    $amount = null;
    $mpesa_receipt_number = null;
    $transaction_time = null;
    $transaction_date = null;
    $phone_number = null;

    //assign variable from json response

    if($result_code == 0){
        $call_back_metadata = $call_back["CallbackMetadata"];
        $items = $call_back_metadata["Item"];

        
            foreach($items as $item){
                if($item["Name"] == 'Amount'){
                    $amount = $item["Value"];
                }

                if($item["Name"] == 'MpesaReceiptNumber'){
                    $mpesa_receipt_number = $item["Value"];
                }

                if($item["Name"] == 'TransactionDate'){
                    $transaction_date = $item["Value"];
                    //$transaction_time = date('H:i:s', strtotime($time_str));
                    //$transaction_date = date("Y-m-d", strtotime($time_str));
                }

                if($item["Name"] == 'PhoneNumber'){
                    $phone_number = $item["Value"];
                }
            }
       

        $user_id = 0;
        $status = 'Unclaimed';
        $reason = 'None';


            $DB_HOST = 'remotemysql.com';
            $DB_USER = 'uO02EnKVLE';
            $DB_PASS = '65rPh8AW4I';
            $DB_SCHEMA = 'uO02EnKVLE';

            // create connection
            $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_SCHEMA);

            // check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

        $time = date("Y-m-d h:i:sa");

        $callback_response = file_get_contents('php://input');

        ################################################WORKBENCH####################################################################
        $sql = "INSERT INTO stk_lipa_callbacks (user_id, status, reason, amount, mpesa_receipt_number, phone_number, transaction_date)
        VALUES ('$user_id', '$status', '$reason', '$amount', '$mpesa_receipt_number', '$phone_number', '$transaction_date')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }


        $conn->close();


        $log_file = "callback_response.json";

        //write to file
        $log = fopen($log_file, "a");

        fwrite($log, $callback_response);
        fclose($log);
    }
?>