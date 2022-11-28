<?php

require_once 'em-faq-plugin-css.php';

add_action('init', function () {
  add_shortcode(shortcode_exists('faq') ? 'em-faq' : 'faq', 'em_faq_plugin_shortcode');
});

function em_faq_plugin_shortcode($atts = []) {
  global $post;

  $faqs = get_post_meta($post->ID, 'emfaqs', true);

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


  // faq html
  $faq_list = [];

  // faq json (structured data)
  $faq_json = [];

  /* Creating html and json */
  foreach ($faqs as $faq) {
    if (empty($faq['question']) || empty($faq['answer'])) continue;

    $faq_json[] = [
      '@type' => 'Question',
      'name' => $faq['question'],
      'acceptedAnswer' => [
        '@type' => 'answer',
        'text' => $faq['answer']
      ]
    ];

    $faq_list[] = <<<HTML
                    <li class="em-faqs">
                      <details class="em-faqs">
                        <summary class="em-faqs"><h3 class="em-faqs">{$faq['question']}</h3></summary>
                        <div class="em-faqs">{$faq['answer']}</div>
                      </details>
                    </li>
                  HTML;
  }

  add_action('wp_footer', function () use ($css, $faq_json) {
    $json = json_encode($faq_json);
    echo <<<OUT
      <style data-name="em-faqs-plugin">
        $css
      </style>
       <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": $json
      }
      </script>
    OUT;
  });

  return sprintf('<ul class="em-faqs">%s</ul>', implode('', $faq_list));
}
