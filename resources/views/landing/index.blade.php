<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Professional Property and Rent Management System with M-Pesa integration, automated invoicing, and arrears monitoring.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>RentPro - Property & Rent Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #0f172a;
            --accent: #10b981;
            --background: #ffffff;
            --surface: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            background-color: var(--background);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            z-index: 1000;
            padding: 1rem 0;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }

        /* Hero Section */
        .hero {
            padding: 8rem 0 5rem;
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        }

        .hero-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Features Section */
        .features {
            padding: 5rem 0;
            background: var(--surface);
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 1rem;
        }

        .section-header p {
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 3rem;
            height: 3rem;
            background: #eff6ff;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        /* Benefits Section */
        .benefits {
            padding: 5rem 0;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .benefit-item {
            text-align: center;
            padding: 1.5rem;
        }

        .benefit-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .benefit-label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Security Section */
        .security {
            padding: 5rem 0;
            background: var(--secondary);
            color: white;
        }

        .security h2 {
            color: white;
        }

        .security p {
            color: #94a3b8;
        }

        .security-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .security-item {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .security-icon {
            width: 2.5rem;
            height: 2.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Contact Section */
        .contact {
            padding: 5rem 0;
            background: var(--surface);
        }

        .contact-form {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        /* Footer */
        .footer {
            background: var(--secondary);
            color: #94a3b8;
            padding: 3rem 0;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h4 {
            color: white;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .nav-links {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .features-grid,
            .benefits-grid,
            .security-grid {
                grid-template-columns: 1fr;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .hero-buttons .btn {
                width: 100%;
                max-width: 300px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container navbar-content">
            <a href="/" class="logo">RentPro</a>
            
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#benefits">Benefits</a>
                <a href="#security">Security</a>
                <a href="#contact">Contact</a>
                <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                <a href="#contact" class="btn btn-primary">Get Started</a>
            </div>

            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-content">
            <h1>Complete Property & Rent Management Solution</h1>
            <p>Streamline your property management with automated rent collection, M-Pesa integration, real-time arrears monitoring, and comprehensive financial reporting.</p>
            <div class="hero-buttons">
                <a href="{{ route('login') }}" class="btn btn-primary">Access Portal</a>
                <a href="#features" class="btn btn-outline">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="section-header">
                <h2>Powerful Features for Modern Landlords</h2>
                <p>Everything you need to manage properties, tenants, and finances efficiently</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🏢</div>
                    <h3>Property Management</h3>
                    <p>Organize properties, blocks, and units in a hierarchical structure. Track occupancy status and manage unit configurations effortlessly.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">💳</div>
                    <h3>M-Pesa Integration</h3>
                    <p>Seamless M-Pesa Daraja integration for automated payment collection. Each tenant gets a unique payment reference for easy reconciliation.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Automated SMS Reminders</h3>
                    <p>Send automated rent reminders, payment confirmations, and arrears notifications via SMS. Configurable templates and scheduling.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Arrears Monitoring</h3>
                    <p>Real-time arrears calculation with configurable risk thresholds. Identify low, medium, high, and critical risk tenants automatically.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Tenant Management</h3>
                    <p>Complete tenant profiles with tenancy history, payment records, and deposit tracking. Manage allocations and vacating workflows.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📈</div>
                    <h3>Financial Reporting</h3>
                    <p>Comprehensive revenue reports, collection analytics, occupancy rates, and arrears aging. Export to CSV and Excel formats.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📄</div>
                    <h3>Automated Invoicing</h3>
                    <p>Monthly rent invoices generated automatically on configurable schedules. Include previous balances, credits, and other charges.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Security & Audit</h3>
                    <p>Role-based access control, complete audit trails, and multi-landlord data isolation. Every financial transaction is traceable.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Deposit Management</h3>
                    <p>Separate deposit tracking from rent. Manage deposits, refunds, deductions, and reconcile upon tenant vacating.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="benefits">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose RentPro?</h2>
                <p>Built for financial accuracy and operational efficiency</p>
            </div>

            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-number">100%</div>
                    <div class="benefit-label">Payment Reconciliation</div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-number">24/7</div>
                    <div class="benefit-label">Automated Processing</div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-number">0</div>
                    <div class="benefit-label">Duplicate Payments</div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-number">100%</div>
                    <div class="benefit-label">Audit Trail Coverage</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Section -->
    <section id="security" class="security">
        <div class="container">
            <div class="section-header">
                <h2>Enterprise-Grade Security</h2>
                <p>Your financial data protected with industry-standard security measures</p>
            </div>

            <div class="security-grid">
                <div class="security-item">
                    <div class="security-icon">🛡️</div>
                    <div>
                        <h3>Data Isolation</h3>
                        <p>Multi-landlord architecture ensures complete data separation. Landlords can only access their own properties and records.</p>
                    </div>
                </div>

                <div class="security-item">
                    <div class="security-icon">🔐</div>
                    <div>
                        <h3>Role-Based Access</h3>
                        <p>Granular permissions for administrators, landlords, property managers, accountants, and staff members.</p>
                    </div>
                </div>

                <div class="security-item">
                    <div class="security-icon">📝</div>
                    <div>
                        <h3>Complete Audit Trail</h3>
                        <p>Every sensitive action logged with actor, timestamp, IP address, and before/after values for full traceability.</p>
                    </div>
                </div>

                <div class="security-item">
                    <div class="security-icon">✅</div>
                    <div>
                        <h3>Financial Integrity</h3>
                        <p>Immutable transaction ledger, idempotent payment processing, and prevention of duplicate or fraudulent transactions.</p>
                    </div>
                </div>

                <div class="security-item">
                    <div class="security-icon">🔒</div>
                    <div>
                        <h3>Secure Payments</h3>
                        <p>M-Pesa callbacks verified server-side. Never trust browser-supplied payment information. Full transaction logging.</p>
                    </div>
                </div>

                <div class="security-item">
                    <div class="security-icon">🌐</div>
                    <div>
                        <h3>HTTPS & Encryption</h3>
                        <p>All data transmitted over secure connections. Sensitive credentials stored in environment variables, never in code or database.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="section-header">
                <h2>Get Started Today</h2>
                <p>Ready to transform your property management? Contact us for a demo or registration enquiry.</p>
            </div>

            <div class="contact-form">
                <form action="#" method="POST" id="contactForm">
                    @csrf
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required placeholder="Enter your full name">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email address">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number">
                    </div>

                    <div class="form-group">
                        <label for="properties">Number of Properties</label>
                        <select id="properties" name="properties">
                            <option value="">Select an option</option>
                            <option value="1">1 Property</option>
                            <option value="2-5">2-5 Properties</option>
                            <option value="6-10">6-10 Properties</option>
                            <option value="10+">10+ Properties</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Tell us about your requirements..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Enquiry</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>RentPro</h4>
                    <p>Professional property and rent management system built for accuracy, security, and scalability.</p>
                </div>

                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#benefits">Benefits</a></li>
                        <li><a href="#security">Security</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Features</h4>
                    <ul>
                        <li><a href="#features">Property Management</a></li>
                        <li><a href="#features">M-Pesa Integration</a></li>
                        <li><a href="#features">Automated Invoicing</a></li>
                        <li><a href="#features">Financial Reports</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Contact</h4>
                    <ul>
                        <li><a href="mailto:info@rentpro.com">info@rentpro.com</a></li>
                        <li><a href="tel:+254700000000">+254 700 000 000</a></li>
                        <li>Nairobi, Kenya</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} RentPro. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
        }

        // Smooth scrolling for anchor links
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

        // Form submission handler (placeholder)
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for your enquiry. We will contact you shortly.');
            this.reset();
        });
    </script>
</body>
</html>
