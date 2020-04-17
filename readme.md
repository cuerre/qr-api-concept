## About Cuerre

## Framework info (Apr/2020)
* Laravel 7.X


## Dependencies
* PHP >= 7.2.0
* BCMath PHP Extension
* Ctype PHP Extension
* JSON PHP Extension
* Mbstring PHP Extension
* OpenSSL PHP Extension
* PDO PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* Qrencode Library 
* Zbar-tools Library (zbarimg command)
* Symfony Process (included in composer)


## Preparing

## Installing for production

#### 1. The hard way

0. Install PHP, PHP extensions, Libraries, Composer, Git
1. Clone the repository to a temporary folder
2. Copy composer.json and composer.lock to the final location
3. Go to that folder: 
```
cd /final/location
```
4. Execute: 
```
composer install --no-dev --no-scripts
```
5. Move the temporary folder's content to the final location
6. Re-build the autoload file executing: 
```
composer dump-autoload
```
7. Configure *.env* file
8. Create your DB tables executing: 
```
php artisan migrate
```
9. Give the right permissions to the files: 
```
find /final/location -type f -exec chmod 644 {} \;
find /final/location -type d -exec chmod 755 {} \;
```
10. Configure the web server (like Nginx) to route all the requests 
to public/index.php



#### 2. The easy way (testing mode)
```
0. Install Docker
1. Clone the repository
2. Build the image with the Dockerfile included
3. Upload the image to a repository
4. Use the image with Compose or Kubernetes
   * Set all the environment variables Laravel uses
     + APP_VENDOR
     + APP_NAME
     + [...]

   * Mount /var/www in a volume
   * Mount /var/www/storage into a volume
```

Now, your container is listening on port 9000 (PHP_FPM)


You just must configure an NGINX container in the way 
Laravel documentation explains to use the app



## How to use (some routes only)
#### 1. Encoding into a QR
[GET /api/encode?{QUERY PARAMS}]

  * **data** (mandatory)
    A string to encode
    
  * **dotsize** (optional)
    The size of each QR dot. The value is just a number between 1 and 5
    
  * **ecc** (optional)
    Defines the error correction code (ECC) which determines the degree of data 
    redundancy. The more data redundancy exists, the more data can be restored if 
    a QR code is damaged. Use upper case for this parameter

    Possible values:
    L (low, ~7% destroyed data may be corrected)
    M (middle, ~15% destroyed data may be corrected)
    Q (quality, ~25% destroyed data may be corrected)
    H (high, ~30% destroyed data may be corrected)
    Default (will be used if no or invalid value is set): L
    
    Best practice:
    L. A higher ECC results in more data to save and thus leads to a QR code with 
    more data pixels and a larger data matrix. Because many cell phone readers 
    have problems with QR codes > Version 4 (matrix of 33×33 modules), the lowest 
    ECC is the best choice for common purpose – legacy QR code readers are a more 
    common problem than destroyed QR codes.
    
  * **marginsize** (optional)
    Thickness of the margin. Just a number between 1 and 5. This parameter will 
    be ignored if svg or eps is used as QR code format (=if the QR code output 
    is a vector graphic).
  
  * **dpi** (optional)
    A number between 50 and 100 indicating how many dots are displayed into the
    same space. Higher is better but slower. Default is 72 to balance between 
    quality and speed.
    
  * **output** (optional)
    Format of the resulting image. You can select between PNG, SVG and EPS. By default,
    this setting is set to PNG. Use vector choice just when needed for graphical design
    environments. Use upper case for this parameter.
    
  * **download** (optional)
    Force the generated file to be downloaded
    
  ```
  **example**
  GET /api/encode?data=http://myweb.com&output=PNG
  GET /api/encode?data=Hello, there&output=SVG&ecc=H
  ```
  
[POST /api/decode]
  * **photo** parameter
    An image file (e.g. a photo), which contains the QR code to read, as direct 
    upload for the API (only possible by using a HTTP POST request).

    Format:
    The image file data to read (Content-Type: multipart/form-data). 
    The file has to be a PNG or JP(E)G image which is smaller than 1 MiB..
    
    ```
    Example of a QR code upload form:
    <html>
        <form enctype="multipart/form-data" action="http://cuerre.achetronic.com/api/decode" method="POST">
            Choose image to scan: 
            <input name="photo" type="file" />
            <input type="submit" value="Scan" />
        </form>
    </html>
    ```


## Security Vulnerabilities

If you discover security vulnerabilities, please send 
an e-mail to Alby Hernández [me@achetronic.com]. 

All security vulnerabilities will 
be fixed as soon as we notice them.

## License
This is privative software and it is NOT allowed to redistribute
any copy neither partial not complete.
