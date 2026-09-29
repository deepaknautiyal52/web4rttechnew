<div class="footer-cta">
    <div class="container footer-cta-inner">
        <div>
            <h3>Have a project in mind?</h3>
            <p>Let's talk about how Web4rtTech can help you build, grow, and scale.</p>
        </div>
        <a href="{{ url('/contact') }}" class="footer-cta-btn">Get a Quote →</a>
    </div>
</div>

<footer class="footer">
    <div class="footer-content container">
        <div class="footer-section footer-brand">
            <a href="{{ url('/') }}" class="footer-logo">
                <span class="footer-logo-mark">4</span>
                <span class="footer-logo-copy">
                    <span class="footer-logo-text">Web<span>4</span>rtTech</span>
                    <span class="footer-logo-tagline">Build · Grow · Automate</span>
                </span>
            </a>
            <p class="footer-tagline">Engineering digital experiences that move your business forward.</p>
        </div>

        <div class="footer-section">
            <h3>Company</h3>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/about') }}">About Us</a></li>
                <li><a href="{{ url('/careers') }}">Careers</a></li>
                <li><a href="{{ url('/news') }}">Newsroom</a></li>
                <li><a href="{{ url('/contact') }}">Contact</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Services</h3>
            <ul>
                <li><a href="{{ url('/services/web-development') }}">Web Development</a></li>
                <li><a href="{{ url('/services/mobile-app-development') }}">Mobile App Development</a></li>
                <li><a href="{{ url('/services/seo') }}">SEO</a></li>
                <li><a href="{{ url('/services/ppc-google-ads') }}">PPC &amp; Google Ads</a></li>
                <li><a href="{{ url('/services/ai-solutions') }}">AI Solutions</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Our Ventures</h3>
            <ul>
                @foreach (\App\Support\Site::ventures() as $v)
                    <li>
                        @if ($v['url'])
                            <a href="{{ $v['url'] }}" target="_blank" rel="noopener noreferrer">{{ $v['name'] }} ↗</a>
                        @else
                            <a href="{{ url('/ventures') }}#{{ $v['id'] }}">{{ $v['name'] }}</a>
                        @endif
                    </li>
                @endforeach
                <li><a href="{{ url('/ventures') }}">All Ventures</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Resources</h3>
            <ul>
                <li><a href="{{ url('/services') }}">All Services</a></li>
                <li><a href="{{ url('/news') }}">Insights &amp; News</a></li>
                <li><a href="{{ url('/careers') }}">Open Roles</a></li>
                <li><a href="#faq">FAQs</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Connect With Us</h3>
            <div class="social-links">
                <a href="#twitter" class="social-icon">𝕏</a>
                <a href="#facebook" class="social-icon">f</a>
                <a href="#linkedin" class="social-icon">in</a>
                <a href="#youtube" class="social-icon">▶</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <p>&copy; {{ date('Y') }} Web4rtTech. All rights reserved.</p>
            <div class="footer-links">
                <a href="{{ url('/terms') }}">Terms of Use</a>
                <a href="{{ url('/privacy') }}">Privacy Statement</a>
                <a href="{{ url('/cookies') }}">Cookie Policy</a>
                <a href="{{ url('/sitemap') }}">Site Map</a>
            </div>
        </div>
    </div>
</footer>
