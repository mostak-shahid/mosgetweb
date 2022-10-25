(function() {
       tinymce.PluginManager.add('mos_mce_button', function( editor, url ) {
           editor.addButton('mos_mce_button', {
                       text: 'Button',
                       icon: false,
                       onclick: function() {
                         // change the shortcode as per your requirement
                          editor.insertContent('[mos-button url="#" title="Button" class="button"]');
                      }
             });
       });
})();