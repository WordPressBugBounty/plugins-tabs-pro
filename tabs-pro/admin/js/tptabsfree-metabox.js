jQuery(document).ready(function($) {

    var previewTimer;

    /**
     * Fetch Live Frontend Preview via AJAX
     */
    function fetchExactFrontendPreview() {
        var $canvas = $('#tc-exact-preview-canvas');
        var $loader = $('#tc-preview-loading');
        var postId  = $('#post_ID').val();

        // 1. Serialize all form fields inside the metabox dynamically
        var formData = $('#tc-tabs-container input, #tc-tabs-container select, #tc-tabs-container textarea').serialize();

        $loader.show();
        $canvas.css('opacity', '0.5');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'tcaccordion_get_live_preview',
                security: $('#tcaccordion_nonce_field').val(),
                post_id: postId,
                form_data: formData // Pass live unsaved input data
            },
            success: function(response) {
                $loader.hide();
                $canvas.css('opacity', '1');

                if (response.success) {
                    $canvas.html(response.data.html);

                    // Re-trigger color/theme JS initialization scripts if needed
                    if (typeof initTCAccordionFrontend === 'function') {
                        initTCAccordionFrontend();
                    }
                } else {
                    $canvas.html('<p>' + response.data.message + '</p>');
                }
            },
            error: function() {
                $loader.hide();
                $canvas.css('opacity', '1');
                $canvas.html('<p>Error rendering live preview.</p>');
            }
        });
    }

    // Trigger preview fetch when switching to the Live Preview tab
    $(document).on('click', '.tc-tab-link[href="#tab-accordion-preview"]', function() {
        fetchExactFrontendPreview();
    });

    // Manual Refresh Button Click
    $(document).on('click', '#tc-refresh-preview-btn', function(e) {
        e.preventDefault();
        fetchExactFrontendPreview();
    });

    // Debounced automatic reload when metabox inputs change
    $(document).on('input change', '#tc-tabs-container input, #tc-tabs-container select, #tc-tabs-container textarea', function() {
        if ($('#tab-accordion-preview').hasClass('active')) {
            clearTimeout(previewTimer);
            previewTimer = setTimeout(fetchExactFrontendPreview, 600);
        }
    });

    /**
     * Helper to check and enforce maximum item limits (Free vs Pro)
     */
    function checkItemLimit() {
        var count = $('#tcaccordion-sortable .tcaccordion-item').length;
        if (typeof tcAccordion !== 'undefined' && count >= tcAccordion.maxItems) {
            $('#tcaccordion-add').prop('disabled', true);
        } else {
            $('#tcaccordion-add').prop('disabled', false);
        }
    }

    /**
     * Helper to re-initialize TinyMCE on a specific element or container
     */
    function initEditor($container) {
        $container.find('.wp-editor-area').each(function() {
            var $textarea = $(this);
            var id = $textarea.attr('id');
            if (!id) return;

            // 1. Remove existing TinyMCE instance if present
            if (typeof tinyMCE !== 'undefined' && tinyMCE.get(id)) {
                tinyMCE.execCommand('mceRemoveEditor', false, id);
            }

            // 2. Clean up duplicate wrapper markup injected by templates or old instances
            var $wrap = $textarea.closest('.wp-editor-wrap');
            if ($wrap.length) {
                var content = $textarea.val();
                // Replace the bloated wrapper with just the raw textarea to start fresh
                $wrap.replaceWith($textarea);
                $textarea.val(content);
                // Assign a fresh unique ID if it conflicts
                id = 'tc_desc_' + new Date().getTime() + Math.floor(Math.random() * 1000);
                $textarea.attr('id', id);
            }

            // 3. Re-initialize Quicktags (HTML Tab)
            if (typeof quicktags !== 'undefined') {
                try {
                    quicktags({ id: id });
                    QTags._init();
                } catch(e) {}
            }

            // 4. Re-initialize TinyMCE (Visual Tab) cleanly
            if (typeof tinyMCE !== 'undefined' && typeof wp !== 'undefined' && wp.editor) {
                wp.editor.initialize(id, {
                    tinymce: {
                        wpautop: true,
                        plugins: 'charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern',
                        toolbar1: 'bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv',
                        textarea_rows: 6
                    },
                    quicktags: true
                });
            }
        });
    }

    /**
     * 1. Initialize Sortable with TinyMCE Destruction Hooks & Re-index Callback
     */
    if ($('#tcaccordion-sortable').length) {
        $('#tcaccordion-sortable').sortable({
            handle: '.tcaccordion-header',
            items: '.tcaccordion-item',
            placeholder: 'tcaccordion-placeholder',
            start: function(event, ui) {
                // Before drag starts: Save editor content to raw textarea & remove TinyMCE instances
                ui.item.find('.wp-editor-area').each(function() {
                    var id = $(this).attr('id');
                    if (typeof tinyMCE !== 'undefined' && tinyMCE.get(id)) {
                        tinyMCE.get(id).save(); // Sync visual editor content to hidden textarea
                        tinyMCE.execCommand('mceRemoveEditor', false, id);
                    }
                });
            },
            stop: function(event, ui) {
                // After drag completes: Re-initialize TinyMCE on the dragged item
                initEditor(ui.item);
                reindexItems();
            }
        });
    }

    /**
     * 2. Re-index Input Names, IDs, and Header Titles After Sorting, Adding, or Removing
     */
    function reindexItems() {
        $('#tcaccordion-sortable .tcaccordion-item').each(function(index) {
            var $item = $(this);
            var tabNum = index + 1;
            
            // Re-index input title name
            $item.find('.tcaccordion-title').attr('name', 'custom_accordion_wordpresspro_columns[' + index + '][title]');
            
            // Re-index description textarea name
            $item.find('.wp-editor-area').attr('name', 'custom_accordion_wordpresspro_columns[' + index + '][description]');

            // Re-index icon inputs and legacy field tracking names for first-publish persistence
            $item.find('.tc-icon-input-field').attr('name', 'custom_accordion_wordpresspro_columns[' + index + '][icon]');
            $item.find('input[type="hidden"][name*="[field-14]"]').attr('name', 'custom_accordion_wordpresspro_columns[' + index + '][field-14]');

            // Dynamic Header Label Synchronization (Tab 1: Title, Tab 2: Title...)
            var userTitle = $item.find('.tcaccordion-title').val();
            userTitle = userTitle ? userTitle.trim() : '';
            var titleText = userTitle ? userTitle : 'New Accordion';
            $item.find('.tcaccordion-label').text('Tab ' + tabNum + ': ' + titleText);
        });
        checkItemLimit();
    }

    /**
     * 3. Add New Accordion Item
     */
    $(document).on('click', '#tcaccordion-add', function(e) {
        e.preventDefault();

        var count = $('#tcaccordion-sortable .tcaccordion-item').length;
        if (typeof tcAccordion !== 'undefined' && count >= tcAccordion.maxItems) {
            alert(tcAccordion.limitText);
            return;
        }

        var template = $('#tcaccordion-template').html();
        var newIndex = new Date().getTime(); // Unique timestamp key
        var itemHtml = template.replace(/__INDEX__/g, newIndex);

        var $newItem = $(itemHtml);
        $('#tcaccordion-sortable').append($newItem);

        // Initialize editor for newly created row
        initEditor($newItem);

        // Instantly assign correct dynamic numbers (Tab 1:, Tab 2:, etc.)
        reindexItems();
    });

    /**
     * 4. Remove Item
     */
    $(document).on('click', '.tcaccordion-remove', function(e) {
        e.preventDefault();
        var $item = $(this).closest('.tcaccordion-item');

        $item.find('.wp-editor-area').each(function() {
            var id = $(this).attr('id');
            if (typeof tinyMCE !== 'undefined' && tinyMCE.get(id)) {
                tinyMCE.execCommand('mceRemoveEditor', false, id);
            }
        });

        $item.remove();
        reindexItems();
    });

    /**
     * 5. Accordion Toggle UI
     */
    $(document).on('click', '.tcaccordion-header', function(e) {
        if ($(e.target).hasClass('tcaccordion-title') || $(e.target).is('input')) {
            return;
        }
        $(this).next('.tcaccordion-body').slideToggle(200);
    });

    /**
     * 6. Live Title Syncing to Header Label
     */
    $(document).on('keyup change', '.tcaccordion-title', function() {
        var $item = $(this).closest('.tcaccordion-item');
        var tabIndex = $item.index() + 1;
        var val = $(this).val();
        val = val ? val.trim() : '';
        
        var titleText = val ? val : 'New Accordion';
        $item.find('.tcaccordion-label').text('Tab ' + tabIndex + ': ' + titleText);
    });

    // Save TinyMCE content directly to textareas before parent form submit
    $('form#post').on('submit', function() {
        if (typeof tinyMCE !== 'undefined') {
            tinyMCE.triggerSave();
        }
    });

    // Tab switching event
    $('.tc-tabs-menu .tc-tab-link').on('click', function(e) {
        e.preventDefault();
        var targetTab = $(this).attr('href');

        // Set value in hidden input field for form post
        $('#tc_active_tab_input').val(targetTab);

        // Active class update
        $(this).parent().addClass('current').siblings().removeClass('current');
        $('.tc-tab-content').removeClass('active');
        $(targetTab).addClass('active');
    });

    // Initialize WP Color Picker
    if ($.fn.wpColorPicker) {
        $('.tc-color-field').wpColorPicker();
    }

    // Icon Selector Handler
    $(document).on('change', '.tcacc-icon-selector input[type="radio"]', function () {
        const $parentGroup = $(this).closest('.tcacc-icon-selector');
        
        // Remove active class from siblings and add to checked parent
        $parentGroup.find('.tcacc-icon-btn').removeClass('active');
        $(this).closest('.tcacc-icon-btn').addClass('active');

        // Trigger live preview update if active
        if (typeof triggerLivePreview === 'function') {
            triggerLivePreview();
        }
    });

    // Run reindex on DOM ready to fix pre-saved backend items on page load
    reindexItems();
});