(() => {
  class Text {}
  class FAQ {
    static get toolbox() {
      return {
        title: 'FAQ',
        icon: 'F',
      };
    }

    constructor({ data }) {
      this._data = { answer: '', question: '' };
      this._element = this.createTag();
      this.data = data;
    }

    set data(data) {
      this._data = data;

      this._question.innerHTML = data.question ?? '';
      this._answer.innerHTML = data.answer ?? '';
    }

    createTag() {
      const div = document.createElement('div');
      div.classList.add('faq-container');

      const question = document.createElement('question');
      question.setAttribute('contentEditable', 'true');

      this._question = question;

      const answer = document.createElement('answer');
      answer.setAttribute('contentEditable', 'true');

      this._answer = answer;

      div.appendChild(question);
      div.appendChild(answer);

      return div;
    }

    render() {
      console.log(this._element);
      return this._element;
    }

    save(BlockContent) {
      return {
        question: BlockContent.querySelector('question').innerHTML,
        answer: BlockContent.querySelector('answer').innerHTML,
      };
    }
  }

  const editor = new EditorJS({
    holder: 'em-faq-plugin-editor',
    tools: {
      faq: FAQ,
      paragraph: Text,
    },
    defaultBlock: 'faq',
    data: faqsData,
  });

  const form = document.querySelector('form#post');
  const faqs = document.querySelector('input#faqs');
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    editor.save().then((outputData) => {
      faqs.value = JSON.stringify(outputData);
      form.submit();
    });
  });
})();
