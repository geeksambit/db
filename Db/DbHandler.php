<?php

class DbHandler {

    private $connection;

    function __construct() {
        require_once 'DbConnect.php';
        // opening db connection
        $db = new DbConnect ();
        $this->connection = $db->connect();
    }

    public function isShopOPen($storeKey) {
        $stmt = $this->connection->prepare("SELECT isShopOpen FROM `vendor_details` WHERE vendorStoreKey=?");
        $stmt->bind_param("s", $storeKey);
        $stmt->execute();
        $stmt->bind_result($active);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $active;
    }

    public function getAllProductName($storeKey,$start,$end) {
        $stmt = $this->connection->prepare("SELECT vendor_by_product.total_quantity,vendor_by_product.sell_quantity,vendor_by_product.user_id, vendor_by_product.vendor_id,vendor_by_product.sell_price,vendor_by_product.cost_price,product_details.quanity,product_details.type,product_details.specification,product.product_name,product.image_path,category.category_name from vendor_by_product INNER join vendor_details on vendor_details.vendorDetailsId=vendor_by_product.user_id INNER JOIN product_details on vendor_by_product.product_details_id=product_details.productDetailsId INNER JOIN product on product.productId=product_details.productId INNER join category on category.id=product.categoryId where vendor_details.vendorStoreKey=? and vendor_by_product.isActive=1 ORDER BY (select COUNT(add_cart.Cartdetails_Id) from add_cart INNER join cart_order on cart_order.cartId=add_cart.cartId where cart_order.vendorDetailsId=vendor_by_product.vendor_id) ASC limit $start,$end");
        $stmt->bind_param("s", $storeKey);
        $stmt->execute();
        $stmt->bind_result($totalQuantity, $soldQuantity, $userId, $vendorId, $sp, $cp, $quantity, $type, $specification, $productName, $image_path, $catName);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["productDetailsId" => $vendorId, "costPrice" => $cp, "sellingPrice" => $sp, "quantity" => $quantity, "type" => $type, "productName" => $productName, "imagepath" => $image_path, "categoryName" => $catName, "specification" => $specification, "totalQuantity" => $totalQuantity, "soldQuantity" => $soldQuantity];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function searchProduct($storekey, $pname) {
        $stmt = $this->connection->prepare("SELECT vendor_by_product.total_quantity,vendor_by_product.sell_quantity,vendor_by_product.user_id, vendor_by_product.vendor_id,vendor_by_product.sell_price,vendor_by_product.cost_price,product_details.quanity,product_details.type,product_details.specification,product.product_name,product.image_path,category.category_name from vendor_by_product INNER join vendor_details on vendor_details.vendorDetailsId=vendor_by_product.user_id INNER JOIN product_details on vendor_by_product.product_details_id=product_details.productDetailsId INNER JOIN product on product.productId=product_details.productId INNER join category on category.id=product.categoryId where vendor_details.vendorStoreKey=? and product.product_name LIKE '$pname%' and vendor_by_product.isActive=1 ORDER BY product.product_name ASC limit 50");
        $stmt->bind_param("s", $storekey);
        $stmt->execute();
        $stmt->bind_result($totalQuantity, $soldQuantity, $user_id, $productDetailsId, $sp, $cp, $quantity, $type, $specification, $productName, $image_path, $catName);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["productDetailsId" => $productDetailsId, "costPrice" => $cp, "sellingPrice" => $sp, "quantity" => $quantity, "type" => $type, "productName" => $productName, "imagepath" => $image_path, "categoryName" => $catName, "specification" => $specification, "totalQuantity" => $totalQuantity, "soldQuantity" => $soldQuantity];
            $stmt->close();
            return $storeDetails;
        }
    }
    public function searchProductById($storekey, $pname,$catId) {
        $stmt = $this->connection->prepare("SELECT vendor_by_product.total_quantity,vendor_by_product.sell_quantity,vendor_by_product.user_id, vendor_by_product.vendor_id,vendor_by_product.sell_price,vendor_by_product.cost_price,product_details.quanity,product_details.type,product_details.specification,product.product_name,product.image_path,category.category_name from vendor_by_product INNER join vendor_details on vendor_details.vendorDetailsId=vendor_by_product.user_id INNER JOIN product_details on vendor_by_product.product_details_id=product_details.productDetailsId INNER JOIN product on product.productId=product_details.productId INNER join category on category.id=product.categoryId where vendor_details.vendorStoreKey=? and product.product_name LIKE '$pname%' and category.id=? and vendor_by_product.isActive=1 ORDER BY product.product_name ASC limit 50");
        $stmt->bind_param("si", $storekey,$catId);
        $stmt->execute();
        $stmt->bind_result($totalQuantity, $soldQuantity, $user_id, $productDetailsId, $sp, $cp, $quantity, $type, $specification, $productName, $image_path, $catName);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["productDetailsId" => $productDetailsId, "costPrice" => $cp, "sellingPrice" => $sp, "quantity" => $quantity, "type" => $type, "productName" => $productName, "imagepath" => $image_path, "categoryName" => $catName, "specification" => $specification, "totalQuantity" => $totalQuantity, "soldQuantity" => $soldQuantity];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function getProductBySubId($subId, $storeKey,$start,$end) {
        $stmt = $this->connection->prepare("SELECT vendor_by_product.total_quantity,vendor_by_product.sell_quantity,vendor_by_product.user_id,vendor_by_product.vendor_id,vendor_by_product.sell_price,vendor_by_product.cost_price,product_details.quanity,product_details.type,product_details.specification,product.product_name,product.image_path,category.category_name FROM vendor_by_product INNER JOIN vendor_details ON vendor_details.vendorDetailsId = vendor_by_product.user_id INNER JOIN product_details ON vendor_by_product.product_details_id = product_details.productDetailsId INNER JOIN product ON product.productId = product_details.productId INNER JOIN category ON category.id = product.categoryId WHERE category.id = ? AND vendor_details.vendorStoreKey = ? AND vendor_by_product.isActive = 1 ORDER BY product.product_name ASC limit $start,$end");
        $stmt->bind_param("is", $subId, $storeKey);
        $stmt->execute();
        $stmt->bind_result($totalQuantity, $soldQuantity, $userId, $vendorId, $sp, $cp, $quantity, $type, $specification, $productName, $image_path, $catName);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["productDetailsId" => $vendorId, "costPrice" => $cp, "sellingPrice" => $sp, "quantity" => $quantity, "type" => $type, "productName" => $productName, "imagepath" => $image_path, "categoryName" => $catName, "specification" => $specification, "totalQuantity" => $totalQuantity, "soldQuantity" => $soldQuantity];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function getCategorydata($subId) {
        $stmt = $this->connection->prepare("select product_details.productDetailsId,product_details.quanity,product_details.type,product.product_name,product.productId,category.category_name,category.id from product_details INNER join product on product.productId=product_details.productId INNER join category on category.id=product.categoryId where category.id=? ORDER BY product.product_name ASC");
        $stmt->bind_param("i", $subId);
        $stmt->execute();
        $stmt->bind_result($productDetailId, $quantity, $type, $productName, $productId, $categoryName, $categoryId);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["productDetailsId" => $productDetailId, "quantity" => $quantity, "type" => $type, "productName" => $productName, "productId" => $productId, "categoryId" => $categoryId];
            $stmt->close();
            return $storeDetails;
        }
    }

//    public function order
    public function getAllCategory() {
        $stmt = $this->connection->prepare("select id,category_name,active,category_image from category where active=1 order by orderInSequence asc");
        $stmt->execute();
        $stmt->bind_result($id, $catName, $active,$image);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["id" => $id, "categoryName" => $catName, "active" => $active,"category_image"=>$image];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function addToCart($cartId, $userId, $productId, $quantity, $amount) {
        $stmt = $this->connection->prepare("insert into add_cart values(null,?,?,?,?,?,1,null,1)");
        $stmt->bind_param("iiiid", $cartId, $userId, $productId, $quantity, $amount);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            return TRUE;
        }
    }

    public function removeFromCart($productId, $userId, $cartDetailsId, $cartId) {
        $stmt = $this->connection->prepare("delete from add_cart where productId=? and userId=? and Cartdetails_Id=? and cartId=?");
        $stmt->bind_param("iiii", $productId, $userId, $cartDetailsId, $cartId);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            return TRUE;
        }
    }

    public function getAddCartAmount($productId, $userId, $cartDetailsId, $cartId) {
        $stmt = $this->connection->prepare("select Amount from add_cart where productId=? and userId=? and Cartdetails_Id=? and cartId=?");
        $stmt->bind_param("iiii", $productId, $userId, $cartDetailsId, $cartId);
        $stmt->execute();
        $stmt->bind_result($quantity);
        $stmt->store_result();
        $stmt->fetch();
        return $quantity;
    }

    public function updateCart($cartId, $productId, $quantity, $amount, $userId) {
        $stmt = $this->connection->prepare("UPDATE add_cart SET quantity=?,Amount=? where productId=? and userId=? and cartId=?");
        $stmt->bind_param('iidii', $quantity, $amount, $productId, $userId, $cartId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function getQuantityFromCart($productId, $userId, $cartId) {
        $stmt = $this->connection->prepare("select Quantity from add_cart where productId=? and userId=? and cartId=?");
        $stmt->bind_param("iii", $productId, $userId, $cartId);
        $stmt->execute();
        $stmt->bind_result($quantity);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            return $quantity;
        }
    }

    public function confirmCart($productId, $cartId, $userId, $orderStatus) {
        $stmt = $this->connection->prepare("UPDATE add_cart SET order_status=? where productId=? and Cartdetails_Id and userId=? and isActive=1");
        $stmt->bind_param('iiii', $orderStatus, $productId, $cartId, $userId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
    
    public function countCartDetailsId($cartDetailsId){
        $stmt = $this->connection->prepare("select count(*) FROM `add_cart` WHERE Cartdetails_Id=?");
        $stmt->bind_param("i", $cartDetailsId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }
    
    public function updateCartDetails($cartDetailsId) {
        $stmt = $this->connection->prepare("update add_cart set isActive=0 where Cartdetails_Id=?");
        $stmt->bind_param("i", $cartDetailsId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function cartDetailsById($userId) {
        $stmt = $this->connection->prepare("select add_cart.Cartdetails_Id,add_cart.isActive,add_cart.Amount,add_cart.quantity,vendor_by_product.cost_price,vendor_by_product.sell_price,product_details.quanity,vendor_by_product.total_quantity,vendor_by_product.sell_quantity,vendor_by_product.vendor_id,product_details.type,product_details.specification,product.product_name,product.image_path,category.category_name from add_cart INNER join vendor_by_product on vendor_by_product.vendor_id=add_cart.productId INNER join cart_order on cart_order.cartId=add_cart.cartId INNER join user_details on user_details.id=cart_order.userId INNER join product_details on product_details.productDetailsId=vendor_by_product.product_details_id INNER join product on product.productId=product_details.productId INNER join category on category.id=product.categoryId where cart_order.userId=? and category.active=1 and cart_order.isActive=1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($cartId,$isActive, $amount, $cartQuantity, $cp, $sp, $quantity, $totalQuantity, $soldQuantity, $productDetailsId, $type, $specification, $productName, $image_path, $catName);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["cartDetailsId" => $cartId,"isActive"=>$isActive, "amount" => $amount, "productDetailsId" => $productDetailsId, "costPrice" => $cp, "sellingPrice" => $sp, "quantity" => $quantity, "type" => $type, "productName" => $productName, "imagepath" => $image_path, "categoryName" => $catName, "specification" => $specification, "totalQuantity" => $totalQuantity, "soldQuantity" => $soldQuantity, "cartQuantity" => $cartQuantity];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function getAllAmount($cartId){
        $stmt = $this->connection->prepare("select SUM(Amount) from add_cart where cartId=$cartId");
        $stmt->execute();
        $stmt->bind_result($cartId);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $cartId;
    }

     public function getAllInActiveAmount($cartId){
        $stmt = $this->connection->prepare("select SUM(Amount) from add_cart where cartId=$cartId and isActive=0");
        $stmt->execute();
        $stmt->bind_result($cartId);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $cartId;
    }
    
    public function updateCartAmount($amount,$cartId) {
        $stmt = $this->connection->prepare("UPDATE cart_order SET Amount=$amount where cartId=?");
        $stmt->bind_param('i',$cartId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
    
    public function countCartOrder($userId, $vendorDetailsId) {
        $stmt = $this->connection->prepare("select count(*) FROM `cart_order` WHERE userId=? and isActive=1 and vendorDetailsId=?");
        $stmt->bind_param("ii", $userId, $vendorDetailsId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function insertCartOrder($userId, $orderNum, $vendorDetailsId) {
        $statement = $this->connection->prepare("insert into cart_order (userId,order_status,order_number,isActive,vendorDetailsId,isSeen) values($userId,1,'$orderNum',1,$vendorDetailsId,0)");
        $statement->execute();
        $statement->store_result();
        if ($statement->num_rows > 0) {
            return TRUE;
        }
    }
    public function updateIsSeen($cartId){
        $stmt = $this->connection->prepare("UPDATE cart_order SET isSeen=1 where cartId=?");
        $stmt->bind_param('i',$cartId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function getCartId($userId) {
        $stmt = $this->connection->prepare("select cartId from cart_order where userId=? and isActive=1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($cartId);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $cartId;
    }

    public function getAmountFromCart($userId) {
        $stmt = $this->connection->prepare("select Amount from cart_order where userId=$userId and isActive=1");
        $stmt->execute();
        $stmt->bind_result($amount);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $amount;
    }

    // public function updateCartAmount($amount, $storeKey, $userId) {
    //    $stmt = $this->connection->prepare("UPDATE cart_order SET  where userId=? and isActive=1");
    //    $stmt->bind_param('dsi', $amount, $storeKey, $userId);
    //     $stmt->execute();
    //      $num_affected_rows = $stmt->affected_rows;
    //      $stmt->close();
    //       return $num_affected_rows > 0;
    //   }

    public function confirmOrder($amount, $storeKey, $order_status, $cartId, $userId) {
        $stmt = $this->connection->prepare("UPDATE cart_order SET Amount=?,vendorDetailsId=?,order_status=?,isActive=0 where userId=? and cartId=? and isActive=1");
        $stmt->bind_param('diiii', $amount, $storeKey, $order_status, $userId, $cartId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function orderPlaced($userId) {
        $stmt = $this->connection->prepare("SELECT cart_order.amount,cart_order.order_number,order_steps.id,order_steps.step_name FROM cart_order INNER JOIN user_details on cart_order.userId=user_details.id INNER JOIN order_steps on cart_order.order_status=order_steps.id  where cart_order.userId=? and cart_order.isActive=0  ORDER by cartId DESC limit 1");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->bind_result($amount, $orderNumber, $stepId, $stepName);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $storeDetails = ["amount" => $amount, "orderNumber" => $orderNumber, "statusId" => $stepId, "status" => $stepName];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function listOrder($userId, $storeKey) {
        $stmt = $this->connection->prepare("SELECT cart_order.amount,cart_order.createdDate,cart_order.order_number,order_steps.id,order_steps.step_name,user_address.userAddress FROM cart_order INNER JOIN vendor_details on cart_order.vendorDetailsId=vendor_details.vendorDetailsId INNER join user_details on user_details.id=cart_order.userId INNER JOIN user_address on user_address.userId=user_details.id INNER JOIN order_steps on cart_order.order_status=order_steps.id where cart_order.userId=? and vendor_details.vendorStoreKey=? and cart_order.isActive=0 and cart_order.createdDate BETWEEN curdate() and curdate() + interval 3 day ORDER by cart_order.createdDate desc");
        $stmt->bind_param('is', $userId, $storeKey);
        $stmt->execute();
        $stmt->bind_result($amount, $createdDate, $orderNumber, $stepId, $stepName, $address);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["amount" => $amount, "orderDate" => $createdDate, "orderNumber" => $orderNumber, "statusId" => $stepId, "status" => $stepName, "address" => $address];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function orderDetailsById($userId, $orderId) {
        $stmt = $this->connection->prepare("select add_cart.Cartdetails_Id,add_cart.isActive,add_cart.Amount,add_cart.quantity,product.product_name,product.image_path from add_cart INNER join vendor_by_product on vendor_by_product.vendor_id=add_cart.productId INNER join cart_order on cart_order.cartId=add_cart.cartId INNER join user_details on user_details.id=cart_order.userId INNER join product_details on product_details.productDetailsId=vendor_by_product.product_details_id INNER join product on product.productId=product_details.productId INNER join category on category.id=product.categoryId where cart_order.userId=? and cart_order.order_number=? and cart_order.isActive=0");
        $stmt->bind_param('is', $userId, $orderId);
        $stmt->execute();
        $stmt->bind_result($cartId,$isActive, $amount, $quantity, $productName, $imagePath);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["cartDetailsId" => $cartId,"isActive"=>$isActive, "amount" => $amount, "quantity" => $quantity, "productName" => $productName, "imagepath" => $imagePath];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function getSubCategoryById($id) {
        $stmt = $this->connection->prepare("select subId,Subcategory_Name,categoryId,active from subcategory where CategoryId=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($id, $subName, $catId, $active);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["subId" => $id, "subCategoryName" => $subName, "categoryId" => $catId, "active" => $active];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function updateFcmId($fcmId, $userId) {
        $stmt = $this->connection->prepare("UPDATE user_details SET fcmId=? where id=?");
        $stmt->bind_param('si', $fcmId, $userId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function updateVFcmId($fcmId, $userId) {
        $stmt = $this->connection->prepare("UPDATE vendor_details SET vendorFcmId=? where vendorDetailsId=?");
        $stmt->bind_param('si', $fcmId, $userId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function updateStoreOpen($closeId, $userId) {
        $stmt = $this->connection->prepare("UPDATE vendor_details SET isShopOpen=? where vendorDetailsId=?");
        $stmt->bind_param('ii', $closeId, $userId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function countEmailId($emailId) {
        $stmt = $this->connection->prepare("select count(*) from user_details where emailId=? and user_type=1");
        $stmt->bind_param("s", $emailId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function countUserId($userId) {
        $stmt = $this->connection->prepare("select count(*) from user_details where id=?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function countVendorId($userId) {
        $stmt = $this->connection->prepare("select count(*) from vendor_details where vendorDetailsId=?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function countProductId($vendorId) {
        $stmt = $this->connection->prepare("select SUM(total_quantity-sell_quantity) from vendor_by_product where vendor_id=?");
        $stmt->bind_param("i", $vendorId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function countproductIdByUser($productId, $userId) {
        $stmt = $this->connection->prepare("select count(*) from add_cart INNER join cart_order on add_cart.cartId=cart_order.cartId where cart_order.isActive=1 and add_cart.productId=? and cart_order.userId=?");
        $stmt->bind_param("ii", $productId, $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function getProductSellPrice($vendorId) {
        $stmt = $this->connection->prepare("select sell_price from vendor_by_product where vendor_id=?");
        $stmt->bind_param("i", $vendorId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function countPhoneNum($phno) {
        $stmt = $this->connection->prepare("select count(*) from user_details where mobilenum=? and user_type=1");
        $stmt->bind_param("d", $phno);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function insertUser($name, $emailId, $phone, $password,$actualPwd) {
        $statement = $this->connection->prepare("insert into user_details (name,emailId,mobilenum,password,actual_password,user_type) values(?,?,?,?,?,1)");
        $statement->bind_param('ssdss', $name, $emailId, $phone, $password,$actualPwd);
        $result = $statement->execute();
        // clsoing the statement
        $statement->close();
        // Check for successful insertion
        $out = $result != 0 ? true : false;
        if ($out)
            $last_id = $this->connection->insert_id;
        else
            $last_id = -1;
        return $last_id;
    }

    public function updateUserAddress($userId, $address) {
        $stmt = $this->connection->prepare("UPDATE user_address SET userAddress=? where userId=?");
        $stmt->bind_param('si', $address, $userId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function updateUserInfo($name, $mobileNum, $userId) {
        $stmt = $this->connection->prepare("UPDATE user_details SET name=?,mobilenum=? where id=?");
        $stmt->bind_param('sdi', $name, $mobileNum, $userId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function insertAddress($userId, $address) {
        $statement = $this->connection->prepare("insert into user_address values(null,?,?)");
        $statement->bind_param('is', $userId, $address);
        $result = $statement->execute();
        // clsoing the statement
        $statement->close();
        // Check for successful insertion
        $out = $result != 0 ? true : false;
    }

    public function getUserDetails($emailId) {
        $stmt = $this->connection->prepare("select user_details.id,user_details.passwordReset,user_details.name,user_details.emailId,user_details.mobilenum,user_details.password,user_details.createdDate,user_address.userAddress,user_details.user_type from user_details INNER join user_address on user_details.id=user_address.userId INNER join user_type on user_type.userTypeId=user_details.user_type where user_details.emailId=? and user_details.user_type=1");
        $stmt->bind_param("s", $emailId);
        $stmt->execute();
        $stmt->bind_result($id,$passwordreset, $name, $emailid, $mobilenum, $password, $createdDate, $address, $usertype);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $userDatails = ["userId" => $id, "resetId"=>$passwordreset,"name" => $name, "emailId" => $emailid, "mobileNumber" => $mobilenum, "password" => $password, "createdDate" => $createdDate, "address" => $address, "userType" => $usertype];
            $stmt->close();
            return $userDatails;
        }
    }

    public function getUserDetailsById($id) {
        $stmt = $this->connection->prepare("select user_details.id,user_details.name,user_details.emailId,user_details.mobilenum,user_details.password,user_details.createdDate,user_address.userAddress,user_details.user_type from user_details INNER join user_address on user_details.id=user_address.userId INNER join user_type on user_type.userTypeId=user_details.user_type where user_details.id=? and user_details.user_type=1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($id, $name, $emailid, $mobilenum, $password, $createdDate, $address, $usertype);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $userDatails = ["userId" => $id, "name" => $name, "emailId" => $emailid, "mobileNumber" => $mobilenum, "password" => $password, "createdDate" => $createdDate, "address" => $address, "userType" => $usertype];
            $stmt->close();
            return $userDatails;
        }
    }

    public function vendorLogin($emailId, $password) {
        $stmt = $this->connection->prepare("select vendorDetailsId,vendorStoreName,vendorEmailId,vendorMobileNum,vendorStoreKey,createdDate,vendorAddress,isPaymentDone,isShopOpen,shopTiming from vendor_details where vendorEmailId =? and vendorPassword=?");
        $stmt->bind_param("ss", $emailId, $password);
        $stmt->execute();
        $stmt->bind_result($id, $name, $emailId, $mobilenum, $key, $date, $address, $isPaymentDone, $isShopOpen,$timing);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            return $userDatails = ["userId" => $id, "name" => $name, "emailId" => $emailId, "mobileNumber" => $mobilenum, "createdDate" => $date, "storeKey" => $key, "address" => $address, "isPaymentDone" => $isPaymentDone, "isShopOpen" => $isShopOpen,"timing"=>$timing];
        }
    }

    public function vendorDetaild($id) {
        $stmt = $this->connection->prepare("select vendorDetailsId,vendorStoreName,vendorEmailId,vendorMobileNum,vendorStoreKey,createdDate,vendorAddress,isPaymentDone,isShopOpen,shopTiming from vendor_details where vendorDetailsId =?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($id, $name, $emailId, $mobilenum, $key, $date, $address, $isPaymentDone, $isShopOpen,$timing);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            return $userDatails = ["userId" => $id, "name" => $name, "emailId" => $emailId, "mobileNumber" => $mobilenum, "createdDate" => $date, "storeKey" => $key, "address" => $address, "isPaymentDone" => $isPaymentDone, "isShopOpen" => $isShopOpen,"timing"=>$timing];
        }
    }

    public function vendorCartDetails($storeKey) {
        $stmt = $this->connection->prepare("select cart_order.isSeen,cart_order.cartId,cart_order.createdDate,cart_order.order_number,cart_order.Amount,cart_order.order_status,user_details.name,user_details.mobilenum,user_address.userAddress from cart_order INNER join vendor_details on vendor_details.vendorDetailsId=cart_order.vendorDetailsId INNER JOIN user_details on user_details.id=cart_order.userId INNER join user_address on user_address.userId=user_details.id INNER JOIN order_steps on order_steps.id=cart_order.order_status where vendor_details.vendorStoreKey=? and cart_order.order_status<=4 and cart_order.order_status>1");
        $stmt->bind_param("s", $storeKey);
        $stmt->execute();
        $stmt->bind_result($isSeen,$cartid, $date, $orderNum, $amount,$status, $name, $mobileNum, $address);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $cartOrder[] = ["isSeen"=>$isSeen,"cartId" => $cartid, "date" => $date, "orderNumber" => $orderNum, "amount" => $amount,"statusId"=>$status, "name" => $name, "mobileNumber" => $mobileNum, "address" => $address];
            $stmt->close();
            return $cartOrder;
        }
    }

    public function vendorCartId($cartId) {
        $stmt = $this->connection->prepare("select add_cart.Cartdetails_Id,add_cart.isActive,add_cart.Amount,add_cart.quantity,product.product_name,product.image_path from add_cart INNER join vendor_by_product on vendor_by_product.vendor_id=add_cart.productId INNER join cart_order on cart_order.cartId=add_cart.cartId INNER join user_details on user_details.id=cart_order.userId INNER join product_details on product_details.productDetailsId=vendor_by_product.product_details_id INNER join product on product.productId=product_details.productId INNER join category on category.id=product.categoryId where cart_order.cartId=?");
        $stmt->bind_param('i', $cartId);
        $stmt->execute();
        $stmt->bind_result($cartId,$isActive, $amount, $quantity, $productName, $imagePath);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["cartDetailsId" => $cartId,"isActive"=>$isActive, "amount" => $amount, "quantity" => $quantity, "productName" => $productName, "imagepath" => $imagePath];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function cartOderIsSeen() {
        $stmt = $this->connection->prepare("update cart_order set ");
        $stmt->bind_param('i', $cartId);
        $stmt->execute();
        $stmt->bind_result($cartId, $amount, $quantity, $productName, $imagePath);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $storeDetails[] = ["cartDetailsId" => $cartId, "amount" => $amount, "quantity" => $quantity, "productName" => $productName, "imagepath" => $imagePath];
            $stmt->close();
            return $storeDetails;
        }
    }

    public function getSellQuantity($cartDetailsId) {
        $stmt = $this->connection->prepare("select vendor_by_product.sell_quantity,vendor_by_product.vendor_id,vendor_by_product.total_quantity,vendor_by_product.user_id from vendor_by_product INNER join add_cart on add_cart.productId=vendor_by_product.vendor_id where add_cart.Cartdetails_Id=?");
        $stmt->bind_param('i', $cartDetailsId);
        $stmt->execute();
        $stmt->bind_result($sell_quantity, $id, $total, $userId);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            return $storeDetails = ["sellQuantity" => $sell_quantity, "vendorId" => $id, "total" => $total, "userId" => $userId];
        }
    }

    public function updateQuantity($quantity, $vendorId, $userId) {
        $stmt = $this->connection->prepare("UPDATE vendor_by_product SET sell_quantity=? where vendor_id=? and user_id=?");
        $stmt->bind_param('iii', $quantity, $vendorId, $userId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function updateCartOrderStatus($status, $cartId) {
        $stmt = $this->connection->prepare("UPDATE cart_order SET order_status=? where cartId=?");
        $stmt->bind_param('ii', $status, $cartId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function orderDetailsByCartId($cartId) {
        $stmt = $this->connection->prepare("SELECT cart_order.amount,cart_order.createdDate,cart_order.order_number,order_steps.id,order_steps.step_name,user_address.userAddress FROM cart_order INNER JOIN user_details on cart_order.userId=user_details.id INNER join user_address on user_address.userId=user_details.id INNER JOIN order_steps on cart_order.order_status=order_steps.id where cart_order.cartId=?");
        $stmt->bind_param('i', $cartId);
        $stmt->execute();
        $stmt->bind_result($amount, $date, $orderNum, $stepId, $stepname, $address);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            return $storeDetails = ["amount" => $amount, "date" => $date, "orderNumber" => $orderNum, "statusId" => $stepId, "status" => $stepname, "address" => $address];
        }
    }

    public function vendorDataDetails($storeKey) {
        $stmt = $this->connection->prepare("select cart_order.cartId,cart_order.createdDate,cart_order.order_number,cart_order.Amount,user_details.name,user_details.mobilenum,user_address.userAddress,order_steps.id,order_steps.step_name from cart_order INNER join vendor_details on vendor_details.vendorDetailsId=cart_order.vendorDetailsId INNER JOIN user_details on user_details.id=cart_order.userId INNER join user_address on user_address.userId=user_details.id INNER join order_steps on order_steps.id=cart_order.order_status where vendor_details.vendorStoreKey=? and cart_order.order_status>=6 ORDER BY cart_order.createdDate DESC");
        $stmt->bind_param("s", $storeKey);
        $stmt->execute();
        $stmt->bind_result($cartid, $date, $orderNum, $amount, $name, $mobileNum, $address, $stepId, $steps);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch())
                $cartOrder[] = ["cartId" => $cartid, "date" => $date, "orderNumber" => $orderNum, "amount" => $amount, "name" => $name, "mobileNumber" => $mobileNum, "address" => $address, "statusId" => $stepId, "status" => $steps];
            $stmt->close();
            return $cartOrder;
        }
    }

    public function getFcmIdFromStoreKey($storeKey) {
        $stmt = $this->connection->prepare("select vendorFcmId from vendor_details where vendorStoreKey=?");
        $stmt->bind_param("s", $storeKey);
        $stmt->execute();
        $stmt->bind_result($fcmId);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            return $fcmId;
        }
    }

    public function getCustomerFcmKey($cartId) {
        $stmt = $this->connection->prepare("select user_details.fcmId from user_details INNER join cart_order on cart_order.userId=user_details.id WHERE cart_order.cartId=?");
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        $stmt->bind_result($fcmId);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            return $fcmId;
        }
    }
    
    public function updatePassword($emailId,$hashPwd,$actpassword,$passwordReset){
         $stmt = $this->connection->prepare("UPDATE user_details SET password=?,passwordReset=?,actual_password=? where emailId=?");
        $stmt->bind_param('siss', $hashPwd,$passwordReset,$actpassword,$emailId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function updateAddress($address, $userId) {
        $stmt = $this->connection->prepare("UPDATE user_address SET userAddress=? where userId=?");
        $stmt->bind_param('si', $address, $userId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function updateVendorInfo($name, $mobileNum, $address, $userId) {
        $stmt = $this->connection->prepare("UPDATE vendor_details SET vendorStoreName=?,vendorMobileNum=?,vendorAddress=? where vendorDetailsId=?");
        $stmt->bind_param('ssss', $name, $mobileNum, $address, $userId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function vendorDetails($id) {
        $stmt = $this->connection->prepare("select vendorDetailsId,vendorStoreName,vendorEmailId,vendorMobileNum,vendorStoreKey,createdDate,vendorAddress,shopTiming from vendor_details where vendorDetailsId=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($id, $name, $emailId, $mobilenum, $key, $date, $address,$timing);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            return $userDatails = ["userId" => $id, "name" => $name, "emailId" => $emailId, "mobileNumber" => $mobilenum, "createdDate" => $date, "storeKey" => $key, "address" => $address,"timing"=>$timing];
        }
    }
    public function vendorInfos($key) {
        $stmt = $this->connection->prepare("select vendorDetailsId,vendorStoreName,vendorEmailId,vendorMobileNum,vendorStoreKey,createdDate,vendorAddress,shopTiming from vendor_details where vendorStoreKey=?");
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $stmt->bind_result($id, $name, $emailId, $mobilenum, $key, $date, $address,$timing);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            return $userDatails = ["userId" => $id, "name" => $name, "emailId" => $emailId, "mobileNumber" => $mobilenum, "createdDate" => $date, "storeKey" => $key, "address" => $address,"timing"=>$timing];
        }
    }
   public function countVendorProductId($vendorId,$vendorDetailsId) {
        $stmt = $this->connection->prepare("SELECT COUNT(*) from vendor_by_product where vendor_id=? and user_id=? and isActive=1");
        $stmt->bind_param("ii", $vendorId,$vendorDetailsId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function countVendorDetailsId($storeKey) {
        $stmt = $this->connection->prepare("SELECT COUNT(*) from vendor_details where vendorStoreKey=? and vendorIsActive=1");
        $stmt->bind_param("s", $storeKey);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function getVendorId($storeKey) {
        $stmt = $this->connection->prepare("SELECT vendorDetailsId from vendor_details where vendorStoreKey=? and vendorIsActive=1");
        $stmt->bind_param("s", $storeKey);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function checkProduct($productName) {
        $stmt = $this->connection->prepare("SELECT COUNT(*) from product where productId=?");
        $stmt->bind_param("s", $productName);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->store_result();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function updateProductByVendor($sp, $cp, $total, $vendorId) {
        $stmt = $this->connection->prepare("UPDATE vendor_by_product SET sell_price=?,cost_price=?,total_quantity=? where vendor_id=?");
        $stmt->bind_param('ddii', $sp, $cp, $total, $vendorId);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function getVendorDetailsId($storeKey) {
        $stmt = $this->connection->prepare("SELECT `vendorDetailsId` FROM `vendor_details` WHERE `vendorStoreKey`=?");
        $stmt->bind_param("s", $storeKey);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            return $id;
        }
    }

    public function insertProducttoVendor($productDetailsId, $sp, $vendorId, $isActive, $isVerify) {
        $statement = $this->connection->prepare("insert into vendor_by_product values(null,?,?,0,0,0,?,?,?)");
        $statement->bind_param('idiii', $productDetailsId, $sp, $vendorId, $isActive, $isVerify);
        $result = $statement->execute();
        // clsoing the statement
        $statement->close();
        // Check for successful insertion
        return $out = $result != 0 ? true : false;
    }

    public function insertProduct($categoryId, $productName) {
        $statement = $this->connection->prepare("insert into product values(null,0,?,?,null)");
        $statement->bind_param('is', $categoryId, $productName);
        $result = $statement->execute();
        // clsoing the statement
        $statement->close();
        // Check for successful insertion
        $out = $result != 0 ? true : false;
        return $out ? $this->connection->insert_id : -1;
        //if ($out)
        //  $last_id = $this->connection->insert_id;
        // else
        //   $last_id = -1;
        //return $last_id;
    }

    public function insertProductDetails($quantity, $productId, $type) {
        $statement = $this->connection->prepare("insert into product_details values(null,?,?,?,null)");
        $statement->bind_param('dis', $quantity, $productId, $type);
        $result = $statement->execute();
        // clsoing the statement
        $statement->close();
        // Check for successful insertion
        $out = $result != 0 ? true : false;
        return $out ? $this->connection->insert_id : -1;
    }

}

?>