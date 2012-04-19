<?php
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=document_name.doc");
 if (!empty($dealusers)) {
	  $coupon_list ="<table style='font-family:Calibri,arial;' align='left'>";
			$coupon_list .="<tr>
					<td width='60%'><b>". __l('Top Code')."</b></td><td><b>". __l('Bottom Code')."</b></td></tr>";


			foreach($dealusers as $dealuser) {
	                $coupon_array = array();
                $unique_coupon_array = array();
                foreach($dealuser['DealUserCoupon'] as $deal_user_coupon) {
                    $coupon_array[] = $deal_user_coupon['coupon_code'];
                    $unique_coupon_array[] = $deal_user_coupon['unique_coupon_code'];
                }
				$coupon_code = !empty($coupon_array) ? implode(',', $coupon_array) : '';
				$unique_coupon_array = !empty($unique_coupon_array) ?  implode(',', $unique_coupon_array) : '';
				$coupon_list .="<tr><td>".$coupon_code."</td><td>".$unique_coupon_array."</td></tr>";
            }		
			$coupon_list .="</table>";
     }
$deal_enddate = strtotime(date("Y-m-d", strtotime($deal['Deal']['end_date'])) . " +5 day");
$coupon_expiry_date = strtotime(date("Y-m-d", strtotime($deal['Deal']['coupon_expiry_date'])) . " +5 day");
echo "<html>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
echo "<body >";
echo "<table border='0' width='100%' style='border:1px solid #fff;' cellpadding='0' cellspacing='0'>";
echo "<tr><td  width='30%' ><img src='".Router::url('/', true)."/img/document-logo.png' width='200' height='100' align='left' /></td><td align='left'  width='70%' ><h2 style='font-family:Calibri,arial;'>Merchant Close-Out Document</h2></td></tr>";
echo "</table><br/>";


echo "<table border='1' width='100%' cellpadding='5' cellspacing='0' style='font-family:Calibri,arial;'>";
echo "<tr><td><b>Merchant Name:</b></td><td colspan='5'>".$deal['Company']['name']."</td></tr>";
echo "<tr><td><b>Deal Name:</b></td><td  colspan='5'>".$deal['Deal']['name']."</td></tr>";
echo "<tr><td><b>Quantity Sold:</b></td><td  colspan='5'>".$deal['Deal']['deal_user_count']."</td></tr>";
$total_revenue = ($deal['Deal']['deal_user_count'] * $deal['Deal']['discounted_price']);
$partner_amount = $total_revenue  - $deal['Deal']['commission_percentage'];

echo "<tr><td><b>Total Revenue:</b></td><td  colspan='5'> RM ".$total_revenue." </td></tr>";
echo "<tr><td><b>Partner's:</b></td><td  colspan='5'> RM ".$partner_amount."</td></tr>";
echo "<tr><td><b>Coupon code: </b></td><td  colspan='5'>".$coupon_list."</td></tr>";
echo "<tr><td><b>Bank Details: </b></td><td  colspan='5'>Bank Name :".$deal['Company']['bank_name']."<br/>
Bank Account :".$deal['Company']['bank_account']."<br/>
Bank Register No :".$deal['Company']['bank_register_no']."<br/>
</td></tr>";
echo "</table>";

echo "<h2 style='font-family:Calibri,arial;'>Payment</h2>";
echo "<table border='1' width='100%' cellpadding='5' cellspacing='0' style='font-family:Calibri,arial;'>";
echo "<tr><td>&nbsp;</td><td><b>Date</b></td><td><b>Value</b></td></tr>";
echo "<tr><td>First Payment</td><td>".date("d-m-Y",$deal_enddate)."</td><td> RM ".($partner_amount/2) ."</td></tr>";
echo "<tr><td>Final Payment</td><td>".date("d-m-Y",$coupon_expiry_date)."</td><td>RM  ".($partner_amount/2) ."</td></tr>";
echo "</table>";

echo "</body>";
echo "</html>";

?>
