```php
<?php
// 1. Oturumu başlat
session_start();

// 2. Tüm oturum değişkenlerini temizle
$_SESSION = array();

// 3. Oturumu sonlandır (destroy)
session_destroy();

// 4. Kullanıcıyı ana sayfaya veya giriş sayfasına yönlendir
header("Location: index.php");
exit;
?>