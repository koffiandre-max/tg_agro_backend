/**
 * PrintToPDF.js v1.1.0 - Bibliothèque JavaScript pour imprimer un élément DOM en PDF
 * Utilisation : $('#monElement').printToPDF(options);
 * 
 * Dépendances : html2canvas, jsPDF
 * @license MIT
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory(require('jquery'));
    } else {
        factory(root.jQuery);
    }
}(typeof self !== 'undefined' ? self : this, function ($) {

    'use strict';

    var defaults = {
        filename: 'document.pdf',
        format: 'a4',
        orientation: 'portrait',
        margin: { top: 10, right: 10, bottom: 10, left: 10 },
        scale: 2,
        imageType: 'image/png',
        imageQuality: 0.95,
        autoPrint: false,
        autoDownload: true,
        header: null,
        footer: null,
        pageNumbers: false,
        pageNumberFormat: 'Page {page} / {total}',
        css: '',
        beforePrint: null,
        afterPrint: null,
        onError: null,
        debug: false
    };

    var Utils = {
        log: function(msg, debug) {
            if (debug) console.log('[PrintToPDF]', msg);
        },
        extend: function(target, source) {
            for (var key in source) {
                if (source.hasOwnProperty(key)) {
                    if (typeof source[key] === 'object' && source[key] !== null && !Array.isArray(source[key])) {
                        target[key] = Utils.extend(target[key] || {}, source[key]);
                    } else {
                        target[key] = source[key];
                    }
                }
            }
            return target;
        },
        formatDimensions: {
            a4: { width: 210, height: 297 },
            a3: { width: 297, height: 420 },
            a5: { width: 148, height: 210 },
            letter: { width: 216, height: 279 },
            legal: { width: 216, height: 356 }
        }
    };

    // Détection robuste de jsPDF (supporte plusieurs formats d'exposition)
    function getJsPDF() {
        if (typeof jspdf !== 'undefined' && jspdf.jsPDF) {
            return jspdf.jsPDF;
        }
        if (typeof jsPDF !== 'undefined') {
            return jsPDF;
        }
        if (typeof window.jspdf !== 'undefined' && window.jspdf.jsPDF) {
            return window.jspdf.jsPDF;
        }
        if (typeof window.jsPDF !== 'undefined') {
            return window.jsPDF;
        }
        return null;
    }

    function checkDependencies() {
        var errors = [];
        if (typeof html2canvas === 'undefined') {
            errors.push('html2canvas non chargé. Ajoutez : <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"><\/script>');
        }
        var JsPDF = getJsPDF();
        if (!JsPDF) {
            errors.push('jsPDF non chargé. Ajoutez : <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"><\/script>');
        }
        return { ok: errors.length === 0, errors: errors, jsPDF: JsPDF };
    }

    function PrintToPDF(element, options) {
        this.element = element;
        this.options = Utils.extend(Utils.extend({}, defaults), options || {});
        this.pdf = null;
        this.totalPages = 0;
    }

    PrintToPDF.prototype = {
        constructor: PrintToPDF,

        generate: function() {
            var self = this;
            var deferred = $.Deferred();

            // Vérification des dépendances
            var deps = checkDependencies();
            if (!deps.ok) {
                var errMsg = 'Dépendances manquantes :\n' + deps.errors.join('\n');
                var error = new Error(errMsg);
                Utils.log(errMsg, true);
                if (self.options.onError) self.options.onError(error);
                deferred.reject(error);
                return deferred.promise();
            }

            var JsPDF = deps.jsPDF;

            if (self.options.beforePrint) {
                try { self.options.beforePrint.call(self.element); } catch(e) {}
            }

            Utils.log('Début génération PDF...', self.options.debug);

            // Cloner l'élément dans un conteneur invisible
            var container = document.createElement('div');
            container.style.position = 'fixed';
            container.style.left = '-99999px';
            container.style.top = '0';
            container.style.zIndex = '-9999';
            document.body.appendChild(container);

            var clone = self.element.cloneNode(true);
            self._copyStylesRecursive(self.element, clone);

            if (self.options.css) {
                var style = document.createElement('style');
                style.textContent = self.options.css;
                container.appendChild(style);
            }

            container.appendChild(clone);

            // Forcer le clone à prendre la largeur naturelle de l'original
            var originalStyle = window.getComputedStyle(self.element);
            clone.style.width = self.element.scrollWidth + 'px';
            clone.style.maxWidth = 'none';
            clone.style.position = 'relative';
            clone.style.left = '0';
            clone.style.top = '0';
            clone.style.margin = '0';

            Utils.log('Clone créé, dimensions : ' + clone.scrollWidth + 'x' + clone.scrollHeight, self.options.debug);

            // Petite temporisation pour laisser le navigateur rendre le clone
            setTimeout(function() {
                html2canvas(clone, {
                    scale: self.options.scale,
                    useCORS: true,
                    allowTaint: true,
                    logging: self.options.debug,
                    backgroundColor: '#ffffff',
                    width: clone.scrollWidth,
                    height: clone.scrollHeight,
                    windowWidth: clone.scrollWidth,
                    windowHeight: clone.scrollHeight,
                    x: 0,
                    y: 0
                }).then(function(canvas) {
                    Utils.log('Canvas généré : ' + canvas.width + 'x' + canvas.height, self.options.debug);

                    try {
                        self._createPDF(canvas, JsPDF);

                        // Nettoyer le DOM
                        if (container.parentNode) container.parentNode.removeChild(container);

                        if (self.options.autoDownload) {
                            self.download();
                        }
                        if (self.options.autoPrint) {
                            self.print();
                        }
                        if (self.options.afterPrint) {
                            try { self.options.afterPrint.call(self.element, self.pdf); } catch(e) {}
                        }
                        deferred.resolve(self.pdf);
                    } catch (e) {
                        if (container.parentNode) container.parentNode.removeChild(container);
                        Utils.log('Erreur création PDF : ' + e.message, true);
                        if (self.options.onError) self.options.onError(e);
                        deferred.reject(e);
                    }
                }).catch(function(error) {
                    if (container.parentNode) container.parentNode.removeChild(container);
                    Utils.log('Erreur html2canvas : ' + error.message, true);
                    if (self.options.onError) self.options.onError(error);
                    deferred.reject(error);
                });
            }, 100);

            return deferred.promise();
        },

        _copyStylesRecursive: function(source, target) {
            var computed = window.getComputedStyle(source);
            var essential = [
                'font-family','font-size','font-weight','font-style','color',
                'background-color','background-image','border','border-radius',
                'padding','margin','width','height','min-width','min-height',
                'max-width','max-height','display','position','top','left',
                'right','bottom','float','clear','text-align','line-height',
                'letter-spacing','box-shadow','text-shadow','opacity',
                'overflow','white-space','word-wrap','word-break',
                'list-style','text-decoration','vertical-align'
            ];
            for (var i = 0; i < essential.length; i++) {
                try {
                    target.style[essential[i]] = computed.getPropertyValue(essential[i]);
                } catch(e) {}
            }
            var sChildren = source.children;
            var tChildren = target.children;
            for (var j = 0; j < sChildren.length && j < tChildren.length; j++) {
                this._copyStylesRecursive(sChildren[j], tChildren[j]);
            }
        },

        _createPDF: function(canvas, JsPDF) {
            var self = this;
            var fmt = Utils.formatDimensions[self.options.format] || Utils.formatDimensions.a4;
            var isLandscape = self.options.orientation === 'landscape';

            var pageW = isLandscape ? fmt.height : fmt.width;
            var pageH = isLandscape ? fmt.width : fmt.height;
            var m = self.options.margin;
            var contentW = pageW - m.left - m.right;
            var contentH = pageH - m.top - m.bottom;

            var pdf = new JsPDF({
                orientation: self.options.orientation,
                unit: 'mm',
                format: self.options.format.toUpperCase()
            });

            var imgData = canvas.toDataURL(self.options.imageType, self.options.imageQuality);

            var imgW = canvas.width;
            var imgH = canvas.height;

            // Ratio pour convertir pixels -> mm (72 dpi = 25.4mm/inch, mais html2canvas est en 96 dpi)
            var pxToMm = 25.4 / 96;
            var renderW = imgW * pxToMm;
            var renderH = imgH * pxToMm;

            // Ajuster pour tenir dans la largeur de contenu
            var scale = contentW / renderW;
            renderW = contentW;
            renderH = renderH * scale;

            var totalH = renderH;
            var pages = Math.ceil(totalH / contentH);
            if (pages < 1) pages = 1;
            self.totalPages = pages;

            Utils.log('Pages : ' + pages + ' (hauteur totale ' + totalH.toFixed(1) + 'mm)', self.options.debug);

            for (var p = 0; p < pages; p++) {
                if (p > 0) pdf.addPage();

                var headerH = 0;
                if (self.options.header) {
                    headerH = self._drawHeader(pdf, pageW, m);
                }

                // Calculer quelle portion de l'image afficher
                var sliceTop = p * contentH / scale / pxToMm;
                var sliceHeight = Math.min(contentH / scale / pxToMm, imgH - sliceTop);
                var drawHeight = sliceHeight * scale * pxToMm;

                // Créer un canvas temporaire pour la tranche
                var sliceCanvas = document.createElement('canvas');
                sliceCanvas.width = imgW;
                sliceCanvas.height = sliceHeight;
                var ctx = sliceCanvas.getContext('2d');
                ctx.drawImage(canvas, 0, -sliceTop);
                var sliceData = sliceCanvas.toDataURL(self.options.imageType, self.options.imageQuality);

                pdf.addImage(
                    sliceData,
                    self.options.imageType === 'image/jpeg' ? 'JPEG' : 'PNG',
                    m.left,
                    m.top + headerH,
                    renderW,
                    drawHeight
                );

                if (self.options.footer) {
                    self._drawFooter(pdf, pageW, pageH, m);
                }
                if (self.options.pageNumbers) {
                    self._drawPageNumber(pdf, pageW, pageH, m, p + 1);
                }
            }

            self.pdf = pdf;
            Utils.log('PDF créé (' + pages + ' pages)', self.options.debug);
        },

        _drawHeader: function(pdf, pageW, m) {
            var h = this.options.header;
            pdf.setFontSize(9);
            pdf.setTextColor(120);
            var text = typeof h === 'string' ? h : (h.text || '');
            var align = typeof h === 'object' ? (h.align || 'center') : 'center';
            var x = typeof h === 'object' && h.x ? h.x : pageW / 2;
            var y = typeof h === 'object' && h.y ? h.y : m.top + 4;
            pdf.text(text, x, y, { align: align });
            pdf.setDrawColor(200);
            pdf.line(m.left, m.top + 7, pageW - m.right, m.top + 7);
            return 10;
        },

        _drawFooter: function(pdf, pageW, pageH, m) {
            var f = this.options.footer;
            pdf.setFontSize(9);
            pdf.setTextColor(120);
            var text = typeof f === 'string' ? f : (f.text || '');
            var align = typeof f === 'object' ? (f.align || 'center') : 'center';
            var x = typeof f === 'object' && f.x ? f.x : pageW / 2;
            var y = typeof f === 'object' && f.y ? f.y : pageH - m.bottom - 3;
            pdf.setDrawColor(200);
            pdf.line(m.left, pageH - m.bottom - 8, pageW - m.right, pageH - m.bottom - 8);
            pdf.text(text, x, y, { align: align });
        },

        _drawPageNumber: function(pdf, pageW, pageH, m, current) {
            var txt = this.options.pageNumberFormat
                .replace('{page}', current)
                .replace('{total}', this.totalPages);
            pdf.setFontSize(8);
            pdf.setTextColor(150);
            pdf.text(txt, pageW - m.right, pageH - m.bottom + 4, { align: 'right' });
        },

        download: function(filename) {
            if (!this.pdf) {
                console.error('[PrintToPDF] Aucun PDF. Appelez generate() d\'abord.');
                return this;
            }
            this.pdf.save(filename || this.options.filename);
            return this;
        },

        print: function() {
            if (!this.pdf) {
                console.error('[PrintToPDF] Aucun PDF. Appelez generate() d\'abord.');
                return this;
            }
            this.pdf.autoPrint({ variant: 'non-conform' });
            var blob = this.pdf.output('bloburl');
            window.open(blob, '_blank');
            return this;
        },

        getBlob: function() {
            return this.pdf ? this.pdf.output('blob') : null;
        },

        getDataUrl: function() {
            return this.pdf ? this.pdf.output('datauristring') : null;
        },

        destroy: function() {
            this.element = null;
            this.pdf = null;
            this.options = null;
        }
    };

    // Plugin jQuery
    $.fn.printToPDF = function(options) {
        var args = Array.prototype.slice.call(arguments, 1);
        return this.each(function() {
            var $this = $(this);
            var instance = $this.data('printToPDF');
            if (typeof options === 'string') {
                if (instance && typeof instance[options] === 'function') {
                    instance[options].apply(instance, args);
                }
            } else {
                if (!instance) {
                    instance = new PrintToPDF(this, options);
                    $this.data('printToPDF', instance);
                }
                instance.generate();
            }
        });
    };

    // API statique
    $.printToPDF = {
        defaults: defaults,
        version: '1.1.0',
        setDefaults: function(opts) { Utils.extend(defaults, opts); },
        checkDependencies: checkDependencies
    };

    return PrintToPDF;
}));