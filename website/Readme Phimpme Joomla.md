In order to install this test system, please follow the following steps:

1. Restore database from sql file in joomla/website/sql folder
  a. Login to mysql use command line:
     - command : mysql -uroot -p

  b. create phimpme_drupal database : 
     - command : CREATE DATABASE phimpme_joomla;
     - command : exit;

  c. Restore database :
     mysql -u root -p phimpme_joomla < link to phimpme_joomla.sql file
     e.g :
     - command : mysql -u root -p phimpme_joomla < ~/phimpme.cms/joomla/website/Sql/phimpme_joomla.sql

2. Move joomla folder to /var/www/ or your specific lolalhost directory and change permission for this folder.
    - Command: cd /var/www/joomla
    - Command: sudo chmod -R 777 joomla/

3. Open configuration.php file and change value of Database in line 14 like this :
        public $dbtype = 'mysqli';
        public $host = 'localhost';
        public $user = 'your mysql_username';
        public $password = 'your mysql_password';
        public $db = 'phimpme_joomla';

Save this file

4. In your browser go to localhost/wordpress/ and check the site.

5. To test Joomla website with Phimp.Me app
    - Connect your phone and your computer same network.
    - Type ifconfig to detect your ip address.
       Command: ifconfig
    Read IP address and type the following into the phimpme drupal form on the app:
    Username: test
    Password: test
    Services link: e.g. on your localhost with the following IP http://192.168.1.19/joomla/

