@extends('layouts.site', [
    'title' => 'Cookie Policy',
    'description' => 'What cookies and local storage Web4rtTech actually uses on this website.',
    'path' => '/cookies',
])

@section('content')
<main>
    <section class="page-hero">
      <div class="container page-hero-flex">
        <div class="page-hero-text">
          <h1>Cookie Policy</h1>
          <p>What cookies and local storage this Site actually uses</p>
        </div>
        <div class="page-hero-illustration">
          @include('partials.hero-illustration', ['variant' => 'services'])
        </div>
      </div>
    </section>

    <section class="legal-content">
      <div class="container legal-wrap">
        <span class="legal-updated">Last updated: September 2026</span>

        <p class="legal-intro">
          This Cookie Policy explains what cookies and similar technologies (like browser local
          storage) are used on the Web4rtTech website, and why.
        </p>

        <div class="legal-disclaimer">
          <strong>Note:</strong> This is a general template written to accurately reflect this
          Site's current, minimal use of cookies and local storage. It has not been drafted or
          reviewed by a lawyer — please have it reviewed by a qualified professional before
          relying on it for a live business.
        </div>

        <div class="legal-section">
          <h2>1. What Are Cookies?</h2>
          <p>
            Cookies are small text files placed on your device by a website you visit. "Local
            storage" is a similar browser feature used to save small amounts of data. Both are
            commonly used to remember information between visits or pages.
          </p>
        </div>

        <div class="legal-section">
          <h2>2. What This Site Actually Uses</h2>
          <p>Being upfront about our current setup:</p>
          <ul>
            <li>
              <strong>Public website (the pages you're browsing now):</strong> we do not use
              tracking, advertising, or analytics cookies of any kind.
            </li>
            <li>
              <strong>Admin panel only:</strong> our internal team's login uses your browser's
              local storage to stay signed in during an admin session. This only applies to
              authenticated staff logging into the admin panel — it does not run for regular
              visitors browsing the public site, and no data from it is shared with third
              parties.
            </li>
          </ul>
        </div>

        <div class="legal-section">
          <h2>3. Cookies We May Add in the Future</h2>
          <p>
            As this site grows, we may introduce analytics (e.g. Google Analytics) or marketing
            tools (e.g. Google Ads, Meta Ads conversion tracking) to better understand how
            visitors use the Site — the same kinds of tools we help our own clients set up. If we
            do, we will update this policy first to describe exactly what's added, what it
            tracks, and how you can opt out.
          </p>
        </div>

        <div class="legal-section">
          <h2>4. Managing Cookies in Your Browser</h2>
          <p>
            Since this Site currently sets no tracking cookies, there's nothing to opt out of
            today. If you'd like to control cookies generally across the sites you visit, every
            major browser lets you block or delete cookies through its settings menu — search
            your browser's help documentation for "manage cookies" for exact steps.
          </p>
        </div>

        <div class="legal-section">
          <h2>5. Changes to This Policy</h2>
          <p>
            We'll update this page whenever what we actually use changes, with a revised "Last
            updated" date at the top.
          </p>
        </div>

        <div class="legal-section">
          <h2>6. Contact Us</h2>
          <div class="legal-contact-box">
            <p>Questions about cookies or tracking on this Site? Reach out:</p>
            <p>Email: <a href="mailto:info@web4rttech.com">info@web4rttech.com</a></p>
            <p>Phone: <a href="tel:+918979528858">+91 89795 28858</a></p>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
