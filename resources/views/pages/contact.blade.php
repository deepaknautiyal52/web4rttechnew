@extends('layouts.site', [
    'title' => 'Contact Us / Get a Quote',
    'description' => 'Get in touch with Web4rtTech for a free project quote. We reply within one business day and work with clients across India and internationally.',
    'path' => '/contact',
])

@php
    use App\Support\Site;
    $budgetRanges = ['Under $1,000', '$1,000 – $5,000', '$5,000 – $15,000', '$15,000 – $50,000', '$50,000+', 'Not sure yet'];
    $timelines = ['ASAP', '1–3 months', '3–6 months', '6+ months', 'Just exploring'];
    $service = old('service_interested', $preselectedService);
@endphp

@section('content')
<main>
    <section class="page-hero">
        <div class="container page-hero-flex">
            <div class="page-hero-text">
                <h1>Get a Quote</h1>
                <p>Tell us about your project — we typically reply within one business day</p>
            </div>
            <div class="page-hero-illustration">
                @include('partials.hero-illustration', ['variant' => 'contact'])
            </div>
        </div>
    </section>

    <section class="contact-content">
        <div class="container">
            <div class="contact-wrapper">
                <div class="contact-form-section">
                    <h2>Tell Us About Your Project</h2>

                    @if (session('contact_success'))
                        <div class="form-alert form-alert-success">
                            Thank you for reaching out! We will get back to you soon.
                        </div>
                    @endif
                    @if (session('contact_error'))
                        <div class="form-alert form-alert-error">{{ session('contact_error') }}</div>
                    @elseif ($errors->any())
                        <div class="form-alert form-alert-error">Please check the highlighted fields and try again.</div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" class="contact-form" novalidate data-submitting-label="Sending...">
                        @csrf
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Your full name" />
                            @error('name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="your@email.com" />
                                @error('email')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company">Company Name</label>
                            <input type="text" id="company" name="company" value="{{ old('company') }}" placeholder="Your company" />
                        </div>

                        <div class="form-group">
                            <label for="service_interested">Service You're Interested In</label>
                            <select id="service_interested" name="service_interested">
                                <option value="">Select a service...</option>
                                @foreach (Site::categories() as $category)
                                    <optgroup label="{{ $category['title'] }}">
                                        @foreach (Site::servicesIn($category['id']) as $s)
                                            <option value="{{ $s['id'] }}" @selected($service === $s['id'])>{{ $s['title'] }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                <option value="other" @selected($service === 'other')>Something else</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="budget_range">Budget Range</label>
                                <select id="budget_range" name="budget_range">
                                    <option value="">Select a range...</option>
                                    @foreach ($budgetRanges as $range)
                                        <option value="{{ $range }}" @selected(old('budget_range') === $range)>{{ $range }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="timeline">Timeline</label>
                                <select id="timeline" name="timeline">
                                    <option value="">Select a timeline...</option>
                                    @foreach ($timelines as $t)
                                        <option value="{{ $t }}" @selected(old('timeline') === $t)>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="What is this about?" />
                            @error('subject')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" required placeholder="Tell us a bit more about what you're looking to build or achieve..." rows="6">{{ old('message') }}</textarea>
                            @error('message')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="submit-btn">Send Message</button>
                    </form>
                </div>

                <div class="contact-info-section">
                    <h2>Contact Information</h2>

                    <div class="info-card">
                        <h3>Headquarters</h3>
                        <p>
                            Web4rtTech<br/>
                            Near Monal Farm, Dehradun, Uttarakhand, India<br/>
                            248001
                        </p>
                    </div>

                    <div class="info-card">
                        <h3>Regional Offices</h3>
                        <p>
                            We work with clients across India and internationally, with support available remotely.
                        </p>
                    </div>

                    <div class="info-card">
                        <h3>Phone</h3>
                        <p>
                            <a href="tel:+918979528858">+91 7417120232</a>,
                            <a href="tel:+917017857510">+91 70178 57510</a>
                        </p>
                    </div>

                    <div class="info-card">
                        <h3>Email</h3>
                        <p>
                            <a href="mailto:info@web4rttech.com">info@web4rttech.com</a>
                        </p>
                    </div>

                    <div class="info-card">
                        <h3>Business Hours</h3>
                        <p>
                            Monday - Friday: 9:00 AM - 6:00 PM IST<br/>
                            Saturday &amp; Sunday: Closed
                        </p>
                    </div>

                    <div class="social-section">
                        <h3>Follow Us</h3>
                        <div class="social-icons">
                            <a href="#twitter" class="social-icon-link" aria-label="X (Twitter)">𝕏</a>
                            <a href="#facebook" class="social-icon-link" aria-label="Facebook">f</a>
                            <a href="#linkedin" class="social-icon-link" aria-label="LinkedIn">in</a>
                            <a href="#youtube" class="social-icon-link" aria-label="YouTube">▶</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
