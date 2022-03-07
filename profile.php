<?php
    include_once 'connection.php';
    $response = array();

    //if(isset($_GET['profile'])){
        if(isset($_POST['guest_id'])){
            $guest_id = $_POST['guest_id'];
            $stmt = $conn->prepare("SELECT fname, lname, email, phone, address, state, pin from guest where guest_id = ?");
            $stmt->bind_param("s", $guest_id);
            if($stmt->execute()){
                $stmt->bind_result($fname, $lname, $email, $mobile, $address, $state, $pin);
                $stmt->fetch();
                $user = array(
                    'fname'=>$fname,
                    'lname'=>$lname,
                    'email'=>$email,
                    'mobile'=>$mobile,
                    'address'=>$address,
                    'state'=>$state,
                    'pin'=>$pin
                );
                $stmt->close();
                $response['error'] = false;
                $response['message'] = "User data successfully fetched!";
                $response['user'] = $user;
            }else{
                $response['error'] = true;
                $response['message'] = "Inavlid operation";
            }

        }else{
            $response['error'] = true;
            $response['message'] = "Inavlid operation";
        }
    //}else{
      //  $response['error'] = true;
        //$response['message'] = "Inavlid api";
    //}
    echo json_encode($response);
?>