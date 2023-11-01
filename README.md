# TMBL Admin Portal
A portal for The Mortgage Broker Staff to send and control forms electronically

## Scheduling:
Crontab record: * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1


## Mail Tasks
Run mail queue: php artisan queue:work --queue=adviseremails,clientemails --tries=1

Add failed queue item back to live queue: php artisan queue:retry all

Kill queue workers: php artisan queue:restart


## When updating:
Node: npm update

Clear cache: php artisan cache:clear

Rebuild config cache: php artisan config:clear

Composer update (for live): composer update --ignore-platform-reqs

Make sure /app_core/bootstrap/cache is cleared


## Assets
npm run watch

npm run dev

npm run prod