<?php

include_once './Db/DbHandler.php';
require_once("./SimpleRest.php");
require_once './model/user.php';
require_once './function/validation.php';
/* $allData=$handler->getAllProductName();

  $result["code"]="00";
  $result["data"]=$allData;

  header('Content-Type: application/json');
  echo json_encode($result);
  ?>
 */

class UserHandler extends SimpleRest {

    private $user;

    function __construct() {
        $this->handler = new DbHandler();
        $this->requestContentType = 'application/json';
        $this->user = new UserModel();
    }

    function register() {
        //$_SERVER['REQUEST_METHOD'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = json_decode(file_get_contents('php://input'), true);
            //echo $this->encodeJson($user);
            if (!empty($user)) {
                $status = $this->user->register($user);
                if ($status == 0) {
                    $this->statusCode = 400;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = "Required Field Empty";
                    //echo $this->encodeJson($result);
                } else {
                    if ($this->handler->countEmailId($this->user->getEmail()) > 0) {
                        $this->statusCode = 200;
                        $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                        $result["data"] = null;
                        $result["code"] = "01";
                        $result["message"] = "EmailId exist";
                        //echo $this->encodeJson($result);
                    } else if ($this->handler->countPhoneNum($this->user->getMobileNum()) > 0) {
                        $this->statusCode = 200;
                        $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                        $result["data"] = null;
                        $result["code"] = "01";
                        $result["message"] = "Mobile Number Exist";
                        // echo $this->encodeJson($result);
                    } else {
                        $enc_password = password_hash($this->user->getPassword(), PASSWORD_DEFAULT);
                        $userId = $this->handler->insertUser($this->user->getName(), $this->user->getEmail(), $this->user->getMobileNum(), $enc_password,$this->user->getPassword());
                        if ($userId != -1) {
                            $this->handler->insertAddress($userId, $this->user->getAddress());
                            $this->statusCode = 200;
                            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                            $result["data"] = null;
                            $result["code"] = "00";
                            $result["message"] = "Success";
                            //echo $this->encodeJson($result);
                        } else {
                            $this->statusCode = 404;
                            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                            $result["data"] = null;
                            $result["code"] = "01";
                            $result["message"] = "Failure";
                        }
                    }
                }
            } else {
                $this->statusCode = 404;
                $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "Empty Request";
                //echo $this->encodeJson($result);
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = "Get Request Not Supported";
            //echo $this->encodeJson($result);
        }
        echo $this->encodeJson($result);
    }

    function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = json_decode(file_get_contents('php://input'), true);
            //echo $this->encodeJson($this->user);
            if (!empty($user)) {
                $status = $this->user->login($user);
                if ($status == 0) {

                    $this->statusCode = 200;
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = "Required Field Empty";
                    //echo $this->encodeJson($result);
                } else {
                    if ($this->handler->countEmailId($this->user->getEmail()) > 0) {
                        $user = $this->handler->getUserDetails($this->user->getEmail());
                        if (password_verify($this->user->getPassword(), $user["password"])) {
                            //if ($result["active"] == 1) {
                            $this->statusCode = 200;
                            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                            $result["data"] = $user;
                            $result["code"] = "00";
                            $result["message"] = "Success";
                        } else {
                            $this->statusCode = 404;
                            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                            $result["data"] = null;
                            $result["code"] = "01";
                            $result["message"] = "Invalid Email Id/Password";
                        }
                    } else {
                        $this->statusCode = 404;
                        $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                        $result["data"] = null;
                        $result["code"] = "01";
                        $result["message"] = "Email Id Doesn't Exist";
                        // echo $this->encodeJson($result);
                    }
                }
            } else {
                $this->statusCode = 404;
                $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "Empty Request";
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = "Get Request Not Supported";
            //echo $this->encodeJson($result);
        }
        echo $this->encodeJson($result);
    }

    function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST["emailId"]) && !empty(trim($_POST["emailId"]))) {
                $emailId = $_POST["emailId"];
                if ($this->handler->countEmailId($emailId)==1) {
                    $pwd = getPasswordNum();
                    $enc_password = password_hash($pwd, PASSWORD_DEFAULT);
                    if ($this->handler->updatePassword($emailId, $enc_password,$pwd,1)) {
                        if (sendMail($emailId, $pwd)) {
                            $this->statusCode = 200;
                            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                            $result["data"] = $pwd;
                            $result["code"] = "00";
                            $result["message"] = "Message Sent to Your registered email id.Check in spam folder";
                        } else {
                            $this->statusCode = 200;
                            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                            $result["data"] = $pwd;
                            $result["code"] = "00";
                            $result["message"] = "Message Sending Failed";
                        }
                    } else {
                        $this->statusCode = 200;
                        $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                        $result["data"] = null;
                        $result["code"] = "01";
                        $result["message"] = "Error Occured";
                    }
                } else {
                    $this->statusCode = 200;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = "Email Id Does not exist";
                }
            } else {
                $this->statusCode = 200;
                $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "Required Field Empty";
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = "Get Request Not Supported";
        }
        echo $this->encodeJson($result);
    }

    function updateFcmId() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST["userId"]) && isset($_POST["fcmId"])) {
                $userId = trim($_POST["userId"]);
                $fcmId = trim($_POST["fcmId"]);
                if (!empty($userId) && $this->handler->countUserId($userId) == 1) {
                    $this->handler->updateFcmId($fcmId, $userId);
                    $this->statusCode = 200;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = null;
                    $result["code"] = "00";
                    $result["message"] = "Success";
                } else {
                    $this->statusCode = 404;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = "User does not exist";
                }
            } else {
                $this->statusCode = 404;
                $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "Requird Field Empty";
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = "Get Request Not Supported";
        }
        echo $this->encodeJson($result);
    }
    function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST["emailId"]) && isset($_POST["password"])) {
                $password = trim($_POST["password"]);
                $emailId = $_POST["emailId"];
                if ($this->handler->countEmailId($emailId)==1) {
                    $enc_password = password_hash($password, PASSWORD_DEFAULT);
                    if ($this->handler->updatePassword($emailId, $enc_password,$password,0)) {
                            $this->statusCode = 200;
                            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                            $result["data"] = NULL;
                            $result["code"] = "00";
                            $result["message"] = "Update Success";
                    } else {
                        $this->statusCode = 200;
                        $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                        $result["data"] = null;
                        $result["code"] = "01";
                        $result["message"] = "Error Occured";
                    }
            } else {
                $this->statusCode = 404;
                $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "Requird Field Empty";
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = "Get Request Not Supported";
        }
        echo $this->encodeJson($result);
    }
    }
    function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = json_decode(file_get_contents('php://input'), true);
            //echo $this->encodeJson($user);
            if (!empty($user)) {
                if ($this->handler->countUserId($user["userId"]) > 0) {
                    $this->handler->updateUserAddress($user["userId"], $user["address"]);
                    $this->handler->updateUserInfo($user["name"], $user["mobileNumber"], $user["userId"]);
                    $userData = $this->handler->getUserDetailsById($user["userId"]);
                    $this->statusCode = 200;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = $userData;
                    $result["code"] = "00";
                    $result["message"] = "Success";
                } else {
                    $this->statusCode = 404;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = "Not Exist";
                }
            } else {
                $this->statusCode = 404;
                $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "Empty Request";
                //echo $this->encodeJson($result);
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = "Get Request Not Supported";
            //echo $this->encodeJson($result);
        }
        echo $this->encodeJson($result);
    }
    
}

?>