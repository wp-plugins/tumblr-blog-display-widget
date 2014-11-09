<?php
/**
 * @package tumblr-blog-display-widget
*/
/*
Plugin Name: Tumblr Blog Display Widget
Plugin URI: http://www.ramit-designs.com
Description: Display your tumblr blogs in Wordpress Widget
Version: 1.0
Author: Matt Armstrong
Author URI: http://www.ramit-designs.com
*/

class TumblrBlogDisplayWidget extends WP_Widget{
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles_tumblr_blog_display_widget' ) );
        $params = array(
            'description' => 'Display your tumblr blogs in Wordpress Widget',
            'name' => 'Tumblr Blog Display Widget'
        );
        parent::__construct('TumblrBlogDisplayWidget','',$params);
    }
    function register_plugin_styles_tumblr_blog_display_widget() {
        wp_register_style( 'tumblr_blog_display_widget', plugins_url( 'style.css' , __FILE__ ) );
        wp_enqueue_style('tumblr_blog_display_widget');
 }
    public function form($instance) {
        extract($instance);
        ?>
<p>
    <label for="<?php echo $this->get_field_id('title');?>">Title : </label>
    <input
	class="widefat"
	id="<?php echo $this->get_field_id('title');?>"
	name="<?php echo $this->get_field_name('title');?>"
        value="<?php echo !empty($title) ? $title : "Tumblr Blog Display Widget"; ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id('blog_url');?>">Blog URL: </label>
    <input
	class="widefat"
	id="<?php echo $this->get_field_id('blog_url');?>"
	name="<?php echo $this->get_field_name('blog_url');?>"
    value="<?php echo !empty($blog_url) ? $blog_url : ""; ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id('api_key');?>">API Key: </label>
    <input
	class="widefat"
	id="<?php echo $this->get_field_id('api_key');?>"
	name="<?php echo $this->get_field_name('api_key');?>"
    value="<?php echo !empty($api_key) ? $api_key : ""; ?>" />
</p>
<p><small><a href="https://wordpress.org/plugins/tumblr-blog-display-widget/installation/" target="_blank">Check Documentation</a></small></p>
<p>
    <label for="<?php echo $this->get_field_id('width');?>">Width: </label>
    <input
	class="widefat"
	id="<?php echo $this->get_field_id('width');?>"
	name="<?php echo $this->get_field_name('width');?>"
    value="<?php echo !empty($width) ? $width : "450"; ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id('limit');?>">Limit: </label>
    <input
	class="widefat"
	id="<?php echo $this->get_field_id('limit');?>"
	name="<?php echo $this->get_field_name('limit');?>"
    value="<?php echo !empty($limit) ? $limit : "6"; ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id('text_limit');?>">Text Limit: </label>
    <input
	class="widefat"
	id="<?php echo $this->get_field_id('text_limit');?>"
	name="<?php echo $this->get_field_name('text_limit');?>"
    value="<?php echo !empty($text_limit) ? $text_limit : "5"; ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'read_more' ); ?>">Read More: </label> 
    <select id="<?php echo $this->get_field_id( 'read_more' ); ?>"
        name="<?php echo $this->get_field_name( 'read_more' ); ?>"
        class="widefat" style="width:100%;">
            <option value="true" <?php if ($read_more == 'true') echo 'selected="true"'; ?> >True</option>
            <option value="false" <?php if ($read_more == 'false') echo 'selected="false"'; ?> >False</option>
    </select>
</p>
<?php
    }
    public function widget($args, $instance) {
        extract($args);
        extract($instance);
        $title = apply_filters('widget_title', $title);
        $description = apply_filters('widget_description', $description);
		if(empty($title)) $title = "Dribbble Portfolio Shots Widget";
        if(empty($blog_url)) $blog_url = "";
        if(empty($limit)) $limit = "6";
        if(empty($text_limit)) $text_limit = "5";
        if(empty($read_more)) $read_more = "true";
        
    $data = "";
    if($blog_url == '' && $api_key == ''){
        $data .= "blog_url &amp; api_key are require field to display tumblr blog in wordpress";
    }
    else{
        if(ini_get('allow_url_fopen') && function_exists('openssl_open')){
		
    $tumblrFeed = "http://api.tumblr.com/v2/blog/$blog_url/posts/text?api_key=$api_key&notes_info=true";
    $tumblrFeedGrab = json_decode(file_get_contents($tumblrFeed),true);
    $postItem[] = $tumblrFeedGrab['response']['posts'];
    $data .= "
        <div class='tumblr_blog_display_widget' style='width: $width";
    $data .= "px;'>
            <div class='blogWarp'>
";
    for($i=0;$i<$limit;$i++){
        foreach($postItem as $value){
            $data .= "<div class='postWarp'>";
                $data .= "<div class='tumblrTitle'>";
                    $t_post_url = $value[$i]['post_url'];
                    $t_post_title = $value[$i]['title'];
                    $data .= "<a href='$t_post_url' target='_blank'>$t_post_title</a>";
                $data .= "</div>";
                $data .= "<div class='tumblrDescription'>";
                    $t_post_desc = $this->trimWords(strip_tags($value[$i]['body']),$text_limit);
                    $data .= "$t_post_desc";
                    if($read_more == "true"){
                        $data .= "<div class='display:block'>";
                            $data .= "<a href='$t_post_url' target='_blank'>read more..</a>";
                        $data .= "</div>";
                    }
                $data .= "</div>";
            $data .= "</div>";

        }
    }
    $data .= "</div>
        </div>";
            
        }else{
		$data .= "Check your php.ini and make sure allow_url_fopen & openssl is set to on";
    }
        }
    $data .= "<div style='color:#ccc; font-size: 9px; text-align:right;'><a href='http://www.telemedicine-jobs.com' title='click here' target='_blank'>Telemedicine Jobs</a></div>";
        echo $before_widget;
        echo $before_title . $title . $after_title;
            echo $data;
        echo $after_widget;
    }
    public function trimWords($string, $limit)
{
    $words = explode(' ', $string);
    return implode(' ', array_slice($words, 0, $limit));
}
}
//start registering the extension
add_action('widgets_init','register_TumblrBlogDisplayWidget');
function register_TumblrBlogDisplayWidget(){
    register_widget('TumblrBlogDisplayWidget');
}