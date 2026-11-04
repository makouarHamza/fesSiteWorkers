<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Heritage - Produits Traditionnels & Chaussures Authentiques</title>
    <style>
        /* * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        } */

        /* body {
            font-family: 'Georgia', serif;
            line-height: 1.6;
            color: #2c1810;
            background: linear-gradient(135deg, #f4f1e8 0%, #e8dcc0 100%);
        } */
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23D2B48C" width="1200" height="600"/><circle fill="%23CD853F" cx="200" cy="150" r="80"/><circle fill="%23DEB887" cx="800" cy="250" r="60"/><circle fill="%23F4A460" cx="400" cy="400" r="100"/><circle fill="%23D2B48C" cx="1000" cy="100" r="90"/></svg>');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-top: 80px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.7);
            font-family: 'Georgia', serif;
        }

        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            max-width: 600px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        }

        .cta-button {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #2c1810;
            padding: 1rem 2rem;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,215,0,0.4);
        }

        /* Categories Section */
        .categories {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #8B4513;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .category-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .category-image {
            height: 200px;
            background: linear-gradient(135deg, #CD853F 0%, #DEB887 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .category-content {
            padding: 1.5rem;
        }

        .category-content h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #8B4513;
        }

        .category-content p {
            color: #666;
            margin-bottom: 1rem;
        }

        .category-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #D2691E;
        }

        /* Featured Products */
        .featured-products {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            padding: 4rem 2rem;
            color: white;
        }

        .featured-products .section-title {
            color: white;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .product-card h4 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .product-card p {
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: bold;
            color: #ffd700;
        }

        /* About Section */
        .about {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .about-text h2 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: #8B4513;
        }

        .about-text p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            color: #666;
        }

        .about-image {
            background: linear-gradient(135deg, #CD853F 0%, #DEB887 100%);
            height: 400px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6rem;
            color: white;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.3);
        }

        /* Footer */
        footer {
            background: #2c1810;
            color: white;
            padding: 3rem 2rem 1rem;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            color: #ffd700;
        }

        .footer-section p, .footer-section a {
            color: #ccc;
            text-decoration: none;
            margin-bottom: 0.5rem;
            display: block;
        }

        .footer-section a:hover {
            color: #ffd700;
        }

        .footer-bottom {
            border-top: 1px solid #555;
            padding-top: 1rem;
            margin-top: 2rem;
        }

        /* Responsive */
       
    </style>
</head>
<body>
    <!-- Header -->
<?php include("header.php") ?>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Artisan Heritage</h1>
            <p>Découvrez l'authenticité de nos produits traditionnels et chaussures artisanales, créés avec passion selon des techniques ancestrales transmises de génération en génération.</p>
            <a href="#categories" class="cta-button">Découvrir nos créations</a>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories" id="categories">
        <h2 class="section-title">Nos Collections</h2>
        <div class="category-grid">
            <div class="category-card">
                <div class="category-image">👞</div>
                <div class="category-content">
                    <h3>Chaussures Traditionnelles</h3>
                    <p>Babouches, mocassins et sandales fabriquées à la main avec des matériaux nobles. Chaque paire raconte une histoire de savoir-faire ancestral.</p>
                    <div class="category-price">À partir de 45€</div>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image">🏺</div>
                <div class="category-content">
                    <h3>Poterie & Céramique</h3>
                    <p>Pièces uniques en terre cuite, tajines authentiques et vaisselle traditionnelle. L'art du potier dans toute sa splendeur.</p>
                    <div class="category-price">À partir de 25€</div>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image">🧶</div>
                <div class="category-content">
                    <h3>Textiles Artisanaux</h3>
                    <p>Tapis berbères, coussins brodés et étoffes traditionnelles. Chaque fil raconte l'histoire de nos artisans.</p>
                    <div class="category-price">À partir de 35€</div>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image">✨</div>
                <div class="category-content">
                    <h3>Bijoux Traditionnels</h3>
                    <p>Parures en argent, colliers berbères et bracelets ornés de pierres précieuses. L'élégance à l'état pur.</p>
                    <div class="category-price">À partir de 60€</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <h2 class="section-title">Produits Vedettes</h2>
        <div class="product-grid">
            <div class="product-card">
                <div class="product-icon">🥿</div>
                <h4>Babouches Royales</h4>
                <p>Cuir véritable, broderies dorées</p>
                <div class="product-price">89€</div>
            </div>
            
            <div class="product-card">
                <div class="product-icon">🍯</div>
                <h4>Tajine Authentique</h4>
                <p>Terre cuite traditionnelle</p>
                <div class="product-price">65€</div>
            </div>
            
            <div class="product-card">
                <div class="product-icon">🌟</div>
                <h4>Tapis Berbère</h4>
                <p>Laine pure, motifs ancestraux</p>
                <div class="product-price">155€</div>
            </div>
            
            <div class="product-card">
                <div class="product-icon">💎</div>
                <h4>Collier Amazigh</h4>
                <p>Argent massif, corail naturel</p>
                <div class="product-price">120€</div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="about-text">
            <h2>Notre Histoire</h2>
            <p>Depuis trois générations, notre famille perpétue les traditions artisanales du Maroc. Chaque produit de notre boutique est le fruit d'un savoir-faire ancestral, transmis de maître à apprenti.</p>
            <p>Nous travaillons directement avec les artisans locaux pour vous offrir des pièces authentiques, respectueuses de l'environnement et du patrimoine culturel.</p>
            <p>Découvrez l'âme de l'artisanat traditionnel à travers nos créations uniques, où chaque détail raconte une histoire millénaire.</p>
        </div>
        <div class="about-image">
            🏛️
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Contact</h3>
                <p>📍 123 Rue des Artisans, Médina</p>
                <p>📞 +212 5 24 44 55 66</p>
                <p>✉️ contact@artisan-heritage.com</p>
            </div>
            
            <div class="footer-section">
                <h3>Informations</h3>
                <a href="#">Livraisons</a>
                <a href="#">Retours</a>
                <a href="#">Garantie</a>
                <a href="#">FAQ</a>
            </div>
            
            <div class="footer-section">
                <h3>Suivez-nous</h3>
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">Twitter</a>
                <a href="#">YouTube</a>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 Artisan Heritage. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(139, 69, 19, 0.95)';
                header.style.backdropFilter = 'blur(10px)';
            } else {
                header.style.background = 'linear-gradient(135deg, #8B4513 0%, #A0522D 100%)';
                header.style.backdropFilter = 'none';
            }
        });

        // Animate cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe category cards
        document.querySelectorAll('.category-card, .product-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>