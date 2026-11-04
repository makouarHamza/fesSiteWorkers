<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Optimized</title>
    <style>
        /* * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        } */

        /* body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8fafc;
        } */

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 600px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Main Content */
        .section {
            padding: 4rem 0;
        }

        .section:nth-child(even) {
            background: white;
        }

        .section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            color: #667eea;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card h3 {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .card p {
            color: #666;
            line-height: 1.7;
        }

        /* Team Section */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .team-member {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .team-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #667eea;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .team-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .team-role {
            color: #666;
            font-size: 0.9rem;
        }

        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            display: block;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 0.5rem;
        }

        /* CTA Section */
        .cta-section {
            background: #667eea;
            color: white;
            text-align: center;
            padding: 4rem 0;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2rem;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: background 0.2s ease;
            border: 2px solid rgba(255,255,255,0.3);
            margin-top: 1rem;
        }

        .cta-button:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .section h2 {
                font-size: 2rem;
            }
            
            .hero-card {
                padding: 1.5rem;
            }
            
            .grid {
                grid-template-columns: 1fr;
            }
        }

        /* Simple fade-in for content */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <?php include("header.php") ?>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>About Our Story</h1>
            <p>Crafting digital experiences that inspire and innovate</p>
            <div class="hero-card">
                <p>We are a passionate team of creators, designers, and developers dedicated to building exceptional digital experiences. Our journey began with a simple belief: technology should be beautiful, functional, and accessible to everyone.</p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <!-- Mission Section -->
        <section class="section">
            <div class="container">
                <h2>Our Mission</h2>
                <div class="grid">
                    <div class="card fade-in">
                        <h3>Innovation First</h3>
                        <p>We push the boundaries of what's possible, constantly exploring new technologies and methodologies to deliver cutting-edge solutions that exceed expectations.</p>
                    </div>
                    <div class="card fade-in">
                        <h3>User-Centered Design</h3>
                        <p>Every decision we make is guided by the user experience. We create intuitive, accessible, and delightful interfaces that people love to use.</p>
                    </div>
                    <div class="card fade-in">
                        <h3>Quality & Excellence</h3>
                        <p>We maintain the highest standards in everything we do, from code quality to design aesthetics, ensuring every project is a masterpiece.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="section">
            <div class="container">
                <h2>Meet Our Team</h2>
                <div class="team-grid">
                    <div class="team-member fade-in">
                        <div class="team-avatar">JS</div>
                        <div class="team-name">Jane Smith</div>
                        <div class="team-role">Creative Director</div>
                    </div>
                    <div class="team-member fade-in">
                        <div class="team-avatar">MD</div>
                        <div class="team-name">Mike Davis</div>
                        <div class="team-role">Lead Developer</div>
                    </div>
                    <div class="team-member fade-in">
                        <div class="team-avatar">AL</div>
                        <div class="team-name">Anna Lee</div>
                        <div class="team-role">UX Designer</div>
                    </div>
                    <div class="team-member fade-in">
                        <div class="team-avatar">RT</div>
                        <div class="team-name">Robert Taylor</div>
                        <div class="team-role">Project Manager</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="section">
            <div class="container">
                <h2>Our Impact</h2>
                <div class="stats-grid">
                    <div class="stat-item fade-in">
                        <span class="stat-number">500+</span>
                        <div class="stat-label">Projects Completed</div>
                    </div>
                    <div class="stat-item fade-in">
                        <span class="stat-number">150+</span>
                        <div class="stat-label">Happy Clients</div>
                    </div>
                    <div class="stat-item fade-in">
                        <span class="stat-number">50+</span>
                        <div class="stat-label">Awards Won</div>
                    </div>
                    <div class="stat-item fade-in">
                        <span class="stat-number">8</span>
                        <div class="stat-label">Years Experience</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <h2>Ready to Work Together?</h2>
                <p style="font-size: 1.2rem; margin-bottom: 1rem; opacity: 0.9;">Let's create something amazing together. Get in touch and let's discuss your next project.</p>
                <a href="contact.php" class="cta-button">Get Started Today</a>
            </div>
        </section>
    </main>

    <script>
        // Lightweight scroll reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Simple CTA click handler
        document.querySelector('.cta-button').addEventListener('click', (e) => {
            e.preventDefault();
            alert('Contact form would open here!');
        });
    </script>
</body>
</html>