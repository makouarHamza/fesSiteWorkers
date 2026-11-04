<?php 
include("header.php");
include("config.php");

// Language handling
// session_start();
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
}

$st = $db->query("SELECT * FROM souks ;");
$souks = $st->fetchAll();
?>
<head>
    <link rel="stylesheet" href="styles/product_section_design.css">
    <link rel="stylesheet" href="styles/Maker_product.css">
    <link rel="stylesheet" href="header.css">
    <style>
        .language-toggle {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 1000;
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 25px;
            padding: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }
        
        .lang-btn {
            background: none;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #666;
        }
        
        .lang-btn.active {
            background: #007bff;
            color: white;
        }
        
        .lang-btn:hover {
            background: #f0f0f0;
        }
        
        .lang-btn.active:hover {
            background: #0056b3;
        }
    </style>
</head>
<body class="<?= $lang === 'ar' ? 'rtl' : '' ?>">
    
    <!-- Language Toggle Button -->
    <div class="language-toggle">
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>">English</a>
        <a href="?lang=ar" class="lang-btn <?= $lang === 'ar' ? 'active' : '' ?>">العربية</a>
    </div>

<div class="section_body">
<?php
// var_dump($data);
foreach($souks as $souk){
    $souk_id = $souk["id"];
    
    // Count sellers who have products
    $st = $db->query("SELECT COUNT(DISTINCT sel.id) AS total_sellers 
                      FROM souks s 
                      LEFT JOIN sellers sel ON s.id = sel.souk_id 
                      INNER JOIN products p ON sel.id = p.seller_id 
                      WHERE s.id = $souk_id");
    $numbOfSeller = $st->fetch();
    $numbOfSeller = $numbOfSeller["total_sellers"];
    
    // Only show souk if it has sellers with products
    if ($numbOfSeller > 0) {
        $logo = "soukLogo/" . $souk['category'].".png";
        
        // Get souk name based on language
        $souk_name = $lang === 'ar' ? $souk["name_ar"] : $souk["name_en"];
        
        ?>
        <div class="Souk_section" id='<?= $souk["name_en"] ?>'>
            <div class="Fendk_header">
                <div class="F_left_section">
                    <div class="div_Fendk_icon">
                        <img class="Fendk_icon" src="<?= $logo ?>">
                            
                    </div>
                    <h2 class="Section_title"><?= $souk_name ?></h2>
                        
                </div>
                <div class="F_right_section">
                    <h4 class="More_store">
                         <!-- <?= $lang === 'ar' ? 'المزيد' : 'More' ?>  -->
                          <?= $lang === 'ar' ? 'بائعين' : 'Sellers' ?>
                    <span style="color: black;"><?= $numbOfSeller ?></span>
                        
                    </h4>
                </div>
            </div>    
        <?php
        
        // Get only sellers who have products
        $sellers = $db->query("SELECT DISTINCT s.* 
                              FROM sellers s 
                              INNER JOIN products p ON s.id = p.seller_id 
                              WHERE s.souk_id = $souk_id");
        $sellers = $sellers->fetchAll();
       
        foreach($sellers as $seller){
            $seller_id = $seller["id"];
            $profile = $db->query("SELECT url FROM images
            WHERE seller_id = $seller_id AND image_type = 'profile'
            LIMIT 1;");
            $profile = $profile->fetch();
            $profileURL = $profile ? $profile["url"] :"sellersProfile/default.jpg";
            
            ?>
            <div class="Store_section">
                <div class="Traditional_maker_info">
                    <div class="Maker_picture">
                        <img class="Maker_photoPicture" src="<?=$profileURL?>">
                    </div>
                    <div class="Maker_name">
                        <h3><?= $seller["name"] ?></h3>
                    </div>
                    <!-- Verified -->
                    <div class="verfied_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="300.000000pt" height="300.000000pt" viewBox="0 0 300.000000 300.000000" preserveAspectRatio="xMidYMid meet">
                            
                            <g transform="translate(0.000000,300.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                
                                <path d="M920 2675 c-67 -113 -130 -210 -139 -214 -12 -7 -200 -50 -324 -75 -110 -23 -137 -31 -137 -46 0 -8 9 -107 20 -220 11 -113 20 -217 20 -231 0 -19 -26 -56 -97 -135 -53 -60 -99 -111 -102 -114 -3 -3 -32 -35 -65 -73 l-58 -67 58 -67 c33 -37 103 -117 157 -177 53 -61 100 -121 103 -134 4 -13 -3 -125 -15 -249 -13 -123 -21 -226 -19 -228 2 -2 106 -26 232 -55 127 -28 234 -53 238 -56 4 -3 53 -83 109 -177 56 -95 112 -189 125 -209 l23 -36 217 94 c119 52 225 94 235 94 10 0 111 -40 225 -90 114 -50 213 -90 219 -90 7 0 67 91 133 203 78 132 128 207 144 214 12 5 121 32 241 58 l219 49 -7 70 c-9 98 -13 146 -25 256 -17 167 -23 150 96 285 58 66 131 148 161 182 l55 63 -109 122 c-188 212 -213 245 -213 282 0 18 9 126 21 240 11 115 19 209 17 210 -2 2 -100 24 -218 50 -118 26 -226 53 -239 59 -17 8 -64 79 -142 212 -64 109 -122 201 -128 203 -5 2 -110 -40 -232 -92 l-221 -96 -187 81 c-102 45 -190 85 -196 89 -5 4 -24 11 -42 14 l-31 7 -122 -206z m1101 -646 c103 -52 144 -183 88 -277 -13 -22 -139 -176 -279 -342 -140 -167 -272 -324 -292 -349 -49 -61 -104 -92 -165 -92 -83 -1 -74 -7 -361 255 -149 135 -172 169 -172 245 0 21 6 54 14 73 21 49 96 115 147 128 74 20 115 1 240 -112 61 -54 114 -96 120 -93 5 3 103 122 218 264 218 268 255 307 299 317 15 3 32 7 37 9 17 6 68 -7 106 -26z"/>
                            </g>
                        </svg>
                    </div>
                    
                </div>
                <div class="Maker_product">
            
            <?php
            
            $products = $db->query("select * from products where seller_id= $seller_id ;");
            $products = $products->fetchAll();
            
            foreach($products as $product){
                $producr_id = $product["id"];
                $imgInfo = $db->query("SELECT img.url, img.alt_text
                                        FROM images img
                                        WHERE img.product_id = $producr_id
                                        LIMIT 1;");
                $imgInfo = $imgInfo->fetch();  
                $url = $imgInfo ? $imgInfo["url"]:"uploads/default.jpg" ;
                $alt = $imgInfo ? $imgInfo["alt_text"] : "alt not find"; 
                
                // Get product name and description based on language
                $product_name = $lang === 'ar' ? $product["name_ar"] : $product["name_en"];
                $product_description = $lang === 'ar' ? $product["description_ar"] : $product["description_en"];
                
                ?>
                <a href="seeProduit.php?id=<?php echo $producr_id ?>&lang=<?= $lang ?>">
            
                <article>
                    <div class="Product_main">
                        <div class="product_div_img">
                            <img class="product_img" src="<?=$url ?>" width="245" height="132" alt loading="lazy">
                            <div class="price_tooltiple">
                                <?= $lang === 'ar' ? 'السعر :' : 'Price :' ?>
                                    <!-- The price here -->
                                    <span class="Price">
                                        &dollar;<?= $product["price"] ?>
                                    </span>
                                    <span class="prev-span">&dollar;<?= $product["price"] ?></span>
                            </div>
                            <div class="Sold_tooltiple">
                                <img class="icon_sold" alt="Solde" src="sold-rubber-stamp-free-png.png">
                            </div>
                        </div>
                        <div class="product_info">
                            <div class="Product_Title">
                                <h2><?= $product_name ?></h2>
                            </div>
                            <div class="Responsive_price">
                                <?= $lang === 'ar' ? 'السعر:' : 'Price:' ?>
                                    <!-- The same price here -->
            
                                <span class="Price">&dollar; <?= $product["price"] ?> </span>
                                <span class="prev-span">&dollar; <?= $product["price"] ?></span>
            
                                
                            </div>
                            <div class="product-description">
                                <?= $product_description ?>
                            </div>
                        </div>
                        
                    </div>
                </article>
                </a>
            

                <?php 

            }
            ?>
            </div>


            </div>
            


            <?php

            
        }    
        ?>
            </div>

            <?php
    } // End of if ($numbOfSeller > 0)
}

?>
</div>

<script>
// Optional: Add smooth transitions when language changes
document.addEventListener('DOMContentLoaded', function() {
    const langButtons = document.querySelectorAll('.lang-btn');
    
    langButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add loading state
            document.body.style.opacity = '0.7';
            document.body.style.transition = 'opacity 0.3s ease';
        });
    });
});
</script>

</body>