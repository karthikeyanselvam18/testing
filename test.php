<?php
if (empty($_COOKIE['user']) && empty($_COOKIE['pass']))
{
$url=base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); 
echo "<script>window.location.href= 'index.php?url=".$url."';</script>";
}
else{
header('Content-Type: text/html; charset=ISO-8859-1');
include 'imports/dbcon.php';
include("mpdf/mpdf.php");
$mpdf=new mPDF('win-1252','A4','','',15,10,16,10,10,10);//A4 page in portrait for landscape add -L.
$mpdf->debug = true;
$mpdf->useOnlyCoreFonts = true;    // false is default
$mpdf->SetDisplayMode('fullpage','two');
//$mpdf->showImageErrors = true;
ob_start();
date_default_timezone_set("Asia/Kolkata");
$date = date("d-m-Y");
$orderid = $_GET['orderid'];
$sqlsta= "SELECT * FROM `orders` WHERE `orderid`='".$orderid."'";
$ressta=mysqli_query($con,$sqlsta);
if ($rowsta=mysqli_fetch_array($ressta))
{
    $dateplaced=$rowsta['dateplaced'];
    $orderstatus=$rowsta['status'];
	$timeplaced=$rowsta['timeplaced'];
    $deaddress = $rowsta['deaddress'];
    $biaddress = $rowsta['biaddress'];


    $in_skirt7 = $rowsta['in_skirt7'];
    $in_skirt8 = $rowsta['in_skirt8'];
    $sareefalls = $rowsta['falls'];
    $blousedesign = $rowsta['stitching'];
    $tailor_total_cost = $rowsta['tailor_total_cost'];


    $tailordetailsprice='';

//    if($in_skirt7 != "0" || $sareefalls !="0")
//     {
//     $tailordetailsprice = $tailordetailsprice.'<div style="float:right;"><b>Tailoring Price</b></div>';
//     }


//     if($in_skirt7 != "0")
//     {
//     $tailordetailsprice =$tailordetailsprice.'<div style="float:right;"> Inskirt - 7 Part Price- '.$in_skirt7.'</div>';
//     }

//     if($in_skirt7 != "0")
//     {
//     $tailordetailsprice =$tailordetailsprice.'<div style="float:right;"> Inskirt - 8 Part Price- '.$in_skirt8.'</div>';
//     }

//     if($sareefalls != "0")
//     {
//     $tailordetailsprice =$tailordetailsprice.'<div style="float:right;"> Saree Falls Price- '.$sareefalls.'</div>';
//     }

//     if($blousedesign != "0")
//     {
//     $tailordetailsprice =$tailordetailsprice.'<div style="float:right;"> Blouse Design Price- '.$blousedesign.'</div>';
//     }

//     if($tailor_total_cost != "0")
//     {
//     $tailordetailsprice =$tailordetailsprice.'<div style="float:right;"> Tailor Total Price- '.$tailor_total_cost.'</div>';
//     }
    
    if($orderstatus == 'intransit'){
        $date1 = $rowsta['intransit'];
    }
    else if($orderstatus == 'placed'){
        $date1 = $rowsta['dateplaced'];
    }
    else if($orderstatus == 'packed'){
        $date1 = $rowsta['datepacked'];
    }
    else if($orderstatus == 'delivered'){
        $date1 = $rowsta['datedelivered'];
    }
    else if($orderstatus == 'cancelled'){
        $date1 = $rowsta['cancelled'];
    }

    
    
    $uid=$rowsta['uid'];
    $sql= "SELECT * FROM `users` WHERE `uid`='".$uid."'";
    $res=mysqli_query($con,$sql);
    if ($row=mysqli_fetch_array($res))
    {
        $cusemail=$row['email'];
        $cusname=$row['name'];
        $cusmobile=$row['country_code'].$row['mobile'];
        if($cusname == ''){
            $cusname=$row['email'];
        }
    }
    $stp_username=$rowsta['stp_username'];
    $stp_email=$rowsta['stp_email'];
    $stp_mobile=$rowsta['stp_mobile'];
    $deliverymethod=$rowsta['deliverymethod'];
    $deliveryaddressid=$rowsta['deliveryaddress'];
	//Generate Billing Address
	$bAddrJson = json_decode($biaddress);
	$door=rawurldecode(str_replace("%2B","%20",$bAddrJson->bdoor));
	$street=rawurldecode(str_replace("%2B","%20",$bAddrJson->bstreet));
	$area=rawurldecode(str_replace("%2B","%20",$bAddrJson->barea));
	$landmark1=rawurldecode(str_replace("%2B","%20",$bAddrJson->blandmark));
	$city=rawurldecode(str_replace("%2B","%20",$bAddrJson->bcity));
	$state=rawurldecode(str_replace("%2B","%20",$bAddrJson->bstate));
	$pincode=rawurldecode(str_replace("%2B","%20",$bAddrJson->bpincode));
	$country=rawurldecode(str_replace("%2B","%20",$bAddrJson->bcountryname));
	$mobile=rawurldecode(str_replace("%2B","%20",$bAddrJson->bmobile));
	$deliveryname=rawurldecode(str_replace("%2B","%20",$bAddrJson->bname));
	$landmarkdisp1 = '';
	if(trim($landmark1) != ''){
            $landmarkdisp1 = $landmark1.', ';
        }

         //get mobile country code
    $add_sql= "SELECT * FROM `address` WHERE `uid`='".$uid."' AND name='".$deliveryname."' AND door='".urlencode($door)."'";
    $add_res=mysqli_query($con,$add_sql);
    if ($adrow=mysqli_fetch_array($add_res))
    {
        $bil_countrycode=$adrow['countrycode'];
    }

	$billingaddress = $deliveryname.'<br>'.$bil_countrycode.$mobile.'<br>'.$door.', '.$street.',<br>'.$area.', '.$landmarkdisp1.'<br>'.$city.', '.$state.'<br>'.$country.' - '.$pincode;

   
    
	//Generate Shipping Address
	$dAddrJson = json_decode($deaddress);
	$door=rawurldecode(str_replace("%2B","%20",$dAddrJson->bdoor));
	$street=rawurldecode(str_replace("%2B","%20",$dAddrJson->bstreet));
	$area=rawurldecode(str_replace("%2B","%20",$dAddrJson->barea));
	$landmark2=rawurldecode(str_replace("%2B","%20",$dAddrJson->blandmark));
	$city=rawurldecode(str_replace("%2B","%20",$dAddrJson->bcity));
	$state=rawurldecode(str_replace("%2B","%20",$dAddrJson->bstate));
	$pincode=rawurldecode(str_replace("%2B","%20",$dAddrJson->bpincode));
	$country=rawurldecode(str_replace("%2B","%20",$dAddrJson->bcountryname));
	$mobile=rawurldecode(str_replace("%2B","%20",$dAddrJson->bmobile));
	$deliveryname=rawurldecode(str_replace("%2B","%20",$dAddrJson->bname));
	$landmarkdisp2 = '';
	if($landmark2 != ''){
            $landmarkdisp2 = $landmark2.', ';
        }

          //get mobile country code
    $add_sql2= "SELECT * FROM `address` WHERE `uid`='".$uid."' AND name='".$deliveryname."' AND door='".urlencode($door)."'";
    $add_res2=mysqli_query($con,$add_sql2);
    if ($adrow=mysqli_fetch_array($add_res2))
    {
        $del_countrycode=$adrow['countrycode'];
    }

	// $deliveryaddress = $deliveryname.'<br>'.$mobile.'<br>'.$door.', '.$street.',<br>'.$area.', '.$landmarkdisp2.'<br>'.$city.', '.$state.'<br>'.$country.' - '.$pincode;
    $deliveryaddress = '';
    if($deliveryname!=''){
         $deliveryaddress = $deliveryname;
    }
    if($mobile!=''){
        $deliveryaddress .= '<br>'.$del_countrycode.$mobile;
   }
   if($door!=''){
    $deliveryaddress .= '<br>'.$door;
    }
    if($street!=''){
        $deliveryaddress .= ', '.$street;
    }
    if($area!=''){
        $deliveryaddress .= ',<br>'.$area;
    }
    if($landmarkdisp2!=''){
        $deliveryaddress .= ', '.$landmarkdisp2;
    }
    if($city!=''){
        $deliveryaddress .= '<br>'.$city;
    }
    if($state!=''){
        $deliveryaddress .= ', '.$state;
    }
    if($country!=''){
        $deliveryaddress .= '<br>'.$country;
    }
    if($pincode!=''){
        $deliveryaddress .= ' - '.$pincode;
    }

    if (strlen($billingaddress)< 10) {
		$billingaddress = $deliveryaddress;
	}
	
    //pricing
    $couponcode=$rowsta['coupon'];
    $coupon_discount=$rowsta['coupon_discount'];
    if($couponcode == ''){
        $couponcode='Nil';
    }
    if($coupon_discount == ''){
        $coupon_discount=0;
    }
    $cdiscount = $rowsta['cdiscount'];
    $shipmentcost = $rowsta['shipmentcost'];
    $points_remption=$rowsta['green_card_redeem_points'];
    if($shipmentcost == '' || $shipmentcost == 'NaN'){$shipmentcost = 0;}


    if($cdiscount == '' || $cdiscount == 'NaN'){
        $cdiscount = 0;
    }
    if($coupon_discount != ''){

        // $cdiscount = $cdiscount+$coupon_discount;
        $cdiscount = $coupon_discount;

    }

    //payment and shipping
    $paymentmethod=trim($rowsta['paymentmethod']);
    if($paymentmethod == 'cod'){
        $paymenttype = 'Cash on Delivery';
    }
    else if($paymentmethod == 'hdfc'){
        $paymenttype = 'RazorPay International Payments';
    }
    else if($paymentmethod == 'hdfcin'){
        $paymenttype = 'RazorPay India Payments';
    }
    else if($paymentmethod == 'paypal'){
        $paymenttype = 'Paypal Payments';
    }
    else if($paymentmethod == 'cc'){
        $paymenttype = 'CC Avenue Payments';
    }
    else if($paymentmethod == 'sezzle'){
        $paymenttype = 'Sezzle';
    }
    else if($paymentmethod == 'glocalpay' || $paymentmethod == 'payglocal'){
        $paymenttype = 'PayGlocal Payments';
    }
    else if($paymentmethod == 'snapmint'){
        $paymenttype = 'Snapmint Payments';
    }

    if($shipmentcost == '' || $shipmentcost == 'NaN' || $shipmentcost == 0){$shipmenttype = 'Free Shipping - Free Total Shipping Charges: Rs.0.00';}
    else{$shipmenttype = 'Federal Express - International Priority Total Shipping Charges: Rs.'.$shipmentcost;}
}

$od_storeid = array();
$sql2 = "SELECT * FROM orderdetails WHERE orderid='$orderid' AND uid='$uid' ORDER BY sno";
            $res2 = mysqli_query($con, $sql2); 
            while($row2 = mysqli_fetch_array($res2)){
                // if($row2['store_id']!=''){
                     array_push($od_storeid,$row2['store_id']);
                // }
            }

            if(in_array('ST01',$od_storeid) || in_array('',$od_storeid)){
                $address_data = 'POTHYS PRIVATE LIMITED<br>
                T.NAGAR, CHENNAI <br>
                TAMIL NADU, INDIA<br>
                enquiry@pothys.com<br>';
                $gst_det = 'GSTIN : 33AAHCP7473N1ZT<br>
                CIN : U74130KA2010PTC052192<br>
                PAN : AAHCP7473N<br>
                IEC CODE : 0414045131<br>
                AD CODE : 0007347<br>
                Account Number : 41785514337<br>
                Contact No : +91-8939593990/ 96<br>';
            }else{
                $address_data = 'Pothys Retail Private Limited<br>
                KC 65/4875/ A-B <br>
                Banerji Road, <br>
                Kaloor, <br>
                Kochi,  <br>
                Ernakulam, <br>
                Kerala - 682017 <br>';
                $gst_det = 'GSTIN : 32AALCP2218B1ZZ<br>
                Contact No : +91-7736386667<br>';
        }
        $bill_address = 'Billing Address : ';
        $deli_address = 'Delivery Address : ';

        if($deliverymethod=='2'){
            $bill_address = '';
            $billingaddress='';
            $deli_address = 'Store Address : ';
        }

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pothy's Bill</title>    
    <style>
        
    table {
        width: 100%;
    }
    
     table td {
        padding: 5px;
        vertical-align: top;
    }
    
     table .table-right {
        text-align: right;
    }
    
     table tr.top table td {
        padding-bottom: 20px;
    }
    
     table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }
    
     table tr.information table td {
        padding-bottom: 40px;
    }
    
     table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    
     table tr.details td {
        padding-bottom: 20px;
    }
    
     table tr.item td{
        border-bottom: 1px solid #eee;
    }
    
     table tr.item.last td {
        border-bottom: none;
    }
    
     table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    .fsize{
        font-size: 0.75rem;
        font-style: italic;
    }
    
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }
    
    .rtl table {
        text-align: right;
    }
    
    .rtl table tr td:nth-child(2) {
        text-align: left;
    }

    .row:after {
        content: "";
        display: table;
        clear: both;
    }
    .column {
        float: left;
        width: 45%;
        padding: 10px; /* Should be removed. Only for demonstration */
    }
    .fa
    {        
        display: inline-block;
        font-family: FontAwesome;
        font-style: normal;
        font-weight: normal;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    </style>
</head>

<body>
    <div class="fsize">
        <p style="float: left; width: 10%; text-align: left;"><?php echo date_format(date_create($date),"d/m/Y"); ?></p>
        <p style="float: left; width: auto; text-align: center; font-size: x-large; margin-left:-100px;" >Order Form</p>
        <p style="float: left; width: auto; text-align: center; font-size: large; margin-top:-8px;margin-left:-35px;" >#<b><?php echo $orderid; ?></b></p>
    </div>
    <div class="row">
        <div class="column" align="center">
            <img src="https://www.pothys.com/images/logos/logo_4.png" style="float: left;width:100%; max-width:100px; max-height: 100px;">
<!--            <span>POTHYS</span>-->
        </div>
    </div>
    <div class="row">
        <div class="column">
            Order Id : <?php echo $orderid; ?><br>
            Order Date: <?php echo $dateplaced; ?>,<?php echo $timeplaced; ?> <br>
            Order Status: <?php echo $orderstatus; ?><br>
            <!-- Invoice ID: <?php //echo substr($orderid, 4); ?><br>
            Invoice Date: <?php //echo $date1; ?><br> -->
        </div>
        <div class="column" style="text-align: right;">            
            <?php echo $cusname; ?><br>
            <?php echo $cusemail; ?><br>
            <?php echo $cusmobile; ?><br>
            <?php if($deliverymethod=='2'){ ?>
                <b>Store Pickup User</b><br>
            <?php echo $stp_username; ?><br>
            <?php echo $stp_email; ?><br>
            <?php echo $stp_mobile; ?><br>

            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="column">
        <?php echo $address_data ?>
        </div>
        <div class="column" style="text-align: right;">            
        <?php echo $gst_det ?>
        </div>
    </div>
    <div class="row">
        <div class="column">
        <b><?php echo $bill_address ?></b><br>
            <?php echo $billingaddress?><br>
        </div>
        <div class="column" style="text-align: right;">
        <b><?php echo $deli_address ?></b><br>	
            <?php echo $deliveryaddress?><br>
        </div>
    </div>
    <table cellpadding="0" cellspacing="0">
            
            <tr class="heading">
                <td>
                    Item Name
                </td>
				<!-- <td>
                    Image
                </td> -->
                <!--<td>
                    code
                </td>-->
				 <td>
                    Size
                </td>
                <td>
                    Quantity
                </td>
                <td>
                    MRP Price
                </td>
                <td>
                    Selling Price
                </td>
                <td>
                   Tailor Cost
                </td>
                <td>
                    Total Saving
                </td>
                <td>
                    CGST
                </td>
                <td>
                    SGST
                </td>
                <td>
                    Total value
                </td>
            </tr>
            

            <?php
            if(isset($_COOKIE['store_id']) && $_COOKIE['store_id']=='ST02'){
                $check_storeid = " store_id='ST02' ";
              }else{
               $check_storeid = " (store_id='ST01' OR store_id='ST02' OR store_id='') ";
             }
            $cgst = 0;
            $sgst = 0;
            $sql2 = "SELECT * FROM orderdetails WHERE orderid='$orderid' AND uid='$uid' ORDER BY sno";
            $res2 = mysqli_query($con, $sql2); 
            while($row2 = mysqli_fetch_array($res2)){
                $productid = $row2['productid'];
                $quantity = $row2['quantity'];
                $productname = urldecode($row2['prodname']);
                $imagepath1 = $row2['prodimage'];
				$prodsize = $row2['prodsize'];
                $sprice = $row2['price'];
                $exact_selling_price = $row2['price'];
                $sprice_sub = $row2['price'];
                $mrp_price_selling= $row2['mrp'];

                $productcode = $row2['productcode'];
                $price = $row2['mrp'];
                
                $sql3 = "SELECT * FROM product WHERE productid='$productid'";
                $res3 = mysqli_query($con, $sql3); 
                if($row3 = mysqli_fetch_array($res3)){
                    $category = $row3['category'];
                    $subcategory = $row3['subcategory'];
                    if($productcode=="")
                    {
                        $productcode=$row3['productcode'];
                    }
                }
				if (strpos($productname, $productcode) === FALSE) 
				{
				   $productname = $productname." ".$productcode;
				}
				$tailordetails = '';
                $total_tailor_cost='';
                $sql4 = "SELECT * FROM tailored_items WHERE productid='$productid' AND (orderid='$orderid' OR temporderid='$orderid') ORDER BY `sno` DESC LIMIT 1";
                $res4 = mysqli_query($con, $sql4); 
                if($row4 = mysqli_fetch_array($res4)){
                    $tailorcost = (float)$row4['cost'];
                    $tailor_values=$row4['tailor_values'];
					$imprintname=trim($row4['imprintname']);
                    $total_tailor_cost= (float)$row4['cost'];
                    $taxvalue=(float)$row4['taxvalue'];
					$addnotes=trim($row4['addnotes']);
					$giftmessage=trim($row4['giftmessage']);
                    $tailorarray=json_decode($tailor_values, true);
                    $tailordetails = '<div><b>Tailoring Details - '.$productcode.'</b></div>';
                    foreach($tailorarray as $key => $value) {
                        $sql5 = "SELECT * FROM `tailormode_options` WHERE sno='".$value."'";
                        $res5 = mysqli_query($con,$sql5);
                        $tailormode_options_count=mysqli_num_rows($res5);
                        if($tailormode_options_count>0)
                        {
                           if($row5 = mysqli_fetch_array($res5)){
                              if($row5['type'] == "image"){
                                 $tailordetails =$tailordetails.'<div> Design Type - '.$row5['tname'].'</div>';
                                 $tailordetails =$tailordetails.'<div><img src="'.$row5['measurement'].'" style="width:150px;height:auto;"><div>';
                       
                              }
                              else{
                                 $tailordetails =$tailordetails.'<div>'.$row5['option_type'].' - '.$row5['measurement'].' '.$row5['dimension'].'</div>';
                              }
                           }
                        }
                        else
                        {
                           $sql5 = "SELECT * FROM `tailormode_options_backup` WHERE sno='".$value."'";
                       
								$res5 = mysqli_query($con,$sql5);
                        $tailormode_options_count=mysqli_num_rows($res5);
                        if($tailormode_options_count>0)
                        {
                           if($row5 = mysqli_fetch_array($res5)){
                              if($row5['type'] == "image"){
                                 $tailordetails =$tailordetails.'<div> Design Type - '.$row5['tname'].'</div>';
                                 $tailordetails =$tailordetails.'<div><img src="'.$row5['measurement'].'" style="width:150px;height:auto;"><div>';
                       
                              }
                              else{
                                 $tailordetails =$tailordetails.'<div>'.$row5['option_type'].' - '.$row5['measurement'].' '.$row5['dimension'].'</div>';
                              }
                           }
                        
                        }
                     }
                    }
					$tailordetails =$tailordetails.'<br>';
					if($imprintname != "")
					{
						$tailordetails =$tailordetails.'<div> Imprint Name - '.$imprintname.'</div>';
					}
					if($addnotes != "")
					{
						$tailordetails =$tailordetails.'<div> Additional Notes - '.$addnotes.'</div>';
					}
					if($giftmessage != "")
					{
						$tailordetails =$tailordetails.'<div> Gift Message - '.$giftmessage.'</div>';
					}
                    if($taxvalue > 0)
                    {
                        $tailordetails =$tailordetails.'<div> Tailoring Tax - '.$taxvalue.'</div>';
                    }
                    $sprice_sub = $sprice + $tailorcost;
					$price =  $sprice;

                }
                if($tailordetails=='')
                {
                    
                    $tailoredcost=$row2['total_tailoring_amount'];
                      $tailor_total_cost=$tailor_total_cost;
                      $tailor_values=$row2['tailoring_details'];
                      $imprintname=trim($row2['imprintname']);
                      $addnotes=trim($row2['addnotes']);
                      $giftmessage=trim($row2['giftmessage']);
                      $taxvalue=trim($row2['tailor_tax']);
                      $total_tailor_cost = (float)$row2['total_tailoring_amount'];



                      $sprice_sub = $sprice + $total_tailor_cost;
                      // $price = $price + $tailorcost;
                      $sprice=$sprice;

                      // $tailorarray=explode(",",$tailor_values);
                      // print_r($tailorarray[0]);
                      $tailorarray=json_decode($tailor_values, true);
                      // print_r($tailorarray);
                      $tailordetails = '';
                      
                      // echo count($tailorarray);
                      // echo $tailorarray['designer blouse'];
                      foreach($tailorarray as $key => $value) {
                            // echo "$key is at $value";
                              $sql5 = "SELECT * FROM `tailormode_options` WHERE sno='".$value."'";
                              $res5 = mysqli_query($con,$sql5);
                              if($row5 = mysqli_fetch_array($res5)){
                                if($row5['type'] == "image"){
                                    $tailordetails =$tailordetails.'<div> Design Type - '.$row5['tname'].'</div>';
                                  $tailordetails =$tailordetails.'<div><img src="'.$row5['measurement'].'" style="width:150px;height:auto;"><div>';
                                }
                                else{
                                  $tailordetails =$tailordetails.'<div>'.$row5['option_type'].' - '.$row5['measurement'].' '.$row5['dimension'].'</div>';
                                }
                              }
                            }
                            $tailordetails =$tailordetails.'<br>';
                            if($imprintname != "")
                            {
                              $tailordetails =$tailordetails.'<div> Imprint Name - '.$imprintname.'</div>';
                            }
                            if($addnotes != "")
                            {
                              $tailordetails =$tailordetails.'<div> Additional Notes - '.$addnotes.'</div>';
                            }
                            if($giftmessage != "")
                            {
                              $tailordetails =$tailordetails.'<div> Gift Message - '.$giftmessage.'</div>';
                            }
                            if($tailor_total_cost != "0")
              {
                $tailorprice =$tailorprice.'<div>  Tailor Cost - '.$tailor_total_cost.'</div>';
              }
              if($taxvalue != ""  && $taxvalue!="0.00" && $taxvalue!="0" )
                              {
                                $tailordetails =$tailordetails.'<div> Tailoring Tax - '.$taxvalue.'</div>';
                              }
                           
                }
                $mrp_price_selling_quan=abs($mrp_price_selling-$exact_selling_price);
                $savingPrice = abs($mrp_price_selling_quan*$quantity);
                $qprice = abs($quantity * $sprice);     
                $qprice_sub = abs($quantity * $sprice_sub);                
                $subtotal = $subtotal+$qprice_sub;           
              
				$commentsdiv = "";	
                 $sql44 = "SELECT * FROM addressinformation WHERE orderid='".$orderid."' ORDER BY `sno` DESC";	
                $res44 = mysqli_query($con, $sql44);	
                while($row44 = mysqli_fetch_array($res44)){	
                  $information = urldecode($row44['information']);	
                  $dateinfo= $row44['dateinfo'];	
                  $valuetoappend = '<div class="comment mb-2 row"><div class="comment-content col-md-11 col-sm-10 col-xs-12"><h6 class="small comment-meta" style="margin-left:580px;margin-bottom:-15px;"><span style="padding-left: 7px;"></span>'.$dateinfo.'</h6><div class="comment-body"><p><ul><li>'.$information.'</li></ul></p></div></div></div>';	
                  $commentsdiv = $commentsdiv.$valuetoappend;	
                }
                $stag='';
                if(((isset($_COOKIE['store_id']) && $_COOKIE['store_id']=='ST01') || !isset($_COOKIE['store_id'])) && $row3['store_id']=='ST02'){
                  $stag = '<div style="font-weight:600;">( Kerala Collection )</div>';
                }
				if (@getimagesize($imagepath1)) {
					
            ?>

            <tr class="item">
                <td  style="text-align:left">
                    <img src="<?php echo $imagepath1;?>" style="width: 10%;"><br><br>
                    <?php echo $productname;?><br>
                    <?php echo $stag;?><br>
                </td>
			
                <!--<td>
                    <?php echo $productcode;?>
                </td>-->
				<td  style="text-align:center">
                    <?php echo "Size - ".$prodsize;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $quantity;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $mrp_price_selling;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $sprice;?>
                </td>
                <td style="text-align:center">
                    <?php echo $total_tailor_cost;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $savingPrice;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $cgst;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $sgst;?>
                </td>
                <td  style="text-align:center">
                    <?php echo number_format((float)$sprice_sub*$quantity, 2, '.', '');?>
                </td>
            </tr>
            <?php
				}
				else
				{
					?>
					<tr class="item">
                <td  style="text-align:center">
                    <?php echo $productname;?><br>
                </td>
				<!-- <td>
                    
                </td> -->
                <!--<td>
                    <?php echo $productcode;?>
                </td>-->
				<td  style="text-align:center">
                    <?php echo "Size - ".$prodsize;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $quantity;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $mrp_price_selling;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $sprice;?>
                </td>
                <td style="text-align:center">
                    <?php echo $total_tailor_cost;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $savingPrice;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $cgst;?>
                </td>
                <td  style="text-align:center">
                    <?php echo $sgst;?>
                </td>
                <td  style="text-align:center">
                    <?php echo number_format((float)$sprice*$quantity, 2, '.', '');?>
                </td>
            </tr>
					<?php
				}
			if($tailordetails != "")
			{
				echo '<tr class="item"><td colspan="10">'.$tailordetails.'</td></tr>';
			}
            }
            if($tailordetailsprice !="0"){

                echo '<tr class="item"><td colspan="10">'.$tailordetailsprice.'</td></tr>';
            }
			if($couponcode != null && $couponcode != '' && $couponcode != 'Nil')
			{
                $ordertotal = $subtotal - $cdiscount + $shipmentcost -(float)$points_remption;
			}
			else
			{
				$ordertotal = $subtotal + $shipmentcost -(float)$points_remption;
			}
            ?>
            
            <tr class="total">
                <td style="font-weight: bold;">Coupon Code used</td>
                <td>
                    <?php 
                        echo $couponcode;
                    ?>
                </td>
                <td style="font-weight: bold;">Coupon Discount</td>
                <td>
                    <?php 
                        echo number_format((float)$cdiscount, 2, '.', '');
                    ?>
                </td>
                <td style="font-weight: bold;">Shipping Amount</td>
                <td>
                    <?php 
                        echo number_format((float)$shipmentcost, 2, '.', '');
                    ?>
                    </td>
                    <td style="font-weight: bold;">Points Redemption</td>
                <td>
                    <?php 
                        echo $points_remption;
                    ?>
                </td>
                <td style="font-weight: bold;">Total</td>
                <td>
                    <?php 
				        echo number_format((float)$subtotal, 2, '.', '');
                    ?>
                </td>
            </tr>
            <br>
        </table>
		<br>
		 <div class="row">
        <div style="text-align: right;">
            <b> Net Total :</b><?php echo number_format((float)$ordertotal, 2, '.', ''); ?>
        </div>
        <br>
    </div>
        <div class="row">
            <div class="column">
                PAYMENT INFORMATION : <br>
                <?php echo $paymenttype?><br>
            </div>
            <div class="column" style="text-align: right;">
                SHIPPING INFORMATION : <br>
                <?php echo $shipmenttype?><br>
            </div>
        </div>
        <div class="row">
            <br><br>
            <div style="text-align: right;">
                Signature
            </div>
            <div class="fsize">
                <!-- <p style="text-align: center;">As per Section 31 of CGST Act read with Rules, invoice is issued at the point of delivering the goods</p> -->
            </div>
        </div>
		<div class="panel panel-default">	
      <!-- <div class="panel-heading" style="font-size: 22px;">Comments</div>	 -->
      <!-- <div class="panel-body">	
         <fieldset>	
            <div class="form-group">	
                <div class="col-sm-10">	
                  <div>	
                     <div class="container">	
                        <div class="row">	
                           <div class="comments col-md-9">	
                              <div class="row">	
                                 	
                                	
                                	
                              <div id="comments">	
                                 <?php //echo $commentsdiv; ?>	
                              </div>	
                              </div>	
                           </div>	
                        </div>	
                     </div>	
                  </div>	
               </div>	
            </div>	
         </fieldset>	
      </div>	 -->
   </div>
</body>
</html>
<?php 
try
{
$invocename = "TAX/INVOICE/".$orderid."/POTHYS.pdf";
$html = utf8_encode(ob_get_contents());
ob_end_clean();
// send the captured HTML from the output buffer to the mPDF class for processing
$mpdf->WriteHTML($html);
//$mpdf->SetProtection(array(), 'user', 'password'); uncomment to protect your pdf page with password.
$mpdf->Output( $invocename, 'I');
}
catch (customException $e) {
  //display custom message
  echo $e->errorMessage();
}
mysqli_close($con);
?>
<?php
}
?>
