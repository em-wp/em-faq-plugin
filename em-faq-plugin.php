<?php

require_once 'inc/em-faq-plugin-shortcode.php';
require_once 'inc/em-faq-plugin-updater.php';

/**
 * Plugin Name: FAQ by EM.
 * Description: Adds FAQ editor per page and shortcode to insert said FAQ on the page.
 * Author: Effektiv Markedsføring
 * Version: 2.0.0
 */


add_action('plugins_loaded', 'em_faq_plugin_init');
function em_faq_plugin_init() {

  // wp_register_script('editor-js', 'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest', [], false, true);

  wp_register_script('faq-tinymce', plugin_dir_url(__FILE__) . 'assets/tinymce.js', [], false, true);

  // wp_register_script('editor-js-config', plugin_dir_url(__FILE__) . 'assets/editor.js', ['editor-js'], '1.0.27', true);
}

add_action('admin_enqueue_scripts', function ($page) {
  if ($page !== 'post.php') return;
  wp_enqueue_editor();
  wp_enqueue_script('faq-tinymce');
});

// add_action('admin_footer', function () {
//   echo <<<SCRIPT
//       <script data-name="tinymce-script">
//       console.log(wp)
//       setTimeout(() => {
//         wp.editor.initialize("em-faq-container")

//       }, 1000);
//     </script>
//   SCRIPT;
// }, 999999999999999999999999, 1);


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

  // add_meta_box(
  //   'em-faq-plugin',
  //   'FAQ',
  //   'em_faq_plugin_metabox_callback',
  //   null,
  //   'advanced',
  //   'high'
  // );
}

function em_faq_plugin_metabox_callback_new($post) {

  $faq = get_post_meta($post->ID, 'emfaqs', true);
  if (!$faq) $faq = [];

  /* OLD FAQ DATA (TEMP) */
  $old_faq = [];
  $faqs = get_post_meta($post->ID, 'em_faqs', true);
  if ($faqs) {
    $faqs = json_decode($faqs, true);
    if (!empty($faqs['blocks'])) {
      foreach ($faqs['blocks'] as $f) {
        $old_faq[] = [
          'question' => $f['data']['question'] ?? '',
          'answer' => $f['data']['answer'] ?? ''
        ];
      }
    }
  }

?>
  <div>
    <button onclick="this.parentNode.lastElementChild.classList.toggle('em-faq-hidden')" type="button" class="button button-secondary">Show old faq data (temp)</button>
    <div class="em-faq-hidden">
      <pre><?php print_r($old_faq) ?></pre>
    </div>
  </div>
  <input type="hidden" name="emfaqs" value="1">
  <div>
    <ul class="em-faq-meta">
      <?php for ($i = 0; $i < sizeof($faq); $i++) :
        if (empty($faq[$i]['question']) && empty($faq[$i]['answer'])) continue; ?>
        <li data-faq="<?= $i ?>" class="em-faq-container">
          <div class="em-faq-question">
            <h4 class="em-faq-title">Question</h4>
            <?php wp_editor(
              $faq[$i]['question'],
              'em-faq-question-' . $i,
              [
                'media_buttons' => false,
                'textarea_rows' => 1,
                'tinymce' => [
                  'toolbar1' => 'bold,italic,underline,link,unlink,charmap',
                  'height' => '50px'
                ]
              ]
            ) ?>
          </div>
          <div class="em-faq-answer">
            <h4 class="em-faq-title">Answer</h4>
            <?php wp_editor(
              $faq[$i]['answer'],
              'em-faq-answer-' . $i,
              [
                'media_buttons' => false,
                'textarea_rows' => 1,
                'tinymce' => [
                  'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,link,unlink'
                ]
              ]
            ) ?>
          </div>
          <button onclick="removeFAQ(this)" type="button" class="button button-secondary" style="margin-top: 10px">Remove FAQ</button>
        </li>
      <?php endfor; ?>
    </ul>
    <button onclick="addFAQ(this)" type="button" class="button button-primary">Add FAQ</button>
  </div>
  <style>
    .em-faq-container {
      counter-increment: faq-counter;
    }

    .em-faq-container {
      padding: 0 20px 20px;
      background-color: hsl(60, 15%, 95%);
      margin-bottom: 40px;
    }

    .em-faq-title {
      margin: 0;
      font-size: 18px;
      font-weight: 700;
      position: relative;
      top: 30px;
      display: inline-block;
    }

    .em-faq-question::before {
      position: relative;
      top: 30px;
      content: '#'counter(faq-counter) ' ';
      font-size: 16px;
      font-weight: 500;
      padding: 3px 5px;
      color: #333;
    }

    .em-faq-question iframe {
      height: 60px !important;
    }

    .em-faq-hidden {
      display: none;
    }
  </style>
<?php
}

function em_faq_plugin_metabox_callback_OLD($post) {
  return;
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

  if (!isset($_POST['emfaqs'])) return;

  $faq = [];

  for ($i = 0; $i < 100; $i++) {
    if (!isset($_POST['em-faq-question-' . $i]) && !isset($_POST['em-faq-answer-' . $i]))
      continue;

    $faq[] = [
      'question' => $_POST['em-faq-question-' . $i] ?? '',
      'answer' => $_POST['em-faq-answer-' . $i] ?? ''
    ];
  }

  update_post_meta($post_id, 'emfaqs', $faq);

  // wp_die('<xmp>' . print_r($faq, true) . '</xmp>');
  // $faqs = $_POST['faqs'];

  // update_post_meta($post_id, 'em_faqs', $faqs);

  // $faqs = stripslashes($faqs);
  // $faqs = json_decode($faqs);


  // wp_die('<xmp>' . print_r($faqs ?? 'nada', true) . '</xmp>');
}
