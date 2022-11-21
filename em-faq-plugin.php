<?php

require_once 'inc/em-faq-plugin-shortcode.php';
require_once 'inc/em-faq-plugin-updater.php';

/**
 * Plugin Name: FAQ by EM.
 * Description: Adds FAQ editor per page and shortcode to insert said FAQ on the page.
 * Author: Effektiv Markedsføring
 * Version: 1.0.2
 */


add_action('plugins_loaded', 'em_faq_plugin_init');
function em_faq_plugin_init() {

  wp_register_script('editor-js', 'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest', [], false, true);

  wp_register_script('editor-js-config', plugin_dir_url(__FILE__) . 'assets/editor.js', ['editor-js'], '1.0.27', true);
}

add_action('add_meta_boxes_page', 'em_faq_plugin_metabox');
add_action('add_meta_boxes_post', 'em_faq_plugin_metabox');
function em_faq_plugin_metabox() {

  wp_enqueue_script('editor-js-config');

  add_meta_box(
    'em-faq-plugin-new',
    'FAQ new',
    'em_faq_plugin_metabox_callback_new',
    null,
    'advanced',
    'high'
  );

  add_meta_box(
    'em-faq-plugin',
    'FAQ',
    'em_faq_plugin_metabox_callback',
    null,
    'advanced',
    'high'
  );
}

function em_faq_plugin_metabox_callback_new($post) {
  echo <<<HTML
    <div contenteditable="true">hi</div>
  HTML;
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
        background-color: hsl(220, 22%, 90%);
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

      .faq-plugin-helper-container {
        margin-bottom: 20px;
      }

      .faq-plugin-ul-helper {
        margin: 3px 0 10px;
      }

      .faq-plugin-li-helper {
        display: flex;

        border: solid 1px #eee;
        border-radius: 3px;
        padding: 4px;
        margin-bottom: 10px;
      }

      .faq-plugin-li-helper > span:first-child {
        width: 150px;
        font-weight: 700;
        flex-shrink: 0;
      }

      .faq-plugin-h4-helper {
        margin: 0;
      }

      .faq-plugin-hidden {
        display: none;
      }

    </style>
    <div class="faq-plugin-helper-container">
      <button onclick="this.parentNode.lastElementChild.classList.toggle('faq-plugin-hidden');this.innerHTML = this.innerHTML === 'Show explaination' ? 'Hide explaination' : 'Show explaination'" class="button button-secondary" type="button">Show explaination</button>
      <div class="faq-plugin-hidden">
      <ul class="faq-plugin-ul-helper">
        <li class="faq-plugin-li-helper"><span>Navigate</span> <span>between FAQs, Questions and Answers with up and down arrow keys. Or click on the question or answer element.</span></li>
        <li class="faq-plugin-li-helper"><span>Create new FAQ</span> <span> by clicking "+" on the left side;<br>Enter key in "answer" section or clicking below the FAQ list.</span></li>
        <li class="faq-plugin-li-helper"><span>Delete a FAQ</span> <span> by erasing all text in both answer and question and hitting backspace;<br>Use tune tools (6 points icon or tab key) and click cross.</span></li>
        <li class="faq-plugin-li-helper"><span>Move FAQ</span> <span> by tune tool and up and down arrow icons.</span></li>
        <li class="faq-plugin-li-helper"><span>Add link/bold/italic</span> <span>by marking text and popup will show with the options.</span></li>
      </ul>
      <h4 class="faq-plugin-h4-helper">Keys:</h4>
      <ul class="faq-plugin-ul-helper">
        <li class="faq-plugin-li-helper"><span>Arrow up/down</span> <span>Navigate between Question, Answer and FAQs</span></li>
        <li class="faq-plugin-li-helper"><span>Backspace</span> <span>Deletes FAQ if FAQ is empty.</span></li>
        <li class="faq-plugin-li-helper"><span>Mouse click</span> <span>Selects question or answer or creates a new FAQ.</span></li>
        <li class="faq-plugin-li-helper"><span>Enter</span> <span>Creates a new FAQ.</span></li>
      </ul>
    </div>
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
