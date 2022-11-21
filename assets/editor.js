(() => {
  class Text {}
  class FAQ {
    static get toolbox() {
      return {
        title: 'FAQ',
        icon: 'F',
      };
    }

    static get sanitize() {
      return {
        div: false,
        h1: false,
        h2: false,
        h3: false,
        p: true,
        ul: true,
        li: true,
        b: true,
        img: true,
      };
    }

    static get pasteConfig() {
      return {
        tags: [
          'H1',
          'H2',
          'H3',
          'H4',
          'H5',
          'H6',
          'P',
          'STRONG',
          'I',
          'UL',
          'LI',
          'DIV',
        ],
      };
    }

    constructor({ data, api }) {
      this._api = api;
      this._data = { answer: '', question: '' };
      this._element = this.createTag();
      this.data = data;

      // this._api.listeners.on(this._element);
    }

    set data(data) {
      this._data = data;

      this._question.innerHTML = data.question ?? '';
      this._answer.innerHTML = data.answer ?? '';
    }

    createTag() {
      const div = document.createElement('div');
      div.classList.add('faq-plugin-container');

      const qTitle = document.createElement('div');
      qTitle.classList.add('faq-plugin-title', 'faq-plugin-question-title');
      div.appendChild(qTitle);

      const question = document.createElement('question');
      question.setAttribute('contentEditable', 'true');

      this._question = question;

      const answer = document.createElement('answer');
      answer.setAttribute('contentEditable', 'true');

      this._answer = answer;

      div.appendChild(question);

      const aTitle = document.createElement('div');
      aTitle.classList.add('faq-plugin-title', 'faq-plugin-answer-title');
      div.appendChild(aTitle);

      div.appendChild(answer);
      // console.log(div);
      return div;
    }

    onPaste(event) {
      console.log(event);
      if (event.type !== 'tag') return;

      // if (event.detail.data.tagName === 'UL') {
      this._answer.appendChild(event.detail.data);
      // }
      // if (event.detail)
      // switch (event.detail)
      // console.log(event);
      // console.log(event.detail.data.tagName);
    }

    render() {
      // console.log(this._element);
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
    inlineToolbar: true,
    tools: {
      faq: { class: FAQ, inlineToolbar: true },
      paragraph: Text,
    },
    defaultBlock: 'faq',
    data: faqsData,
    onChange: (api, event) => {
      event.preventDefault();
      console.log(event);
    },
  });

  const form = document.querySelector('form#post');
  const faqs = document.querySelector('input#faqs');
  form.addEventListener('submit', (event) => {
    // event.preventDefault();
    editor.save().then((outputData) => {
      faqs.value = JSON.stringify(outputData);
      // form.submit();
    });
  });
})();
