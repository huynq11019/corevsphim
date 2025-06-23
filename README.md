# Installation:
1. Extension PHP: fileinfo, exif, mbstring , ionCube
2. Disable function PHP: putenv, proc_open, shell_exec, symlink

3. Import file sqltheme.sql vào database 
4. Cấu hình file .env

5. Create new user by command: `php artisan ophim:user`

6. Run `php artisan optimize:clear`

# Command:
- Generate menu categories & regions: `php artisan ophim:menu:generate`

# Reset view counter:
- Setup crontab, add this entry:
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```