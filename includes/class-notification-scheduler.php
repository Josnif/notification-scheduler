<?php
/**
 * Main plugin class
 */
class Notification_Scheduler {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'init_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_popup'));
        add_action('wp_ajax_ns_save_settings', array($this, 'save_settings'));
        add_action('wp_ajax_nopriv_ns_save_settings', array($this, 'save_settings'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'Notification Scheduler Settings',
            'Notification Scheduler',
            'manage_options',
            'notification-scheduler',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        register_setting('ns_settings_group', 'ns_settings');
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'ns-popup-script',
            NS_PLUGIN_URL . 'assets/js/popup.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'ns-popup-style',
            NS_PLUGIN_URL . 'assets/css/popup.css',
            array(),
            '1.0.0'
        );
        
        // Localize script with settings
        $settings = get_option('ns_settings', array());
        wp_localize_script('ns-popup-script', 'nsSettings', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ns_nonce'),
            'settings' => $settings
        ));
    }
    
    /**
     * Render popup HTML
     */
    public function render_popup() {
        $settings = get_option('ns_settings', array());
        if (empty($settings)) return;
        
        echo '<div id="ns-popup-container"></div>';
    }
    
    /**
     * Admin settings page
     */
    public function admin_page() {
        $settings = get_option('ns_settings', array());
        ?>
        <div class="wrap">
            <h1>Notification Scheduler Settings</h1>
            
            <form method="post" action="options.php" id="ns-settings-form">
                <?php settings_fields('ns_settings_group'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Interval (seconds)</th>
                        <td>
                            <input type="number" name="ns_settings[interval]" 
                                   value="<?php echo esc_attr($settings['interval'] ?? 30); ?>" 
                                   min="10" max="300" />
                            <p class="description">Delay before showing popup (in seconds)</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Text Template</th>
                        <td>
                            <textarea name="ns_settings[text_template]" 
                                      class="large-text" rows="3"><?php echo esc_textarea($settings['text_template'] ?? 'Someone from {city} just purchased {product}'); ?></textarea>
                            <p class="description">Use {variable_name} as placeholders for variables</p>
                        </td>
                    </tr>
                </table>
                
                <h2>Variables</h2>
                <div id="ns-variables">
                    <?php
                    $variables = $settings['variables'] ?? array();
                    if (!empty($variables)) {
                        foreach ($variables as $var_name => $var_data) {
                            $this->render_variable_row($var_name, $var_data);
                        }
                    } else {
                        $this->render_variable_row('city', array('type' => 'array', 'values' => 'New York|Los Angeles|Chicago'));
                    }
                    ?>
                </div>
                
                <p>
                    <button type="button" id="ns-add-variable" class="button">Add New Variable</button>
                </p>
                
                <?php submit_button(); ?>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            let varIndex = <?php echo count($variables); ?>;
            
            $('#ns-add-variable').on('click', function() {
                const template = `
                    <div class="ns-variable-row" data-index="${varIndex}">
                        <h3>Variable ${varIndex + 1}</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Variable Name</th>
                                <td>
                                    <input type="text" name="ns_settings[variables][var_${varIndex}][name]" 
                                           class="regular-text" placeholder="e.g., city, product, name" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Type</th>
                                <td>
                                    <select name="ns_settings[variables][var_${varIndex}][type]" class="ns-var-type">
                                        <option value="array">Array (separated by |)</option>
                                        <option value="text">Single Text</option>
                                        <option value="number">Random Number (1-100)</option>
                                        <option value="range">Number Range (e.g., 5-15)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="ns-var-values-row">
                                <th scope="row">Values</th>
                                <td>
                                    <textarea name="ns_settings[variables][var_${varIndex}][values]" 
                                              class="large-text" rows="3" placeholder="For array: value1|value2|value3&#10;For text: single value&#10;For range: 5-15"></textarea>
                                    <p class="description">For arrays, separate values with |. For ranges, use format like 5-15.</p>
                                </td>
                            </tr>
                        </table>
                        <button type="button" class="button ns-remove-variable">Remove Variable</button>
                    </div>
                `;
                
                $('#ns-variables').append(template);
                varIndex++;
            });
            
            $(document).on('click', '.ns-remove-variable', function() {
                $(this).closest('.ns-variable-row').remove();
            });
            
            $(document).on('change', '.ns-var-type', function() {
                const row = $(this).closest('.ns-variable-row');
                const type = $(this).val();
                const valuesRow = row.find('.ns-var-values-row');
                const textarea = valuesRow.find('textarea');
                const description = valuesRow.find('.description');
                
                if (type === 'number') {
                    valuesRow.hide();
                } else if (type === 'array') {
                    valuesRow.show();
                    textarea.attr('placeholder', 'value1|value2|value3');
                    description.text('Separate values with | character');
                } else if (type === 'text') {
                    valuesRow.show();
                    textarea.attr('placeholder', 'single value');
                    description.text('Enter a single text value');
                } else if (type === 'range') {
                    valuesRow.show();
                    textarea.attr('placeholder', '5-15');
                    description.text('Enter range in format: min-max');
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * Render variable row
     */
    private function render_variable_row($var_name, $var_data) {
        $type = $var_data['type'] ?? 'array';
        $values = $var_data['values'] ?? '';
        ?>
        <div class="ns-variable-row">
            <h3>Variable: <?php echo esc_html($var_name); ?></h3>
            <table class="form-table">
                <tr>
                    <th scope="row">Variable Name</th>
                    <td>
                        <input type="text" name="ns_settings[variables][<?php echo esc_attr($var_name); ?>][name]" 
                               value="<?php echo esc_attr($var_name); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">Type</th>
                    <td>
                        <select name="ns_settings[variables][<?php echo esc_attr($var_name); ?>][type]" class="ns-var-type">
                            <option value="array" <?php selected($type, 'array'); ?>>Array (separated by |)</option>
                            <option value="text" <?php selected($type, 'text'); ?>>Single Text</option>
                            <option value="number" <?php selected($type, 'number'); ?>>Random Number (1-100)</option>
                            <option value="range" <?php selected($type, 'range'); ?>>Number Range (e.g., 5-15)</option>
                        </select>
                    </td>
                </tr>
                <tr class="ns-var-values-row" <?php echo ($type === 'number') ? 'style="display:none;"' : ''; ?>>
                    <th scope="row">Values</th>
                    <td>
                        <textarea name="ns_settings[variables][<?php echo esc_attr($var_name); ?>][values]" 
                                  class="large-text" rows="3"><?php echo esc_textarea($values); ?></textarea>
                        <p class="description">
                            <?php 
                            if ($type === 'array') {
                                echo 'Separate values with | character';
                            } elseif ($type === 'text') {
                                echo 'Enter a single text value';
                            } elseif ($type === 'range') {
                                echo 'Enter range in format: min-max';
                            }
                            ?>
                        </p>
                    </td>
                </tr>
            </table>
            <button type="button" class="button ns-remove-variable">Remove Variable</button>
        </div>
        <?php
    }
    
    /**
     * Save settings via AJAX
     */
    public function save_settings() {
        if (!wp_verify_nonce($_POST['nonce'], 'ns_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $settings = $_POST['settings'];
        update_option('ns_settings', $settings);
        
        wp_send_json_success('Settings saved successfully');
    }
} 