<?php
/**
 * Toast Elegant - 配置选项
 * 使用WordPress原生Settings API
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 获取配置选项
 */
function toast_elegant_get_option($key, $default = false) {
    $options = get_option('toast_elegant_options', array());
    return isset($options[$key]) ? $options[$key] : $default;
}

/**
 * 注册设置
 */
function toast_elegant_register_settings() {
    register_setting(
        'toast_elegant_options_group',
        'toast_elegant_options',
        'toast_elegant_sanitize_options'
    );
}
add_action('admin_init', 'toast_elegant_register_settings');

/**
 * 数据验证和清理
 */
function toast_elegant_sanitize_options($input) {
    $sanitized = array();
    
    // 启用开关
    $sanitized['enable'] = isset($input['enable']) ? true : false;
    
    // 视觉风格
    $valid_styles = array('gradient', 'glass', 'solid', 'outline');
    $sanitized['style'] = isset($input['style']) && in_array($input['style'], $valid_styles) 
        ? $input['style'] : 'gradient';
    
    // 显示位置
    $valid_positions = array('top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right');
    $sanitized['position'] = isset($input['position']) && in_array($input['position'], $valid_positions)
        ? $input['position'] : 'top-center';
    
    // 动画效果
    $valid_animations = array('slide-fade', 'zoom', 'bounce', 'flip');
    $sanitized['animation'] = isset($input['animation']) && in_array($input['animation'], $valid_animations)
        ? $input['animation'] : 'slide-fade';
    
    // 圆角大小
    $sanitized['border_radius'] = isset($input['border_radius']) ? absint($input['border_radius']) : 12;
    
    // 显示时长
    $sanitized['duration'] = isset($input['duration']) ? absint($input['duration']) : 3000;
    if ($sanitized['duration'] < 1000) $sanitized['duration'] = 1000;
    if ($sanitized['duration'] > 10000) $sanitized['duration'] = 10000;
    
    // 开关选项
    $sanitized['show_icon'] = isset($input['show_icon']) ? true : false;
    $sanitized['show_progress'] = isset($input['show_progress']) ? true : false;
    
    // 位置偏移
    $sanitized['position_top_desktop'] = isset($input['position_top_desktop']) && $input['position_top_desktop'] !== ''
        ? absint($input['position_top_desktop']) : '';
    $sanitized['position_top_mobile'] = isset($input['position_top_mobile']) && $input['position_top_mobile'] !== ''
        ? absint($input['position_top_mobile']) : '';
    
    return $sanitized;
}

/**
 * 添加菜单页面
 */
function toast_elegant_add_admin_menu() {
    add_menu_page(
        'Toast Elegant - 优雅通知',           // 页面标题
        '✨ Toast Elegant',                    // 菜单标题
        'manage_options',                       // 权限
        'toast-elegant',                        // 菜单slug
        'toast_elegant_options_page',           // 回调函数
        'dashicons-megaphone',                  // 图标
        80                                      // 位置
    );
}
add_action('admin_menu', 'toast_elegant_add_admin_menu');

/**
 * 设置页面HTML
 */
function toast_elegant_options_page() {
    // 检查权限
    if (!current_user_can('manage_options')) {
        wp_die(__('您没有权限访问此页面。'));
    }
    
    // 获取当前选项
    $options = get_option('toast_elegant_options', array());
    
    // 设置默认值
    $defaults = array(
        'enable' => false,
        'style' => 'gradient',
        'position' => 'top-center',
        'animation' => 'slide-fade',
        'border_radius' => 12,
        'duration' => 3000,
        'show_icon' => true,
        'show_progress' => true,
        'position_top_desktop' => '',
        'position_top_mobile' => '',
    );
    
    $options = wp_parse_args($options, $defaults);
    ?>
    
    <div class="wrap toast-elegant-settings">
        <h1>✨ Toast Elegant - 优雅通知</h1>
        <p class="description">现代化的优雅通知插件配置</p>
        
        <?php settings_errors(); ?>
        
        <form method="post" action="options.php">
            <?php settings_fields('toast_elegant_options_group'); ?>
            
            <div class="toast-elegant-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#basic" class="nav-tab nav-tab-active">⚙️ 基本设置</a>
                    <a href="#appearance" class="nav-tab">🎨 外观设置</a>
                    <a href="#position" class="nav-tab">📍 位置和动画</a>
                    <a href="#test" class="nav-tab">🧪 测试预览</a>
                    <a href="#docs" class="nav-tab">📖 使用文档</a>
                </nav>
                
                <!-- 基本设置 -->
                <div id="basic" class="tab-content active">
                    <h2>基本设置</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">启用插件</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="toast_elegant_options[enable]" value="1" <?php checked($options['enable'], true); ?>>
                                    启用 Toast Elegant 优雅通知
                                </label>
                                <p class="description">启用后将在前台和后台加载通知组件</p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- 外观设置 -->
                <div id="appearance" class="tab-content">
                    <h2>外观设置</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">视觉风格</th>
                            <td>
                                <fieldset>
                                    <label><input type="radio" name="toast_elegant_options[style]" value="gradient" <?php checked($options['style'], 'gradient'); ?>> 渐变风格 (推荐)</label><br>
                                    <label><input type="radio" name="toast_elegant_options[style]" value="glass" <?php checked($options['style'], 'glass'); ?>> 毛玻璃风格</label><br>
                                    <label><input type="radio" name="toast_elegant_options[style]" value="solid" <?php checked($options['style'], 'solid'); ?>> 纯色风格</label><br>
                                    <label><input type="radio" name="toast_elegant_options[style]" value="outline" <?php checked($options['style'], 'outline'); ?>> 描边风格</label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">圆角大小</th>
                            <td>
                                <input type="range" name="toast_elegant_options[border_radius]" min="0" max="30" value="<?php echo esc_attr($options['border_radius']); ?>" class="toast-elegant-range">
                                <span class="range-value"><?php echo esc_html($options['border_radius']); ?>px</span>
                                <p class="description">通知框的圆角大小 (0-30px)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">显示图标</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="toast_elegant_options[show_icon]" value="1" <?php checked($options['show_icon'], true); ?>>
                                    在通知中显示类型图标
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">显示进度条</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="toast_elegant_options[show_progress]" value="1" <?php checked($options['show_progress'], true); ?>>
                                    显示倒计时进度条
                                </label>
                                <p class="description">在通知底部显示自动关闭的进度条</p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- 位置和动画 -->
                <div id="position" class="tab-content">
                    <h2>位置和动画</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">显示位置</th>
                            <td>
                                <fieldset>
                                    <label><input type="radio" name="toast_elegant_options[position]" value="top-left" <?php checked($options['position'], 'top-left'); ?>> ↖️ 左上角</label><br>
                                    <label><input type="radio" name="toast_elegant_options[position]" value="top-center" <?php checked($options['position'], 'top-center'); ?>> ⬆️ 顶部居中</label><br>
                                    <label><input type="radio" name="toast_elegant_options[position]" value="top-right" <?php checked($options['position'], 'top-right'); ?>> ↗️ 右上角</label><br>
                                    <label><input type="radio" name="toast_elegant_options[position]" value="bottom-left" <?php checked($options['position'], 'bottom-left'); ?>> ↙️ 左下角</label><br>
                                    <label><input type="radio" name="toast_elegant_options[position]" value="bottom-center" <?php checked($options['position'], 'bottom-center'); ?>> ⬇️ 底部居中</label><br>
                                    <label><input type="radio" name="toast_elegant_options[position]" value="bottom-right" <?php checked($options['position'], 'bottom-right'); ?>> ↘️ 右下角</label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">顶部距离 (桌面端)</th>
                            <td>
                                <input type="number" name="toast_elegant_options[position_top_desktop]" value="<?php echo esc_attr($options['position_top_desktop']); ?>" min="0" max="500" class="small-text"> px
                                <p class="description">自定义桌面端距离顶部的距离 (留空使用默认值: 20px)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">顶部距离 (移动端)</th>
                            <td>
                                <input type="number" name="toast_elegant_options[position_top_mobile]" value="<?php echo esc_attr($options['position_top_mobile']); ?>" min="0" max="500" class="small-text"> px
                                <p class="description">自定义移动端距离顶部的距离 (留空使用默认值: 20px)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">动画效果</th>
                            <td>
                                <fieldset>
                                    <label><input type="radio" name="toast_elegant_options[animation]" value="slide-fade" <?php checked($options['animation'], 'slide-fade'); ?>> 滑动淡入</label><br>
                                    <label><input type="radio" name="toast_elegant_options[animation]" value="zoom" <?php checked($options['animation'], 'zoom'); ?>> 缩放</label><br>
                                    <label><input type="radio" name="toast_elegant_options[animation]" value="bounce" <?php checked($options['animation'], 'bounce'); ?>> 弹跳</label><br>
                                    <label><input type="radio" name="toast_elegant_options[animation]" value="flip" <?php checked($options['animation'], 'flip'); ?>> 翻转</label>
                                </fieldset>
                                <p class="description">通知出现和消失的动画效果</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">显示时长</th>
                            <td>
                                <input type="range" name="toast_elegant_options[duration]" min="1000" max="10000" step="500" value="<?php echo esc_attr($options['duration']); ?>" class="toast-elegant-range">
                                <span class="range-value"><?php echo esc_html($options['duration']); ?>ms</span>
                                <p class="description">通知自动关闭的时间 (1000-10000ms)</p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- 测试预览 -->
                <div id="test" class="tab-content">
                    <h2>测试预览</h2>
                    <div class="toast-test-section">
                        <p class="description">点击下方按钮测试不同类型的通知效果</p>
                        <div class="toast-test-buttons">
                            <button type="button" class="button button-success" onclick="testToast('success')">
                                <span class="dashicons dashicons-yes"></span> 成功消息
                            </button>
                            <button type="button" class="button button-error" onclick="testToast('error')">
                                <span class="dashicons dashicons-no"></span> 错误消息
                            </button>
                            <button type="button" class="button button-warning" onclick="testToast('warning')">
                                <span class="dashicons dashicons-warning"></span> 警告消息
                            </button>
                            <button type="button" class="button button-info" onclick="testToast('info')">
                                <span class="dashicons dashicons-info"></span> 信息消息
                            </button>
                            <button type="button" class="button button-loading" onclick="testToast('loading')">
                                <span class="dashicons dashicons-update"></span> 加载消息
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- 使用文档 -->
                <div id="docs" class="tab-content">
                    <h2>使用文档</h2>
                    <div class="toast-docs">
                        <h3>JavaScript API</h3>
                        <h4>基础用法</h4>
                        <pre><code>// 成功消息
ToastElegant.success('操作成功！');

// 错误消息
ToastElegant.error('操作失败！');

// 警告消息
ToastElegant.warning('请注意！');

// 信息消息
ToastElegant.info('这是一条提示');

// 加载消息
ToastElegant.loading('加载中...');</code></pre>

                        <h4>高级配置</h4>
                        <pre><code>ToastElegant.success('操作成功！', {
    duration: 5000,        // 显示时长(ms)
    showClose: true,       // 显示关闭按钮
    showIcon: true,        // 显示图标
    showProgress: true,    // 显示进度条
    onClose: function() {  // 关闭回调
        console.log('通知已关闭');
    }
});</code></pre>

                        <h4>关闭通知</h4>
                        <pre><code>// 关闭所有通知
ToastElegant.closeAll();

// 关闭特定通知
var toast = ToastElegant.success('消息');
toast.close();</code></pre>

                        <h4>兼容性说明</h4>
                        <p>✅ 自动兼容 <code>window.notyf</code> 调用</p>
                        <pre><code>notyf('消息内容', 'success', 5000);</code></pre>
                    </div>
                </div>
            </div>
            
            <?php submit_button('保存设置'); ?>
        </form>
    </div>
    
    <style>
        .toast-elegant-settings {
            max-width: 1200px;
        }
        .toast-elegant-tabs {
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            margin-top: 20px;
        }
        .nav-tab-wrapper {
            border-bottom: 1px solid #ccd0d4;
            padding: 0;
            margin: 0;
        }
        .tab-content {
            display: none;
            padding: 20px;
        }
        .tab-content.active {
            display: block;
        }
        .toast-elegant-range {
            width: 300px;
            vertical-align: middle;
        }
        .range-value {
            display: inline-block;
            min-width: 50px;
            font-weight: bold;
            color: #2271b1;
        }
        .toast-test-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 8px;
        }
        .toast-test-section p {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
        }
        .toast-test-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-top: 20px;
        }
        .toast-test-buttons .button {
            height: auto;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s;
        }
        .toast-test-buttons .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        .button-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .button-error { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .button-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .button-info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .button-loading { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .toast-docs {
            line-height: 1.6;
        }
        .toast-docs h3 {
            color: #1e293b;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
            margin-top: 30px;
        }
        .toast-docs h4 {
            color: #475569;
            margin-top: 20px;
        }
        .toast-docs pre {
            background: #1e293b;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
        }
        .toast-docs code {
            font-family: 'Monaco', 'Menlo', 'Consolas', monospace;
            font-size: 13px;
            line-height: 1.6;
        }
    </style>
    
    <script>
        jQuery(document).ready(function($) {
            // 标签页切换
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                $('.tab-content').removeClass('active');
                $(target).addClass('active');
            });
            
            // 滑块值更新
            $('.toast-elegant-range').on('input', function() {
                $(this).next('.range-value').text($(this).val() + ($(this).attr('name').includes('duration') ? 'ms' : 'px'));
            });
        });
        
        // 测试通知
        function testToast(type) {
            if (typeof ToastElegant === 'undefined') {
                alert('请先保存设置并启用插件');
                return;
            }
            
            var messages = {
                success: '🎉 操作成功！您的更改已保存',
                error: '😞 操作失败！请检查后重试',
                warning: '⚡ 请注意！这是一条重要提醒',
                info: '💡 友情提示：这是一条信息消息',
                loading: '⏳ 正在处理中，请稍候...'
            };
            
            ToastElegant[type](messages[type] || '测试消息');
        }
    </script>
    <?php
}