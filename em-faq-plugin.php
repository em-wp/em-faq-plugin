<?php

/**
 * Plugin Name: FAQ by EM.
 * Description: Adds FAQ editor per page and shortcode to insert said FAQ on the page.
 * Author: Effektiv Markedsføring
 * Version: 2.0.1
 */


add_action('plugins_loaded', 'em_faq_plugin_init');
function em_faq_plugin_init() {

  // edit page
  require_once 'inc/em-faq-plugin-edit.php';

  // shortcode
  require_once 'inc/em-faq-plugin-shortcode.php';

  // custom updater (to be removed before uploading to wordpress)
  require_once 'inc/em-faq-plugin-updater.php';

  // registering faq edit javascript file.
  wp_register_script('faq-tinymce', plugin_dir_url(__FILE__) . 'assets/faq-tinymce.js', [], '1.0.1', true);

  /* FAQ EDIT JAVASCRIPT/TINYMCE  */
  add_action('admin_enqueue_scripts', function ($page) {

    // off-ramp if page is not an edit page.
    if ($page !== 'post.php') return;

    // adding javascript
    wp_enqueue_editor();
    wp_enqueue_script('faq-tinymce');
  });

  /* FAQ SHORTCODE */
  add_shortcode(shortcode_exists('faq') ? 'em-faq' : 'faq', 'em_faq_plugin_shortcode');

  /* FAQ META BOX */
  add_action('add_meta_boxes_page', 'em_faq_plugin_metabox');
  add_action('add_meta_boxes_post', 'em_faq_plugin_metabox');

  /* FAQ META BOX SAVE */
  add_action('save_post_post', 'em_faq_plugin_save', 10, 1);
  add_action('save_post_page', 'em_faq_plugin_save', 10, 1);
}
