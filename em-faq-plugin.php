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

  if (empty($faqs)) $faqs = '{}';

  echo <<<HTML
    <script data-name="em-faqs">
      const faqsData = $faqs;
    </script>
    <style>
      #em-faq-plugin-editor {
        background-color: hsl(220, 22%, 80%);
        padding: 10px;
        border-radius: 5px;
      }

      .faq-plugin-container {
        display: flex;
        flex-direction: column;
        
        margin-bottom: 40px;
        border-radius: 5px;

        background-color: #fff;
        border: solid 1px #aaa;
      }

      .codex-editor__redactor .ce-block {
        counter-increment: my-awesome-counter;
      }

      .faq-plugin-container answer,
      .faq-plugin-container question {
        font-size: 14px;
        
        padding: 20px 10px;
        border-left: dashed 2px;
        border-right: dashed 2px;
        border-color: transparent;
      }



      .faq-plugin-container answer:focus,
      .faq-plugin-container question:focus {
        outline: none;
        border-color: #6a6;
      }

      .faq-plugin-title::before {
        display: block;
        content: '#' counter(my-awesome-counter) ' Question';
        background-color: hsl(220, 10%, 90%);
        font-size: 16px;
        font-weight: 500;
        padding: 3px 5px;
        color: #333;
      }


      .faq-plugin-question-title::before {
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
      }
        .faq-plugin-answer-title::before {
        content: 'Answer';
      }

      .faq-plugin-li-helper {
        display: flex;
        flex-gap: 20px;
      }

    </style>
    <div>
      <ul>
        <li><strong>Navigate</strong> between FAQs, Questions and Answers with up and down arrow keys. Or click on the question or answer element.</li>
        <li>Create <strong>new</strong> FAQ by click "+"; hitting enter when "answer" is focused or clicking below the FAQ list</li>
        <li><strong>Delete</strong> a FAQ by erasing all text in both answer and question and hitting backspace; Use tune tools (6 points icon; hit tab key) and click cross.</li>
        <li><strong>Move</strong> FAQ by tune tool and up and down arrow icons.</li>
      </ul>
    </div>
    <div>
      <ul>
        <li class="faq-plugin-li-helper"><strong>Arrow up/down</strong><span>Navigate between Question, Answer and FAQs</li>
        <li><strong>Backspace</strong></li>
        <li><strong>Mouse click</strong></li>
        <li><strong>Enter</strong></li>
      </ul>
    </div>
    <div id="em-faq-plugin-editor"></div>
    <input type="hidden" id="faqs" name="faqs">
  HTML;
}


add_action('save_post_post', 'em_faq_plugin_save', 10, 1);
add_action('save_post_page', 'em_faq_plugin_save', 10, 1);
function em_faq_plugin_save($post_id) {

  if (!isset($_POST['faqs'])) return;

  $faqs = $_POST['faqs'];

  update_post_meta($post_id, 'em_faqs', $faqs);

  // $faqs = stripslashes($faqs);
  // $faqs = json_decode($faqs);


  // wp_die('<xmp>' . print_r($faqs ?? 'nada', true) . '</xmp>');
}
