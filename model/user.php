<?php
    class UserModel{
        private $userId;
        private $name;
        private $mobileNumber;
        private $emailId;
        private $password;
        private $createdDate;
        private $address;
        
        public function setUserId($userId){
            $this->userId=$userId;
        }
        
        public function getUserId(){
            return $this->userId;
        }
        
        public function setAddress($address){
            $this->address=$address;
        }
        
        public function getAddress(){
            return $this->address;
        }
        
        public function setName($name){
            $this->name=$userId;
        }
        
        public function getName(){
            return $this->name;
        }
          public function setEmail($email){
            $this->emailId=$email;
        }
        
        public function getEmail(){
            return $this->emailId;
        }
        public function setMobileNum($mobileNum){
            $this->mobileNumber=$mobileNum;
        }
        
        public function getMobileNum(){
            return $this->mobileNumber;
        }
        
        public function setPassword($password){
            $this->password=$password;
        }
        
        public function getPassword(){
            return $this->password;
        }
        
        public function setCreatedDate($date){
            $this->createdDate=$date;
        }
        
        public function getCreatedDate(){
            return $this->createdDate;
        }
        
       function __construct() {
            $this->userId = isset($_POST['userId']) ? $_POST['userId'] : null;
            $this->name = isset($_POST['name']) ? $_POST['name'] : null;
            $this->emailId = isset($_POST['emailId']) ? $_POST['emailIds'] : null;
            $this->mobileNumber = isset($_POST['mobileNumber']) ? $_POST['mobileNumber'] : null;
            $this->password=isset($_POST['password']) ? $_POST['password'] : null;
            $this->createdDate=isset($_POST['createdDate']) ? $_POST['createdDate'] : null;
        }
        function register($user) {
        if (empty($user["name"]) || empty($user["mobileNumber"]) || empty($user["emailId"])|| empty($user["password"]) || empty($user["address"])) {
            return 0;
        }

        else
        {
             //$this->userId = $user["name"];
            $this->name = $user["name"];
            $this->emailId = $user["emailId"];
            $this->mobileNumber = $user["mobileNumber"];
            $this->password=$user["password"];
            $this->address=$user["address"];
            //$this->createdDate=isset($_POST['createdDate']) ? $_POST['createdDate'] : null;
            return 1;
        }
    }
    function login($user) {
        if ( empty($user["emailId"])|| empty($user["password"])) {
            return 0;
        }

        else
        {
             //$this->userId = $user["name"];
           // $this->name = $user["name"];
            $this->emailId = $user["emailId"];
           // $this->mobileNumber = $user["mobileNumber"];
            $this->password=$user["password"];
            //$this->createdDate=isset($_POST['createdDate']) ? $_POST['createdDate'] : null;
            return 1;
        }
    }
    }

?>