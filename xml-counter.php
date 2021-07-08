<?php
/**
* Plugin Name: XMLCounter
* Description: Simple XML Counter
* Version: 1.0.0
* Requires at least: 5.7
* Requires PHP:
* Author: Vaggelis Karanikolos
*/

require "library.php";

//add to wordpress menu
add_action('admin_menu','xml_counter_add_to_menu');
function xml_counter_add_to_menu(){
    add_menu_page('XMLCounter','XMLCounter','administrator',__FILE__,'xmlcounter_admin_page',plugins_url('/images/icon.png',__FILE__));
    add_action('admin_init','xml_counter_fields');
}

//register option fields
function xml_counter_fields(){
    register_setting('xml_counter_group_data', 'new_xml_counter_date');
    register_setting('xml_counter_group_data', 'old_xml_counter_date');
    register_setting('xml_counter_group_data', 'new_xml_counter_results');
    register_setting('xml_counter_group_data', 'old_xml_counter_results');
}

// register the ajax action for authenticated users
add_action('wp_ajax_run_the_counter', 'run_the_counter');

// register the ajax action for unauthenticated users
add_action('wp_ajax_nopriv_run_the_counter', 'run_the_counter');

//the ajax counter action
function run_the_counter(){
        $counter=null;
        $fileURL=  $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/feed/skroutz.xml';
        $counter= runTheCounter($fileURL);
        if(!empty($counter)){
            saveValues($counter);
            resultsChecker();
        } 
}

//make a custom cron
function cron_set_once_a_day($schedules){
    $schedules['once_a_day'] = array(
        'interval'  => 60 * 60 * 24,
        'display'   => __( 'Once a day', 'textdomain' )
    );
    return $schedules;
}

//add custom cron to cronjobs
add_filter('cron_schedules', 'cron_set_once_a_day');

//register the cron event
if ( ! wp_next_scheduled( 'cron_set_once_a_day' ) ) {
    wp_schedule_event( time(), 'once_a_day', 'cron_set_once_a_day' );
}

//call cron function
add_action( 'cron_set_once_a_day', 'run_the_counter' );


function xmlcounter_admin_page (){ 
    $fileURL=  $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/feed/skroutz.xml';
    echo "<div class='wrap'>";
    echo "<h1>XML Counter</h1>";
    if(file_exists($fileURL)) { ?>
            <?php echo empty(get_option( 'new_xml_counter_date' ))? "Δεν έχει γίνει ακόμα καταμέτρηση του αρχείου. Πάτηστε το κουμπί 'Εναρξη καταμέτρησης' <br><br>" : ""; ?> 
            <?php echo (!empty(get_option( 'new_xml_counter_date' )) && empty(get_option( 'old_xml_counter_date' ))) ? "Έχει γίνει μόνο μία καταμέτρηση αρχείου. Ανανεώστε το αρχείο και πάτηστε το κουμπί 'Εναρξη καταμέτρησης' <br><br>" : ""; ?> 
            <table style="width:600px;padding-top:5%;">
                <tr>
                    <th style="text-align:left">Καταμέτρηση νέου αρχείου </th>
                    <th style="text-align:left">Καταμέτρηση παλαιού αρχείου </th>
                </tr>
                <tr>
                    <td>Ημερομηνία: <?php echo get_option( 'new_xml_counter_date' ) ?></td>
                    <td>Ημερομηνία: <?php echo get_option( 'old_xml_counter_date' ) ?></td>
                </tr>
                <tr>
                    <td>Σύνολο: <?php echo get_option( 'new_xml_counter_results' ) ?></td>
                    <td>Σύνολο: <?php echo get_option( 'old_xml_counter_results' ) ?></td>
                </tr>
            </table>
            <br>
            <br>
            <br>
            <button class="button button-primary button-large run-the-counter">Έναρξη καταμέτρησης</button>
        <?php }else{
            echo "<p style='color:red;font-size:15px;'>Το αρχείο δεν βρέθηκε. Παρακαλούμε ανεβάστε το αρχείο skroutz.xml στον φάκελο /wp-content/uploads/feed/";
        } ?>           


        <script>
            jQuery(document).ready(function() { 
                jQuery(".run-the-counter").click(function () {
                    console.log('The function is hooked up');
                    jQuery.ajax({
                        type: "POST",
                        url: "admin-ajax.php",
                        data: {
                            action: 'run_the_counter',
                        },
                        success: function (output) {
                            window.location.reload();
                        }
                    });
                });
            });

        </script>
        
    </div>
<?php } ?>