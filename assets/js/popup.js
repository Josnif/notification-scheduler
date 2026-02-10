(function($) {
    'use strict';
    
    class NotificationPopup {
        constructor(settings) {
            this.settings = settings;
            this.variables = settings.variables || {};
            this.template = settings.template || 'custom';
            this.position = settings.position || 'left';
            this.effect = settings.effect || 'fade';
            this.delay = typeof settings.delay !== 'undefined' ? parseInt(settings.delay) : 0;
            this.isCardVisible = false;
            this.showTimer = null;
            this.hideTimer = null;
            this.waitTimer = null;
            this.woocommerceProducts = settings.woocommerce_products || [];
            this.init();
        }
        
        init() {
            this.createPopupHTML();
            setTimeout(() => {
                this.managePopupCycle();
            }, (this.delay || 0) * 1000);
        }
        
        createPopupHTML() {
            const container = $('#ns-popup-container');
            if (container.length === 0) return;
            // Add position and effect classes
            container.html(`
                <div id="ns-popup" class="ns-popup ns-popup-${this.position} ns-effect-${this.effect}" style="display: none;">
                    <button class="ns-close-btn" type="button" id="ns-close-button" aria-label="Close notification popup" onclick="nsPopup.close()">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>
                        </svg>
                    </button>
                    <div class="ns-icon-container">
                        <div class="ns-image-or-icon"></div>
                    </div>
                    <div class="ns-content">
                        <p class="ns-text"></p>
                    </div>
                </div>
            `);
        }
        
        managePopupCycle() {
            const interval = this.settings.interval || 30;
            const displayTime = this.settings.display_time || 10; // Default to 10 seconds

            setTimeout(() => {
                this.show();
                setTimeout(() => {
                    this.hide();
                    setTimeout(() => {
                        this.managePopupCycle();
                    }, interval * 1000);
                }, displayTime * 1000);
            }, this.delay * 1000);
        }
        
        processVariable(varName, varData) {
            const type = varData.type || 'text';
            switch (type) {
                case 'array':
                    const values = varData.values ? varData.values.split('|') : [];
                    return values.length > 0 ? values[Math.floor(Math.random() * values.length)].trim() : '';
                case 'text':
                    return varData.values || '';
                case 'number':
                    return Math.floor(Math.random() * 100) + 1;
                case 'range':
                    if (varData.values) {
                        const range = varData.values.split('-');
                        if (range.length === 2) {
                            const min = parseInt(range[0]) || 1;
                            const max = parseInt(range[1]) || 100;
                            return Math.floor(Math.random() * (max - min + 1)) + min;
                        }
                    }
                    return Math.floor(Math.random() * 100) + 1;
                default:
                    return '';
            }
        }
        
        getImageForVariable(varName, varData) {
            if (varData && varData.image && varData.image.trim() !== '') {
                return varData.image.trim();
            }
            return null;
        }
        
        formatText(text) {
            // Bold: *text*
            text = text.replace(/\*([^*]+)\*/g, '<strong>$1</strong>');
            // Italic: _text_
            text = text.replace(/_([^_]+)_/g, '<em>$1</em>');
            return text;
        }
        
        processTextTemplate(template, contextVars) {
            let processedText = template;
            const variableRegex = /\{([^}]+)\}/g;
            let match;
            while ((match = variableRegex.exec(template)) !== null) {
                const varName = match[1];
                let value = '';
                if (contextVars && contextVars[varName] !== undefined) {
                    value = contextVars[varName];
                } else if (this.variables[varName]) {
                    value = this.processVariable(varName, this.variables[varName]);
                }
                processedText = processedText.replace(match[0], value);
            }
            // Apply formatting for *bold* and _italic_
            processedText = this.formatText(processedText);
            return processedText;
        }
        
        show() {
            const popup = $('#ns-popup');
            let textTemplate = this.settings.text_template || 'Someone just purchased a product!';
            let imageUrl = null;
            let contextVars = {};

            if (this.template === 'woocommerce' && this.woocommerceProducts && this.woocommerceProducts.length > 0) {
                // Pick a random product
                const product = this.woocommerceProducts[Math.floor(Math.random() * this.woocommerceProducts.length)];
                contextVars = {
                    product: product.product,
                    price: product.price,
                    image: product.image
                };
                imageUrl = product.image;
            } else {
                // Try to find an image from variables
                for (const varName in this.variables) {
                    const varData = this.variables[varName];
                    const img = this.getImageForVariable(varName, varData);
                    if (img) {
                        imageUrl = img;
                        break;
                    }
                }
            }
            // Process the text template with variables
            const processedText = this.processTextTemplate(textTemplate, contextVars);

            // Update content
            popup.find('.ns-text').html(processedText);
            // Show image or default icon
            if (imageUrl) {
                popup.find('.ns-image-or-icon').html(`<img src="${imageUrl}" alt="notification image" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">`);
            } else {
                popup.find('.ns-image-or-icon').html(`
                    <svg width="35" height="35" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                `);
            }
            // Show popup with effect
            if (this.effect === 'slide') {
                popup.stop(true, true).css('display', 'block').hide().slideDown(300);
            } else {
                popup.fadeIn(300);
            }
            this.isCardVisible = true;
        }
        
        hide() {
            const popup = $('#ns-popup');
            if (this.effect === 'slide') {
                popup.slideUp(300);
            } else {
                popup.fadeOut(300);
            }
            this.isCardVisible = false;
        }
        
        close() {
            this.hide();
            clearTimeout(this.showTimer);
            clearTimeout(this.hideTimer);
            clearTimeout(this.waitTimer);
            setTimeout(() => {
                this.managePopupCycle();
            }, 20000 + Math.random() * 5000);
        }
        
        destroy() {
            clearTimeout(this.showTimer);
            clearTimeout(this.hideTimer);
            clearTimeout(this.waitTimer);
            $('#ns-popup').remove();
        }
    }
    
    $(document).ready(function() {
        if (typeof nsSettings !== 'undefined' && nsSettings.settings) {
            // Check if notifications are enabled
            const enabled = nsSettings.settings.enabled !== undefined ? nsSettings.settings.enabled : true;
            if (enabled) {
                window.nsPopup = new NotificationPopup(nsSettings.settings);
            }
        }
    });
    
})(jQuery); 