# securityrouter-central
Small, simple website for listing and taking config backups of http://securityrouter.org systems

## Installation
1. Clone to a PHP webserver
2. Put some kind of authentication (such as `htpasswd`) in place
3. Edit inc.php and change `sqlite:/tmp/routers.sqlite3` 
4. Edit api.php and change the API key `secret` 
5. Run `php install.txt`
6. Create a `cron` job to run `php backup_cfg.txt` as often as you like

## Usage
Create a file such as `/cfg/callhome.sh` which you start from `/cfg/skel/rc.local` on your securityrouters: 

```
serial=`cat /cfg/serial`
while true
do
        ftp -o/dev/null "http(s)://URL/PATH/api.php?api-key=secret&type=unit&serial=$serial"
        sleep 10
done
```

and once they show up in the router list (they will add themself to the database via the `api.php` request), press the *edit* button and add a URL (in the format `https://HOST/remote/`), username and password (to a read-only user, preferably). 
