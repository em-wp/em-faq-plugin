<?php

require_once 'inc/em-faq-plugin-shortcode.php';

/**
 * Plugin Name: FAQ by EM.
 * Description: Adds FAQ editor per page and shortcode to insert said FAQ on the page.
 * Author: Effektiv Markedsføring
 * Version: 0.0.1
 */


add_action('plugins_loaded', 'em_faq_plugin_init');
function em_faq_plugin_init() {

  wp_register_script('editor-js', 'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest', [], false, true);

  wp_register_script('editor-js-config', plugin_dir_url(__FILE__) . 'assets/editor.js', ['editor-js'], false, true);
}

add_action('add_meta_boxes_page', 'em_faq_plugin_metabox');
add_action('add_meta_boxes_post', 'em_faq_plugin_metabox');
function em_faq_plugin_metabox() {

  wp_enqueue_script('editor-js-config');

  add_meta_box(
    'em-faq-plugin',
    'FAQ',
    'em_faq_plugin_metabox_callback',
    null,
    'advanced',
    'high'
  );
}
function em_faq_plugin_metabox_callback($post) {

  $faqs = get_post_meta($post->ID, 'em_faqs', true);
  // wp_die('<xmp>' . print_r($faqs, true) . '</xmp>');

  echo <<<HTML
    <script data-name="em-faqs">
      const faqsData = $faqs;
    </script>
    <style>
      #em-faq-plugin-editor {
        background-color: hsl(0, 2%, 96%);
        padding: 10px;
      }

      .faq-container {
        display: flex;
        flex-direction: column;
        
        margin-bottom: 40px;
        border-radius: 5px;

        background-color: #fff;

      }

      .faq-container answer,
      .faq-container question {
        font-size: 18px;
        margin-bottom: 10px;
        padding: 10px;
      }


      .faq-container > answer::before,
      .faq-container > question::before {
        display: block;
        content: 'Question';
        background-color: hsl(220, 70%, 70%);
        font-size: 20px;
        font-weight: 700;
        padding: 3px 5px;
        color: #fff;
        border-radius: 5px;
        margin-bottom: 5px;
      }

      .faq-container > answer::before {
        content: 'Answer';
      }

    </style>
    <div id="em-faq-plugin-editor"></div>
    <input type="hidden" id="faqs" name="faqs">
  HTML;
}


add_action('save_post_post', 'em_faq_plugin_save', 10, 1);
function em_faq_plugin_save($post_id) {

  if (!isset($_POST['faqs'])) return;

  $faqs = $_POST['faqs'];

  update_post_meta($post_id, 'em_faqs', $faqs);

  // $faqs = stripslashes($faqs);
  // $faqs = json_decode($faqs);


  // wp_die('<xmp>' . print_r($faqs ?? 'nada', true) . '</xmp>');
}
