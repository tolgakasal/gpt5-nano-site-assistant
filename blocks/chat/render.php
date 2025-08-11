<?php
echo gnsa_render_widget([
  'model' => isset($attributes['model']) ? $attributes['model'] : '',
  'language' => isset($attributes['language']) ? $attributes['language'] : 'auto',
  'maxchars' => isset($attributes['maxChars']) ? intval($attributes['maxChars']) : 1200,
  'showsources' => !empty($attributes['showSources']) ? 'true' : 'false',
]);
