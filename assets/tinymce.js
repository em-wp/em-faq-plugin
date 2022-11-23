const removeFAQ = (element) => {
  console.log(element.parentNode);
  if (confirm('deleeeete the faq?')) element.parentNode.remove();
};

let FAQindex = -1;

const addFAQ = (element) => {
  try {
    const container = element.parentNode.firstElementChild;

    const newFAQ = document.createElement('li');
    newFAQ.classList.add('em-faq-container');

    if (FAQindex === -1)
      FAQindex =
        parseInt(container.lastElementChild?.getAttribute('data-faq') ?? -1) +
        1;
    else FAQindex++;

    newFAQ.setAttribute('data-faq', FAQindex);

    newFAQ.innerHTML = `
    <div class="em-faq-question">
      <h4 class="em-faq-title">Question</h4>
      <div id="em-faq-question-${FAQindex}"></div>
    </div>
    <div class="em-faq-answer">
      <h4 class="em-faq-title">Answer</h4>
      <div id="em-faq-answer-${FAQindex}"></div>
    </div>
    <button style="margin-top: 10px" class="button button-secondary" type="button" onclick="removeFAQ(this)">Remove FAQ</button>
  `;

    container.appendChild(newFAQ);
    wp.editor.initialize('em-faq-question-' + FAQindex, {
      tinymce: { toolbar1: 'bold,italic,underline,link,unlink,charmap' },
      quicktags: true,
    });
    wp.editor.initialize('em-faq-answer-' + FAQindex, {
      tinymce: {
        toolbar1:
          'formatselect,bold,italic,underline,bullist,numlist,blockquote,link,unlink',
        height: '150px',
      },
      quicktags: true,
    });
  } catch (error) {
    console.log(error);
  }
};
