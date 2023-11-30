<?php
if(!defined('ABSPATH')) exit;

add_action( 'init', 'create_post_type' );
function create_post_type() {

  // 施工事例
  my_register_post_type('works', '事例', 'dashicons-images-alt');

  // タクソノミー
  my_register_taxonomy('works_cat', '事例カテゴリー', array('works'), array('rewrite' => array('slug' => 'cat')));

}


/* ============================================================================================================ */
function my_register_post_type($type, $name, $icon = '', $add_support = '', $option = '') {
  if(!$type || !$name) return;

  $labels = array(
    'name' => $name,
    'singular_name' => $name,
    'all_items' => $name . '一覧',
    'add_new_item' => $name . 'を追加',
    'edit_item' => $name . 'の編集',
    'search_items' => $name . 'を検索',
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    /* 'exclude_from_search' => false,  */
    'show_ui' => true,
    'query_var' => true,
    /* 'rewrite' => array('slug' => $link,'with_front' => false), */
    'rewrite' => true,
    'capability_type' => 'post',
    /* 'hierarchical' => true, */
    'hierarchical' => false,
    'menu_position' => 5,
    'supports' => array('title', 'editor' ,'author','thumbnail', 'revisions', 'excerpt'),
    'has_archive'=>true,
    'show_in_rest'  => true,
    'show_admin_column' => true
  );
  if(is_string($icon) &&  !empty($icon)) $args['menu_icon'] = $icon;

  if($add_support === true) $args['supports'][] = 'editor';
  elseif(is_array($add_support)) $args['supports'] = array_merge($args['supports'], $add_support);
  elseif(is_string($add_support) && !empty($add_support)) $args['supports'][] = $add_support;

  if(is_array($option)) $args = array_merge($args, $option);

  register_post_type($type, $args);
}


/* ============================================================================================================ */
function my_register_taxonomy($slug, $name, $posttype, $option = '') {
  $args = array(
    'label' => $name,
    'hierarchical' => true,
    'query_var' => true,
    'show_in_rest'  => true,
    'show_tagcloud' => false
  );
  if(is_array($option)) $args = array_merge($args, $option);
  register_taxonomy($slug, $posttype, $args);
}

/* ============================================================================================================ */
function my_register_category_post_type($posttype) {
  if(is_array($posttype)) {
    foreach($posttype as $p) register_taxonomy_for_object_type('category', $p);
  } else {
    register_taxonomy_for_object_type('category', $posttype);
  }
}

