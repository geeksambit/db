<?php

include_once './Db/DbHandler.php';
require_once("./SimpleRest.php");
require_once("./function/Constants.php");
require_once("./function/validation.php");
include_once './fcm.php';

/* $allData=$handler->getAllProductName();

  $result["code"]="00";
  $result["data"]=$allData;

  header('Content-Type: application/json');
  echo json_encode($result);
  ?>
 */

class ProductHandler extends SimpleRest {

    public static function Instance() {
        static $inst = null;
        if ($inst === null) {
            $inst = new ProductHandler();
        }
        return $inst;
    }

    //private 
    function __construct() {
        $this->handler = new DbHandler();
        $this->requestContentType = 'application/json';
    }

    function isshopopen() {
        $storeKey = $_POST["storeKey"];
        $rawData = $this->handler->isShopOPen($storeKey);

        $this->statusCode = 200;
        $result["data"] = $rawData;
        $result["code"] = "00";
        $result["message"] = "success";

        $this->setHttpHeaders($this->requestContentType, $this->statusCode);

        if (strpos($this->requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($result);
            echo $response;
        }
    }

    function getAllProduct() {
        $storeKey = $_GET["storeKey"];
         $size=$_POST["size"];
       if($size==0)
        $start=$size;
        else 
        $start=$size+1;
        $end=10;
         $rawData = $this->handler->getAllProductName($storeKey,$start,$end);
        if (empty($rawData)) {
            $this->statusCode = 404;
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = "No Data Found";
            //$rawData = array('code' => "01",'message'=>'No Data Available');		
        } else {
            $this->statusCode = 200;
            $result["data"] = $rawData;
            $result["code"] = "00";
            $result["message"] = "success";
        }

        $this->setHttpHeaders($this->requestContentType, $this->statusCode);

        if (strpos($this->requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($result);
            echo $response;
        }
    }

    function getAllCategory() {
        $rawData = $this->handler->getAllCategory();
        if (empty($rawData)) {
            $this->statusCode = 404;
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = "No Data Found";
            //$rawData = array('code' => "01",'message'=>'No Data Available');		
        } else {
            $this->statusCode = 200;
            $result["data"] = $rawData;
            $result["code"] = "00";
            $result["message"] = "success";
        }

        $this->setHttpHeaders($this->requestContentType, $this->statusCode);

        if (strpos($this->requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($result);
            echo $response;
        }
    }

    function getCartData() {
        $userId = $_POST['userId'];
        if (!empty($userId) && $this->handler->countUserId($userId) == 1) {
            $rawData = $this->handler->cartDetailsById($userId);
            if (empty($rawData)) {
                $this->statusCode = 404;
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "No Data Found";
                //$rawData = array('code' => "01",'message'=>'No Data Available');		
            } else {
                $this->statusCode = 200;
                $result["data"] = $rawData;
                $result["code"] = "00";
                $result["message"] = "success";
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = Constants::userId_unAvailable;
        }
        $this->setHttpHeaders($this->requestContentType, $this->statusCode);
        if (strpos($this->requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($result);
            echo $response;
        }
    }

    function getStoreInfo() {
        $storeKey = $_POST['storeKey'];
        if (!empty($storeKey)) {
            $rawData = $this->handler->vendorInfos($storeKey);
            if (empty($rawData)) {
                $this->statusCode = 404;
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "No Data Found";
                //$rawData = array('code' => "01",'message'=>'No Data Available');		
            } else {
                $this->statusCode = 200;
                $result["data"] = $rawData;
                $result["code"] = "00";
                $result["message"] = "success";
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = "Error";
        }
        $this->setHttpHeaders($this->requestContentType, $this->statusCode);
        if (strpos($this->requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($result);
            echo $response;
        }
    }

    function addToCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = json_decode(file_get_contents('php://input'), true);
            $userId = $_GET['userId'];
            $storeKey = $_GET["storeKey"];
            if (!empty($userId) && $this->handler->countUserId($userId) == 1 && !empty($storeKey) && $this->handler->countVendorDetailsId($storeKey)) {
                if (!empty($product)) {
                    $vendorDetailsId = $this->handler->getVendorId($storeKey);
                    $count = $this->handler->countCartOrder($userId, $vendorDetailsId);
                    if ($count == 0) {
                        $this->handler->insertCartOrder($userId, getRandomNum(), $vendorDetailsId);
                    }
                    $cartId = $this->handler->getCartId($userId);
                    $total;
                    //  var_dump($product)  ;
                    if ($cartId > 0) {
                        foreach ($product as $value) {
                            //$total = $value["cartQuantity"] * $this->handler->getProductSellPrice($value["productDetailsId"]);

                            $count = $this->handler->countProductId($value["productDetailsId"]);

                            if ($this->handler->countproductIdByUser($value["productDetailsId"], $userId) == 1) {
                                $quantity = $this->handler->getQuantityFromCart($value["productDetailsId"], $userId, $cartId);

                                $quantity += $value["cartQuantity"];

                                // if ($count > 0 && $count >= $quantity) {
                                $check = $this->handler->updateCart($cartId, $value["productDetailsId"], $quantity, $quantity * $this->handler->getProductSellPrice($value["productDetailsId"]), $userId);
                                //  }
                            } else {
                                // if ($count > 0 && $count >= $value["cartQuantity"]) {
                                $check = $this->handler->addToCart($cartId, $userId, $value["productDetailsId"], $value["cartQuantity"], $value["cartQuantity"] * $this->handler->getProductSellPrice($value["productDetailsId"]));
                                // }
                            }
                        }
                    }
                    //echo 'Final-'.$total+$this->handler->getAmountFromCart($userId),$userId;
                    //$this->handler->updateCartAmount($total+$this->handler->getAmountFromCart($userId),$userId);
                    $data = $this->handler->cartDetailsById($userId);
                    $this->statusCode = 200;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = $data;
                    $result["code"] = "00";
                    $result["message"] = "Success";
                } else {
                    $this->statusCode = 404;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = Constants::product_add;
                }
            } else {
                $this->statusCode = 404;
                $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = Constants::userId_unAvailable;
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = Constants::get_notSupport;
        }
        echo $this->encodeJson($result);
    }

    function removeFromCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['userId'];
            $productId = $_POST['productId'];
            $cartId = $_POST["cartDetailsId"];
            if (!empty($userId) && $this->handler->countUserId($userId) == 1) {
                if (!empty($productId) && $this->handler->countproductIdByUser($productId, $userId, $cartId) == 1) {
                    $cartDetailsId = $this->handler->getCartId($userId);
                    //$total=$this->handler->getAddCartAmount($productId, $userId, $cartId,$cartDetailsId);
                    $this->handler->removeFromCart($productId, $userId, $cartId, $cartDetailsId);

                    // echo 'total Amount--'.$this->handler->getAmountFromCart($userId);
                    //$this->handler->updateCartAmount($this->handler->getAmountFromCart($userId)-$total,$userId);
                    $data = $this->handler->cartDetailsById($userId);
                    $this->statusCode = 200;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = $data;
                    $result["code"] = "00";
                    $result["message"] = "Success";
                } else {
                    $this->statusCode = 404;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = "Product Id Not Available";
                }
            } else {
                $this->statusCode = 404;
                $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = Constants::userId_unAvailable;
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = Constants::get_notSupport;
        }
        echo $this->encodeJson($result);
    }

    function confirmOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = json_decode(file_get_contents('php://input'), true);
            $userId = $_GET['userId'];
            $storeKey = $_GET['storeKey'];
            $vendorDetailsId = $this->handler->getVendorDetailsId($storeKey);
            if (!empty($userId) && $this->handler->countUserId($userId) == 1) {
                if (!empty($product)) {
                    $cartId = $this->handler->getCartId($userId);
                    $total = 0;
                    foreach ($product as $value) {
                        $total = $total + $value["cartQuantity"] * $this->handler->getProductSellPrice($value["productDetailsId"]);
                    }
                    //$this->handler->updateCartAmount($total, $storeKey,$userId);
                    $count = $this->handler->confirmOrder($total, $vendorDetailsId, 2, $cartId, $userId);
                    if ($count > 0) {
                        $fcmId = $this->handler->getFcmIdFromStoreKey($storeKey);
                        sendFcm($fcmId, "You Got One Order");
                    }
                    //  var_dump($product)  ;
                    //foreach ($product as $value) {
                    //     $count = $this->handler->countProductId($value["productDetailsId"]);
                    //     if ($this->handler->countproductIdByUser($value["productDetailsId"], $userId) == 1) {
                    //          $this->handler->confirmCart($value["productDetailsId"], $value["cartId"], $userId, 2);
                    //       }
                    //}

                    $data = $this->handler->orderPlaced($userId);
                    $this->statusCode = 200;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = $data;
                    $result["code"] = "00";
                    $result["message"] = "Success";
                } else {
                    $this->statusCode = 404;
                    $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = "Cart is Empty";
                }
            } else {
                $this->statusCode = 404;
                $this->setHttpHeaders($this->requestContentType, $this->statusCode);
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = Constants::userId_unAvailable;
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = Constants::get_notSupport;
        }
        echo $this->encodeJson($result);
    }

    function getListOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['userId'];
            $storeKey = $_POST['storeKey'];
            $data = $this->handler->listOrder($userId, $storeKey);
            $this->statusCode = 200;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = $data;
            $result["code"] = "00";
            $result["message"] = "Success";
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = Constants::get_notSupport;
        }
        echo $this->encodeJson($result);
    }

    function searchProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['search'])) {
                if($_POST["subCatId"]==0){
                    $data= 'Inside all Data';
                    $rawData = $this->handler->searchProduct(trim($_POST["storeKey"]), trim($_POST['search']));
                }
                else {
                     $data= 'Inside search';
                $rawData = $this->handler->searchProductById(trim($_POST["storeKey"]), trim($_POST['search']),trim($_POST["subCatId"]));
                }
                if (empty($rawData)) {
                    $this->statusCode = 404;
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["inside"]=$data;
                    $result["message"] = "No Data Found";
                    //$rawData = array('code' => "01",'message'=>'No Data Available');		
                } else {
                    $this->statusCode = 200;
                    $result["data"] = $rawData;
                    $result["code"] = "00";
                      $result["inside"]=$data;
                    $result["message"] = "success";
                }
            } else {
                $this->statusCode = 404;
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "Required Field Missing";
            }
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = Constants::get_notSupport;
        }
        echo $this->encodeJson($result);
    }

    function productBySubCatId() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['subCatId']) && isset($_POST['storeKey'])) {
                $size=$_POST["size"];
                if($size==0)
        $start=$size;
        else 
        $start=$size+1;
        $end=10;
                $rawData = $this->handler->getProductBySubId(trim($_POST['subCatId']), trim($_POST['storeKey']),$start,$end);
                if (empty($rawData)) {
                    $this->statusCode = 404;
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = "No Data Found";
                    //$rawData = array('code' => "01",'message'=>'No Data Available');		
                } else {
                    $this->statusCode = 200;
                    $result["data"] = $rawData;
                    $result["code"] = "00";
                    $result["message"] = "success";
                }
            } else {
                $this->statusCode = 404;
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "Required Field Missing";
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

    function categoryData() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['subCatId'])) {
                $rawData = $this->handler->getCategorydata(trim($_POST['subCatId']));
                if (empty($rawData)) {
                    $this->statusCode = 404;
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = "No Data Found";
                    //$rawData = array('code' => "01",'message'=>'No Data Available');		
                } else {
                    $this->statusCode = 200;
                    $result["data"] = $rawData;
                    $result["code"] = "00";
                    $result["message"] = "success";
                }
            } else {
                $this->statusCode = 404;
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "Required Field Missing";
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

    function orderDetailsByOrderId() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST["userId"];
            $orderId = $_POST["orderNumber"];
            $rawData = $this->handler->orderDetailsById($userId, $orderId);
            $this->statusCode = 200;
            $result["data"] = $rawData;
            $result["code"] = "00";
            $result["message"] = "success";
        } else {
            $this->statusCode = 404;
            $this->setHttpHeaders($this->requestContentType, $this->statusCode);
            $result["data"] = null;
            $result["code"] = "01";
            $result["message"] = "Get Request Not Supported";
        }
        echo $this->encodeJson($result);
    }

    function getSubCatById() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['categoryId'])) {
                $rawData = $this->handler->getSubCategoryById(trim($_POST['categoryId']));
                if (empty($rawData)) {
                    $this->statusCode = 404;
                    $result["data"] = null;
                    $result["code"] = "01";
                    $result["message"] = "No Data Found";
                    //$rawData = array('code' => "01",'message'=>'No Data Available');		
                } else {
                    $this->statusCode = 200;
                    $result["data"] = $rawData;
                    $result["code"] = "00";
                    $result["message"] = "success";
                }
            } else {
                $this->statusCode = 404;
                $result["data"] = null;
                $result["code"] = "01";
                $result["message"] = "Required Field Missing";
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

?>