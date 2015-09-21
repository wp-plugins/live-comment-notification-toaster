<?php 
/*
Plugin Name: Live comments notification
Plugin URI: 
Description: We can have toaster notification if comment is posted on some post.
Version: 1.0.1
Text Domain: toast_notification
Author: biztechc
Author URI: https://profiles.wordpress.org/biztechc/
License: GPLv2
*/ ?>
<?php 

// add admin menu for settings
add_action('admin_menu', 'bc_notification_setting');  

function bc_notification_setting() {
    if (function_exists('add_menu_page')) {                
        add_menu_page('Toaster Notification Setting', __('Toaster Notification', 'toast_notification'), 'manage_options', plugin_dir_path( __FILE__ ) . 'notification_settings.php', '', 'dashicons-format-status');
    } 
}

//uninstall hook
register_uninstall_hook( __FILE__, 'uninstall_taoster_notification' );
function uninstall_taoster_notification()
{
    global $wpdb;
    if (is_multisite()) {
    $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    foreach ($blogids as $blog_id) {
    switch_to_blog($blog_id); 
    delete_option('toast_post_types');
    delete_option('toast_enable');
    restore_current_blog();
    }
  }
  else
    {
         delete_option('toast_post_types');
         delete_option('toast_enable');
    } 
}
 
add_action( 'wp_footer', 'bc_ajax_call',1 ); 
function bc_ajax_call()
{ 
    $time_d= get_option('default_toast_time')*1000;
    $enable=get_option('toast_enable');
    if($enable==1){?>
   <script type="text/javascript" >
   var interval_id=0;
   var toast_flag=1;
   jQuery(document).ready(function(){
       
     interval_id =  setInterval(function(){check_live_comments_bc();}, 5000);
   });
   jQuery(window).blur(function() {       
    clearInterval(interval_id);
    interval_id = 0;
});
jQuery(window).focus(function() {
    
       if (!interval_id)
        {interval_id =  setInterval(function(){check_live_comments_bc();}, 5000); }
        
        });
    
   //setTimeout(function(){check_live_comments_bc();}, 1000);
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <input type="hidden" name="default_toast_time" id="default_toast_time" value="<?php echo $time_d;?>">
<?php }
}

add_action( 'wp_ajax_nopriv_check_new_comments_ajax_toast', 'check_new_comments_ajax_fun_toast' );
add_action( 'wp_ajax_check_new_comments_ajax_toast', 'check_new_comments_ajax_fun_toast' );

function check_new_comments_ajax_fun_toast() {
      // debugbreak();
    global $wpdb; // this is how you get access to the database
    $post_IDs=array();
    $post_types_ary=explode(",",get_option('toast_post_types'));
    for($i=0;$i<count($post_types_ary);$i++)
    {
        $new_ary[]="'".$post_types_ary[$i]."'";
    }
    $in_post=implode(",",$new_ary);
    $enable=get_option('toast_enable');
    $user=wp_get_current_user();
    $uname=$user->data->user_login;
     $tab= get_option('toast_new_tab');
     if($tab==1){$trgt=" target='_blank' ";}
     else{$trgt='';}
   
    if($enable==1)
    {
        
        $result=$wpdb->get_results("select post.post_title,com.comment_post_ID,com.comment_author_email,com.comment_ID from ".$wpdb->prefix."posts post,".$wpdb->prefix."comments com where 
                                TIMESTAMPDIFF(MINUTE,comment_date_gmt,UTC_TIMESTAMP())<1 and 
                                comment_approved = 1 and post.ID= com.comment_post_ID and com.comment_author_IP<>'".$_SERVER['REMOTE_ADDR']."' and post.post_type IN (".$in_post.")"); 
        /* $result=$wpdb->get_results("select post.post_title,com.comment_post_ID,com.comment_author_email,com.comment_ID from ".$wpdb->prefix."posts post,".$wpdb->prefix."comments com where 
                                TIMESTAMPDIFF(MINUTE,comment_date_gmt,UTC_TIMESTAMP())<60  and post.ID= com.comment_post_ID  and post.post_type IN (".$in_post.")");  */
        for($i=0;$i<count($result);$i++)
        {
            $cookieValue = explode(",",$_COOKIE['toast_showed_com_id']);
            if(!in_array($result[$i]->comment_post_ID,$post_IDs) && !in_array($result[$i]->comment_ID,$cookieValue))
            {
                 $post_IDs[]=$result[$i]->comment_post_ID;
                  $pid=$result[$i]->comment_post_ID;
               echo "<div class='lcn-desc'>A new comment is added on ".$result[$i]->post_title."<br/>
               Click <a href='".get_permalink($pid)."' ".$trgt.">Here</a> to view.</div>
               <div class='lcn-thumb'>".get_avatar( $result[$i]->comment_author_email, 65, '', '' )."</div>#####";
               
                
                
            }
            if(!in_array($result[$i]->comment_ID,$cookieValue)){
                $cookieValue[]= $result[$i]->comment_ID;
                setcookie('toast_showed_com_id', implode(",",$cookieValue), strtotime('+1 day'));
            } 
        }
    }
    else
    {
        echo "-2";
        
    }
    wp_die(); // this is required to terminate immediately and return a proper response
}

//add toaster js and css
function toaster_scripts() {
    wp_enqueue_style( 'toaster-style', plugins_url( 'css/toastr.css', __FILE__ ) );
    wp_enqueue_script( 'toastr-script', plugins_url( 'js/toastr.js', __FILE__ ), array(), '1.0.0', true );
    wp_enqueue_script( 'ajax-call', plugins_url( 'js/ajaxcall.js', __FILE__ ), array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'toaster_scripts' );



//Add setting data
add_action( 'admin_post_add_toast', 'prefix_admin_add_toast' );
function prefix_admin_add_toast()
{   status_header(200);    
    $post_type=implode(",",$_REQUEST['post_type']);
    update_option('toast_post_types',$post_type);
    update_option('toast_enable',$_POST['plg_enable']);
    $new_tab_set=($_POST['new_tab'])?$_POST['new_tab']:0;
    $default_toast_time=($_POST['seconds'])?$_POST['seconds']:10;
    update_option('toast_new_tab',$new_tab_set);
    update_option('default_toast_time',$default_toast_time); 
    wp_redirect(admin_url('admin.php?page='.basename(dirname(__FILE__)).'/notification_settings.php&success'));
}
?>