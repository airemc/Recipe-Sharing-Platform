<?php
// Oturumu başlat
session_start();

// Veritabanı bağlantısı
include 'db_connection.php';

// 1. GÜVENLİK KONTROLÜ: Kullanıcı giriş yapmış mı?
// Eğer 'user_id' oturumda kayıtlı değilse, bu kişi giriş yapmamıştır.
if (!isset($_SESSION['user_id'])) {
    // Giriş yapmayan kullanıcıyı ana sayfaya yönlendir.
    header("Location: index.php");
    exit;
}

// 2. FORM KONTROLÜ: Veriler POST metoduyla mı geldi?
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. VERİLERİ AL
    // Formdan gelen verileri al (ve güvenli hale getir)
    $recipe_id = (int)$_POST['recipe_id'];
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comments']); // Yorum metni
    
    // Kullanıcı ID'sini oturumdan al
    $user_id = $_SESSION['user_id'];

    // Temel doğrulama: Puan (1-5 arası) ve tarif ID'si geçerli mi?
    if ($recipe_id > 0 && $rating >= 1 && $rating <= 5) {
        
        try {
            // 4. VERİTABANINA EKLE
            $sql = "INSERT INTO reviews (recipe_id, user_id, rating, comments) 
                    VALUES (:recipe_id, :user_id, :rating, :comments)";

            $stmt = $pdo->prepare($sql);
            
            // Parametreleri bağla
            $stmt->bindParam(':recipe_id', $recipe_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':rating', $rating);
            $stmt->bindParam(':comments', $comment);

            // Sorguyu çalıştır
            $stmt->execute();
            
            // 5. BAŞARILI: Kullanıcıyı yorum yaptığı tarif sayfasına geri yönlendir
            header("Location: recipe_detail.php?id=" . $recipe_id . "&status=review_added");
            exit;

        } catch (PDOException $e) {
            // Veritabanı hatası olursa (örn: aynı kullanıcı aynı tarife 2. kez yorum yapmaya çalışırsa
            // ve bir 'unique' kısıtlaması olsaydı), hatayı yönet.
            // Şimdilik basitçe geri yönlendirelim.
            header("Location: recipe_detail.php?id=" . $recipe_id . "&status=review_error");
            exit;
        }

    } else {
        // Form verileri geçersizse (örn: puan seçilmemişse)
        header("Location: recipe_detail.php?id=" . $recipe_id . "&status=review_invalid");
        exit;
    }

} else {
    // POST metodu dışında (örn: adresi kopyalayıp yapıştırarak) gelinirse
    header("Location: index.php");
    exit;
}
?>