
<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'About Us';
include __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">About FixServices</h1>
        <p class="lead text-muted">
            Connecting you with trusted local service providers.
        </p>
    </div>

    <div class="row align-items-center g-4 mb-5">
        <div class="col-lg-6">
            <h2 class="h3 fw-bold">Who We Are</h2>
            <p>
                FixServices is a local home service discovery and booking
                platform designed to help people find service providers
                in their own area. Whether you need an electrician,
                plumber, cleaner, painter, or appliance repair professional,
                FixServices helps you explore available services in your locality.
            </p>
            <p>
                Our goal is to make finding and booking home services
                easier by bringing customers and local professionals
                together through one simple website.
            </p>
        </div>

        <div class="col-lg-6">
            <div class="card panel p-4">
                <h3 class="h5 fw-bold">Our Purpose</h3>
                <p class="mb-0">
                    To simplify local service discovery by allowing customers
                    to search by district, taluka, town or village, and
                    service category without needing a PIN code.
                </p>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <h2 class="h3 fw-bold text-center mb-4">How FixServices Works</h2>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card panel h-100 p-4">
                    <h3 class="h5 fw-bold">1. Choose Your Location</h3>
                    <p class="mb-0">
                        Select your district, taluka, and town or village
                        to discover services available in your area.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card panel h-100 p-4">
                    <h3 class="h5 fw-bold">2. Explore Services</h3>
                    <p class="mb-0">
                        Choose a service category and compare available
                        providers, descriptions, prices, and ratings
                        where provided.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card panel h-100 p-4">
                    <h3 class="h5 fw-bold">3. Book a Service</h3>
                    <p class="mb-0">
                        Select a suitable service, submit your booking
                        request, and follow its status through the website.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card panel h-100 p-4">
                <h2 class="h4 fw-bold">For Customers</h2>
                <ul class="mb-0">
                    <li>Discover services in your local area.</li>
                    <li>Browse different service categories.</li>
                    <li>Review provider information and prices.</li>
                    <li>Submit and track booking requests.</li>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card panel h-100 p-4">
                <h2 class="h4 fw-bold">For Service Providers</h2>
                <ul class="mb-0">
                    <li>Create a service provider account.</li>
                    <li>Add service descriptions and prices.</li>
                    <li>Reach customers searching in their area.</li>
                    <li>Manage booking requests using available features.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="text-center py-4">
        <h2 class="h3 fw-bold">Our Vision</h2>
        <p class="text-muted mx-auto" style="max-width: 700px;">
            We aim to make local home services easier to discover and
            more accessible, while helping local professionals connect
            with customers who need their skills.
        </p>

        <a href="services.php" class="btn btn-primary btn-lg mt-3">
            Explore Services
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
```