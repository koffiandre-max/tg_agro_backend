(function($) {
    'use strict';

    var printCounter = 0;

    $.EPrint = function(elementId, options) {
        if (!(this instanceof $.EPrint)) {
            return new $.EPrint(elementId, options);
        }

        var defaults = {
            titre: document.title || 'Impression',
            styles: [],
            scripts: [],
            cssInline: '',
            header: '',
            footer: '',
            pageMargin: '1cm',
            beforePrint: null,      // return false pour annuler
            afterPrint: null,
            popupWidth: 800,        // taille du cadre d'aperçu (mode preview uniquement)
            popupHeight: 600,
            printBodyClass: 'printing',
            keepOpen: false,        // laisse l'aperçu ouvert (utilisé par preview())
            closeDelay: 1000,
            removeScripts: true,
            preserveStyles: true,
            timeout: 5000,
            debug: false
        };

        this.options = $.extend({}, defaults, options);
        this.elementId = elementId;
        this.$element = $('#' + elementId);
        this._iframe = null;
        this._backdrop = null;
        this._isPrinting = false;
        this._uid = ++printCounter;

        if (!this.$element.length) {
            console.error('EPrint: Élément avec l\'ID "' + elementId + '" introuvable.');
            return this;
        }

        this._log('EPrint initialisé pour l\'ID: ' + elementId);

        return this;
    };

    $.EPrint.prototype = {

        print: function() {
            var self = this;

            if (this._isPrinting) {
                this._log('Impression déjà en cours');
                return this;
            }

            if (!this.$element.length) {
                console.error('EPrint: Élément introuvable');
                return this;
            }

            var shouldContinue = this._executeCallback('beforePrint');
            if (shouldContinue === false) {
                this._log('Impression annulée par beforePrint');
                return this;
            }

            this._isPrinting = true;

            try {
                var printContent = this._buildPrintContent();
                var iframe = this.options.keepOpen
                    ? this._createPreviewIframe()
                    : this._createHiddenIframe();

                this._iframe = iframe;
                this._writeContent(iframe, printContent);
                this._setupIframe(iframe);

                this._timeoutId = setTimeout(function() {
                    if (self._isPrinting) {
                        self._log('Timeout: fermeture forcée');
                        self._closePrintWindow();
                    }
                }, this.options.timeout);

            } catch (error) {
                console.error('EPrint: Erreur lors de l\'impression', error);
                this._isPrinting = false;
                this._executeCallback('afterPrint', [error]);
            }

            return this;
        },

        /**
         * Iframe invisible pour une impression directe, sans UI et sans
         * ouvrir de fenêtre/onglet.
         */
        _createHiddenIframe: function() {
            var iframe = document.createElement('iframe');
            iframe.setAttribute('id', 'eprint-frame-' + this._uid);
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            iframe.style.visibility = 'hidden';
            document.body.appendChild(iframe);
            return iframe;
        },

        /**
         * Iframe visible dans une petite fenêtre modale (utilisée par preview()),
         * toujours dans la page courante : aucun popup, aucun onglet.
         */
        _createPreviewIframe: function() {
            var self = this;

            var backdrop = document.createElement('div');
            backdrop.className = 'eprint-backdrop';
            backdrop.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.5);' +
                'z-index:99998;display:flex;align-items:center;justify-content:center;';

            var container = document.createElement('div');
            container.style.cssText = 'position:relative;background:#fff;box-shadow:0 10px 30px rgba(0,0,0,.3);' +
                'width:' + this.options.popupWidth + 'px;max-width:95vw;' +
                'height:' + this.options.popupHeight + 'px;max-height:90vh;';

            var closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.textContent = '\u00D7';
            closeBtn.setAttribute('aria-label', 'Fermer l\'aperçu');
            closeBtn.style.cssText = 'position:absolute;top:-14px;right:-14px;width:28px;height:28px;' +
                'border-radius:50%;border:0;background:#222;color:#fff;font-size:16px;line-height:1;' +
                'cursor:pointer;z-index:99999;';

            var iframe = document.createElement('iframe');
            iframe.setAttribute('id', 'eprint-frame-' + this._uid);
            iframe.style.cssText = 'width:100%;height:100%;border:0;';

            container.appendChild(iframe);
            container.appendChild(closeBtn);
            backdrop.appendChild(container);
            document.body.appendChild(backdrop);

            closeBtn.addEventListener('click', function() { self.cancel(); });
            backdrop.addEventListener('click', function(e) {
                if (e.target === backdrop) self.cancel();
            });

            this._backdrop = backdrop;
            return iframe;
        },

        _writeContent: function(iframe, content) {
            var doc = iframe.contentWindow.document;
            doc.open();
            doc.write(content);
            doc.close();
        },

        _setupIframe: function(iframe) {
            var self = this;
            var triggered = false;

            var runPrint = function() {
                if (triggered) return;
                triggered = true;

                self._log('Contenu chargé');

                try {
                    var body = iframe.contentWindow.document.body;
                    if (self.options.printBodyClass && body) {
                        body.classList.add(self.options.printBodyClass);
                    }
                } catch (e) {}

                // En mode aperçu (keepOpen), on n'imprime pas automatiquement :
                // l'utilisateur regarde puis lance l'impression lui-même via
                // Ctrl+P dans l'iframe, ou on peut exposer .doPrint().
                if (self.options.keepOpen) {
                    self._executeCallback('afterPrint');
                    self._isPrinting = false;
                    return;
                }

                setTimeout(function() {
                    self._log('Lancement de l\'impression');
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                        self._executeCallback('afterPrint');
                    } catch (error) {
                        console.error('EPrint: Erreur lors de l\'impression', error);
                        self._isPrinting = false;
                    } finally {
                        setTimeout(function() {
                            self._closePrintWindow();
                        }, self.options.closeDelay);
                    }
                }, 300);
            };

            iframe.onload = runPrint;

            try {
                if (iframe.contentWindow.document.readyState === 'complete') {
                    runPrint();
                }
            } catch (e) {}
        },

        _buildPrintContent: function() {
            var self = this;
            var content = this.$element.clone();

            if (this.options.removeScripts) {
                content.find('script').remove();
            }

            var styles = this.options.preserveStyles ? (this.$element.attr('style') || '') : '';
            var customStyles = this.options.cssInline;

            var html = '<!DOCTYPE html>\n';
            html += '<html>\n';
            html += '<head>\n';
            html += '<meta charset="UTF-8">\n';
            html += '<meta name="viewport" content="width=device-width, initial-scale=1.0">\n';
            // FIX CSS : sans cette balise <base>, les URLs relatives (styles,
            // images, scripts) ne se résolvent pas car le document de l'iframe
            // (ou de l'ancien popup) n'a pas la même URL de base que la page.
            html += '<base href="' + this._escapeHtml(document.baseURI || location.href) + '">\n';
            html += '<title>' + this._escapeHtml(this.options.titre) + '</title>\n';

            html += '<style>\n';
            html += '  * { margin: 0; padding: 0; box-sizing: border-box; }\n';
            html += '  @page { margin: ' + this._escapeHtml(this.options.pageMargin) + '; }\n';
            html += '  body { \n';
            html += '    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;\n';
            html += '    padding: 20px;\n';
            html += '    background: white;\n';
            html += '    color: #000;\n';
            html += '  }\n';
            html += '  .' + this.options.printBodyClass + ' { background: white; }\n';

            html += '  @media print {\n';
            html += '    body { padding: 0; margin: 0; }\n';
            html += '    .no-print { display: none !important; }\n';
            html += '    .print-only { display: block !important; }\n';
            html += '    img { max-width: 100% !important; }\n';
            html += '  }\n';

            if (customStyles) {
                html += '  /* Styles personnalisés */\n';
                html += '  ' + customStyles + '\n';
            }

            html += '</style>\n';

            if (this.options.styles && this.options.styles.length) {
                $.each(this.options.styles, function(i, styleUrl) {
                    html += '<link rel="stylesheet" href="' + self._escapeHtml(styleUrl) + '">\n';
                });
            }

            if (this.options.scripts && this.options.scripts.length) {
                $.each(this.options.scripts, function(i, scriptUrl) {
                    html += '<script src="' + self._escapeHtml(scriptUrl) + '"><\/script>\n';
                });
            }

            html += '</head>\n';
            html += '<body>\n';

            if (this.options.header) {
                html += '<div class="print-header">' + this.options.header + '</div>\n';
            }

            html += '<div id="' + this._escapeHtml(this.elementId) + '" style="' + this._escapeHtml(styles) + '">\n';
            html += content.html() || '';
            html += '</div>\n';

            if (this.options.footer) {
                html += '<div class="print-footer">' + this.options.footer + '</div>\n';
            }

            html += '</body>\n';
            html += '</html>';

            this._log('Contenu HTML construit (taille: ' + html.length + ' caractères)');
            return html;
        },

        /**
         * Déclenche l'impression manuellement depuis un aperçu (keepOpen).
         */
        doPrint: function() {
            if (!this._iframe) return this;
            try {
                this._iframe.contentWindow.focus();
                this._iframe.contentWindow.print();
            } catch (e) {
                console.error('EPrint: Erreur lors de l\'impression manuelle', e);
            }
            return this;
        },

        _closePrintWindow: function() {
            if (this._timeoutId) {
                clearTimeout(this._timeoutId);
                this._timeoutId = null;
            }
            if (this._backdrop && this._backdrop.parentNode) {
                this._backdrop.parentNode.removeChild(this._backdrop);
            } else if (this._iframe && this._iframe.parentNode) {
                this._iframe.parentNode.removeChild(this._iframe);
            }
            this._iframe = null;
            this._backdrop = null;
            this._isPrinting = false;
        },

        _executeCallback: function(name, args) {
            var callback = this.options[name];
            if (typeof callback === 'function') {
                try {
                    return callback.apply(this, args || [this.$element]);
                } catch (error) {
                    console.error('EPrint: Erreur dans le callback ' + name, error);
                }
            }
        },

        _escapeHtml: function(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        _log: function(message) {
            if (this.options.debug) {
                console.log('[EPrint] ' + message);
            }
        },

        getElement: function() {
            return this.$element;
        },

        setOptions: function(options) {
            this.options = $.extend({}, this.options, options);
            this._log('Options mises à jour');
            return this;
        },

        cancel: function() {
            this._log('Annulation de l\'impression');
            this._closePrintWindow();
            return this;
        },

        isPrinting: function() {
            return this._isPrinting;
        },

        /**
         * Aperçu intégré à la page (plus de popup) : affiche le contenu dans
         * un cadre modal ; l'utilisateur imprime ensuite via doPrint() ou
         * Ctrl+P dans le cadre.
         */
        preview: function() {
            this.options.keepOpen = true;
            this.print();
            return this;
        },

        destroy: function() {
            this._closePrintWindow();
            this.$element = null;
            this.options = null;
        }
    };

    $.fn.EPrint = function(options) {
        var elementId = this.attr('id');
        if (!elementId) {
            console.error('EPrint: L\'élément doit avoir un ID');
            return this;
        }
        return $.EPrint(elementId, options);
    };

    $.EPrint.importStyles = function(css) {
        var style = document.createElement('style');
        style.textContent = css;
        document.head.appendChild(style);
        return style;
    };

})(jQuery);

/*
$.EPrint('monId', {
    titre: 'Rapport mensuel',
    styles: ['assets/print.css'],   // résolu correctement grâce à <base> maintenant
    header: '<h1>En-tête personnalisé</h1>',
    footer: '<p>Pied de page</p>',
    debug: true
}).print(); // impression directe, aucun popup/onglet

// Aperçu intégré (modal dans la page, pas de popup) :
var ep = $.EPrint('monId', { titre: 'Aperçu' }).preview();
// puis, sur clic d'un bouton "Imprimer" dans ton UI :
// ep.doPrint();
*/