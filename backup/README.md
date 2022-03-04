# Database Backup & Restore

## Restore from SQL file
```
$ mysql -u root -p vm_db < /backup/file.sql
```

## Export from Database 
```
$ mysqldump -u root -p vm_db > /backup/file.sql
```
