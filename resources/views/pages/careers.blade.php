@extends('layouts.site', [
    'title' => 'Careers',
    'description' => "Join Web4rtTech's team and grow your career in web development, mobile apps, digital marketing, or AI & data science. View current openings and internship programs.",
    'path' => '/careers',
])

@php
    $jobOpenings = [
        ['title' => 'Senior Software Engineer', 'location' => 'Bangalore, India', 'experience' => '5-8 years'],
        ['title' => 'Cloud Architect', 'location' => 'Hyderabad, India', 'experience' => '7+ years'],
        ['title' => 'AI/ML Engineer', 'location' => 'Pune, India', 'experience' => '3-6 years'],
        ['title' => 'DevOps Engineer', 'location' => 'Chennai, India', 'experience' => '4-7 years'],
        ['title' => 'Business Analyst', 'location' => 'Mumbai, India', 'experience' => '2-5 years'],
        ['title' => 'Project Manager', 'location' => 'Delhi, India', 'experience' => '5-10 years'],
    ];
@endphp

@section('content')
<main>
    <section class="page-hero">
        <div class="container page-hero-flex">
            <div class="page-hero-text">
                <h1>Careers at Web4rtTech</h1>
                <p>Join our team and grow your career</p>
            </div>
            <div class="page-hero-illustration">
                @include('partials.hero-illustration', ['variant' => 'careers'])
            </div>
        </div>
    </section>

    <section class="careers-content">
        <div class="container">
            <div class="careers-intro">
                <h2>Why Work at Web4rtTech?</h2>
                <div class="reasons-grid">
                    <div class="reason-card">
                        <h3>🌍 Diverse Teams</h3>
                        <p>Collaborate with talented people across disciplines and backgrounds</p>
                    </div>
                    <div class="reason-card">
                        <h3>📚 Continuous Learning</h3>
                        <p>Access to training and development programs</p>
                    </div>
                    <div class="reason-card">
                        <h3>💰 Competitive Benefits</h3>
                        <p>Attractive salary packages and benefits</p>
                    </div>
                    <div class="reason-card">
                        <h3>🚀 Innovation</h3>
                        <p>Work on cutting-edge technologies</p>
                    </div>
                    <div class="reason-card">
                        <h3>🤝 Inclusive Culture</h3>
                        <p>Diversity and inclusion at the core</p>
                    </div>
                    <div class="reason-card">
                        <h3>📈 Career Growth</h3>
                        <p>Clear career progression pathways</p>
                    </div>
                </div>
            </div>

            <div class="job-openings">
                <h2>Current Job Openings</h2>
                <div class="jobs-list">
                    @foreach ($jobOpenings as $job)
                        <div class="job-card">
                            <div class="job-header">
                                <h3>{{ $job['title'] }}</h3>
                                <span class="job-experience">{{ $job['experience'] }}</span>
                            </div>
                            <p class="job-location">📍 {{ $job['location'] }}</p>
                            <button class="apply-btn">Apply Now</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <section class="internship-section">
                <h2>Internship Programs</h2>
                <p>
                    Web4rtTech offers exciting internship programs for students and freshers.
                    Be part of a growing IT company and kick-start your career.
                </p>
                <button class="learn-more-btn">Learn About Internships</button>
            </section>
        </div>
    </section>
</main>
@endsection
