/* ──────────────────────────────────────────────────────────────────────────
   Syncsity — Conversational Assessment Engine
   ────────────────────────────────────────────────────────────────────────── */
(function () {
  'use strict';

  const root        = document.querySelector('[data-assess]');
  if (!root) return;

  const schemaEl    = document.getElementById('assess-schema');
  if (!schemaEl) return;

  let schema;
  try { schema = JSON.parse(schemaEl.textContent); }
  catch (e) { console.error('[assess] schema parse error', e); return; }

  const stage       = root.querySelector('[data-stage]');
  const progressBar = root.querySelector('[data-progress-bar]');
  const counter     = root.querySelector('[data-counter]');
  const csrf        = root.querySelector('input[name="_csrf"]')?.value || '';

  /* ── State ─────────────────────────────────────────────────────────── */
  const state = {
    answers: {},      // { qid: value }
    history: [],      // [qid, qid, ...] — actually visited
    currentId: null,
  };

  /* Restore from sessionStorage (if mid-form) + URL email prefill */
  try {
    const saved = JSON.parse(sessionStorage.getItem('syncsity-assess') || 'null');
    if (saved && saved.answers) Object.assign(state.answers, saved.answers);
  } catch (_) {}
  try {
    const prefill = sessionStorage.getItem('syncsity-prefill-email');
    if (prefill && !state.answers.email) state.answers.email = prefill;
    const params = new URLSearchParams(location.search);
    const urlEmail = params.get('email');
    if (urlEmail && !state.answers.email) state.answers.email = urlEmail.toLowerCase().trim();
  } catch (_) {}

  /* ── Question lookup by id ─────────────────────────────────────────── */
  const byId = {};
  schema.questions.forEach(function (q) { byId[q.id] = q; });

  /* ── Resolve "next" — supports static or per-answer branching ──────── */
  function nextId(q, answer) {
    if (typeof q.next === 'string') return q.next;
    if (q.next && typeof q.next === 'object') {
      if (Array.isArray(answer)) {
        for (let i = 0; i < answer.length; i++) {
          if (q.next[answer[i]]) return q.next[answer[i]];
        }
      } else if (q.next[answer]) {
        return q.next[answer];
      }
      if (q.next['*']) return q.next['*'];
      if (q.next['_default']) return q.next['_default'];
    }
    // Fallback: next in declaration order
    const idx = schema.questions.findIndex(x => x.id === q.id);
    return schema.questions[idx + 1] ? schema.questions[idx + 1].id : null;
  }

  /* ── Render a single question ──────────────────────────────────────── */
  function render(q) {
    state.currentId = q.id;
    stage.innerHTML = '';

    const numIdx = state.history.length + 1;
    const total  = schema.estimatedTotal || schema.questions.length;
    if (counter) counter.textContent = numIdx + ' / ~' + total;
    if (progressBar) progressBar.style.width = Math.min(100, (numIdx / total) * 100) + '%';

    const wrap = document.createElement('div');
    wrap.className = 'q is-active';

    const num = document.createElement('div');
    num.className = 'q__num';
    num.textContent = (q.label || ('Question ' + numIdx));
    wrap.appendChild(num);

    const title = document.createElement('h2');
    title.className = 'q__title';
    title.textContent = q.title;
    wrap.appendChild(title);

    if (q.subtitle) {
      const sub = document.createElement('p');
      sub.className = 'q__sub';
      sub.textContent = q.subtitle;
      wrap.appendChild(sub);
    }

    const fieldWrap = document.createElement('div');
    fieldWrap.className = 'q__field';
    wrap.appendChild(fieldWrap);

    const errBox = document.createElement('div');
    errBox.className = 'field__error'; errBox.style.minHeight = '20px'; errBox.style.display = 'none';
    wrap.appendChild(errBox);

    const actions = document.createElement('div');
    actions.className = 'q__actions';
    wrap.appendChild(actions);

    /* ── Field by type ─────────────────────────────────────────────── */
    let getValue = function () { return null; };
    let isEmpty  = function () { return false; };

    if (q.type === 'text' || q.type === 'email' || q.type === 'url' || q.type === 'number' || q.type === 'currency') {
      const inp = document.createElement('input');
      inp.className = 'input';
      inp.type = q.type === 'currency' ? 'number' : (q.type === 'number' ? 'number' : (q.type === 'email' ? 'email' : (q.type === 'url' ? 'url' : 'text')));
      inp.placeholder = q.placeholder || '';
      inp.autocomplete = q.autocomplete || 'off';
      if (q.type === 'currency') { inp.min = '0'; inp.step = '1'; inp.inputMode = 'numeric'; }
      if (q.type === 'number')   { inp.min = q.min || '0'; inp.inputMode = 'numeric'; }
      const existing = state.answers[q.id];
      if (existing != null) inp.value = existing;
      fieldWrap.appendChild(inp);

      // Currency adornment
      if (q.type === 'currency') {
        inp.style.paddingLeft = '36px';
        const sym = document.createElement('span');
        sym.textContent = q.currency || '£';
        sym.style.cssText = 'position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-family:var(--font-mono);pointer-events:none;font-size:1rem;';
        fieldWrap.style.position = 'relative';
        fieldWrap.appendChild(sym);
      }

      setTimeout(() => inp.focus(), 50);

      getValue = function () { return inp.value.trim(); };
      isEmpty  = function () { return inp.value.trim() === ''; };

      inp.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); advance(); }
      });
    }

    else if (q.type === 'textarea') {
      const ta = document.createElement('textarea');
      ta.className = 'textarea';
      ta.placeholder = q.placeholder || '';
      ta.rows = 5;
      const existing = state.answers[q.id];
      if (existing != null) ta.value = existing;
      fieldWrap.appendChild(ta);
      setTimeout(() => ta.focus(), 50);
      getValue = function () { return ta.value.trim(); };
      isEmpty  = function () { return ta.value.trim() === ''; };
      ta.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && (e.metaKey || e.ctrlKey)) { e.preventDefault(); advance(); }
      });
    }

    else if (q.type === 'single' || q.type === 'multi') {
      const list = document.createElement('div');
      list.className = 'choices';
      fieldWrap.appendChild(list);

      const selected = q.type === 'multi'
        ? new Set(Array.isArray(state.answers[q.id]) ? state.answers[q.id] : [])
        : new Set(state.answers[q.id] != null ? [state.answers[q.id]] : []);

      const keys = ['1','2','3','4','5','6','7','8','9'];
      q.options.forEach(function (opt, i) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'choice';
        btn.dataset.value = opt.value || opt.label;
        if (selected.has(btn.dataset.value)) btn.classList.add('is-selected');
        btn.innerHTML = '<span class="choice__key">' + (keys[i] || '') + '</span><span class="choice__label">' + escapeHtml(opt.label) + '</span>';
        list.appendChild(btn);

        btn.addEventListener('click', function () {
          if (q.type === 'multi') {
            if (selected.has(btn.dataset.value)) {
              selected.delete(btn.dataset.value);
              btn.classList.remove('is-selected');
            } else {
              selected.add(btn.dataset.value);
              btn.classList.add('is-selected');
            }
          } else {
            list.querySelectorAll('.choice').forEach(c => c.classList.remove('is-selected'));
            btn.classList.add('is-selected');
            selected.clear();
            selected.add(btn.dataset.value);
            // Auto-advance for single-choice
            setTimeout(advance, 220);
          }
        });
      });

      getValue = function () {
        return q.type === 'multi' ? Array.from(selected) : (Array.from(selected)[0] || '');
      };
      isEmpty = function () {
        return q.type === 'multi' ? selected.size === 0 : !Array.from(selected)[0];
      };
    }

    else if (q.type === 'scale') {
      const wrap2 = document.createElement('div');
      wrap2.className = 'scale';
      const min = q.min ?? 0; const max = q.max ?? 10;
      const existing = state.answers[q.id];
      let chosen = existing != null ? String(existing) : null;
      for (let i = min; i <= max; i++) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'scale__btn';
        if (String(i) === chosen) b.classList.add('is-selected');
        b.textContent = i;
        b.dataset.value = i;
        b.addEventListener('click', function () {
          wrap2.querySelectorAll('.scale__btn').forEach(x => x.classList.remove('is-selected'));
          b.classList.add('is-selected');
          chosen = String(i);
          setTimeout(advance, 220);
        });
        wrap2.appendChild(b);
      }
      fieldWrap.appendChild(wrap2);
      if (q.scaleLabels) {
        const labels = document.createElement('div');
        labels.style.cssText = 'display:flex;justify-content:space-between;color:var(--text-dim);font-size:0.82rem;margin-top:8px;font-family:var(--font-mono);';
        labels.innerHTML = '<span>' + escapeHtml(q.scaleLabels[0] || '') + '</span><span>' + escapeHtml(q.scaleLabels[1] || '') + '</span>';
        fieldWrap.appendChild(labels);
      }
      getValue = function () { return chosen != null ? chosen : ''; };
      isEmpty  = function () { return chosen == null; };
    }

    /* ── Action buttons ─────────────────────────────────────────────── */
    if (state.history.length > 0) {
      const back = document.createElement('button');
      back.type = 'button';
      back.className = 'btn btn--ghost btn--sm';
      back.innerHTML = '← Back';
      back.addEventListener('click', goBack);
      actions.appendChild(back);
    }

    if (q.optional) {
      const skip = document.createElement('button');
      skip.type = 'button';
      skip.className = 'btn btn--link';
      skip.textContent = 'Skip';
      skip.addEventListener('click', function () { advance(true); });
      actions.appendChild(skip);
    }

    const next = document.createElement('button');
    next.type = 'button';
    next.className = 'btn btn--primary';
    const isLast = !nextId(q, '');
    next.innerHTML = (isLast ? 'Generate my report' : (q.cta || 'OK')) + ' <span class="arrow">→</span>';
    next.addEventListener('click', function () { advance(); });
    actions.appendChild(next);

    const hint = document.createElement('span');
    hint.className = 'q__hint';
    hint.innerHTML = 'press <kbd>Enter</kbd> ↵';
    actions.appendChild(hint);

    stage.appendChild(wrap);

    /* ── Advance handler ────────────────────────────────────────────── */
    function advance(skipped) {
      const v = skipped ? '' : getValue();
      if (!skipped && q.required !== false && isEmpty()) {
        errBox.textContent = q.errorRequired || 'This one matters — please answer.';
        errBox.style.display = 'block';
        return;
      }
      if (!skipped && q.validate) {
        const err = validate(q, v);
        if (err) {
          errBox.textContent = err;
          errBox.style.display = 'block';
          return;
        }
      }
      errBox.style.display = 'none';
      state.answers[q.id] = v;
      persist();

      const nid = nextId(q, v);
      if (!nid) return submitAll();

      state.history.push(q.id);
      const nq = byId[nid];
      if (!nq) return submitAll();
      render(nq);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ── Keyboard 1-9 for single choice ─────────────────────────────── */
    if (q.type === 'single' || q.type === 'multi') {
      const handler = function (e) {
        const num = parseInt(e.key, 10);
        if (!isNaN(num) && num >= 1 && num <= 9) {
          const btn = list.querySelectorAll('.choice')[num - 1];
          if (btn) btn.click();
        } else if (e.key === 'Enter' && q.type === 'multi') {
          advance();
        }
      };
      window.addEventListener('keydown', handler);
      // Clean up when this question is replaced
      const obs = new MutationObserver(function () {
        if (!stage.contains(wrap)) {
          window.removeEventListener('keydown', handler);
          obs.disconnect();
        }
      });
      obs.observe(stage, { childList: true });
    }
  }

  function goBack() {
    const prev = state.history.pop();
    if (prev && byId[prev]) {
      render(byId[prev]);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }

  function persist() {
    try {
      sessionStorage.setItem('syncsity-assess', JSON.stringify({ answers: state.answers, history: state.history }));
    } catch (_) {}
  }

  function validate(q, v) {
    if (q.type === 'email')   { if (!/^[^@\s]+@[^@\s]+\.[^@\s]{2,}$/.test(v)) return 'That email looks off — try again.'; }
    if (q.type === 'url' && v) { try { new URL(/^https?:/.test(v) ? v : 'https://' + v); } catch (_) { return 'That URL doesn\'t look right.'; } }
    if (q.minLength && v.length < q.minLength) return 'A bit more detail helps the report (min ' + q.minLength + ' chars).';
    return null;
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c];
    });
  }

  /* ── Submit ────────────────────────────────────────────────────────── */
  function submitAll() {
    stage.innerHTML = '<div class="q is-active" style="text-align:center"><h2 class="q__title">Reading your business…</h2><p class="q__sub">We\'re researching your industry and your specific constraint. Hold on for a moment.</p><div style="display:flex;gap:8px;justify-content:center;margin-top:24px"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div></div>';
    if (progressBar) progressBar.style.width = '100%';
    const style = document.createElement('style');
    style.textContent = '.dot{width:8px;height:8px;border-radius:50%;background:var(--accent);animation:pulse 1.4s ease-in-out infinite}.dot:nth-child(2){animation-delay:0.2s}.dot:nth-child(3){animation-delay:0.4s}@keyframes pulse{0%,100%{opacity:0.3;transform:scale(0.8)}50%{opacity:1;transform:scale(1.2)}}';
    document.head.appendChild(style);

    const formData = new FormData();
    formData.append('_csrf', csrf);
    formData.append('answers', JSON.stringify(state.answers));
    Object.keys(state.answers).forEach(function (k) {
      const v = state.answers[k];
      formData.append('a[' + k + ']', Array.isArray(v) ? v.join('|') : (v == null ? '' : String(v)));
    });

    fetch('/api/assess-submit', { method: 'POST', body: formData, credentials: 'same-origin' })
      .then(r => r.json())
      .then(function (res) {
        if (res && res.ok) {
          try { sessionStorage.removeItem('syncsity-assess'); } catch (_) {}
          window.location.href = res.redirect || '/assess/processing';
        } else {
          stage.innerHTML = '<div class="q is-active"><h2 class="q__title">Couldn\'t save your assessment</h2><p class="q__sub">' + escapeHtml(res && res.error || 'Please try again in a moment.') + '</p><button class="btn btn--primary" onclick="window.location.reload()">Try again</button></div>';
        }
      })
      .catch(function (err) {
        console.error('[assess] submit error', err);
        stage.innerHTML = '<div class="q is-active"><h2 class="q__title">Network hiccup</h2><p class="q__sub">Refresh and try again — your answers are saved.</p><button class="btn btn--primary" onclick="window.location.reload()">Reload</button></div>';
      });
  }

  /* ── Boot ──────────────────────────────────────────────────────────── */
  const startId = schema.start || (schema.questions[0] && schema.questions[0].id);
  if (startId && byId[startId]) {
    // If an answer already exists for the start (e.g., email prefilled), advance past it
    if (state.answers[startId] && byId[startId].skipIfFilled) {
      const nid = nextId(byId[startId], state.answers[startId]);
      if (nid && byId[nid]) {
        state.history.push(startId);
        render(byId[nid]);
      } else {
        render(byId[startId]);
      }
    } else {
      render(byId[startId]);
    }
  }
})();
