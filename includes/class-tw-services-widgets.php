<?php
$has_service_category = get_option('wpt_tw_service_category')=='on' ? true : false;
if($has_service_category){
  class twServicesCategoryWidget extends WP_Widget{

    function twServicesCategoryWidget(){
        parent::WP_Widget(false, 'TW Service Categories', array('description'=>''));
    }

    function form($instance){
      $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => '', 'link-text'=>'', 'link-url'=>'' ) );

      if($instance['title']){
    		$title = esc_attr($instance['title']);
  		}else{$title = '';}

  		if($instance['number']){
    		$number = esc_attr($instance['number']);
  		}else{$number = '';}

  		if($instance['link-text']){
    		$link_text = esc_attr($instance['link-text']);
  		}else{$link_text = '';}

  		if($instance['link-url']){
    		$link_url = esc_attr($instance['link-url']);
  		}else{$link_url = '';}
    ?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','tw-services-plugin'); ?> </label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
     </p>
     <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of Service Categories','tw-services-plugin'); ?>: </label>
  		    <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
      </p>
  		<p><label for="<?php echo $this->get_field_id('link-text'); ?>"><?php _e('See All Button Text:','tw-services-plugin'); ?> </label>
  		    <input class="widefat" id="<?php echo $this->get_field_id('link-text'); ?>" name="<?php echo $this->get_field_name('link-text'); ?>" type="text" value="<?php echo $link_text; ?>" />
      </p>
  		<p><label for="<?php echo $this->get_field_id('link-url'); ?>"><?php _e('See All Button URL:','tw-services-plugin'); ?> </label>
  		    <input class="widefat" id="<?php echo $this->get_field_id('link-url'); ?>" name="<?php echo $this->get_field_name('link-url'); ?>" type="text" value="<?php echo $link_url; ?>" />
  		</p>
    <?php

    }

    function update($new_instance, $old_instance){
      $instance = $old_instance;

      $instance['title']     = $new_instance['title'];
      $instance['number']    = $new_instance['number'];
      $instance['link-text'] = $new_instance['link-text'];
      $instance['link-url']  = $new_instance['link-url'];

      return $instance;
    }

    function widget($args, $instance){
      extract($args, EXTR_SKIP);

      $args['title']      = empty($instance['title']) ? ''     : apply_filters('widget_title', $instance['title']);
      $args['number']     = empty($instance['number']) ? ''     : $instance['number'];
      $args['link-text']  = empty($instance['link-text']) ? '' : $instance['link-text'];
      $args['link-url']   = empty($instance['link-url']) ? ''  : $instance['link-url'];

      tw_service_categories_widget($args);
    }

  }

  function tw_service_categories_widget($args){

    $number  = (isset($args['number']) && intval($args['number'])>0) ? intval($args['number']) : 4;
    $service_categories = get_terms( 'tw_service_category', array( 'orderby' => 'count', 'hide_empty' => 0 ));

    $services = array();
    $count = 0;
    foreach($service_categories as $s){
      $meta = get_option( "tax_meta_$s->term_id" );

      $order = (isset($meta['tw_order']) && intval(trim($meta['tw_order']))>0  ) ? intval(trim($meta['tw_order'])) : 0 ;
      $icon  = (isset($meta['tw_icon']) && trim($meta['tw_icon'])!=='') ?  trim($meta['tw_icon']) : '';
      if(isset($meta['tw_image']) && isset($meta['tw_image']['id']) ){
        $img_id = intval($meta['tw_image']['id']);
      }

      if(isset($services[$order])){
        $order = $count+20;
      }

      $services[$order] = array(
          'term_id'     => $s->term_id,
          'name'        => $s->name,
          'description' => $s->description,
          'slug'        => $s->slug,
          'link'        => get_term_link( $s, 'tw_service_category' ),
          'count'       => $s->count,
          'order'       => $order,
          'icon'        => $icon,
          'image_id'    => $img_id,
        );

      $count++;
    }

    ksort($services);
    $title = trim($args['title']);
    $link_text = trim($args['link-text']);
    $link_url = trim($args['link-url']);

    $counter = 0;
    $s_count = count($services);

    $widget_area = $args['id'];
    if($widget_area=='homepage'){
      switch($s_count){
        case 1:
          $class = 'col-xs-12 col-sm-12 col-md-12';
          break;
        case 2:
          $class = 'col-xs-12 col-sm-6 col-md-6';
          break;
        case 3:
        case 5:
        case 6:
          $class = 'col-xs-12 col-sm-6 col-md-4';
          break;
        case 4:
        case 7:
        case 8:
          $class = 'col-xs-12 col-sm-6 col-md-3 col-lg-3';
          break;
        default:
          $class = 'col-xs-12 col-sm-6 col-md-6';
          break;
      }
    }else{
      $class = 'col-xs-12 col-sm-12 col-md-12';
    }




    if ( count($services)>0 ) :
      echo $args['before_widget'];
      ?><div class="services-container"><?php
      if(!empty($args['title'])){
          echo $args['before_title'] . esc_html( $args['title'] ) . $args['after_title'];
      }
    ?>
      <div id="<?php echo $args['widget_id'];?>-service-categories" class="service-categories row">
        <?php foreach($services as $service): ?>

          <div id="service-category-<?php echo $service['term_id'];?>" class="service-category <?php echo $class; ?>">
            <div class="thumbnail">
            <?php
              if(isset($service['image_id']) && trim($service['image_id'])!==''):
              $image_id = $service['image_id'];
                if(function_exists('tw_get_image_src')){
                  $image_sizes = array('4x3-small','16x6-medium','16x6-medium');
                  $img_src = tw_get_image_src($image_id, $image_sizes);
                ?>
                <a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
                  <img src="<?php echo $img_src; ?>" alt="" class="img-responsive" itemscope="image"  />
                </a>
                <?php }else{ ?>
                <a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
                  <?php wp_get_attachment_image( $image_id, 'medium', false, array('itemscope'=>'image','class'=>'img-responsive') ); ?>
                </a>
                  <?php
                }
              endif;
            ?>



              <div class="caption">
                <?php if(isset($service['icon']) && $service['icon']!=='' ): ?>
                  <p class="icon"><i class="fa <?php echo $service['icon'];?>"></i></p>
                <?php endif; ?>
                <h3><a href="<?php echo $service['link'];?>" title="<?php echo $service['name'];?>"><?php echo $service['name'];?></a></h3>
                <p><?php echo $service['description'];?></p>
              </div>
            </div>
          </div>

        <?php endforeach; ?>
      </div><!-- row -->

      <div class="clearfix"></div>
      <?php if($link_text!="" && $link_url!=""): ?>
      <div class="more">
          <a class="btn btn-primary btn-lg" href="<?php echo $args['link-url'] ;?>" title="<?php echo $args['link-text'] ;?>"><?php echo $args['link-text'] ;?></a>
      </div><!-- more -->
      <?php endif; ?>

    <?php echo $args['after_widget']; ?>

    </div>
    <?php endif;
  }

  add_action( 'widgets_init', create_function('', 'return register_widget("twServicesCategoryWidget");') );
}


class twServicesWidget extends WP_Widget{
  private $enable_cat;
  private $enable_tag;

  function twServicesWidget(){
    parent::WP_Widget(false, 'TW Services', array('description'=>''));
    $this->enable_cat = get_option('wpt_tw_service_category')=='on' ? true : false;
    $this->enable_tag      = get_option('wpt_tw_service_tag')=='on' ? true : false;
  }

  function form($instance){
    $instance = wp_parse_args( (array) $instance, array( 'title'=>'','number' => '', 'order'=>'', 'category'=>'', 'tag' => '' ) );

    if($instance['title']){
    		$title = esc_attr($instance['title']);
  		}else{$title = '';}

    if($instance['number']){
  		$number = esc_attr($instance['number']);
		}else{$number = '';}

		if($instance['order']){
  		$order = esc_attr($instance['order']);
		}else{$order = '';}

		if($this->enable_cat){
      if($instance['category']){
    		$category = esc_attr($instance['category']);
  		}else{$category = '';}

      $slide_cats = get_terms('tw_service_category',
                               array(
                               	'orderby'    => 'count',
                               	'hide_empty' => 0,
                               )
                              );
    }
    if($this->enable_tag){
      if($instance['tag']){
    		$tag = esc_attr($instance['tag']);
  		}else{$tag = '';}
    }
  ?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','tw-services-plugin'); ?> </label>
		    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		    <small><?php _e('Enter comma seperated list of tags','tw-services-plugin');?></small>
    </p>

    <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of Services','tw-services-plugin'); ?> </label>
		    <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
    </p>

    <p><label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order by','tw-services-plugin'); ?> </label>
      <select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
           <option value="name"       <?php selected( $order, 'name' ); ?>><?php echo __('Name','tw-services-plugin'); ?></option>
           <option value="menu_order" <?php selected( $order, 'menu_order' ); ?>><?php echo __('Assigned Order','tw-services-plugin'); ?></option>
           <option value="date"       <?php selected( $order, 'date' ); ?>><?php echo __('Date','tw-services-plugin'); ?></option>
      </select>
    </p>

    <?php if($this->enable_cat): ?>
    <p><label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Service Category','tw-services-plugin'); ?> </label>
      <select id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>">
        <option value=""  <?php selected( $category, '' ); ?>><?php echo __('','tw-services-plugin'); ?></option>

        <?php foreach($slide_cats as $scat): ?>
          <option value="<?php echo $scat->slug; ?>"  <?php selected( $category, $scat->slug ); ?>><?php echo $scat->name; ?></option>
        <?php endforeach; ?>

      </select>
    </p>
    <?php endif;?>

    <?php if($this->enable_tag): ?>
		<p><label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Service Tags ','tw-services-plugin'); ?> </label>
		    <input class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" type="text" value="<?php echo $tag; ?>" />
		    <small><?php _e('Enter comma seperated list of tags','tw-services-plugin');?></small>
    </p>
    <?php endif; ?>
  <?php
  }

  function update($new_instance, $old_instance){
    $instance = $old_instance;

    $instance['title']      = $new_instance['title'];
    $instance['number']     = $new_instance['number'];
    $instance['order']      = $new_instance['order'];
    if($this->enable_cat){
      $instance['category'] = $new_instance['category'];
    }
    if($this->enable_tag){
      $instance['tag']      = $new_instance['tag'];
    }

    return $instance;
  }

  function widget($args, $instance) {
    extract($args, EXTR_SKIP);
    // outputs the content of the widget
    $args['title']   = empty($instance['title'])  ? '' : $instance['title'];
    $args['number']  = empty($instance['number']) ? '' : $instance['number'];
    $args['order']   = empty($instance['order'])  ? '' : $instance['order'];
    if($this->enable_cat){
      $args['category'] = empty($instance['category']) ? '' : $instance['category'];
    }

    if($this->enable_tag){
      $args['tag']     = empty($instance['tag'])    ? '' : $instance['tag'];
    }

    $args['enable_cat'] = $this->enable_cat;
    $args['enable_tag'] = $this->enable_tag;
    tw_services_widget($args);
  }

}
function tw_services_widget($args){
  $title = isset($args['title']) ? trim($args['title']) : '';
  $num = isset($args['number']) ? intval(trim($args['number'])) : 5 ;
  $orderby = isset($args['order']) ? trim($args['order']) : 'date';
  $order = 'desc';
  switch ($orderby) {
    case 'date':
      $order = 'desc';
      break;
    case 'name':
      $order = 'asc';
      break;
    case 'menu_order':
      $order = 'asc';
      break;
    default:
      $order = 'desc';
  }

  $query_args= array(
  	'post_type' => 'tw_service',
  	'posts_per_page' => $num,
  	'order' => $order,
  	'orderby' => $orderby,
  );

  $relationship = false;
  if($args['enable_cat'] && $args['enable_tag']){
    $relationship = true;
  }

  if($args['enable_cat']){
    $category = trim($args['category']);
    if($category!==''){
      $tax_query[] = array(
  			'taxonomy' => 'tw_service_category',
  			'field'    => 'slug',
  			'terms'    => $category,
  		);
    }else{
      $relationship = false;
    }
  }

  if($args['enable_tag']){
    $tag = trim($args['tag']);
    if(!empty($tag) && $tag!==""){
      $tag =  explode(',', $tag);
      $tax_query[] = array(
  			'taxonomy' => 'tw_service_tag',
  			'field'    => 'slug',
  			'terms'    => $tag,
  			'operator' => 'IN',
  		);

    }else{
      $relationship = false;
    }
  }

  if($relationship){
    $tax_query['relation'] = 'AND';
  }
  $query_args['tax_query'] = $tax_query;

  $services = new WP_Query( $query_args );

  if ( $services->have_posts() ) :
    echo $args['before_widget'];


    $widget_area = $args['id'];
    if($widget_area=='homepage'){
      $post_count = $services->post_count;
      switch($post_count){
        case 1:
          $class = 'col-xs-12 col-sm-12 col-md-12';
          break;
        case 2:
          $class = 'col-xs-12 col-sm-6 col-md-6';
          break;
        case 3:
        case 5:
        case 6:
          $class = 'col-xs-12 col-sm-6 col-md-4';
          break;
        case 4:
        case 7:
        case 8:
          $class = 'col-xs-12 col-sm-6 col-md-3';
          break;
        default:
          $class = 'col-xs-12 col-sm-6 col-md-6';
          break;
      }
    }else{
      $class = 'col-xs-12 col-sm-12 col-md-12';
    }

?>
    <div class="services-container">

      <?php if($title!==''){
        echo $args['before_title'] . $title . $args['after_title'];
      }?>

      <div id="<?php echo $args['widget_id'];?>-services" class="services row">
      <?php while($services->have_posts()): $services->the_post(); ?>
        <div id="service-<?php the_id(); ?>" class="service <?php echo $class;?>">
          <div class="thumbnail">
            <?php
              if(has_post_thumbnail()):
                if(function_exists('tw_get_image_src')){
                  $image_sizes = array('4x3-small','16x9-medium','16x9-medium');
                ?>
                <a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
                  <?php echo tw_the_post_thumbnail($image_sizes, $attr = array('itemscope'=>'image','class'=>'img-responsive') ); ?>
                </a>
                <?php }else{ ?>
                <a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
                  <?php get_the_post_thumbnail(get_the_id(), 'medium', array('itemscope'=>'image','class'=>'img-responsive')); ?>
                </a>
                  <?php
                }
              endif;
            ?>

            <div class="caption">
              <h3><a href="<?php the_permalink();?>" title="<?php the_title(); ?>"><?php the_title();?></a></h3>
              <?php the_excerpt(); ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
      </div>
    </div>
<?php
    echo $args['after_widget'];
  endif;
}
add_action( 'widgets_init', create_function('', 'return register_widget("twServicesWidget");') );