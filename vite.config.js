import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/login.css',
                'resources/js/app.js',
                'resources/js/profile_edit.js',
                'resources/js/day_stats.js',
                'resources/js/leaderboard.js',
                'resources/js/login.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        port: 5173,
        host: '0.0.0.0',
        hmr: {
            host: 'localhost'
        },
    }
});
