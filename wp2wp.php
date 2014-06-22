<?php
/*
Plugin Name: WP2WP
Plugin URI: http://www.utubon.com
Description: 一个利用xmlrpc远程同步博客到另外的WP2PCS的程序 
Version:  1.0
Author: 否子戈
Author URI: http://www.utubon.com
*/

// 更新设置
add_action('admin_init','wp_to_wp_update_options');
function wp_to_wp_update_options(){
	if(!is_admin() && !current_user_can('edit_theme_options'))return;
	if(!empty($_POST) && isset($_POST['page']) && $_POST['page'] == $_GET['page'] && isset($_POST['action']) && $_POST['action'] == 'wp_to_wp_update_options'){
		check_admin_referer();
		$wp2wp_blogs = $_POST['wp2wp_blogs'];
		$update_blogs = array();
		if(!empty($wp2wp_blogs))foreach($wp2wp_blogs as $blog){
			if(!isset($blog['name']) || !trim($blog['name']))continue;
			$update_blogs[] = $blog;
		}
		update_option('wp2wp_blogs',$update_blogs);
		wp_redirect(add_query_arg(array('time'=>time())));
		exit;
	}
}
// 添加菜单和设置页面
add_action('admin_menu','wp_to_wp_menu');
function wp_to_wp_menu(){
	add_plugins_page('WP2WP同步多个WordPress','WP2WP','edit_theme_options','wp2wp','wp_to_wp_options');
}
// 设置页面
function wp_to_wp_options(){
?>
<style>
.order-num{width:60px;}
.short-text{width:150px;}
#wp2wp-blogs-list-table *{text-align:center;}
</style>
<div class="wrap" id="wp2pcs-admin-dashbord">
	<h2>WP2WP WordPress同步到其他WordPress</h2>
	<div class="metabox-holder">
		<div class="postbox">
			<h3>远端博客设置</h3>
			<div class="inside" style="border-bottom:1px solid #CCC;margin:0;padding:8px 10px;">
			<form method="post" autocomplete="off">
				<!-- 以免自动填充 { -->
				<div style="width:1px;height:1px;float:right;overflow:hidden;">
					<input type="text" />
					<input type="password" />
				</div>
				<!-- } -->
				<table id="wp2wp-blogs-list-table">
					<thead>
						<tr>
							<th class="order-num">序号</th>
							<th>标记名(不能重复)</th>
							<th>远端博客地址(home_url)</th>
							<th>远端作者(user_login)</th>
							<th>远端密码(user_pass)</th>
							<th>远端分类名(cat_name)</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$key_poser = 0;
					$wp2wp_blogs = get_option('wp2wp_blogs');
					if(!empty($wp2wp_blogs))foreach($wp2wp_blogs as $num => $blog){
						echo '<tr>
							<td class="order-num">'.($num+1).'</td>
							<td><input type="text" name="wp2wp_blogs['.$num.'][name]" value="'.$blog['name'].'" class="short-text" /></td>
							<td><input type="text" name="wp2wp_blogs['.$num.'][url]" value="'.$blog['url'].'" class="regular-text" /></td>
							<td><input type="text" name="wp2wp_blogs['.$num.'][user]" value="'.$blog['user'].'" class="short-text" /></td>
							<td><input type="password" name="wp2wp_blogs['.$num.'][pwd]" value="'.$blog['pwd'].'" class="short-text" /></td>
							<td><input type="text" name="wp2wp_blogs['.$num.'][cat]" value="'.$blog['cat'].'" class="short-text" /></td>
						</tr>';
						$key_poser = $num;
					}
					else echo '<tr>
						<td class="order-num">1</td>
						<td><input type="text" name="wp2wp_blogs[0][name]" value="" class="short-text" /></td>
						<td><input type="text" name="wp2wp_blogs[0][url]" value="" class="regular-text" /></td>
						<td><input type="text" name="wp2wp_blogs[0][user]" value="" class="short-text" /></td>
						<td><input type="password" name="wp2wp_blogs[0][pwd]" value="" class="short-text" /></td>
						<td><input type="text" name="wp2wp_blogs[0][cat]" value="" class="short-text" /></td>
					</tr>';
					?>
					</tbody>
				</table>
				<p></p>
				<p>
					<input type="submit" value="更新" class="button-primary" />
					<a href="javascript:void(0)" class="button" id="add-input-line">添加一行</a>
				</p>
				<input type="hidden" name="action" value="wp_to_wp_update_options" />
				<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
				<?php wp_nonce_field(); ?>
			</form>
			</div>
			<div class="inside" style="border-bottom:1px solid #CCC;margin:0;padding:8px 10px;">
				<p>向插件作者捐赠：<a href="http://me.alipay.com/tangshuang" target="_blank">支付宝</a>、BTC（164jDbmE8ncUYbnuLvUzurXKfw9L7aTLGD）、PPC（PNijEw4YyrWL9DLorGD46AGbRbXHrtfQHx）、XPM（AbDGH5B7zFnKgMJM8ujV3br3R2V31qrF2F）</p>
				<p>如果你还想要更高级的功能，例如在后台写文章的时候增加选项，可以选择这篇文章只同步到上面配置的博客列表中的某一个或几个（非全部）的话，可以和我联系，获得更高级别的开发成果。frustigor@163.com <a href="http://www.utubon.com" target="_blank">http://www.utubon.com</a></p>
			</div>
		</div>
	</div>
</div>
<script>
var $line = <?php echo $key_poser ?>;
jQuery('#add-input-line').click(function(e){
	$line ++;
	jQuery('#wp2wp-blogs-list-table tbody').append('<tr><td class="order-num">'+($line+1)+'</td><td><input type="text" name="wp2wp_blogs['+$line+'][name]" value="" class="short-text" /></td><td><input type="text" name="wp2wp_blogs['+$line+'][url]" class="regular-text" value="" /></td><td><input type="text" name="wp2wp_blogs['+$line+'][user]" value="" class="short-text" /></td><td><input type="password" name="wp2wp_blogs['+$line+'][pwd]" value="" class="short-text" /></td><td><input type="text" name="wp2wp_blogs['+$line+'][cat]" value="" class="short-text" /></td></tr>');
});
</script>
<?php
}

// 保存文章的时候就开始同步
add_action('save_post','wp_to_wp_post');
function wp_to_wp_post($post_id){

	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
		return;
	if(defined('DOING_AJAX') && DOING_AJAX)
		return;
	if(false!==wp_is_post_revision($post_id))
		return;

	$get_post = get_post($post_id,ARRAY_A);
	if(!$get_post)return;
	if($get_post['post_type']!='post' || trim($get_post['post_content'])=='')return;
	
	$status = ($get_post['post_status']=='publish' ? true : false);// 同步到的文章的状态，远程编辑的时候，又可以使文章成为待审

	/*发表文章 http://codex.wordpress.org/XML-RPC_MetaWeblog_API */
	$content = array(
		'title' => $get_post['post_title'],
		'description' => $get_post['post_content'].'<p>'.wp_get_shortlink($post_id).'</p>',
		'post_type' => $get_post['post_type'],
		//'dateCreated' => $get_post['post_date'],
		//'date_created_gmt' => $get_post['post_date_gmt'],
		'mt_excerpt' => $get_post['post_excerpt'],
		'mt_allow_comments' => 1,//$get_post['comment_status'],
		'mt_allow_pings' => 1,//$get_post['ping_status'],
		'wp_slug' => $get_post['post_name'],
		'post_status' => $get_post['post_status'],
		'custom_fields' => ''
	);

	$get_tags = wp2wp_get_tags($post_id);
	if($get_tags){
		$content['mt_keywords'] = implode(',',$get_tags);
	}

	$is_edit = $get_post['post_modified']>$get_post['post_date'] ? true : false;
	$client_posts = get_post_meta($post_id,'_wp2wp_posted',true);
	if(!$client_posts || empty($client_posts)){
		$is_edit = false;
	}
	$wp2wp_posted = array();
	$wp2wp_blogs = get_option('wp2wp_blogs');

	include_once(ABSPATH."wp-includes/class-IXR.php");
	if(!empty($wp2wp_blogs))foreach($wp2wp_blogs as $num => $blog){
		/*
		ob_flush();flush();
		ini_set('session.gc_maxlifetime', 86400);
		ini_set('max_execution_time', 90000);
		set_time_limit(0);
		*/
		$client_xmlrpc = trailingslashit($blog['url']).'xmlrpc.php';
		$client = new IXR_Client($client_xmlrpc);
		$client_user = $blog['user'];
		$client_pwd = $blog['pwd'];
		// 如果把这篇文章移到回收站，或转为隐私文章，这时将客户端的文章也删除
		if($get_post['post_status']=='trash' || $get_post['post_status']=='private'){
			$client_post_id = @$client_posts[$blog['name']];
			if(!$client_post_id)continue;
			$client_action = 'metaWeblog.deletePost';
			$client->query($client_action,array('',$client_post_id,$client_user,$client_pwd,$status));
			continue;
		}
		// 正常的修改或发布文章
		else{
			$client_cat = $blog['cat'];
			$content['categories'] = array($blog['cat']);
			$client_post_id = 0;
			$client_action = 'metaWeblog.newPost';
			if($is_edit){
				$client_post_id = @$client_posts[$blog['name']];
				if($client_post_id)$client_action = 'metaWeblog.editPost';
			}
			$is_success = $client->query($client_action,array($client_post_id,$client_user,$client_pwd,$content,$status));
			if($is_success){
				$client_posted_id = $client->message->params;
				$client_posted_id = $client_posted_id[0];
				$wp2wp_posted[$blog['name']] = $client_posted_id;
			}
		}//endif
	}

	// 更新这个文章，表明它在被发布的博客中的ID号
	if($is_success && !$is_edit){
		add_post_meta($post_id,'_wp2wp_posted',$wp2wp_posted,true) || update_post_meta($post_id,'_wp2wp_posted',$wp2wp_posted);
	}
}

// 获取文章的标签列表（标签名称）
function wp2wp_get_tags($post_id){
	global $wpdb;
	// 获取标签对象
	$sql = sprintf('SELECT term_id FROM %s r LEFT JOIN %s t ON r.term_taxonomy_id = t.term_taxonomy_id WHERE t.taxonomy = "post_tag" and r.object_id = %d',$wpdb->term_relationships,$wpdb->term_taxonomy,$post_id);
	$tags = $wpdb->get_results($sql);
	if(empty($tags)){
		return array();
	}
	$tag_ids = array();
	foreach($tags as $obj){
		$tag_ids[] = $obj->term_id;
	}
	$tag_ids = implode(',',$tag_ids);
	$sql = sprintf('SELECT name FROM %s WHERE term_id IN (%s)',$wpdb->terms,$tag_ids);
	$results = $wpdb->get_results($sql);
	if(empty($results)){
		return array();
	}
	$tags = array();
	foreach($results as $obj){
		$tags[] = $obj->name;
	}
	return $tags;
}