<?php
    include_once 'connection.php';
    $response = array();
	$arr=array();
        if(isset($_POST['guest_id'])){
            $guest_id = $_POST['guest_id'];
//$guest_id = "1631344571-1925972604";
            $stmt = $conn->prepare("SELECT * FROM pastBooking WHERE guest_id = ?");
            $stmt->bind_param("s", $guest_id);
            if($stmt->execute()){
                $result = $stmt->get_result();
                while($booking = $result->fetch_assoc()){ 
                    $arr[] = $booking;
                }
            }

            $stmt->close();
            $response['error'] = false;
            $response['message'] = "User booking history successfully fetched!";
	    $response['data'] = $arr;
        }else{
           $response['error'] = true;
          $response['message'] = "Inavlid operation";
        }

    echo json_encode($response);
?>