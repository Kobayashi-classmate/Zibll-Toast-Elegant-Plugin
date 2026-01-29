# Toast Elegant - 优雅通知 (WP Plugin)

> 为 WordPress 打造的现代化通知插件，提供炫彩渐变设计和流畅动画效果。

[![Version](https://img.shields.io/badge/version-2.0.0-blue.svg?style=flat-square)](https://github.com/Kobayashi-classmate/Zibll-Toast-Elegant-Plugin/releases)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-444140.svg?style=flat-square&logo=wordpress)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4.svg?style=flat-square&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg?style=flat-square)](LICENSE)

---

## ✨ 核心特性

Toast Elegant 旨在替换陈旧的浏览器弹窗，提供**零依赖**、**高性能**的用户交互反馈。

### 🎨 视觉与交互
- **4种视觉风格**：`Gradient`(渐变)、`Glass`(毛玻璃)、`Solid`(纯色)、`Outline`(描边)
- **4种动画效果**：滑动淡入、缩放弹出、弹性跳动、3D翻转
- **深色模式适配**：自动跟随系统或主题切换 Dark/Light 模式
- **完全响应式**：从宽屏桌面到移动端完美适配

### ⚡ 技术优势
- **轻量高效**：基于原生 JavaScript (Vanilla JS)，无 jQuery 强依赖
- **兼容性强**：无缝替换 `notyf` 等旧版调用库
- **无障碍支持 (a11y)**：符合 WCAG 标准，支持屏幕阅读器
- **开发者友好**：提供丰富的 JS API 和回调函数

---

## 📦 安装指南

1. 下载最新版插件压缩包 (`zip`)。
2. 登录 WordPress 后台，进入 **插件 > 安装插件 > 上传插件**。
3. 选择压缩包并点击"现在安装"。
4. 激活插件。
5. 前往 **设置 > Toast Elegant** 配置全局样式。

**目录结构：**
```text
/toast-elegant/
├── assets/
│   ├── css/toast-elegant.css    # 核心样式
│   └── js/toast-elegant.js      # 核心逻辑
├── inc/
│   └── options.php              # 后台设置页面
├── toast-elegant.php            # 主文件
└── readme.md
```

---

## 🚀 开发文档

### 1. 基础调用 (JavaScript)

您可以在任何加载了 footer 的前端页面使用以下代码：

```javascript
// ✅ 成功 - 绿色渐变
ToastElegant.success('文章保存成功！');

// ❌ 错误 - 红色警示
ToastElegant.error('网络连接失败，请重试。');

// ⚠️ 警告 - 黄色提醒
ToastElegant.warning('此操作不可撤销。');

// ℹ️ 信息 - 蓝色提示
ToastElegant.info('您有一条新私信。');

// ⏳ 加载 - 紫色等待 (需手动关闭)
const loader = ToastElegant.loading('数据同步中...');
setTimeout(() => {
    loader.close();
    ToastElegant.success('同步完成');
}, 2000);
```

### 2. 高级配置参数

在调用时传入第二个参数对象来覆盖全局设置：

```javascript
ToastElegant.success('操作成功！', {
    duration: 5000,         // 停留 5 秒
    showClose: true,        // 显示关闭按钮
    position: 'top-right',  // 显示在右上角
    style: 'glass',         // 使用毛玻璃风格
    animation: 'bounce',    // 使用弹跳动画
    onClose: () => {        // 关闭后的回调
        console.log('用户关闭了通知');
    }
});
```

### 3. PHP 端调用示例

虽然本插件主要运行在前端，但你可以通过 PHP 钩子输出 JS 来触发通知（例如表单提交后）：

```php
// 在 functions.php 或业务逻辑中
add_action('wp_footer', function() {
    if (isset($_GET['status']) && $_GET['status'] == 'success') {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                ToastElegant.success('表单提交成功！');
            });
        </script>
        <?
    }
});
```

### 4. 全局 API

| 方法 | 描述 |
| :--- | :--- |
| `ToastElegant.config(options)` | 运行时动态修改全局配置 |
| `ToastElegant.closeAll()` | 立即关闭所有屏幕上的通知 |
| `notyf(msg, type)` | 兼容旧版调用接口 |

---

## ⚙️ 配置参考表

| 选项 (Key) | 类型 | 默认值 | 可选值 / 说明 |
| :--- | :--- | :--- | :--- |
| `style` | String | `gradient` | `glass`, `solid`, `outline` |
| `position` | String | `top-center` | `top-left`, `top-right`, `bottom-left`, `bottom-center`, `bottom-right` |
| `animation` | String | `slide-fade` | `zoom`, `bounce`, `flip` |
| `duration` | Number | `3000` | 单位毫秒 (ms) |
| `showIcon` | Boolean | `true` | 是否显示状态图标 |
| `showProgress`| Boolean | `true` | 是否显示倒计时进度条 |
| `maxToasts` | Number | `5` | 这里限制屏幕上同时显示的最大数量 |

---

## 📝 更新日志

### v2.0.0 (2024-01-29) - 重构版
- **🎉 重大更新**：
    - 全新重构代码库，采用面向对象设计。
    - 移除对第三方库的重度依赖。
- **✨ 新增功能**：
    - 新增 **毛玻璃 (Glass)**、**描边 (Outline)** 等 4 种视觉风格。
    - 新增 **深色模式** 自动适配。
    - 新增后台实时预览测试功能。
- **🐛 问题修复**：
    - 修复浅色模式下纯白背景导致文字不可见的问题。
    - 修复后台设置页面静态资源加载 403 问题。
    - 修复 `loading` 类型通知在测试模式下无法自动关闭的问题。

---

## 🤝 贡献与支持

- **作者**: Kobayashi-classmate
- **仓库**: [GitHub Repository](https://github.com/Kobayashi-classmate/Zibll-Toast-Elegant-Plugin)
- **反馈**: 请通过 GitHub Issues 提交 Bug 或建议。

## 📄 许可证

本项目基于 [GPL v2 or later](http://www.gnu.org/licenses/gpl-2.0.html) 开源。
您可以自由修改、分发此插件，但必须保持开源属性。

---
*Made with ❤️ for WordPress*