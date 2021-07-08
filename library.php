<?php
//run the xml counter
function runTheCounter($fileURL){
    if(file_exists($fileURL)){
        $counter=0;
        $products = new SimpleXMLElement(file_get_contents($fileURL));
        foreach ($products->products->product as $product){
            $counter++;
        }
        return $counter;
    }
}

//save the results
function saveValues($counter){
    if (!empty(get_option( 'new_xml_counter_date' )) || !empty(get_option( 'old_xml_counter_date' ))){
        update_option( "old_xml_counter_date", get_option( 'new_xml_counter_date' ));
        update_option( "old_xml_counter_results", get_option( 'new_xml_counter_results' ));
        update_option( "new_xml_counter_date", date("Y-m-d H:i:s") );
        update_option( "new_xml_counter_results", $counter);
    }elseif(empty(get_option( 'new_xml_counter_date' )) && empty(get_option( 'old_xml_counter_date' ))){
        update_option( "new_xml_counter_date", date("Y-m-d H:i:s") );
        update_option( "new_xml_counter_results", $counter);
    }
}

//check the results for difference
function resultsChecker(){
    $diff=null;
    if(empty(get_option('old_xml_counter_results' )) ||  empty(get_option('new_xml_counter_results'))){
        return;
    }elseif(get_option('old_xml_counter_results' ) == get_option('new_xml_counter_results')){
        return;
    }elseif(get_option('old_xml_counter_results') > get_option('new_xml_counter_results')){
       if ((get_option('old_xml_counter_results') - get_option( 'new_xml_counter_results')) / get_option('old_xml_counter_results' ) >= 0.3){
        $diff= (get_option( 'old_xml_counter_results') - get_option( 'new_xml_counter_results')) / get_option('old_xml_counter_results' );
        $notify= "μειώθηκε κατά";
       }
    }elseif(get_option('old_xml_counter_results') < get_option( 'new_xml_counter_results')){
        if (((get_option( 'new_xml_counter_results') - get_option( 'old_xml_counter_results'))/ get_option( 'old_xml_counter_results' )) >= 0.3){
         $diff=(get_option( 'new_xml_counter_results') - get_option( 'old_xml_counter_results')) / get_option( 'old_xml_counter_results' );
         $notify= "αυξήθηκε κατά";
        }

    }
    if (!empty($diff)){

        $subject= "Μεγάλη διαφορά μεταξύ προϊόντων στην καταμέτρηση του νέου XML!";
        $message= "Η διαφορά των προϊόντων $notify " . $diff * 100 .  "% ";

        $args = array(
            'role'    => 'administrator',
        );
        $users = get_users( $args );
        foreach($users as $user){
            wp_mail($user->user_email, $subject, $message);
        }

    }

    // if(($old_counter - $new_counter)/ $old_counter >= 0.3){
    //     $subject= "Τα προϊόντα ";
    //     $args = array(
    //         'role'    => 'administrator',
    //     );
    //     $users = get_users( $args );
    //     foreach($users as $user){
    //         echo $user->user_email;
    //     }


    // }

}