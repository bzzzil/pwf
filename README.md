# pwf
PHP Web Framework

## Configuration
Give write permission to *include\smarty\cache*
### Apache
*.htaccess* should containg following:
```
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase /
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^(.*)$ ./index.php
</IfModule>
```
### nginx
```
location / {
    index index.php;
    if (!-f $request_filename) {
        rewrite ^(.*)$ /index.php last;
    }
}
```