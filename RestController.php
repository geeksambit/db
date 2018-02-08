<?php

require_once("./ProductDetails.php");
require_once("./UserDetails.php");
require_once './VendorDetails.php';
$method = $_SERVER['REQUEST_METHOD'];
$view = "";
if (isset($_GET["page_key"]))
    $page_key = $_GET["page_key"];

switch (strtolower($page_key)) {

    case "alldata":
        // to handle REST Url /api/allData/
        //echo 'Hello';
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->getAllProduct();
        break;
    case "allcategory":
        // to handle REST Url /api/allData/
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->getAllCategory();
        break;
    case "subcategorybyid":
        // to handle REST Url /api/subcategorybyid/
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->getSubCatById();
        break;
    case "productbysubcatid":
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->productBySubCatId();
        break;
    case "addtocart":
        // to handle REST Url /api/product/addtocart
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->addToCart();
        break;
    case "removefromcart":
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->removeFromCart();
        break;
    case "place":
        $productDetails = ProductHandler::Instance();
        $productDetails->confirmOrder();
        break;
    case "cartdata":
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->getCartData();
        break;
    case "search":
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->searchProduct();
        break;
    case "register":
        //to Handle REST Url api/user/register/
        $user = new UserHandler();
        $user->register();
        break;
    case "login":
        //to Handle REST Url api/user/login/
        $user = new UserHandler();
        $user->login();
        break;
    case "userupdate":
        $user = new UserHandler();
        $user->updateProfile();
        break;
    case "forgotpasword":
        $user = new UserHandler();
        $user->forgotPassword();
        break;
    case "resetpassword":
         $user = new UserHandler();
        $user->updatePassword();
        break;
    case "updatefcm":
        $user = new UserHandler();
        $user->updateFcmId();
        break;
    case "listorder":
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->getListOrder();
        break;
    case "listproductbyorderid":
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->orderDetailsByOrderId();
        break;
    case "vendor_login":
        $vendor = VendorDetails::Instance();
        $vendor->login();
        break;
    case "vendororderdata":
        $vendor = VendorDetails::Instance();
        $vendor->orderDetails();
        break;
    case "vendororderdetails":
        $vendor = VendorDetails::Instance();
        $vendor->orderDetailsByVendorId();
        break;
    case "vendorapprove":
        $vendor = VendorDetails::Instance();
        $vendor->orderConfirmed();
        break;
    case "vendordecline":
        $vendor = VendorDetails::Instance();
        $vendor->ordercanceled();
        break;
    case "vendordatadetails":
        $vendor = VendorDetails::Instance();
        $vendor->vendordatadetails();
        break;
    case "vendorupdate":
        echo 'Update';
        $vendor = VendorDetails::Instance();
        $vendor->vendorupdate();
        break;
    case "vendorproductupdate":
        $vendor = VendorDetails::Instance();
        $vendor->vendorproductupdate();
        break;
    case "vupdatefcm":
        $vendor = VendorDetails::Instance();
        $vendor->updateFcmId();
        break;
    case "vendordispatch":
        $vendor = VendorDetails::Instance();
        $vendor->orderDispatch();
        break;
    case "vendordelivery":
        $vendor = VendorDetails::Instance();
        $vendor->orderDelivered();
        break;
    case "addproduct":
        $vendor = VendorDetails::Instance();
        $vendor->addProduct();
        break;
    case "logout":
        $vendor = VendorDetails::Instance();
        $vendor->logout();
        break;
    case "isshopopen":
        $product = ProductHandler::Instance();
        $product->isshopopen();
        break;
    case "vendorpayment":
        $vendor = VendorDetails::Instance();
        $vendor->vendorPayment();
        break;
    case "vendorstatus":
        $vendor = VendorDetails::Instance();
        $vendor->vendorStatus(); 
        break;
    case "categorybydata":
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->categoryData();
        break;
    case "shopopen":
        $productDetails = VendorDetails::Instance();
        $result = $productDetails->vendorShopOpen();
        break;
    case "getstoreinfo":
        $productDetails = ProductHandler::Instance();
        $result = $productDetails->getStoreInfo();
        break;
    case "vendorremovecart":
    $vendor = VendorDetails::Instance();
        $result = $vendor->vendorRemoveProduct();
        break;
    default :
        echo $page_key;
        break;
}
?>
