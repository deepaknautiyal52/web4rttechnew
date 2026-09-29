@extends('layouts.site', [
    'title' => 'About Us',
    'description' => 'Web4rtTech is a technology partner for businesses that want to move faster — we design, build, and scale digital products, cloud platforms, and AI-driven solutions.',
    'path' => '/about',
])

@section('content')
<main>
    <section class="page-hero">
      <div class="container page-hero-flex">
        <div class="page-hero-text">
          <h1>About Web4rtTech</h1>
          <p>A digital-first IT services and consulting company</p>
        </div>
        <div class="page-hero-illustration">
          @include('partials.hero-illustration', ['variant' => 'about'])
        </div>
      </div>
    </section>

    <section class="about-content">
      <div class="container">
        <div class="about-section">
          <h2>Who We Are</h2>
          <p>
            Web4rtTech is a technology partner for businesses that want to move faster. We design,
            build, and scale digital products, cloud platforms, and AI-driven solutions that
            help organizations navigate their next chapter of growth.
          </p>
        </div>

        <div class="about-grid">
          <div class="about-card">
            <h3>Client-First Delivery</h3>
            <p>Dedicated teams that work as an extension of your business, not just a vendor</p>
          </div>
          <div class="about-card">
            <h3>Innovation Focus</h3>
            <p>Continuously exploring emerging technologies to keep our clients ahead</p>
          </div>
          <div class="about-card">
            <h3>Proven Reliability</h3>
            <p>Trusted by growing businesses for delivering quality, on-time results</p>
          </div>
          <div class="about-card">
            <h3>Social Responsibility</h3>
            <p>Committed to sustainable practices and giving back to our community</p>
          </div>
        </div>

        <div class="about-section">
          <h2>Our Values</h2>
          <ul class="values-list">
            <li><strong>Integrity:</strong> We maintain the highest ethical standards in all our dealings</li>
            <li><strong>Excellence:</strong> We strive for excellence in everything we do</li>
            <li><strong>Inclusivity:</strong> We embrace diversity and foster an inclusive workplace</li>
            <li><strong>Sustainability:</strong> We are committed to sustainable and responsible business practices</li>
            <li><strong>Client Focus:</strong> We put our clients at the center of everything we do</li>
          </ul>
        </div>
      </div>
    </section>
</main>
@endsection
