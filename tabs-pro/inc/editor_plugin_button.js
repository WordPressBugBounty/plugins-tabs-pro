(function() {

	tinymce.create('tinymce.plugins.tptabsultimates', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished its initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {

			//this command will be executed when the button in the toolbar is clicked
			ed.addCommand('mceTpTabs', function() {

				selection = tinyMCE.activeEditor.selection.getContent();

				//prompt for a tag to use


				var tptabs_ultimate = '';
				tptabs_ultimate += '[tptabs_ultimate width="" initialtab=1 autoplayinterval=0 color="dark"]<br/>';
				tptabs_ultimate += '[tptabs_tab_container]<br/>';
				tptabs_ultimate += '[tptabs_tab]First Tab[/tptabs_tab]<br/>' + '[tptabs_tab]Second Tab[/tptabs_tab]<br/>' + '[tptabs_tab]Third Tab[/tptabs_tab]<br/>';
				tptabs_ultimate += '[/tptabs_tab_container]<br/>[tptabs_content_container]<br/>';
				tptabs_ultimate += '[tptabs_content]Content For First Tab.[/tptabs_content]<br/>';
				tptabs_ultimate += '[tptabs_content]Content For Second Tab.[/tptabs_content]<br/>';
				tptabs_ultimate += '[tptabs_content]Content For Third Tab.[/tptabs_content]<br/>';
				tptabs_ultimate += '[/tptabs_content_container]<br/>[/tptabs_ultimate]';

				tinyMCE.activeEditor.selection.setContent(tptabs_ultimate);

			});

			ed.addButton('tptabs_ultimate', {
				title : 'tptabs_ultimate',
				cmd : 'mceTpTabs',
				image : url + '/display-icon.png'
			});
		},
	});

	// Register plugin
	tinymce.PluginManager.add('TptabsUltimate', tinymce.plugins.tptabsultimates);
})();