
    <style>
    

     

        header {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            padding: 0.6rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #f4f1e8;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: #f4f1e8;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #ffd700;
        }
         /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .about {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .category-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

<header>
        <nav>
            <div class="logo">🏺 Artisan Heritage</div>
            <ul class="nav-links">
                <li><a href="home.php">Accueil</a></li>
                <li><a href="store.php">Produits</a></li>
                <li><a href="about.php">À Propos</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>