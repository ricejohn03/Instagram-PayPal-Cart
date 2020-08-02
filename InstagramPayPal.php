
                        
<?php

function get_instagram($user_id="your User id",$count="Int value for how many",$access_token='your token',$width=190,$height=190){

    $paypal_business_value='you paypal business value'; 
    $url = 'https://api.instagram.com/v1/users/'.$user_id.'/media/recent/?access_token='.$access_token.'&count='.$count;

    
        $jsonData = json_decode((file_get_contents($url)));
        file_put_contents($cache,json_encode($jsonData));
        $count = 1;

        // This checks the open file to see if store is open
        $file = fopen("open.txt","r");

           while(! feof($file))
           {
            $open_var = fgets($file);
           }

          fclose($file);

    foreach ($jsonData->data as $key=>$value) {

        //$url = $value->url;
        $title = $value->caption->text;

        //Checks to see if the #onlinestore is in text string && to check if it has been #sold to remove it from store
        if (strpos($title,'#onlinestore') !== false) { 
            preg_match('/\$([0-9]+[\.]*[0-9]*)/', $value->caption->text, $match); // looks for dollor amount in string 
            $dollar_amount = $match[1];

            $uncut = $value->caption->text; // Cuts sting into array by "." 
            $t1=explode('.',$uncut);
            $product_title = $t1[0];


            // Create Individual Product Pages Based on Title 
            $page_title = substr($product_title, 0, strpos($product_title, "$"));
            $html_path = preg_replace('/\s+/', '', $product_title);       // remove white space from string
            $html_path = substr($html_path, 0, strpos($html_path, "$"));  // remove text after $ char
            $html_path = strtolower($html_path);                          // convert string to lower case
            $html_full_path = 'store_pages/'.$html_path.'.html';          // url link to any given store page


            $product_description = substr($value->caption->text, 0, strpos($value->caption->text, "#"));
            //$product_description = strstr($product_description, $dollar_amount);
            $product_description = str_replace($page_title,"", $product_description);
            $product_description = str_replace($dollar_amount,"", $product_description);
            $product_description = str_replace("$","", $product_description);
            //Pay Pal button Logic depending on if #sold in active : if so then do not display the button to buy.

             if (strpos($title,'#onlinestore') !== false && strpos($title,'#sold') != true){

                    $paypal_button = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                          <input type="hidden" name="cmd" value="_xclick">
                          <input type="hidden" name="business" value="reillyclementine@gmail.com">
                          <input type="hidden" name="lc" value="US">
                          <input type="hidden" name="item_name" value="'.$page_title.'">
                          <input type="hidden" name="amount" value="'.$dollar_amount.'">
                          <input type="hidden" name="currency_code" value="USD">
                          <input type="hidden" name="button_subtype" value="services">
                          <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">
                          <input type="image" src="../buy.jpg" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                          <img class="text-center" alt="" border="0" src="../buy.jpg" width="1" height="1">
                          </form>';
                if($open_var !== 'open'){
                                    $paypal_button = '<h2>Store Is currently Closed</h2>';

                }
                
                    
            }

            // Now to Create each page in the for loop with fopen & write useing 'fwrite' 
             $myfile = fopen('store_pages/'.$html_path.'.html', "w") or die("Unable to open file!");
            // Set $txt var to the html file you want for each product and ref vars: $page_title, $value->images->standard_resolution->url, $product_description, $dollar_amount, $paypal_button 
             $txt = '';
             fwrite($myfile, $txt);
             fclose($myfile);

             
}
   
    return $result;
    return $count;

}

echo get_instagram();
?>
             