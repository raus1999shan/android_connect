<?php
    require_once 'connection.php';

    $response = array();

    if(isset($_GET['action'])){

        switch($_GET['action']){
            case 'checkRoom':
                if(isTheseParametersAvailable(array('start_date','end_date'))){
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];
                    $quant = $_POST['guestNo'];
                    $choice = $_POST['roomType'];
                
                    $query="select h.room_id from hotel h where h.room_id not in(select room_id from booking where '$start_date' between start_date and end_date OR '$end_date' between start_date and end_date) LIMIT 1";
                    $result = mysqli_query($conn, $query);
        
                    if (mysqli_num_rows($result) > 0) {
                        while($row = $result->fetch_assoc()){
                            //echo "id: " . $row["room_id"]. "<br>";
                            $response['error'] = false;
                            $response['message'] = "Room is available";
                            $response['room_id'] = $row["room_id"];
                        }
                    }
                    else{
                        $response['error'] = true;
                        $response['message'] = "No room available";
                    }
                }else{
                    $response['error'] = true; 
                    $response['message'] = 'required parameters are not available'; 
                }
                break;
            case 'bookRoom':
                if(isTheseParametersAvailable(array('room_id','start_date','end_date','guest_id','guestNo'))){
                    $room_id = (int)$_POST['room_id'];
                    $guest_id = $_POST['guest_id'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];
                    $quant = (int)$_POST['guestNo'];
                
                    $query = "INSERT INTO booking(room_id,guest_id,start_date,end_date,guestno) values(?,?,?,?,?)";
                    $stmt = $conn->prepare($query);
           
                    $stmt->bind_param("isssi",$room_id,$guest_id,$start_date,$end_date,$quant);
                    $res = $stmt->execute();
                    if($res){
                        $stmt->close();
                        $query = "INSERT INTO pastBooking(room_id,guest_id,start_date,end_date,guestno) values(?,?,?,?,?)";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("isssi",$room_id,$guest_id,$start_date,$end_date,$quant);
                        $res = $stmt->execute();
                        if($res){
                            $stmt->close();
                            $response['error'] = false;
                            $response['message'] = "Room no: ". $room_id . " booked!";
                        }
                    }else{
                        $response['error'] = true;
                        $response['message'] = "Database error";
                    }
                }else{
                    $response['error'] = true; 
                    $response['message'] = 'required parameters are not available'; 
                }
                break;
            default:
            $response['error'] = true;
            $response['message'] = 'Invalid operation';
        }
        
    }else{
        $response['error'] = true;
        $response['message'] = 'Invalid API call';
    }
    echo json_encode($response);

    function isTheseParametersAvailable($params){
        foreach($params as $param){
            if(!isset($_POST[$param])){
                return false; 
            }
        }
        return true; 
    }
?>