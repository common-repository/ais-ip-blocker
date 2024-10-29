<?php 
    /**
    * Plugin Name: AIS: IP Blocker
    * Plugin URI: 
    * Description: Blocks malicious IP Addresses, Spammers and Hackers from accessing page without compromising the performance of your WordPress Website.
    * Version: 2.2.0
    * Author: AIS Technolabs
    * Author URI: https://www.aistechnolabs.com/
    * Text Domain: ais-ip-blocker
    */
    define( 'IPBLOCKER__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    define( 'IPBLOCKER__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    function ais_ipBlocker_register_settings() {
        add_option( 'ipBlockOptionName', array('user-hit-count' => 15, 'time-in-min' => 1, 'display-content' => '', 'notification-mail' => ''));
        register_setting( 'ipBlockOptionGroup', 'ipBlockOptionName', 'ipBlockCallback' );
    }
    add_action( 'admin_init', 'ais_ipBlocker_register_settings' );

    function ais_ipBlocker_admin_actions() {
        add_options_page("IP Blocker", "IP Blocker", "manage_options", "ip-blocker", "ais_ipBlocker_plugin_options");
    }
    function ais_ipBlocker_plugin_options(){
        if(!current_user_can('manage_options')){
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        /*-- Start of Block IP --*/
       /*--- Start of include css --*/
       wp_register_style( 'ais_ipBlocker_bootstrap-css', IPBLOCKER__PLUGIN_URL.'css/bootstrap.css', false, '1.0.0');
       wp_enqueue_style('ais_ipBlocker_bootstrap-css');
       wp_register_style('ais_ipBlocker_css', IPBLOCKER__PLUGIN_URL.'css/ipBlocker.css', false, '1.0.0');
       wp_enqueue_style('ais_ipBlocker_css');
       /*--- End of include css --*/
       /*--- Start of include scripts --*/
        wp_register_script('ais_ipBlocker_dataTablesJS.min', IPBLOCKER__PLUGIN_URL.'js/jquery.dataTables.min.js', false, '1.0.0');
        wp_enqueue_script('ais_ipBlocker_dataTablesJS.min');
        wp_register_script('ais_ipBlocker_bootstrap4JS', IPBLOCKER__PLUGIN_URL.'js/dataTables.bootstrap4.min.js', false, '1.0.0');
        wp_enqueue_script('ais_ipBlocker_bootstrap4JS');
        wp_register_script('ais_ipBlocker_JS', IPBLOCKER__PLUGIN_URL.'js/ip-blocker.js', false, '1.0.0');
        wp_enqueue_script('ais_ipBlocker_JS');
        /*--- End of include scripts --*/
?>
<div class="container mt-5">
        <div class="row">
            <div class="col-sm-12">
            <h2 class="text-center text-capitalize mb-5"><?php _e("IP Blocker", 'ais-ip-blocker' ); ?></h2>
            <?php
            global $wpdb;
            $tableNmPrefix = $wpdb->prefix;
            $ipblocksTable = $wpdb->prefix.'ipBlocker';

            if(isset($_POST['ais_ipBlocker_user_ip_nonce_field']) && wp_verify_nonce($_POST['ais_ipBlocker_user_ip_nonce_field'], 'ais_ipBlocker_user_ip_action')){
                if(isset($_POST['checkValue'])){
                    $removedId = intval(base64_decode($_POST['checkValue']));
                    $getUserIPAdd = sanitize_text_field($_POST['userIPAdd']);
                    $counterInit = 1;
                    $crntTimeInit = date("Y-m-d H:i:s");
                    $updateData = array('counter' =>$counterInit, 'status' => 'unblocked', 'visitTime' => $crntTimeInit);
                    $whereData = array('id' =>$removedId);
                    $updateDB = $wpdb->update( $ipblocksTable, $updateData, $whereData);
                    if($updateDB){
                        _e('<h5 class="text-success text-center font-weight-bold mb-4 notif-msg">Successfully removed IP Address <span class="text-danger">'.$getUserIPAdd.'</span> from Banned IP list.</h5>', 'ais-ip-blocker');
                    }
                }
            } else {
                /* Invalid nonce. you can throw an error here. */
            }
            $myrows = $wpdb->get_results( "SELECT * FROM $ipblocksTable WHERE status = 'blocked'  ORDER BY id DESC" );
            ?>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12 col-md-3 nav-left-tab">
                        <div class="tab">
                            <button class="tablinks" onclick="openTab(event, 'configTab')" id="defaultOpen"><?php _e("Configuration", 'ais-ip-blocker' ); ?></button>
                            <button class="tablinks" onclick="openTab(event, 'banIPTab')"><?php _e("Banned IP List", 'ais-ip-blocker' ); ?></button>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9">
                        <div id="configTab" class="tabcontent">
                            <form name="form0" method="post" action="options.php">
                                <?php settings_fields( 'ipBlockOptionGroup' );
                                    $ipBlockOptionName = get_option('ipBlockOptionName'); ?>
                                <div class="container mt-5">
                                    <div class="form-group row border-bottom">
                                        <label for="ipBlockOptionName[user-hit-count]" class="col-sm-4 col-form-label"><?php _e("After How many url hits, You want to ban User ? (Only no. allowed. e.g. 15)", 'ais-ip-blocker' ); ?></label>
                                        <div class="col-sm-8 ">
                                        <input type="number" class="form-control-plaintext" id="ipBlockOptionName[user-hit-count]" name="ipBlockOptionName[user-hit-count]"  value="<?php echo esc_attr_e($ipBlockOptionName['user-hit-count']); ?>" min="2" max="99">
                                        </div>
                                    </div>
                                    <div class="form-group row border-bottom">
                                        <label for="ipBlockOptionName[time-in-min]" class="col-sm-4 col-form-label"><?php _e("After How many time (in minutes), You want to ban User ? (Only no. allowed. e.g. 1)", 'ais-ip-blocker' ); ?></label>
                                        <div class="col-sm-8 ">
                                        <input type="number" class="form-control-plaintext" id="ipBlockOptionName[time-in-min]" name="ipBlockOptionName[time-in-min]"  value="<?php echo esc_attr_e($ipBlockOptionName['time-in-min']); ?>" min="1" max="59">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="ipBlockOptionName[display-content]" class="col-sm-4 col-form-label"><?php _e("If you want to display text to Banned Users then write inside text-area, OR If you want to display blank page to the Banned User then keep it blank.", 'ais-ip-blocker' ); ?></label>
                                        <div class="col-sm-8 ">
                                        <textarea class="form-control" rows="5" id="ipBlockOptionName[display-content]" name="ipBlockOptionName[display-content]"><?php echo esc_attr_e($ipBlockOptionName['display-content']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row border-bottom">
                                        <label for="ipBlockOptionName[notification-mail]" class="col-sm-4 col-form-label"><?php _e("Notification E-mail Address when User banned. (If you don't want to receive mail when User banned then just keep it blank)", 'ais-ip-blocker' ); ?></label>
                                        <div class="col-sm-8 ">
                                        <input type="email" class="form-control-plaintext" id="ipBlockOptionName[notification-mail]" name="ipBlockOptionName[notification-mail]"  value="<?php echo esc_attr_e($ipBlockOptionName['notification-mail']); ?>" placeholder="<?php _e("Email Address", 'ais-ip-blocker' ); ?>">
                                        </div>
                                    </div>
                                    <?php 
                                    do_settings_sections('ipBlockOptionGroup');
                                    submit_button(); ?>
                                </div>
                            </form>
                        </div>
                        <div id="banIPTab" class="tabcontent">
                            <form name="form1" id="form1" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                <?php wp_nonce_field('ais_ipBlocker_user_ip_action', 'ais_ipBlocker_user_ip_nonce_field'); ?>
                                <div class="container mt-5">
                                    <div class="row">
                                            <div class="col-sm-12">
                                                <div class="text-danger font-weight-bold"><p><?php _e("If you want to unblock User then click on 'checkbox'.", 'ais-ip-blocker' ); ?></p>
                                                </div>
                                                <div class="table-responsive">
                                                    <table id="banned-ip-list" class="table table-striped table-bordered nowrap" style="width:95%">
                                                            <thead>
                                                                <tr>
                                                                    <th style="max-width:60px;"><?php _e("No.", 'ais-ip-blocker' ); ?></th>
                                                                    <th style="max-width:110px;"><?php _e("IP Address", 'ais-ip-blocker' ); ?></th>
                                                                    <th style="max-width:125px;"><?php _e("Banned Time (UTC)", 'ais-ip-blocker' ); ?></th>
                                                                    <th style="max-width:110px;"><?php _e("Checkbox", 'ais-ip-blocker' ); ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody> 
                                                            <?php
                                                            if($myrows){
                                                                $i = 1;
                                                                foreach($myrows as $myrow){?>
                                                                <tr>
                                                                    <td><?php echo $i; ?>.</td>
                                                                    <td><?php echo $myrow->ipAddress; ?></td>
                                                                    <td><?php echo $myrow->visitTime; ?></td>
                                                                    <td><input type="hidden" name="userIPAdd" value="<?php echo $myrow->ipAddress; ?>"><input type="checkbox" name="checkValue" data-ip="<?php echo $myrow->ipAddress; ?>" onClick="bannedCheckbox(this);" value="<?php echo base64_encode($myrow->id); ?>"></td>
                                                                </tr>
                                                                <?php    
                                                                    $i++;
                                                                }
                                                            } ?>
                                                            </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<?php } 
    add_action('admin_menu', 'ais_ipBlocker_admin_actions');
/*-- Start of Init action --*/
add_action( 'init', 'ais_ipBlocker_init_function');
function ais_ipBlocker_init_function() {
    if ( !is_admin() && ($GLOBALS['pagenow'] != 'wp-login.php') ) { 
        /*-- Start of Block IP --*/
        $client_ip = '';
        $counter = 1;
        $blockDefaultData = get_option('ipBlockOptionName');
        $blockDefaultDataUserHitCount = trim($blockDefaultData['user-hit-count']);
        $blockDefaultDataUserTimeInMin = trim($blockDefaultData['time-in-min']);
        $blockDefaultDataDisplayContent = trim($blockDefaultData['display-content']);
        $blockDefaultDataNotificationMail = trim($blockDefaultData['notification-mail']);
        if($blockDefaultDataUserHitCount){
            $blockDefaultDataUserHitCount = intval($blockDefaultDataUserHitCount);
        } else {
            $blockDefaultDataUserHitCount = intval('200');
        }
        if($blockDefaultDataUserTimeInMin){
            $blockDefaultDataUserTimeInMin = intval($blockDefaultDataUserTimeInMin);
        } else {
            $blockDefaultDataUserTimeInMin = intval('1');
        }
        if($blockDefaultDataDisplayContent){
            $blockDefaultDataDisplayContent = htmlentities($blockDefaultDataDisplayContent);
        } 
        if($blockDefaultDataNotificationMail){
            if (!filter_var($blockDefaultDataNotificationMail, FILTER_VALIDATE_EMAIL)) {
                $blockDefaultDataNotificationMail = '';
              }
        }
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $client_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
            $client_ip = $_SERVER['HTTP_X_FORWARDED'];
        } else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $client_ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if(isset($_SERVER['HTTP_FORWARDED'])) {
            $client_ip = $_SERVER['HTTP_FORWARDED'];
        } else if(isset($_SERVER['REMOTE_ADDR'])) {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        } else{
            $client_ip = 'unknown';
        }
        global $wpdb;
        $tableNmPrefix = $wpdb->prefix;
        $ipblocksTable = $wpdb->prefix.'ipBlocker';
        $crntTime = date("Y-m-d H:i:s");
        $myrows = $wpdb->get_results( "SELECT * FROM $ipblocksTable WHERE ipAddress = '$client_ip' ORDER BY id DESC" );
        if($myrows){
            $counter = $myrows[0]->counter;
            $status = $myrows[0]->status;
            $visitTime = $myrows[0]->visitTime;
            $visitTimeTempMinutes = '+'.$blockDefaultDataUserTimeInMin.' minutes';
            $visitTimeAddMinutes = date('Y-m-d H:i:s',strtotime($visitTimeTempMinutes,strtotime($visitTime)));
            if(($counter >= $blockDefaultDataUserHitCount)){
                if($status === 'unblocked'){
                    $updateData = array('status' => 'blocked');
                   $whereData = array('ipAddress' =>$client_ip);
                   $updateDB = $wpdb->update( $ipblocksTable, $updateData, $whereData);
                   $toMail = $blockDefaultDataNotificationMail;
                   $subjectMail = 'Banned User Notification';
                   $messageMail = 'Some one with '.$client_ip.' IP tried to access your site many times. So automatically banned to access site. If you want to allow him then please unblock him from IP-BLocker.';
                   if($toMail){
                       $mailStatus = wp_mail( $toMail, $subjectMail, $messageMail);
                   }   
                }
                   echo html_entity_decode($blockDefaultDataDisplayContent);
                exit;
            } else{
                if($crntTime <= $visitTimeAddMinutes){
                    $counter++; /*Increment counter if same user visit again in stipulated time*/
                    $updateData = array('counter' =>$counter, 'status' => 'unblocked', 'visitTime' => $visitTime);
                    $whereData = array('ipAddress' =>$client_ip);
                    $updateDB = $wpdb->update( $ipblocksTable, $updateData, $whereData);
                } else{
                   $counter = 1;
                   $updateData = array('counter' =>$counter, 'status' => 'unblocked', 'visitTime' => $crntTime);
                   $whereData = array('ipAddress' =>$client_ip);
                   $updateDB = $wpdb->update( $ipblocksTable, $updateData, $whereData);
                    /*If time is not same then update time and set counter to zero*/
                }
            }
        } else{
            $counter = 1;
            $insertData = array('ipAddress' => $client_ip, 'counter' => $counter, 'status' => 'unblocked', 'visitTime' => $crntTime);
            $inserIntoDB = $wpdb->insert( $ipblocksTable, $insertData);
            /*If Current IP is not available then Insert IP in DB*/
         }
        /*-- End of Block IP --*/
    }
}
/*-- End of Init action --*/
/*-- Start of Create Table, When Plugins activated --*/
    function ais_ipBlocker_install() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ipBlocker';
	$charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
        ipAddress varchar(20) NOT NULL,
        counter mediumint(9) NOT NULL,
        status varchar(10) NOT NULL DEFAULT 'unblocked',
        visitTime TIMESTAMP NULL,
		PRIMARY KEY (id)
    ) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
register_activation_hook( __FILE__, 'ais_ipBlocker_install' );
/*-- End of Create Table, When Plugins activated --*/
/*-- Start of Delete Table, When Plugins Deleted --*/
function ais_ipBlocker_uninstall() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ipBlocker';
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}
register_uninstall_hook( __FILE__, 'ais_ipBlocker_uninstall' );
/*-- End of Delete Table, When Plugins Deleted --*/
?>