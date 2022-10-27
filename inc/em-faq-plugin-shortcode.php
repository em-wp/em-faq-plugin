<?php


add_action('init', function () {
  add_shortcode('faq', 'em_faq_plugin_shortcode');
});

function em_faq_plugin_shortcode($atts = []) {
  global $post;

  $faqs = get_post_meta($post->ID, 'em_faqs', true);
  if (empty($faqs)) return;

  $faqs = json_decode($faqs, true);
  if (empty($faqs['blocks'])) return;


  $css_one = <<<CSS
    ul.em-faq {
      padding: 0;
      list-style: none;
    }
    li.em-faqs {
      margin-bottom: 20px;
    }
    summary.em-faqs {
      cursor: pointer;
    }

    h6.em-faqs {
      display: inline-block;
      margin: 0;
      font-weight: 400;
      font-size: 16px;
      border-bottom: solid 1px black;
    }

    div.em-faqs {
      padding-left: 18px;
    }
  CSS;


  $faq_list = [];

  $faq_json = [];


  foreach ($faqs['blocks'] as $faq) {
    if (empty($faq['data']) || empty($faq['data']['question']) || empty($faq['data']['answer'])) continue;

    $faq_json[] = [
      '@type' => 'Question',
      'name' => $faq['data']['question'],
      'acceptedAnswer' => [
        '@type' => 'answer',
        'text' => $faq['data']['answer']
      ]
    ];

    $faq_list[] = <<<HTML
                    <li class="em-faqs">
                      <details class="em-faqs">
                        <summary class="em-faqs"><h6 class="em-faqs">{$faq['data']['question']}</h6></summary>
                        <div class="em-faqs">{$faq['data']['answer']}</div>
                      </details>
                    </li>
                  HTML;
  }

  add_action('wp_footer', function () use ($css_one, $faq_json) {
    $json = json_encode($faq_json);
    echo <<<OUT
      <style data-name="em-faqs-plugin">
        $css_one
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

  return sprintf('<ul class="em-faq">%s</ul>', implode('', $faq_list));
}
