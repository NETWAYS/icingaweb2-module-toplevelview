;(function (Icinga, $) {

    'use strict';

    class Toplevelview {
        constructor(module) {
            this.icinga = module.icinga;

            module.on('click', '.tlv-view-tree .tlv-tree-node', this.processTreeNodeClick);
            module.on('click', 'div[href].action', this.onNodeClick);
            module.on('click', '.btn-remove', this.onRemoveClick);
            module.on('rendered', this.rendered);
        }

        /**
         * rendered adds the CodeMirror editor to the designated div
         */
        rendered(event) {
            this.collapseOnLoad(event);
            const container = event.currentTarget;

            container.querySelectorAll('.codemirror').forEach(el => {
                const mode = el.getAttribute('data-codemirror-mode');
                const editor = CodeMirror.fromTextArea(el, {
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
            // TODO: Refactor to native JS
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
         * collapseOnLoad sets all OK nodes to collapsed, because these are not as interesting.
         */
        collapseOnLoad(event) {
            const el = event.currentTarget;
            const okNodes = el.querySelectorAll('.tlv-view-tree .tlv-tree-node.tlv-collapsible.ok');
            okNodes.forEach(node => node.classList.add('tlv-collapsed'));
        }

        /**
         * onRemoveClick shows a Browser confirmation windows.
         */
        onRemoveClick(event) {
            event.stopPropagation();
            const el = event.currentTarget;
            const confirmMsg = el.getAttribute('data-confirmation');

            if (!confirm(confirmMsg)) {
                event.preventDefault();
            }
        }

        /**
         * onNodeClick handles clicks on a TLV node so that it links to the Icinga object
         */
        onNodeClick(event) {
            event.stopPropagation();
            const el = event.currentTarget;

            const links = el.querySelectorAll('a[href]');

            if (links.length > 0) {
                links[0].click();
            }
        }
    }

    Icinga.availableModules.toplevelview = Toplevelview;

})(Icinga, jQuery);
