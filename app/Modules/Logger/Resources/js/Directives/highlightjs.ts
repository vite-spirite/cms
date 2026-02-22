import hljs from 'highlight.js/lib/core';
import json from 'highlight.js/lib/languages/json';
import 'highlight.js/styles/github-dark.min.css';

hljs.registerLanguage('json', json);

export const vHighlight = {
    mounted(el: HTMLElement) {
        const block = el.querySelector('code');
        if (block) hljs.highlightElement(block);
    },
    updated(el: HTMLElement) {
        const block = el.querySelector('code');
        if (block) hljs.highlightElement(block);
    },
};
