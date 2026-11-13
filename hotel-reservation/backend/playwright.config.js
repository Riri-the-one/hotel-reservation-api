import { defineConfig } from '@playwright/test';
export default defineConfig({
    testDir: './tests/Browser',
    workers: 1,
    use: { baseURL: process.env.DEMO_URL || 'http://127.0.0.1:8000', channel: process.env.CI ? undefined : 'msedge', headless: true, trace: 'off', screenshot: 'off' },
});
