<?php 
 
    require_once 'connection.php';
    
    $response = array();
    
    if(isset($_GET['apicall'])){
    
        switch($_GET['apicall']){
        
        case 'signup':
        if(isTheseParametersAvailable(array('guest_id','fname','lname','email','password','address','state','pin'))){
		$guest_id = $_POST['guest_id'];
        $fname = $_POST['fname'];
 		$lname = $_POST['lname'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$mobile = (int)$_POST['mobile'];
		$address = $_POST['address'];
		$state = $_POST['state'];
		$pin = (int)$_POST['pin']; 
        
        $stmt = $conn->prepare("SELECT guest_id FROM guest WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->num_rows > 0){
            $response['error'] = true;
            $response['message'] = 'User already registered';
            $stmt->close();
        }else{
            $stmt = $conn->prepare("INSERT INTO guest(guest_id,fname,lname,phone,email,address,state,pin,password) values(?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssisssis",$guest_id, $fname, $lname, $mobile, $email, $address, $state, $pin, $password);
            
            if($stmt->execute()){
                $stmt = $conn->prepare("SELECT guest_id, fname, lname, email FROM guest WHERE guest_id = ?"); 
                $stmt->bind_param("s",$guest_id);
                $stmt->execute();
                $stmt->bind_result($guest_id, $fname, $lname, $email);
                $stmt->fetch();
                
                $user = array(
                'guest_id'=>$guest_id, 
                'fname'=>$fname,
                'lname'=>$lname, 
                'email'=>$email
                );
                
                $stmt->close();
                
                $response['error'] = false; 
                $response['message'] = 'User registered successfully'; 
                $response['user'] = $user; 
            }
        }
    
        }else{
            $response['error'] = true; 
            $response['message'] = 'required parameters are not available'; 
        }
        
        break; 
        
        case 'login':
        
            if(isTheseParametersAvailable(array('email', 'password'))){
            
                $email = $_POST['email'];
                $password = $_POST['password']; 
                $stmt = $conn->prepare("SELECT guest_id,fname,lname,email,password FROM guest WHERE email=? LIMIT 1");
                $stmt->bind_param("s",$email);

                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if(mysqli_num_rows($result)>0){
                        $user = $result->fetch_assoc();
                        if (password_verify($password, $user['password'])) { // if password matches
                            $stmt->close();
                            $user = array(
                                'guest_id'=>$user['guest_id'], 
                                'fname'=>$user['fname'], 
                                'lname'=>$user['lname'],
                                'email'=>$user['email']
                                );
                            $response['error'] = false; 
                            $response['message'] = 'Login successful'; 
                            $response['user'] = $user; 
                        }else{
                            $response['error'] = true;
                            $response['message'] = 'Incorrect password';
                        }
                    }else{
                        $response['error'] = true; 
                        $response['message'] = 'Invalid email';
                    }
                }else{
                    $response['error'] = true; 
                    $response['message'] = 'Invalid operation';
                }
            }else{
                $response['error'] = true; 
                $response['message'] = 'required parameters are not available'; 
            }
            break; 
            
            default: 
                $response['error'] = true; 
                $response['message'] = 'Invalid Operation Called';
            }
        
    }else{
        $response['error'] = true; 
        $response['message'] = 'Invalid API Call';
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