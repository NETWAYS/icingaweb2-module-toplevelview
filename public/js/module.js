;(function (Icinga, $) {

    'use strict';

    class Toplevelview {
        constructor(module) {
            this.icinga = module.icinga;

            module.on('click', '.tlv-view-tree .tlv-tree-node', this.processTreeNodeClick);
            module.on('click', 'div[href].action', this.buttonClick);
            module.on('click', '.btn-remove', this.onRemoveClick);
            module.on('rendered', this.rendered);
        }

        /**
         * rendered adds the CodeMirror editor to the designated div
         */
        rendered(event) {
            this.collapseOnLoad(event);
            var $container = $(event.currentTarget);
            $container.find('.codemirror').each(function (i, el) {
                var mode = el.getAttribute('data-codemirror-mode');
                var editor = CodeMirror.fromTextArea(el, {
                    lineNumbers: true,
                    mode: mode,
                    foldGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
                });
                // avoid entering tab chars
                editor.setOption('extraKeys', {
                    Tab: function(cm) {
                        var spaces = new Array(cm.getOption('indentUnit') + 1).join(' ');
                        cm.replaceSelection(spaces);
                    },
                    'Ctrl-F': 'findPersistent'
                });
            });
        }

        /**
         * processTreeNodeClick toggles the collapsed status of the tree nodes
         */
        processTreeNodeClick(event) {
            event.stopPropagation();
            var $el = $(event.currentTarget);
            var $parent = $el.parents('.tlv-tree-node');
            var $all = $el.find('.tlv-tree-node');
            if (($parent.length === 0 && $all.hasClass('tlv-collapsed')) || $el.hasClass('tlv-collapsed')) {
                $el.removeClass('tlv-collapsed');
                $all.removeClass('tlv-collapsed');
            } else {
                $el.addClass('tlv-collapsed');
                $all.addClass('tlv-collapsed');
            }
        }

        /**
         * collapseOnLoad sets all OK nodes to collapsed,
         * because these are not as interesting.
         */
        collapseOnLoad(event) {
            var $el = $(event.currentTarget);
            $el.find('.tlv-view-tree .tlv-tree-node.tlv-collapsible.ok').addClass('tlv-collapsed');
        }

        /**
         * onRemoveClick shows a Browser confirmation windows.
         */
        onRemoveClick(event) {
            event.stopPropagation();
            let target = event.currentTarget;
            const confirmMsg = target.getAttribute('data-confirmation');

            return confirm(confirmMsg);
        }

        buttonClick (event) {
            event.stopPropagation();
            var $el = $(event.currentTarget);
            var $links = $el.find('a[href]');
            if ($links.length > 0) {
                $links[0].click();
            }
        }
    }

    Icinga.availableModules.toplevelview = Toplevelview;

})(Icinga, jQuery);
