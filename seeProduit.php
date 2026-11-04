<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>See Produit</title>
    <link rel="stylesheet" href="styles/seeProduits.css">
</head>
<body>
    
<?php 
include("config.php");
include("header.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    include("proHeader.html");
}



// Get product ID and language from URL
$id_produit = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$lang = isset($_GET["lang"]) && $_GET["lang"] === "ar" ? "ar" : "en";

// Fetch product
$stmt = $db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$id_produit]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch images
$stmtImg = $db->prepare("SELECT url, alt_text FROM images WHERE product_id = ? LIMIT 8");
$stmtImg->execute([$id_produit]);
$imgInfo = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

if ($product):
    // Language fields
    $nameField = "name_" . $lang;
    $descField = "description_" . $lang;
?>



<!-- Language Toggle Button -->
<div class="language-toggle">
    <a href="?id=<?= $id_produit ?>&lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>">English</a>
    <a href="?id=<?= $id_produit ?>&lang=ar" class="lang-btn <?= $lang === 'ar' ? 'active' : '' ?>">العربية</a>
</div>

<!-- Product Content -->
<article style="<?= $lang === 'ar' ? 'text-align:right; direction:rtl;' : '' ?>" <?= $lang === 'ar' ? 'dir="rtl"' : '' ?>>
    
<!-- Search Bar -->
<div class="search-container-cc">
    <form method="GET" action="" style="display: flex;">
        <input type="hidden" name="lang" value="<?= $lang ?>">
        <input 
            type="number" 
            name="id" 
            class="search-input" 
            placeholder="<?= $lang === 'ar' ? 'ابحث عن منتج برقم المعرف...' : 'Search product by ID...' ?>" 
            value="<?= $id_produit > 0 ? $id_produit : '' ?>"
            min="1"
            required
        >
        <button type="submit" class="search-btn">
            <svg class="search-icon" viewBox="0 0 24 24">
                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
            <?= $lang === 'ar' ? 'بحث' : 'Search' ?>
        </button>
    </form>
</div>

    <?php if ($imgInfo): ?>
        <div class="<?= count($imgInfo) === 1 ? 'single-image' : 'img-grid' ?>">
            <?php foreach ($imgInfo as $img): ?>
                <img src="<?= htmlspecialchars($img['url']) ?>" alt="<?= htmlspecialchars($img['alt_text']) ?>">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2><?= htmlspecialchars($product[$nameField]) ?></h2> 
    <p><?= htmlspecialchars($product[$descField]) ?></p> 

    <a href="#" class="whatsapp-btn" onclick="sendWhatsAppMessage(<?= $id_produit?>)">
        <svg class="whatsapp-icon" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
        </svg>
        <?= $lang === 'ar' ? 'إرسال رسالة واتساب' : 'Send WhatsApp Message' ?>
    </a>
</article>


<?php else: ?>
    <!-- Search Bar (shown even when no product found) -->
     <div class="search-container-cc">
            <form method="GET" action="" style="display: flex;">
                <input type="hidden" name="lang" value="<?= $lang ?>">
                <input 
                    type="number" 
                    name="id" 
                    class="search-input" 
                    placeholder="<?= $lang === 'ar' ? 'ابحث عن منتج برقم المعرف...' : 'Search product by ID...' ?>" 
                    value="<?= $id_produit > 0 ? $id_produit : '' ?>"
                    min="1"
                    required
                >
                <button type="submit" class="search-btn">
                    <svg class="search-icon" viewBox="0 0 24 24">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <?= $lang === 'ar' ? 'بحث' : 'Search' ?>
                </button>
            </form>
        </div>
        
    <div class="error">
        <p>
            <?= $lang === 'ar' ? 'المنتج غير موجود.' : 'Product not found.' ?>
        </p>
    
    </div>
    <!-- Language Toggle Button -->
    <div class="language-toggle">
        <a href="?id=<?= $id_produit ?>&lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>">English</a>
        <a href="?id=<?= $id_produit ?>&lang=ar" class="lang-btn <?= $lang === 'ar' ? 'active' : '' ?>">العربية</a>
    </div>

    
<?php endif; ?>

<script>
    function sendWhatsAppMessage(productId) {
        // Your WhatsApp business number (replace with your actual number)
        const phoneNumber = '0617763777'; // Replace with your WhatsApp business number
        
        // Message text with product ID
        const message = <?= json_encode($lang === 'ar' 
            ? 'مرحبا، أنا مهتم بالمنتج رقم: ' 
            : 'Hello, I am interested in product ID: ') ?> + productId;
        
        // Encode the message for URL
        const encodedMessage = encodeURIComponent(message);
        
        // Create WhatsApp URL
        const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
        
        // Open WhatsApp
        window.open(whatsappUrl, '_blank');
    }

    // Auto-focus search input when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('.search-input');
        if (searchInput && !searchInput.value) {
            searchInput.focus();
        }
    });

    // Handle Enter key in search input
    document.querySelector('.search-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.querySelector('.search-btn').click();
        }
    });
</script>
</body>
</html>
