<?php
global $wpdb;


$post_types_ary=array();
$post_types_ary=explode(",",get_option('toast_post_types'));
$enable=get_option('toast_enable');
$time_d=get_option('default_toast_time');
$tab=get_option('toast_new_tab');
    
?> <div class="wrap">
<h3><?php echo _e('Toaster Notification Settings','toast_notification');?></h3>
<?php
if(isset($_REQUEST['success']))
{?>
<div class="updated below-h2" id="message"><p><?php echo _e('Your settings have been saved.','toast_notification');?></p></div>
<?php }?>

<p>Select your post types for applying toast notification.</p>

<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
<table class="form-table">
<tr>
<th><?php echo _e('Post Types','toast_notification');?></th>
<td><?php  $post_types = get_post_types( '', 'names' );
            unset($post_types['attachment']);
            unset($post_types['revision']);
            unset($post_types['nav_menu_item']);
            foreach ( $post_types as $post_type ) 
            {?><input type="checkbox" name="post_type[]" id="<?php echo $post_type;?>" value="<?php echo $post_type;?>" <?php if(in_array($post_type,$post_types_ary)){?> checked="checked" <?php }?>><?php echo ucfirst($post_type);?><br/>  

            <?php 
            }?>
</td>
</tr>
<tr>
<th><?php echo _e('Plugin Enable?','toast_notification');?></th>
<td><input type="radio" name="plg_enable" value="1" <?php if($enable==1 || $enable==''){?> checked="checked" <?php }?>> Yes<br/>   
<input type="radio" name="plg_enable" value="0" <?php if($enable==0){?> checked="checked" <?php }?>> No</td>
</tr>
<tr>
<th><?php echo _e('Notification visible Time<br>(in seconds)','toast_notification');?></th>
<td><select name="seconds" id='seconds'>
<?php
                     
for($i=0;$i<60;$i++)
{?><option value="<?php echo $i;?>" <?php if($i==$time_d){echo "selected";}?>><?php echo $i;?></option>
<?php }?>
</select> &nbsp;(Default = 10 secs)</td>
</tr>
<tr>
<th><?php echo _e('Notification link open in new tab?','toast_notification');?></th>
<td><input type="radio" name="new_tab" value="1" <?php if($tab==1){?> checked="checked" <?php }?>> Yes<br/>   
<input type="radio" name="new_tab" value="0" <?php if($tab==0){?> checked="checked" <?php }?>> No</td>
</tr>
<tr>
<td>
<input type="hidden" name="action" value="add_toast">
<input type="submit" name="submit" value="Submit" class="button-secondary"></td>
<td></td></tr>
</table>

</form>
</div>
