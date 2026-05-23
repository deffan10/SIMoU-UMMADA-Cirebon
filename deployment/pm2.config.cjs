module.exports = {
    apps: [
        {
            name: 'simou-queue-default',
            script: 'artisan',
            args: 'queue:work database --sleep=3 --tries=3 --max-time=3600 --queue=default',
            interpreter: '/usr/bin/php8.4',
            cwd: '/home/htdocs/mou',
            instances: 2,
            autorestart: true,
            max_restarts: 10,
            watch: false,
            max_memory_restart: '256M',
            env: {
                APP_ENV: 'production',
            },
        },
        {
            name: 'simou-queue-notifications',
            script: 'artisan',
            args: 'queue:work database --sleep=5 --tries=3 --max-time=3600 --queue=notifications',
            interpreter: '/usr/bin/php8.4',
            cwd: '/home/htdocs/mou',
            instances: 1,
            autorestart: true,
            max_restarts: 10,
            watch: false,
            max_memory_restart: '128M',
            env: {
                APP_ENV: 'production',
            },
        },
        {
            name: 'simou-scheduler',
            script: 'artisan',
            args: 'schedule:work',
            interpreter: '/usr/bin/php8.4',
            cwd: '/home/htdocs/mou',
            instances: 1,
            autorestart: true,
            cron_restart: '0 */6 * * *',
            watch: false,
            max_memory_restart: '128M',
            env: {
                APP_ENV: 'production',
            },
        },
    ],
};
