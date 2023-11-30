<?php
if(!defined('ABSPATH')) exit;

/*
 * CSS 読み込み
* ----------------------------------------------*/
function page_style() {

  // -------------------------
  // 全ページ共通
  // -------------------------
  //wp_enqueue_style('vendor-style', get_template_directory_uri() . "/assets/vendor/vendor.css", array(), '0.3.3');
  wp_enqueue_style('common-style', get_template_directory_uri() . "/assets/css/style.css", array(), '0.0.1');

}
add_action( 'wp_enqueue_scripts', 'page_style' );

/*
 * js 読み込み
* ----------------------------------------------*/
function add_jquery_files() {
	
  // -------------------------
  // 全ページ共通
  // -------------------------
	// WordPress本体のjquery.jsを読み込まない
	wp_deregister_script('jquery');
  // common
  //wp_enqueue_script( 'vendor-script', get_template_directory_uri() .'/assets/vendor/vendor.js', '','0.3.3', false);
  wp_enqueue_script( 'app-script', get_template_directory_uri() .'/assets/js/app.js', '','0.0.1', true);

}
add_action( 'wp_enqueue_scripts', 'add_jquery_files');

/*
 * アイキャッチ画像を有効にする。
* ----------------------------------------------*/
add_theme_support('post-thumbnails');

/*
 * WPログイン時、記事ページから
 * 編集画面に遷移できるボタンを追加
* ----------------------------------------------*/
function edit($the_content) {
  if (is_single() && is_user_logged_in()) {
      $return = '<div style="text-align: center; padding: 2rem; background: #eee;"><a target="_blank" href="'.get_edit_post_link().'" style="display: inline-block; padding: .5rem 1rem; background: #fff;">記事を編集</a></div>';
      $return .= $the_content;
      return $return;
  } else {
      return $the_content;
  }
}
add_filter('the_content','edit');

/*
 * ダッシュボードウィジェット削除
* ----------------------------------------------*/
function remove_dashboard_meta() {
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );// WordPress ニュース
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' ); // クイックドラフト
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal'); // アクティビティ
}
add_action( 'admin_init', 'remove_dashboard_meta' );

/*
 * 画面上のバー整理
* ----------------------------------------------*/
function remove_admin_bar_menus( $wp_admin_bar ) {

	$wp_admin_bar->remove_menu( 'comments' ); // コメント.
  $wp_admin_bar->remove_menu('new-link'); // 新規 -> リンク

}
add_action( 'admin_bar_menu', 'remove_admin_bar_menus', 999 );

/*
 * 更新通知を管理者権限のみに表示
* ----------------------------------------------*/
function update_nag_admin_only() {
  if ( ! current_user_can( 'administrator' ) ) {
		remove_action( 'admin_notices', 'update_nag', 3 );
		remove_action( 'admin_notices', 'maintenance_nag', 10 );
  }
}
add_action( 'admin_init', 'update_nag_admin_only' );

/*
 * wp_headから削除
* ----------------------------------------------*/
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head','wp_oembed_add_host_js');
remove_action('wp_head', 'print_emoji_detection_script', 7 );
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head','wp_resource_hints',2);

add_filter( 'show_admin_bar', '__return_false' );

/*
 * 画像の名前変換
* ----------------------------------------------*/
function rename_file_md5($fileName) {
  if(!preg_match("/^[a-zA-Z0-9]+$/", explode(".", $fileName)[0])) {
    $i = strrpos($fileName, '.');
    if ($i) $Exts = '.'.substr($fileName, $i + 1);
    else $Exts = '';
    $fileName = strtolower(md5(time().$fileName).$Exts);
  }

  return $fileName;
}
add_filter('sanitize_file_name', 'rename_file_md5', 10);

/*
* タイトル出力
* ----------------------------------------------*/
add_theme_support( 'title-tag' );

/*
* タイトルの仕切り変更
* ----------------------------------------------*/
function wp_document_title_separator( $separator ) {
  $separator = '|';
  return $separator;
}
add_filter( 'document_title_separator', 'wp_document_title_separator' );

function wp_document_title_parts( $title ) {

  if ( is_home() || is_front_page() ) {
    unset( $title['tagline'] ); // キャッチフレーズを出力しない
  }
  return $title;
}
add_filter( 'document_title_parts', 'wp_document_title_parts', 10, 1 );

/*
* OGPタグ/Twitterカード設定を出力
* ----------------------------------------------*/
function my_meta_ogp() {
  global $post;
  $ogp_title = get_bloginfo('name');
  $ogp_descr = get_bloginfo('description') ? get_bloginfo('description'): '';
  $ogp_url = home_url('/');
  $ogp_img = '';
  $insert = '';
  $ogp_type = 'article';


  if(is_home() || is_front_page()) {
    $ogp_type = 'website';
  } else if( is_singular() ) { //記事＆固定ページ
    setup_postdata($post);
    $ogp_title = $post->post_title . "|" . get_bloginfo('name');
    $ogp_descr = mb_substr(get_the_excerpt(), 0, 100);
    $ogp_url = get_permalink();
    wp_reset_postdata();
  } else if(is_category() || is_tag() || is_tax()){
    $queryObject = get_queried_object();
    $ogp_title = $queryObject->name . "|" . get_bloginfo('name');
    $ogp_url = home_url('/').$queryObject->slug.('/');
  } else if(is_archive() || is_post_type_archive()) {
    $queryObject = get_queried_object();
    $ogp_title = $queryObject->label . "一覧|" . get_bloginfo('name');
    $ogp_url = home_url('/').$queryObject->name.('/');
  }

 //og:image
 if ( is_singular() && has_post_thumbnail() ) {
    $ps_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
    $ogp_img = $ps_thumb[0];
 } else {
  $ogp_img = imgDir().'/common/ogp.jpg';
 }

 //出力するOGPタグをまとめる
 $insert .= '<meta property="og:title" content="'.esc_attr($ogp_title).'" />' . "\n";
 $insert .= '<meta property="og:description" content="'.esc_attr($ogp_descr).'" />' . "\n";
 $insert .= '<meta property="og:type" content="'.$ogp_type.'" />' . "\n";
 $insert .= '<meta property="og:url" content="'.esc_url($ogp_url).'" />' . "\n";
 $insert .= '<meta property="og:image" content="'.esc_url($ogp_img).'" />' . "\n";
 $insert .= '<meta property="og:site_name" content="'.esc_attr(get_bloginfo('name')).'" />' . "\n";
 $insert .= '<meta name="twitter:card" content="summary_large_image" />' . "\n";
 $insert .= '<meta property="og:locale" content="ja_JP" />' . "\n";

 echo $insert;
}
add_action('wp_head','my_meta_ogp');//headにOGPを出力


/*
* headにcanonical出力
* ----------------------------------------------*/
function my_meta_canonical() {
  global $paged;
  $canonical = null;

  if(is_home() || is_front_page()) {
    $canonical_url  = home_url('/');
  } elseif(is_post_type_archive()) {
    $canonical_url = get_post_type_archive_link(get_post_type());
  } elseif(is_tax()) {
    $canonical_url = get_term_link(get_queried_object()->term_id);
  } elseif(is_category()) {
    $canonical_url = get_category_link(get_query_var('cat'));
  } elseif(is_tag()) {
    $canonical_url = get_tag_link(get_queried_object()->term_id);
  } elseif((is_page() || is_single())) {
    $canonical_url = get_permalink();
  } elseif(is_404()) {
    $canonical_url =  home_url('/404/');
  } else {
    $canonical_url  = home_url('/');
  }
  
  echo '<link rel="canonical" href="'.$canonical_url.'">';
}

add_action('wp_head','my_meta_canonical');


/*
* 「JSON-LD」形式のパンくずリスト
* ----------------------------------------------*/
function json_breadcrumb() {
 
  if( is_admin() || is_home() || is_front_page() ){ return; }
 
  $position  = 1;
  $query_obj = get_queried_object();
  $permalink = ( empty($_SERVER["HTTPS"] ) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
 
  $json_breadcrumb = array(
    "@context"        => "http://schema.org",
    "@type"           => "BreadcrumbList",
    "name"            => "パンくずリスト",
    "itemListElement" => array(
      array(
        "@type"    => "ListItem",
        "position" => $position++,
        "item"     => array(
          "name" => "HOME",
          "@id"  => esc_url( home_url('/') ),
        )
      ),
    ),
  );
 
  if( is_page() ) {
 
    $ancestors   = get_ancestors( $query_obj->ID, 'page' );
    $ancestors_r = array_reverse($ancestors);
    if ( count( $ancestors_r ) != 0 ) {
      foreach ($ancestors_r as $key => $ancestor_id) {
        $ancestor_obj = get_post($ancestor_id);
        $json_breadcrumb['itemListElement'][] = array(
          "@type"    => "ListItem",
          "position" => $position++,
          "item"     => array(
            "name" => esc_html($ancestor_obj->post_title),
            "@id"  => esc_url( get_the_permalink($ancestor_obj->ID) ),
          )
        );
      }
    }
    $json_breadcrumb['itemListElement'][] = array(
      "@type"    => "ListItem",
      "position" => $position++,
      "item"     => array(
        "name" => esc_html($query_obj->post_title),
        "@id"  => $permalink,
      )
    );
 
  } elseif( is_post_type_archive() ) {
 
    $json_breadcrumb['itemListElement'][] = array(
      "@type"    => "ListItem",
      "position" => $position++,
      "item"     => array(
        "name" => $query_obj->label,
        "@id"  => esc_url( get_post_type_archive_link( $query_obj->name ) ),
      )
    );
 
  } elseif( is_tax() || is_category() ) {
 
    if ( !is_category() ) {
      $post_type = get_taxonomy( $query_obj->taxonomy )->object_type[0];
      $pt_obj    = get_post_type_object( $post_type );
      $json_breadcrumb['itemListElement'][] = array(
        "@type"    => "ListItem",
        "position" => $position++,
        "item"     => array(
          "name" => $pt_obj->label,
          "@id"  => esc_url( get_post_type_archive_link($pt_obj->name) ),
        )
      );
    }
 
    $ancestors   = get_ancestors( $query_obj->term_id, $query_obj->taxonomy );
    $ancestors_r = array_reverse($ancestors);
    foreach ($ancestors_r as $key => $ancestor_id) {
      $json_breadcrumb['itemListElement'][] = array(
        "@type"    => "ListItem",
        "position" => $position++,
        "item"     => array(
          "name" => esc_html( get_term($ancestor_id)->name ),
          "@id"  => esc_url( get_term_link($ancestor_id, $query_obj->taxonomy) ),
        )
      );
    }
 
    $json_breadcrumb['itemListElement'][] = array(
      "@type"    => "ListItem",
      "position" => $position++,
      "item"     => array(
        "name" => esc_html( $query_obj->name ),
        "@id"  => esc_url( get_term_link($query_obj) ),
      )
    );
 
  } elseif( is_single() ) {
 
    if ( !is_single('post') ) {
      $pt_obj = get_post_type_object( $query_obj->post_type );
      $json_breadcrumb['itemListElement'][] = array(
        "@type"    => "ListItem",
        "position" => $position++,
        "item"     => array(
          "name" => $pt_obj->label,
          "@id"  => esc_url( get_post_type_archive_link($pt_obj->name) ),
        )
      );
    }
 
    $json_breadcrumb['itemListElement'][] = array(
      "@type"    => "ListItem",
      "position" => $position++,
      "item"     => array(
        "name" => esc_html( $query_obj->post_title ),
        "@id"  => $permalink,
      )
    );
 
  } elseif( is_404() ) {
 
    $json_breadcrumb['itemListElement'][] = array(
      "@type"    => "ListItem",
      "position" => $position++,
      "item"     => array(
        "name" => "404 Not found",
        "@id"  => $permalink,
      )
    );
 
  } elseif( is_search() ) {
 
    $json_breadcrumb['itemListElement'][] = array(
      "@type"    => "ListItem",
      "position" => $position++,
      "item"     => array(
        "name" => "「" . get_search_query(). "」の検索結果",
        "@id"  => $permalink,
      )
    );
 
  }
 
  echo '<script type="application/ld+json">'.json_encode($json_breadcrumb).'</script>';
}
 
add_action( 'wp_head', 'json_breadcrumb' );

/*
* Authorアーカイブページを
*トップページにリダイレクト
* ----------------------------------*/

function author_custom_redirection() {
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
  $wp_rewrite->author_base = '';
  $wp_rewrite->author_structure = '/';
  if (isset($_REQUEST['author']) && !empty($_REQUEST['author'])) {
    wp_redirect(home_url());
    exit;
  }
}
add_action('init', 'author_custom_redirection');