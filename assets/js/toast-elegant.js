/**
 * Toast Elegant - 优雅通知 JavaScript核心
 * Version: 2.0.0
 */

(function (window) {
    'use strict';

    // 默认配置
    const defaultConfig = {
        style: 'gradient',          // 视觉风格: gradient, glass, solid, outline
        position: 'top-center',     // 位置: top-left, top-center, top-right, bottom-left, bottom-center, bottom-right
        animation: 'slide-fade',    // 动画: slide-fade, zoom, bounce, flip
        duration: 3000,             // 显示时长(ms)
        showIcon: true,             // 显示图标
        showProgress: true,         // 显示进度条
        showClose: false,           // 显示关闭按钮
        maxToasts: 5,               // 最大通知数量
        html: false,                // 允许HTML
        onClose: null               // 关闭回调
    };

    // 从WordPress传递的配置合并
    let globalConfig = defaultConfig;
    if (typeof ToastElegantConfig !== 'undefined') {
        globalConfig = Object.assign({}, defaultConfig, ToastElegantConfig);
    }

    // 图标SVG
    const icons = {
        success: `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.2"/>
            <path d="M8 12.5L10.5 15L16 9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`,

        error: `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.2"/>
            <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>`,

        warning: `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 20h20L12 2z" fill="currentColor" opacity="0.2"/>
            <path d="M12 9v4M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>`,

        info: `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.2"/>
            <path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>`,

        loading: `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" opacity="0.2"/>
            <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>`,

        close: `<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>`
    };

    // Toast实例类
    class Toast {
        constructor(message, type, options) {
            this.message = message;
            this.type = type;
            this.options = Object.assign({}, globalConfig, options);
            this.element = null;
            this.progressBar = null;
            this.timer = null;
            this.startTime = null;
            this.remainingTime = this.options.duration;
            this.id = Date.now() + Math.random();

            this.init();
        }

        init() {
            this.createElement();
            this.show();
            if (this.options.duration > 0 && this.type !== 'loading') {
                this.startTimer();
            }
        }

        createElement() {
            const item = document.createElement('div');
            item.className = `toast-elegant-item ${this.type}`;
            item.setAttribute('data-id', this.id);

            const content = document.createElement('div');
            content.className = `toast-elegant-content style-${this.options.style}`;

            const body = document.createElement('div');
            body.className = 'toast-elegant-body';

            // 图标
            if (this.options.showIcon) {
                const iconEl = document.createElement('div');
                iconEl.className = `toast-elegant-icon ${this.type}`;
                iconEl.innerHTML = icons[this.type] || icons.info;
                body.appendChild(iconEl);
            }

            // 消息内容
            const messageEl = document.createElement('div');
            messageEl.className = 'toast-elegant-message';
            if (this.options.html) {
                messageEl.innerHTML = this.message;
            } else {
                messageEl.textContent = this.message;
            }
            body.appendChild(messageEl);

            content.appendChild(body);

            // 关闭按钮
            if (this.options.showClose) {
                const closeBtn = document.createElement('button');
                closeBtn.className = 'toast-elegant-close';
                closeBtn.innerHTML = icons.close;
                closeBtn.onclick = () => this.close();
                content.appendChild(closeBtn);
            }

            // 进度条
            if (this.options.showProgress && this.options.duration > 0 && this.type !== 'loading') {
                const progress = document.createElement('div');
                progress.className = 'toast-elegant-progress';
                const progressBar = document.createElement('div');
                progressBar.className = 'toast-elegant-progress-bar';
                progressBar.style.width = '100%';
                progress.appendChild(progressBar);
                content.appendChild(progress);
                this.progressBar = progressBar;
            }

            item.appendChild(content);
            this.element = item;

            // 鼠标悬停暂停
            item.addEventListener('mouseenter', () => this.pause());
            item.addEventListener('mouseleave', () => this.resume());
        }

        show() {
            const container = this.getContainer();

            // 检查最大数量限制
            const items = container.querySelectorAll('.toast-elegant-item');
            if (items.length >= this.options.maxToasts) {
                const oldest = items[0];
                if (oldest) {
                    this.removeElement(oldest);
                }
            }

            container.appendChild(this.element);

            // 触发入场动画
            setTimeout(() => {
                this.playAnimation('in');
            }, 10);
        }

        playAnimation(direction) {
            const animations = {
                'slide-fade': {
                    in: this.isTopPosition() ? 'toastSlideInDown' : 'toastSlideInUp',
                    out: this.isTopPosition() ? 'toastSlideOutUp' : 'toastSlideOutDown'
                },
                'zoom': {
                    in: 'toastZoomIn',
                    out: 'toastZoomOut'
                },
                'bounce': {
                    in: 'toastBounceIn',
                    out: 'toastBounceOut'
                },
                'flip': {
                    in: 'toastFlipIn',
                    out: 'toastFlipOut'
                }
            };

            const animationSet = animations[this.options.animation] || animations['slide-fade'];
            const animationName = animationSet[direction];

            this.element.style.animation = `${animationName} 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards`;
        }

        isTopPosition() {
            return this.options.position.startsWith('top');
        }

        getContainer() {
            let container = document.querySelector(`.toast-elegant-container.${this.options.position}`);

            if (!container) {
                container = document.createElement('div');
                container.className = `toast-elegant-container ${this.options.position}`;
                document.body.appendChild(container);
            }

            return container;
        }

        startTimer() {
            this.startTime = Date.now();
            this.updateProgress();
        }

        updateProgress() {
            if (!this.progressBar) {
                this.timer = setTimeout(() => this.close(), this.remainingTime);
                return;
            }

            const update = () => {
                if (!this.startTime) return;

                const elapsed = Date.now() - this.startTime;
                const remaining = Math.max(0, this.remainingTime - elapsed);
                const progress = (remaining / this.options.duration) * 100;

                this.progressBar.style.width = `${progress}%`;

                if (remaining > 0) {
                    requestAnimationFrame(update);
                } else {
                    this.close();
                }
            };

            requestAnimationFrame(update);
        }

        pause() {
            if (this.startTime && this.type !== 'loading') {
                const elapsed = Date.now() - this.startTime;
                this.remainingTime = Math.max(0, this.remainingTime - elapsed);
                this.startTime = null;

                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }
            }
        }

        resume() {
            if (!this.startTime && this.remainingTime > 0 && this.type !== 'loading') {
                this.startTime = Date.now();
                this.updateProgress();
            }
        }

        close() {
            if (!this.element || !this.element.parentNode) return;

            this.playAnimation('out');

            setTimeout(() => {
                this.removeElement(this.element);

                if (typeof this.options.onClose === 'function') {
                    this.options.onClose();
                }
            }, 400);
        }

        removeElement(element) {
            if (element && element.parentNode) {
                element.parentNode.removeChild(element);
            }
        }
    }

    // 主要API
    const ToastElegant = {
        success(message, options) {
            return new Toast(message, 'success', options);
        },

        error(message, options) {
            return new Toast(message, 'error', options);
        },

        warning(message, options) {
            return new Toast(message, 'warning', options);
        },

        info(message, options) {
            return new Toast(message, 'info', options);
        },

        loading(message, options) {
            return new Toast(message, 'loading', Object.assign({}, options, { duration: 0 }));
        },

        closeAll() {
            const containers = document.querySelectorAll('.toast-elegant-container');
            containers.forEach(container => {
                const items = container.querySelectorAll('.toast-elegant-item');
                items.forEach(item => {
                    if (item.parentNode) {
                        item.parentNode.removeChild(item);
                    }
                });
            });
        },

        config(options) {
            globalConfig = Object.assign({}, defaultConfig, options);
        }
    };

    // 兼容notyf调用
    function compatNotyf(message, type, duration) {
        type = type === 'danger' ? 'error' : (type || 'success');
        duration = duration || 3000;

        if (duration < 100) {
            duration *= 1000;
        }

        // 清理HTML标签和脚本
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = message;
        const textContent = tempDiv.textContent || tempDiv.innerText || '';

        const typeMap = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info',
            loading: 'loading'
        };

        const toastType = typeMap[type] || 'info';
        return ToastElegant[toastType](textContent.trim(), { duration: duration });
    }

    // 轮询覆盖notyf
    let intervalId = setInterval(() => {
        if (typeof window.notyf !== 'undefined' || window.notyf !== compatNotyf) {
            window.notyf = compatNotyf;
            clearInterval(intervalId);
        }
    }, 100);

    // 2秒后停止轮询
    setTimeout(() => {
        clearInterval(intervalId);
        window.notyf = compatNotyf;
    }, 2000);

    // 导出到全局
    window.ToastElegant = ToastElegant;

})(window);