(function(wp){
  const { __ } = wp.i18n;
  const { InspectorControls } = wp.blockEditor || wp.editor;
  const { PanelBody, TextControl, SelectControl, ToggleControl, RangeControl } = wp.components;

  wp.blocks.registerBlockType('gnsa/chat', {
    edit: function(props){
      const { attributes, setAttributes } = props;
      return [
        wp.element.createElement(InspectorControls, { key: 'inspector' },
          wp.element.createElement(PanelBody, { title: __('Chat Settings','gpt5-nano-site-assistant'), initialOpen: true },
            wp.element.createElement(TextControl, {
              label: __('Model (leave empty for default)','gpt5-nano-site-assistant'),
              value: attributes.model || '',
              onChange: v => setAttributes({ model: v })
            }),
            wp.element.createElement(SelectControl, {
              label: __('Language','gpt5-nano-site-assistant'),
              value: attributes.language || 'auto',
              options: [
                {label: 'Auto', value:'auto'},
                {label: 'Turkish', value:'tr'},
                {label: 'English', value:'en'},
              ],
              onChange: v => setAttributes({ language: v })
            }),
            wp.element.createElement(RangeControl, {
              label: __('Max characters (UI)','gpt5-nano-site-assistant'),
              value: attributes.maxChars || 1200,
              min: 200, max: 5000,
              onChange: v => setAttributes({ maxChars: v })
            }),
            wp.element.createElement(ToggleControl, {
              label: __('Show sources','gpt5-nano-site-assistant'),
              checked: !!attributes.showSources,
              onChange: v => setAttributes({ showSources: !!v })
            })
          )
        ),
        wp.element.createElement('div', { className:'gnsa-block-preview', key: 'preview' },
          wp.element.createElement('div', { className:'components-placeholder is-large' },
            wp.element.createElement('div', { className:'components-placeholder__label' }, 'GPT-5 Nano Site Assistant'),
            wp.element.createElement('div', { className:'components-placeholder__instructions' }, __('The chat widget will render on the front-end.', 'gpt5-nano-site-assistant'))
          )
        )
      ];
    },
    save: function(){ return null; }
  });
})(window.wp);
