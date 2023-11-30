<?php
if(!defined('ABSPATH')) exit;

/*
 * 画像ディレクトリまでのURL取得
*----------------------------------*/
function imgDir() {
  return esc_url( get_template_directory_uri().'/assets/img' );  
}

/*
 * サムネイル取得
*----------------------------------*/
function getMyThumbnailUrl($size, $postID = null) {
  if (has_post_thumbnail()){
    // アイキャッチがある時
    $postID = empty($postID) ? get_the_ID(): $postID;
    $url = get_the_post_thumbnail_url($postID, $size);
    return $url;
  } else {
    // アイキャッチがない時はno_img
    $url = get_template_directory_uri()."/assets/img/common/no_img.png";
    return $url;
  }
}