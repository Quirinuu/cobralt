(function () {
  'use strict';

  var state = {
    blocks: Array.isArray(window.PAGE_BUILDER_BLOCKS) && window.PAGE_BUILDER_BLOCKS.length
      ? window.PAGE_BUILDER_BLOCKS
      : [templateBlock('hero'), templateBlock('rich_text')],
    selected: 0
  };

  var typeLabels = {
    hero: 'Hero',
    rich_text: 'Texto',
    cards: 'Cards',
    image_text: 'Imagem + texto',
    stats: 'Numeros',
    cta: 'Chamada',
    gallery: 'Galeria',
    html: 'HTML'
  };

  function uid(prefix) {
    return prefix + '-' + Math.random().toString(16).slice(2, 10);
  }

  function templateBlock(type) {
    var base = {
      id: uid(type),
      type: type,
      enabled: true,
      background: '#ffffff',
      text_color: '#1E293B',
      accent_color: '#0EA5E9',
      padding: 'normal',
      align: 'left',
      width: 'default'
    };

    if (type === 'hero') {
      return Object.assign(base, {
        background: '#002B4E',
        text_color: '#ffffff',
        accent_color: '#38BDF8',
        padding: 'spacious',
        align: 'center',
        width: 'wide',
        eyebrow: 'CoBraLT',
        title: 'Titulo da pagina',
        text: 'Texto de apoio da secao principal.',
        show_button: true,
        button_label: 'Saiba mais',
        button_url: '#',
        show_second_button: false,
        second_button_label: '',
        second_button_url: '',
        show_image: false,
        image_url: '',
        image_alt: ''
      });
    }

    if (type === 'cards') {
      return Object.assign(base, {
        eyebrow: 'Secao',
        title: 'Cards personalizaveis',
        text: 'Use cards para beneficios, topicos, servicos ou destaques.',
        columns: '3',
        items: [
          { icon: '+', title: 'Card 1', text: 'Texto do card.', button_label: '', button_url: '' },
          { icon: '+', title: 'Card 2', text: 'Texto do card.', button_label: '', button_url: '' },
          { icon: '+', title: 'Card 3', text: 'Texto do card.', button_label: '', button_url: '' }
        ]
      });
    }

    if (type === 'image_text') {
      return Object.assign(base, {
        eyebrow: 'Destaque',
        title: 'Titulo do bloco',
        text: 'Texto ao lado da imagem.',
        image_url: '',
        image_alt: '',
        image_position: 'right',
        show_button: false,
        button_label: '',
        button_url: ''
      });
    }

    if (type === 'stats') {
      return Object.assign(base, {
        align: 'center',
        eyebrow: 'Indicadores',
        title: 'Numeros importantes',
        text: '',
        items: [
          { value: '180+', label: 'instituicoes' },
          { value: '5', label: 'regioes' },
          { value: '2003', label: 'fundacao' }
        ]
      });
    }

    if (type === 'cta') {
      return Object.assign(base, {
        background: '#002B4E',
        text_color: '#ffffff',
        accent_color: '#38BDF8',
        align: 'center',
        eyebrow: 'Chamada',
        title: 'Convite para acao',
        text: 'Texto curto para direcionar o visitante.',
        show_button: true,
        button_label: 'Acessar',
        button_url: '#'
      });
    }

    if (type === 'gallery') {
      return Object.assign(base, {
        eyebrow: 'Galeria',
        title: 'Imagens',
        text: '',
        items: []
      });
    }

    if (type === 'html') {
      return Object.assign(base, {
        width: 'narrow',
        html: '<p>Conteudo HTML permitido.</p>'
      });
    }

    return Object.assign(base, {
      width: 'narrow',
      show_title: true,
      title: 'Titulo da secao',
      body: '<p>Texto da secao.</p>'
    });
  }

  function el(id) {
    return document.getElementById(id);
  }

  function escapeHtml(value) {
    return String(value || '').replace(/[&<>"']/g, function (char) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
    });
  }

  function blockTitle(block) {
    return block.title || block.eyebrow || typeLabels[block.type] || 'Bloco';
  }

  function renderBlockList(skipInspector) {
    var list = el('blocksList');
    if (!list) return;
    list.innerHTML = state.blocks.map(function (block, index) {
      return '<div class="builder-block' + (index === state.selected ? ' active' : '') + '" data-index="' + index + '">' +
        '<button type="button" class="builder-block-main" data-action="select">' +
          '<span class="builder-block-type">' + escapeHtml(typeLabels[block.type] || block.type) + '</span>' +
          '<strong>' + escapeHtml(blockTitle(block)) + '</strong>' +
          '<small>' + (block.enabled ? 'Ativo' : 'Oculto') + '</small>' +
        '</button>' +
        '<div class="builder-block-actions">' +
          '<button type="button" title="Mover para cima" data-action="up">↑</button>' +
          '<button type="button" title="Mover para baixo" data-action="down">↓</button>' +
          '<button type="button" title="Duplicar" data-action="duplicate">⧉</button>' +
          '<button type="button" title="Excluir" data-action="delete">×</button>' +
        '</div>' +
      '</div>';
    }).join('');
    if (!skipInspector) renderInspector();
  }

  function renderInspector() {
    var panel = el('blockInspector');
    var block = state.blocks[state.selected];
    if (!panel || !block) {
      if (panel) panel.innerHTML = '<div class="empty-builder">Adicione um bloco para editar.</div>';
      return;
    }

    var html = '<div class="inspector-head"><div><span>' + escapeHtml(typeLabels[block.type] || block.type) + '</span><h3>' + escapeHtml(blockTitle(block)) + '</h3></div></div>';
    html += '<div class="field-grid two">';
    html += checkboxField('enabled', 'Bloco visivel');
    html += selectField('padding', 'Espacamento', [['compact', 'Compacto'], ['normal', 'Normal'], ['spacious', 'Amplo']]);
    html += selectField('align', 'Alinhamento', [['left', 'Esquerda'], ['center', 'Centro']]);
    html += selectField('width', 'Largura', [['narrow', 'Estreita'], ['default', 'Padrao'], ['wide', 'Larga']]);
    html += colorField('background', 'Cor de fundo');
    html += colorField('text_color', 'Cor do texto');
    html += colorField('accent_color', 'Cor de destaque');
    html += '</div>';
    html += '<hr class="soft-sep">';
    html += renderTypeFields(block);
    panel.innerHTML = html;
  }

  function inputField(key, label, type, placeholder) {
    var block = state.blocks[state.selected];
    return '<label class="field"><span>' + label + '</span><input type="' + (type || 'text') + '" data-key="' + key + '" value="' + escapeHtml(block[key] || '') + '" placeholder="' + escapeHtml(placeholder || '') + '"></label>';
  }

  function textareaField(key, label, rows, placeholder) {
    var block = state.blocks[state.selected];
    return '<label class="field full"><span>' + label + '</span><textarea data-key="' + key + '" rows="' + (rows || 4) + '" placeholder="' + escapeHtml(placeholder || '') + '">' + escapeHtml(block[key] || '') + '</textarea></label>';
  }

  function colorField(key, label) {
    var block = state.blocks[state.selected];
    var value = block[key] || '#ffffff';
    return '<label class="field color-field"><span>' + label + '</span><input type="color" data-key="' + key + '" value="' + escapeHtml(value) + '"><input type="text" data-key="' + key + '" value="' + escapeHtml(value) + '"></label>';
  }

  function checkboxField(key, label) {
    var block = state.blocks[state.selected];
    return '<label class="toggle-field"><input type="checkbox" data-key="' + key + '"' + (block[key] ? ' checked' : '') + '><span>' + label + '</span></label>';
  }

  function selectField(key, label, options) {
    var block = state.blocks[state.selected];
    return '<label class="field"><span>' + label + '</span><select data-key="' + key + '">' + options.map(function (opt) {
      return '<option value="' + escapeHtml(opt[0]) + '"' + (String(block[key]) === String(opt[0]) ? ' selected' : '') + '>' + escapeHtml(opt[1]) + '</option>';
    }).join('') + '</select></label>';
  }

  function imageField(key, altKey, label) {
    var block = state.blocks[state.selected];
    return '<div class="field full image-field"><span>' + label + '</span>' +
      '<div class="image-row"><input type="text" data-key="' + key + '" value="' + escapeHtml(block[key] || '') + '" placeholder="assets/img/exemplo.png ou URL">' +
      '<label class="upload-btn">Upload<input type="file" data-upload-key="' + key + '" accept="image/*"></label></div>' +
      '<input type="text" data-key="' + altKey + '" value="' + escapeHtml(block[altKey] || '') + '" placeholder="Texto alternativo da imagem">' +
    '</div>';
  }

  function renderTypeFields(block) {
    var html = '<div class="field-grid">';
    if (block.type === 'hero') {
      html += inputField('eyebrow', 'Rotulo pequeno');
      html += inputField('title', 'Titulo');
      html += textareaField('text', 'Texto', 4);
      html += checkboxField('show_button', 'Mostrar botao principal');
      html += inputField('button_label', 'Texto do botao');
      html += inputField('button_url', 'Link do botao');
      html += checkboxField('show_second_button', 'Mostrar segundo botao');
      html += inputField('second_button_label', 'Texto do segundo botao');
      html += inputField('second_button_url', 'Link do segundo botao');
      html += checkboxField('show_image', 'Mostrar imagem');
      html += imageField('image_url', 'image_alt', 'Imagem');
    } else if (block.type === 'rich_text') {
      html += checkboxField('show_title', 'Mostrar titulo');
      html += inputField('title', 'Titulo');
      html += textareaField('body', 'Texto / HTML', 10, '<p>Texto com HTML basico permitido.</p>');
    } else if (block.type === 'cards') {
      html += inputField('eyebrow', 'Rotulo pequeno');
      html += inputField('title', 'Titulo');
      html += textareaField('text', 'Texto de apoio', 3);
      html += selectField('columns', 'Colunas', [['2', '2'], ['3', '3'], ['4', '4']]);
      html += '</div>' + itemsEditor(block, 'cards') + '<div class="field-grid">';
    } else if (block.type === 'image_text') {
      html += inputField('eyebrow', 'Rotulo pequeno');
      html += inputField('title', 'Titulo');
      html += textareaField('text', 'Texto', 6);
      html += selectField('image_position', 'Posicao da imagem', [['right', 'Direita'], ['left', 'Esquerda']]);
      html += imageField('image_url', 'image_alt', 'Imagem');
      html += checkboxField('show_button', 'Mostrar botao');
      html += inputField('button_label', 'Texto do botao');
      html += inputField('button_url', 'Link do botao');
    } else if (block.type === 'stats') {
      html += inputField('eyebrow', 'Rotulo pequeno');
      html += inputField('title', 'Titulo');
      html += textareaField('text', 'Texto de apoio', 3);
      html += '</div>' + itemsEditor(block, 'stats') + '<div class="field-grid">';
    } else if (block.type === 'cta') {
      html += inputField('eyebrow', 'Rotulo pequeno');
      html += inputField('title', 'Titulo');
      html += textareaField('text', 'Texto', 4);
      html += checkboxField('show_button', 'Mostrar botao');
      html += inputField('button_label', 'Texto do botao');
      html += inputField('button_url', 'Link do botao');
    } else if (block.type === 'gallery') {
      html += inputField('eyebrow', 'Rotulo pequeno');
      html += inputField('title', 'Titulo');
      html += textareaField('text', 'Texto de apoio', 3);
      html += '</div>' + itemsEditor(block, 'gallery') + '<div class="field-grid">';
    } else if (block.type === 'html') {
      html += textareaField('html', 'HTML permitido', 12, '<p>HTML basico.</p>');
    }
    html += '</div>';
    return html;
  }

  function itemsEditor(block, kind) {
    var items = Array.isArray(block.items) ? block.items : [];
    var title = kind === 'stats' ? 'Numeros' : (kind === 'gallery' ? 'Imagens' : 'Cards');
    return '<div class="items-editor"><div class="items-head"><h4>' + title + '</h4><button type="button" class="btn-mini" data-add-item="' + kind + '">Adicionar</button></div>' +
      items.map(function (item, index) {
        if (kind === 'stats') {
          return '<div class="item-row" data-item-index="' + index + '">' +
            '<input data-item-key="value" value="' + escapeHtml(item.value || '') + '" placeholder="Valor">' +
            '<input data-item-key="label" value="' + escapeHtml(item.label || '') + '" placeholder="Rotulo">' +
            '<button type="button" data-remove-item="' + index + '">×</button></div>';
        }
        if (kind === 'gallery') {
          return '<div class="item-card" data-item-index="' + index + '">' +
            '<div class="image-row"><input data-item-key="image_url" value="' + escapeHtml(item.image_url || '') + '" placeholder="Imagem ou URL">' +
            '<label class="upload-btn">Upload<input type="file" data-item-upload="' + index + '" accept="image/*"></label></div>' +
            '<input data-item-key="title" value="' + escapeHtml(item.title || '') + '" placeholder="Legenda">' +
            '<input data-item-key="image_alt" value="' + escapeHtml(item.image_alt || '') + '" placeholder="Texto alternativo">' +
            '<button type="button" data-remove-item="' + index + '">Remover</button></div>';
        }
        return '<div class="item-card" data-item-index="' + index + '">' +
          '<input data-item-key="icon" value="' + escapeHtml(item.icon || '') + '" placeholder="Icone curto">' +
          '<input data-item-key="title" value="' + escapeHtml(item.title || '') + '" placeholder="Titulo">' +
          '<textarea data-item-key="text" rows="3" placeholder="Texto">' + escapeHtml(item.text || '') + '</textarea>' +
          '<div class="item-two"><input data-item-key="button_label" value="' + escapeHtml(item.button_label || '') + '" placeholder="Texto do link"><input data-item-key="button_url" value="' + escapeHtml(item.button_url || '') + '" placeholder="URL"></div>' +
          '<button type="button" data-remove-item="' + index + '">Remover</button></div>';
      }).join('') + '</div>';
  }

  function setSelected(index) {
    state.selected = Math.max(0, Math.min(index, state.blocks.length - 1));
    renderBlockList();
  }

  function moveBlock(index, dir) {
    var target = index + dir;
    if (target < 0 || target >= state.blocks.length) return;
    var block = state.blocks[index];
    state.blocks.splice(index, 1);
    state.blocks.splice(target, 0, block);
    setSelected(target);
  }

  function uploadImage(file, callback) {
    var fd = new FormData();
    fd.append('csrf_token', window.CSRF || '');
    fd.append('image', file);
    showSaving('Enviando imagem...');
    fetch('../api/upload.php', { method: 'POST', body: fd })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        showSaving('');
        if (!data.success) throw new Error(data.message || 'Erro no upload.');
        callback(data.url);
        renderInspector();
      })
      .catch(function (err) {
        showSaving('');
        showToast(err.message || 'Erro no upload.', 'error');
      });
  }

  function showSaving(text) {
    var indicator = el('savingIndicator');
    if (indicator) indicator.textContent = text || '';
  }

  function showToast(msg, type) {
    var t = el('toast');
    if (!t) return;
    t.textContent = msg;
    t.className = 'toast ' + (type || 'success');
    t.style.display = 'block';
    clearTimeout(t._t);
    t._t = setTimeout(function () { t.style.display = 'none'; }, 3500);
  }

  window.savePage = function (forcedStatus) {
    var title = el('title').value.trim();
    var slug = el('slug') ? el('slug').value.trim() : '';
    var status = forcedStatus || (document.querySelector('input[name=statusRadio]:checked') || {}).value || 'draft';
    if (!title) {
      showToast('O titulo e obrigatorio.', 'error');
      return;
    }
    if (!state.blocks.length) {
      showToast('Adicione pelo menos um bloco.', 'error');
      return;
    }

    showSaving('Salvando...');
    var fd = new FormData();
    fd.append('action', window.PAGE_ID ? 'update' : 'create');
    fd.append('csrf_token', window.CSRF || '');
    fd.append('title', title);
    fd.append('slug', slug);
    fd.append('status', status);
    fd.append('blocks_json', JSON.stringify({ builder_version: 1, blocks: state.blocks }));
    if (window.PAGE_ID) fd.append('id', window.PAGE_ID);

    fetch('../api/pages.php', { method: 'POST', body: fd })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        showSaving('');
        if (!data.success) throw new Error(data.message || 'Erro ao salvar.');
        showToast(status === 'published' ? 'Pagina publicada!' : 'Rascunho salvo!');
        if (!window.PAGE_ID && data.id) {
          setTimeout(function () { window.location.href = 'page-editor.php?id=' + data.id; }, 900);
        }
        var radio = el(status === 'published' ? 'statusPublished' : 'statusDraft');
        if (radio) radio.checked = true;
      })
      .catch(function (err) {
        showSaving('');
        showToast(err.message || 'Erro ao salvar.', 'error');
      });
  };

  document.addEventListener('click', function (event) {
    var addType = event.target && event.target.getAttribute('data-add-block');
    if (addType) {
      state.blocks.push(templateBlock(addType));
      setSelected(state.blocks.length - 1);
      return;
    }

    var blockWrap = event.target.closest && event.target.closest('.builder-block');
    if (blockWrap) {
      var index = parseInt(blockWrap.getAttribute('data-index'), 10);
      var actionNode = event.target.closest('[data-action]');
      var action = event.target.getAttribute('data-action') || (actionNode ? actionNode.dataset.action : '');
      if (action === 'select') setSelected(index);
      if (action === 'up') moveBlock(index, -1);
      if (action === 'down') moveBlock(index, 1);
      if (action === 'duplicate') {
        var copy = JSON.parse(JSON.stringify(state.blocks[index]));
        copy.id = uid(copy.type);
        state.blocks.splice(index + 1, 0, copy);
        setSelected(index + 1);
      }
      if (action === 'delete' && confirm('Excluir este bloco?')) {
        state.blocks.splice(index, 1);
        setSelected(Math.min(index, state.blocks.length - 1));
      }
      return;
    }

    var addItem = event.target && event.target.getAttribute('data-add-item');
    if (addItem) {
      var block = state.blocks[state.selected];
      block.items = Array.isArray(block.items) ? block.items : [];
      if (addItem === 'stats') block.items.push({ value: '', label: '' });
      else if (addItem === 'gallery') block.items.push({ image_url: '', image_alt: '', title: '' });
      else block.items.push({ icon: '+', title: '', text: '', button_label: '', button_url: '' });
      renderInspector();
      return;
    }

    var removeItem = event.target && event.target.getAttribute('data-remove-item');
    if (removeItem !== null && removeItem !== undefined) {
      var selected = state.blocks[state.selected];
      selected.items.splice(parseInt(removeItem, 10), 1);
      renderInspector();
    }
  });

  document.addEventListener('input', function (event) {
    var target = event.target;
    var block = state.blocks[state.selected];
    if (!block) return;

    var key = target.getAttribute('data-key');
    if (key) {
      block[key] = target.type === 'checkbox' ? target.checked : target.value;
      renderBlockList(true);
      return;
    }

    var itemKey = target.getAttribute('data-item-key');
    if (itemKey) {
      var itemWrap = target.closest('[data-item-index]');
      var index = parseInt(itemWrap.getAttribute('data-item-index'), 10);
      block.items[index][itemKey] = target.value;
      renderBlockList(true);
    }
  });

  document.addEventListener('change', function (event) {
    var target = event.target;
    var block = state.blocks[state.selected];
    if (!block) return;

    var key = target.getAttribute('data-key');
    if (key) {
      block[key] = target.type === 'checkbox' ? target.checked : target.value;
      renderBlockList(true);
      return;
    }

    var uploadKey = target.getAttribute('data-upload-key');
    if (uploadKey && target.files && target.files[0]) {
      uploadImage(target.files[0], function (url) {
        block[uploadKey] = url;
      });
      return;
    }

    var itemUpload = target.getAttribute('data-item-upload');
    if (itemUpload !== null && itemUpload !== undefined && target.files && target.files[0]) {
      uploadImage(target.files[0], function (url) {
        block.items[parseInt(itemUpload, 10)].image_url = url;
      });
    }
  });

  renderBlockList();
})();
