(function($) {
    'use strict';
    
    class NotificationPopup {
        constructor(settings) {
            this.settings = settings;
            this.variables = settings.variables || {};
            this.isCardVisible = false;
            this.showTimer = null;
            this.hideTimer = null;
            this.waitTimer = null;
            
            this.init();
        }
        
        init() {
            this.createPopupHTML();
            this.managePopupCycle();
        }
        
        createPopupHTML() {
            const container = $('#ns-popup-container');
            if (container.length === 0) return;
            
            container.html(`
                <div id="ns-popup" class="ns-popup" style="display: none;">
                    <button class="ns-close-btn" onclick="nsPopup.close()">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>
                        </svg>
                    </button>
                    <div class="ns-icon-container">
                        <div class="ns-icon">
                            <svg width="35" height="35" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ns-content">
                        <p class="ns-text"></p>
                    </div>
                </div>
            `);
        }
        
        managePopupCycle() {
            this.showTimer = setTimeout(() => {
                this.show();
                
                this.hideTimer = setTimeout(() => {
                    this.hide();
                    
                    this.waitTimer = setTimeout(() => {
                        this.managePopupCycle();
                    }, 20000 + Math.random() * 5000); // hide for 20-25 seconds
                }, 20000 + Math.random() * 5000); // show for 20-25 seconds
            }, (this.settings.interval || 30) * 1000); // base delay
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
        
        processTextTemplate(template) {
            let processedText = template;
            
            // Find all variables in curly brackets
            const variableRegex = /\{([^}]+)\}/g;
            let match;
            
            while ((match = variableRegex.exec(template)) !== null) {
                const varName = match[1];
                const varData = this.variables[varName];
                
                if (varData) {
                    const value = this.processVariable(varName, varData);
                    processedText = processedText.replace(match[0], value);
                }
            }
            
            return processedText;
        }
        
        show() {
            const popup = $('#ns-popup');
            const textTemplate = this.settings.text_template || 'Someone just purchased a product!';
            
            // Process the text template with variables
            const processedText = this.processTextTemplate(textTemplate);
            
            // Update content
            popup.find('.ns-text').text(processedText);
            
            // Show popup
            popup.fadeIn(300);
            this.isCardVisible = true;
        }
        
        hide() {
            const popup = $('#ns-popup');
            popup.fadeOut(300);
            this.isCardVisible = false;
        }
        
        close() {
            this.hide();
            // Clear timers and restart cycle
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
    
    // Initialize popup when DOM is ready
    $(document).ready(function() {
        if (typeof nsSettings !== 'undefined' && nsSettings.settings) {
            window.nsPopup = new NotificationPopup(nsSettings.settings);
        }
    });
    
})(jQuery); 