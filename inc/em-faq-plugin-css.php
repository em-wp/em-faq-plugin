<?php


if (!class_exists('Faq_css')) {
  class Faq_css {

    static $one = <<<CSS
    ul.em-faqs {
      display: flex;
      flex-direction: column;
      align-items: start;
      padding: 0;
      list-style: none;
    }

    li.em-faqs {
      margin-bottom: 20px;
    }

    summary.em-faqs {
      cursor: pointer;
    }

    h3.em-faqs {
      display: inline-block;
      margin: 0;
      font-weight: 400;
      font-size: 16px;
    }

    div.em-faqs {
      padding: 5px 15px;
      background-color: hsl(120, 3%, 93%);
      border-radius: 3px;
    }

    div.em-faqs > p:last-child {
      margin: 0;
    }
    CSS;


    static $two = <<<CSS
    ul.em-faqs {
      list-style: none;
      padding: 0;
    }
    h3.em-faqs {
      display: inline-block;
      margin: 0;
    }
    CSS;

    static $three = <<<CSS
    ul.em-faqs {
      list-style: none;
      padding: 10px 20px;
      background-color: %s;
      color: %s;
      border-radius: 10px;
    }
    h3.em-faqs {
      display: inline-block;
      margin: 0;
    }

    summary.em-faqs {
    }

    div.em-faqs {
      background-color: %s;
      color: %s;
      padding: 3px 3px 3px 19px;
      border-radius: 10px;
    }
    CSS;
  }
}
