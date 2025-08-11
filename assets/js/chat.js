(function($){
  function appendLine($log, who, text){
    const $row = $('<div/>').addClass('gnsa-line ' + (who==='user'?'text-end':'text-start'));
    const $sr = $('<span/>').addClass('visually-hidden').text(who==='user' ? GNSA.i18n.you+': ' : GNSA.i18n.assistant+': ');
    $row.append($sr).append(document.createTextNode(text));
    $log.append($row);
    $log.scrollTop($log.prop('scrollHeight'));
  }

  $(document).on('submit', '.gnsa-form', async function(e){
    e.preventDefault();
    const $form = $(this);
    const $wrap = $form.closest('.gnsa');
    const $log = $wrap.find('.gnsa-log');
    const $input = $form.find('.gnsa-input');
    const msg = ($input.val() || '').trim();
    if(!msg) return;

    appendLine($log, 'user', msg);
    $input.val('');
    const $typing = $('<div/>').addClass('gnsa-line typing text-start').text(GNSA.i18n.typing);
    $log.append($typing);

    const payload = {
      question: msg,
      model: $form.data('model') || '',
      language: $form.data('language') || 'auto',
      maxchars: parseInt($form.data('maxchars') || '1200', 10),
      showsources: ($form.data('showsources') === true || $form.data('showsources') === 'true')
    };

    try {
      const r = await fetch(GNSA.restUrl, {
        method: 'POST',
        headers: {'Content-Type':'application/json', 'X-WP-Nonce': GNSA.nonce},
        body: JSON.stringify(payload)
      });
      const j = await r.json();
      $typing.remove();
      if(r.ok){
        appendLine($log, 'bot', j.answer || '—');
        if(j.sources && j.sources.length){
          const $src = $('<div/>').addClass('gnsa-sources small text-muted mt-2');
          j.sources.forEach(s => {
            const a = $('<a/>').attr('href', s.url).attr('target','_blank').text(s.title);
            $src.append(a).append(' · ');
          });
          $log.append($src);
          $log.scrollTop($log.prop('scrollHeight'));
        }
      } else {
        appendLine($log, 'bot', 'Error: ' + (j.message || r.status));
      }
    } catch(err){
      $typing.remove();
      appendLine($log, 'bot', 'Network error: ' + (err && err.message ? err.message : 'Unknown'));
    }
  });
})(jQuery);
