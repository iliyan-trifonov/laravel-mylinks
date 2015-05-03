# My Links
[https://mylinks.iliyan-trifonov.com/](https://mylinks.iliyan-trifonov.com/ "My Links") Laravel Application

Save all of your interesting links on the site like bookmarks. Protect them behind your user account.

Paste your urls in the application and it fetches their icons and titles and saves them 
remembering the date of creation of the url. Clicking on a saved link will open it in a new tab.

There is a search functionality to help you find your link without browsing through all the pages.

The application supports url redirects so if such exist the final url will be saved.

For parsing the pages on the given urls [yangqi/htmldom](https://github.com/yangqi/htmldom "htmldom") is used.

For developing [barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar "laravel-debugbar") and 
[barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper "laravel-ide-helper") are used.

By default an SQLite database is used and is enough to handle a good number of users.

If you want to make a quick test of the site go to 
[https://mylinks.iliyan-trifonov.com/login](https://mylinks.iliyan-trifonov.com/login "My Links") and use this default 
account: iliyan@host.com/1234 (email/pass).

### Install (in production)
    cd /var/www/
    git clone https://github.com/iliyan-trifonov/laravel-mylinks.git mylinks
    cd mylinks
    composer self-update && composer install
    touch app/database/production.sqlite
    php artisan migrate --seed --force
    chown -R www-data:www-data ./
