<?php

function em_faq_plugin_metabox() {

  wp_enqueue_script('editor-js-config');

  add_meta_box(
    'em-faq-plugin-new',
    'FAQ new',
    'em_faq_plugin_metabox_callback',
    null,
    'advanced',
    'high'
  );
}


function em_faq_plugin_metabox_callback($post) {

  $faq = get_post_meta($post->ID, 'emfaqs', true);
  if (!$faq) $faq = [];

?>
  <input type="hidden" name="emfaqs" value="1">
  <input type="hidden" name="emfaqs-">
  <!-- META CONTAINER -->
  <div>
    <!-- FAQ LIST -->
    <ul class="em-faq-meta">
      <?php for ($i = 0; $i < sizeof($faq); $i++) :
        if (empty($faq[$i]['question']) && empty($faq[$i]['answer'])) continue; ?>
        <li data-faq="<?= $i ?>" class="em-faq-container">

          <!-- QUESTION -->
          <div class="em-faq-question">
            <h4 class="em-faq-title">Question</h4>
            <?php wp_editor(
              $faq[$i]['question'],
              'em-faq-question-' . $i,
              [
                'media_buttons' => false,
                'textarea_rows' => 1,
                'tinymce' => [
                  /* TINYMCE QUESTION SETTINGS */
                  'toolbar1' => 'formatselect,bold,italic,underline,link,unlink,charmap,forecolor,backcolor',
                  'height' => '50px'
                ]
              ]
            ) ?>
          </div>

          <!-- ANSWER -->
          <div class="em-faq-answer">
            <h4 class="em-faq-title">Answer</h4>
            <?php wp_editor(
              $faq[$i]['answer'],
              'em-faq-answer-' . $i,
              [
                'media_buttons' => false,
                'textarea_rows' => 1,
                'tinymce' => [
                  /* TINYMCE ANSWER SETTINGS */
                  'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,link,unlink,forecolor,backcolor'
                ]
              ]
            ) ?>
          </div>
          <!-- REMOVE BUTTON -->
          <button onclick="removeFAQ(this)" type="button" class="button button-secondary" style="margin-top: 10px">Remove FAQ</button>
        </li>
      <?php endfor; ?>
    </ul>
    <!-- ADD BUTTON -->
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

    .em-faq-title--new {
      top: 0;
      padding: 10px 0;
    }


    .em-faq-question .em-faq-title::before {
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
}
