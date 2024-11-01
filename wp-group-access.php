<?php
/*
Plugin Name: WP Group Access
Plugin URI: http://adminofsystem.net/
Description: Plugin for wordpress group access.
Version: 1.1
Author: Yakhin Ruslan
Author URI: http://adminofsystem.net
*/

/*  Copyright 2010  Yakhin Ruslan (email : nessus@adminofsystem.netL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function wp_access_update_user_group($ID)
{
	update_user_meta($ID, 'wp_access_group', $_POST['wp_access_group']);
}
function wp_access_edit_user_group($wp_user_object)
{
	$userdata = $wp_user_object->data;
	if(is_super_admin())
	{
		$wp_access_group_list=get_option('wp_group_access_group_list');
?>
		<table class="form-table">
		<tr>
			<th><label for="group">Group</label></th>
			<td>
				<select name="wp_access_group">
				<option value=""></option>
				<?php if(is_array($wp_access_group_list)): foreach($wp_access_group_list as $group): ?>

				<?php if($userdata->wp_access_group==$group): ?>
					<option value="<?php echo $group;?>" selected><?php echo $group;?></option>
				<?php else: ?>
					<option value="<?php echo $group;?>"><?php echo $group;?></option>
				<?php endif; ?>
				<?php endforeach; endif; ?>
				</select>
			</td>
		</tr>
		</table>
<?php
	}
}
function wp_access_edit_category_form($term)
{
	$wp_access_group_list=get_option('wp_group_access_group_list');

        if(is_super_admin() && $_GET['action'] == 'edit' && is_array($wp_access_group_list))
        {
		$ID=$term->term_id;

		$category['wp_access_post_group'] = get_option('wp_access_post_group');
	        $category['wp_access_read_group'] = get_option('wp_access_read_group');
?>
                <table class="form-table">
		<tr>
                	<th><label for="group">Read group</label></th>
                	<td>
				<?php 
					if(is_array($wp_access_group_list))
					{ 
						$count=1;
						foreach($wp_access_group_list as $group)		
						{
							if($category['wp_access_read_group'][$ID][$group]=='on')
							{
								echo '<input type="checkbox" name=wp_access_read_group['.$group.'] checked="checked">'.$group.'&nbsp;';
							}
							else
							{
								echo '<input type="checkbox" name=wp_access_read_group['.$group.'] >'.$group.'&nbsp;';
							}
							if($count==6) 
							{
								echo '<br>';
								$count=0;
							}
							else
							{
								$count++;
							}
						}
					}
				?>
			</td>	
		</tr>
		<tr>
			<th><label for="group">Post group</label></th>
			<td>
				<?php
                                        if(is_array($wp_access_group_list))
                                        {
                                                $count=1;
                                                foreach($wp_access_group_list as $group)
                                                {
							if($category['wp_access_post_group'][$ID][$group]=='on')
                              				{
                                      				echo '<input type="checkbox" name=wp_access_post_group['.$group.'] checked="checked">'.$group.'&nbsp;';
                              				}
                              				else
                              				{
                                      				echo '<input type="checkbox" name=wp_access_post_group['.$group.'] >'.$group.'&nbsp;';
                              				}
                                                        if($count==6)
                                                        {
                                                                echo '<br>';
                                                                $count=0;
                                                        }
                                                        else
                                                        {
                                                                $count++;
                                                        }
                                                }
                                        }
                                ?>
			</td>
		</tr>
		</table>
<?php
	}
}	
function wp_access_edit_category($ID)
{
	$category = array();
	
	$category['wp_access_post_group'] = get_option('wp_access_post_group');
	$category['wp_access_read_group'] = get_option('wp_access_read_group');
	
	$category['wp_access_post_group'][$ID]=$_POST['wp_access_post_group'];
	$category['wp_access_read_group'][$ID]=$_POST['wp_access_read_group'];

        update_option('wp_access_post_group', $category['wp_access_post_group']);
        update_option('wp_access_read_group', $category['wp_access_read_group']);
}
function wp_access_admin_control_menu()
{
	$wp_access_group_list=get_option('wp_group_access_group_list');

	if(!empty($_POST['wp_access_group']))
	{
		$wp_access_group_list[]=$_POST['wp_access_group'];
		update_option('wp_group_access_group_list',$wp_access_group_list);
	}
	if(!empty($_GET['wp_access_group']) && $_GET['action']=='del')
        {
                $key=array_search($_GET['wp_access_group'],$wp_access_group_list);
		unset($wp_access_group_list[$key]);
                update_option('wp_group_access_group_list',$wp_access_group_list);
        }
?>
	<div class="wrap">
		<h2><?php _e('WP Group Access', 'wp-group-access'); ?></h2>

	<form name="wp-group-access" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=wp-group-access.php">
	<table>
	<tr>
        	<td class="submit" colspan="2">
			<input type="text" name="wp_access_group"><input type="submit" name="submit" value="<?php _e('Add') ?>"/>
		</td>
	</tr>
	<?php if(is_array($wp_access_group_list)): foreach($wp_access_group_list as $group):?>
	<tr>
		<td class="submit"><?php echo $group; ?></td>
		<td>
			<a href="<?php echo $_SERVER['PHP_SELF'];?>?page=wp-group-access.php&action=del&wp_access_group=<?php echo $group;?>">Del</a>
		</td>
	</tr>
        <?php endforeach;  endif; ?>
	</table>
	</form>
	</div>
<?php
}
function wp_access_admin_menu()
{
	add_options_page('WP Group Access', 'WP Group Access',8, basename(__FILE__), 'wp_access_admin_control_menu');
}
function wp_access_comments_open($flag)
{
	global $post,$current_user;

        $category['wp_access_post_group'] = get_option('wp_access_post_group');

        $category_term=get_the_category($post->id);
        $wp_access_post_group=$category['wp_access_post_group'][$category_term[0]->term_id][$current_user->data->wp_access_group];

        if($wp_access_post_group=='on' || $wp_access_post_group=="" || $current_user->data->ID==1)
        {
                return $flag;
        }
}
function wp_access_wp_list_categories()
{
	global $current_user;

	remove_action('wp_list_categories', 'wp_access_wp_list_categories');

	$user_data = $current_user->data;
	$category['wp_access_read_group'] = get_option('wp_access_read_group');
	$categories = get_categories('type=post');

	$count=0;
	foreach($categories as $cat)
	{
		$wp_access_read_group = $category['wp_access_read_group'][$cat->term_id];

		if($wp_access_read_group[$user_data->wp_access_group]!='on' && isset($wp_access_read_group) && $current_user->data->user_level!=10)
		{
			if($count==0)
                       	{
                               	$exclude.=$cat->term_id;
                       	}
                       	else
                       	{
                               	$exclude.=','.$cat->term_id;
                       	}
                       	$count++;	
		}
	}
	$args = array(
	      'show_count'   => 1,	
              'hierarchical' => 1,
              'title_li'     => '',
	      'hide_empty'   => 1,
	      'exclude'      => $exclude,
            );

	return wp_list_categories($args);
}
function wp_access_pre_get_posts($query)
{
	global $current_user;

	$user_data = $current_user->data;
        $category['wp_access_read_group'] = get_option('wp_access_read_group');
	$categories = get_categories('type=post');

	foreach($categories as $cat)
       	{
		$wp_access_read_group = $category['wp_access_read_group'][$cat->term_id];
	
               	if($wp_access_read_group[$user_data->wp_access_group]!='on' && $current_user->data->user_level!=10 && isset($wp_access_read_group))
		{
			if(empty($include)) $include='-'.$cat->term_id;
			else $include.=',-'.$cat->term_id;
		}
       	}
	$query->set('cat',$include);
}

function wp_access_group_deactivation()
{
        delete_option('wp_access_post_group');
        delete_option('wp_access_read_group');
        delete_option('wp_group_access_group_list');
}

add_action('comments_open','wp_access_comments_open');
add_action('wp_list_categories','wp_access_wp_list_categories');
add_action('pre_get_posts','wp_access_pre_get_posts');

add_action('edit_category','wp_access_edit_category');
add_action('edit_category_form','wp_access_edit_category_form');
add_action('edit_user_profile_update', 'wp_access_update_user_group');
add_action('edit_user_profile','wp_access_edit_user_group');
add_action('admin_menu', 'wp_access_admin_menu');

register_deactivation_hook( __FILE__, 'wp_access_group_deactivation');

?>
