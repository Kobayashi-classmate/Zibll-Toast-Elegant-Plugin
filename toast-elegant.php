<?php
/**
 * Plugin Name:  Toast Elegant - 优雅通知
 * Plugin URI:   https://github.com/yourname/toast-elegant
 * Description:  现代化的优雅通知插件，支持多种消息类型，炫彩渐变设计，流畅动画效果
 * Version:      2.0.0
 * Author:       Your Name
 * Author URI:   https://yourwebsite.com
 * License:      GPL v2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  toast-elegant
 * Requires PHP: 7.4
 * Requires at least: 5.0
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 定义插件常量
define('TOAST_ELEGANT_VERSION', '2.0.0');
define('TOAST_ELEGANT_FILE', __FILE__);
define('TOAST_ELEGANT_PATH', plugin_dir_path(__FILE__));
define('TOAST_ELEGANT_URL', plugin_dir_url(__FILE__));

// 加载配置选项
require_once dirname(__FILE__) . '/inc/options.php';

/**
 * 添加设置链接到插件列表
 */
function toast_elegant_plugin_action_links($links) {
    $options_key = 'toast-elegant'; 
    $settings_link = '<a href="' . admin_url('admin.php?page=' . $options_key) . '">⚙️ 设置</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'toast_elegant_plugin_action_links');

/**
 * 前台加载脚本
 */
function toast_elegant_enqueue_scripts() {
    if (!toast_elegant_get_option('enable', false)) {
        return;
    }

    wp_enqueue_script(
        'toast-elegant-js',
        TOAST_ELEGANT_URL . 'assets/js/toast-elegant.js',
        array(),
        TOAST_ELEGANT_VERSION,
        true
    );
    
    // 获取配置
    $config = array(
        'style' => toast_elegant_get_option('style', 'gradient'),
        'position' => toast_elegant_get_option('position', 'top-center'),
        'animation' => toast_elegant_get_option('animation', 'slide-fade'),
        'duration' => intval(toast_elegant_get_option('duration', 3000)),
        'showIcon' => (bool) toast_elegant_get_option('show_icon', true),
        'showProgress' => (bool) toast_elegant_get_option('show_progress', true),
    );
    
    wp_localize_script('toast-elegant-js', 'ToastElegantConfig', $config);
}
add_action('wp_enqueue_scripts', 'toast_elegant_enqueue_scripts');

/**
 * 输出CSS样式
 */
function toast_elegant_output_css() {
    if (!toast_elegant_get_option('enable', false)) {
        return;
    }
    
    $css_file = TOAST_ELEGANT_PATH . 'assets/css/toast-elegant.css';
    if (file_exists($css_file)) {
        $css = file_get_contents($css_file);
        
        // 自定义位置
        $top_desktop = toast_elegant_get_option('position_top_desktop', '');
        $top_mobile = toast_elegant_get_option('position_top_mobile', '');
        
        if ($top_desktop !== '' && is_numeric($top_desktop)) {
            $css .= "\n.toast-elegant-container.top-center,\n.toast-elegant-container.top-left,\n.toast-elegant-container.top-right { top: {$top_desktop}px !important; }";
        }
        
        if ($top_mobile !== '' && is_numeric($top_mobile)) {
            $css .= "\n@media (max-width:767px) {\n  .toast-elegant-container.top-center,\n  .toast-elegant-container.top-left,\n  .toast-elegant-container.top-right { top: {$top_mobile}px !important; }\n}";
        }
        
        // 自定义圆角
        $border_radius = toast_elegant_get_option('border_radius', '');
        if ($border_radius !== '' && is_numeric($border_radius)) {
            $css .= "\n.toast-elegant-item { border-radius: {$border_radius}px !important; }";
        }
        
        echo '<style id="toast-elegant-css">' . $css . '</style>';
    }
}
add_action('wp_head', 'toast_elegant_output_css');

/**
 * 后台加载脚本
 */
function toast_elegant_admin_enqueue_scripts() {
    $options_key = 'toast_elegant_options';
    $screen = get_current_screen();
    
    if ($screen && (strpos($screen->id, $options_key) !== false)) {
        add_action('admin_head', 'toast_elegant_output_admin_css');
        
        wp_enqueue_script(
            'toast-elegant-admin-js',
            TOAST_ELEGANT_URL . 'assets/js/toast-elegant.js',
            array('jquery'),
            TOAST_ELEGANT_VERSION,
            true
        );
        
        $config = array(
            'style' => toast_elegant_get_option('style', 'gradient'),
            'position' => toast_elegant_get_option('position', 'top-center'),
            'animation' => toast_elegant_get_option('animation', 'slide-fade'),
            'duration' => intval(toast_elegant_get_option('duration', 3000)),
            'showIcon' => (bool) toast_elegant_get_option('show_icon', true),
            'showProgress' => (bool) toast_elegant_get_option('show_progress', true),
        );
        
        wp_localize_script('toast-elegant-admin-js', 'ToastElegantConfig', $config);
    }
}
add_action('admin_enqueue_scripts', 'toast_elegant_admin_enqueue_scripts');

/**
 * 后台输出CSS
 */
function toast_elegant_output_admin_css() {
    $css_file = TOAST_ELEGANT_PATH . 'assets/css/toast-elegant.css';
    if (file_exists($css_file)) {
        $css = file_get_contents($css_file);
        
        $border_radius = toast_elegant_get_option('border_radius', '');
        if ($border_radius !== '' && is_numeric($border_radius)) {
            $css .= "\n.toast-elegant-item { border-radius: {$border_radius}px !important; }";
        }
        
        echo '<style id="toast-elegant-admin-css">' . $css . '</style>';
    }
}

/**
 * 在后台设置页面加载脚本和样式
 */
function toast_elegant_admin_scripts($hook) {
    // 确保只在插件的设置页面加载（'toplevel_page_' + 菜单slug）
    if ($hook != 'toplevel_page_toast-elegant') {
        return;
    }

    // 加载 CSS
    wp_enqueue_style(
        'toast-elegant-css',
        TOAST_ELEGANT_URL . 'assets/css/toast-elegant.css',
        array(),
        TOAST_ELEGANT_VERSION
    );

    // 加载 JS
    wp_enqueue_script(
        'toast-elegant-js',
        TOAST_ELEGANT_URL . 'assets/js/toast-elegant.js',
        array('jquery'),
        TOAST_ELEGANT_VERSION,
        true
    );

    // 传递配置参数到 JS (确保测试时能读取到当前配置)
    // 注意：需确保 toast_elegant_get_option 函数已在 options.php 中定义并加载
    $config = array(
        'style'         => toast_elegant_get_option('style', 'gradient'),
        'position'      => toast_elegant_get_option('position', 'top-center'),
        'animation'     => toast_elegant_get_option('animation', 'slide-fade'),
        'duration'      => intval(toast_elegant_get_option('duration', 3000)),
        'showIcon'      => (bool) toast_elegant_get_option('show_icon', true),
        'showProgress'  => (bool) toast_elegant_get_option('show_progress', true),
    );
    
    wp_localize_script('toast-elegant-js', 'ToastElegantConfig', $config);
}
// 挂载到后台脚本加载钩子
add_action('admin_enqueue_scripts', 'toast_elegant_admin_scripts');