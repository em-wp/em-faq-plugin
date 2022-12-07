<?php

/**
 * Adds shortcode that reads current post's metadata for FAQs.
 * 
 * @param array $atts
 * Possible atts: design, background, text, answer-text
 * @return HTML FAQ data formatted with HTML.
 */
function em_faq_plugin_shortcode($atts = []) {
  // css
  require_once 'em-faq-plugin-css.php';

  // current post
  global $post;

  // FAQ metadata
  $faqs = get_post_meta($post->ID, 'emfaqs', true);

  if (!$faqs) return;

  // getting css
  switch ($atts['design'] ?? false) {
    case 2:
      $css = Faq_css::$two;
      break;
    case 3:
      $css = sprintf(
        Faq_css::$three,
        $atts['background'] ?? 'inherit',
        $atts['text'] ?? 'inherit',
        $atts['answer-background'] ?? 'inherit',
        $atts['answer-text'] ?? 'inherit'
      );
      break;
    default:
      $css = Faq_css::$one;
  }

  // FAQ html
  $faq_list = [];

  // FAQ json (structured data)
  $faq_json = [];

  // creating HTML & JSON
  foreach ($faqs as $faq) {

    // both fields of FAQ needs to be not empty
    if (empty($faq['question']) || empty($faq['answer'])) continue;

    // structured data
    $faq_json[] = [
      '@type' => 'Question',
      'name' => $faq['question'],
      'acceptedAnswer' => [
        '@type' => 'answer',
        'text' => $faq['answer']
      ]
    ];

    // html
    $faq_list[] = <<<HTML
                    <li class="em-faqs">
                      <details class="em-faqs">
                        <summary class="em-faqs"><h3 class="em-faqs">{$faq['question']}</h3></summary>
                        <div class="em-faqs">{$faq['answer']}</div>
                      </details>
                    </li>
                  HTML;
  }

  // wp_die('<xmp>' . print_r(json_encode($faq_json), true) . '</xmp>');

  // adds structured data and CSS to front-end
  add_action('wp_footer', function () use ($css, $faq_json) {
?>
    <style data-name="em-faqs-plugin">
      <?= $css ?>
    </style>
    <script data-name="em-faqs-plugin-sd" type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": <?= json_encode($faq_json) ?>
      }
    </script>
<?php
  });

  return sprintf('<ul class="em-faqs">%s</ul>', implode('', $faq_list));
}
