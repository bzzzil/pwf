# pwf
PHP Web Framework

## Configuration
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
TODO
