<?php
// plan.php - Menu Plan Page
require_once 'inc/auth.php';
include_once 'inc/header.php';
include_once 'inc/navbar.php';
include_once 'inc/sidebar.php';
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Menu Plan</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Monthly Plan -->
                        <div class="col-md-4 mb-4">
                            <div class="card border-primary h-100">
                                <div class="card-header bg-primary text-white text-center">
                                    <h4>Monthly Plan</h4>
                                </div>
                                <div class="card-body text-center">
                                    <h2 class="card-title">₹499</h2>
                                    <p class="card-text">Access all features for 1 month.</p>
                                    <ul class="list-unstyled">
                                        <li>✔ Unlimited Patients</li>
                                        <li>✔ Unlimited Tests</li>
                                        <li>✔ Support Included</li>
                                    </ul>
                                    <button class="btn btn-primary">Choose Monthly</button>
                                </div>
                            </div>
                        </div>
                        <!-- Yearly Plan -->
                        <div class="col-md-4 mb-4">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success text-white text-center">
                                    <h4>Yearly Plan</h4>
                                </div>
                                <div class="card-body text-center">
                                    <h2 class="card-title">₹4999</h2>
                                    <p class="card-text">Access all features for 1 year.</p>
                                    <ul class="list-unstyled">
                                        <li>✔ Unlimited Patients</li>
                                        <li>✔ Unlimited Tests</li>
                                        <li>✔ Priority Support</li>
                                    </ul>
                                    <button class="btn btn-success">Choose Yearly</button>
                                </div>
                            </div>
                        </div>
                        <!-- Discount Plan -->
                        <div class="col-md-4 mb-4">
                            <div class="card border-warning h-100">
                                <div class="card-header bg-warning text-white text-center">
                                    <h4>Discount Offer</h4>
                                </div>
                                <div class="card-body text-center">
                                    <h2 class="card-title">20% OFF</h2>
                                    <p class="card-text">Get 20% discount on yearly plan for a limited time.</p>
                                    <ul class="list-unstyled">
                                        <li>✔ All Yearly Features</li>
                                        <li>✔ Limited Time Offer</li>
                                    </ul>
                                    <button class="btn btn-warning text-white">Get Discount</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
include_once 'inc/footer.php';
?>
