import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // 或者使用 '127.0.0.1'，确保主机地址正确
        port: 3000, // 确保端口未被其他服务占用
    },
    resolve: {
        alias: {
            '@': '/resources', // 简化资源路径引用
        },
    },
});