
</main>
<!-- ======================================================
     FOOTER  — 3D Premium
     ====================================================== -->
<footer class="tm-footer mt-auto">
    <div class="container">
        <div class="row gy-5">

            <!-- Brand Column -->
            <div class="col-lg-4">
                <div class="tm-brand mb-3" style="color:#ffffff; font-size:1.5rem; font-weight:800; display:inline-flex; align-items:center; gap:0.5rem; text-shadow:0 0 18px rgba(14,165,233,0.5);">
                    <i class="bi bi-compass-fill" style="color:#38bdf8; filter:drop-shadow(0 0 8px rgba(14,165,233,0.8));"></i>TravelMate
                </div>
                <p class="mb-4" style="color:rgba(190,215,245,0.95); font-size:0.9rem; line-height:1.8; max-width:290px;">
                    Collaborative travel planning platform. Plan together, share responsibilities, reduce costs, and create unforgettable memories.
                </p>
                <!-- Feature Badges -->
                <div class="d-flex flex-wrap gap-2">
                    <span class="tm-footer-feature-badge"><i class="bi bi-shield-check"></i> Safe &amp; Trusted</span>
                    <span class="tm-footer-feature-badge"><i class="bi bi-people"></i> Community</span>
                    <span class="tm-footer-feature-badge"><i class="bi bi-wallet2"></i> Cost Sharing</span>
                    <span class="tm-footer-feature-badge"><i class="bi bi-images"></i> Memories</span>
                </div>
            </div>

            <!-- Platform Column -->
            <div class="col-lg-2 col-6">
                <h6>Platform</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/trips" class="tm-footer-link">Explore Trips</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/auth/register" class="tm-footer-link">Join Now</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/trips/create" class="tm-footer-link">Create Trip</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/auth/login" class="tm-footer-link">Sign In</a></li>
                </ul>
            </div>

            <!-- Trip Types Column -->
            <div class="col-lg-2 col-6">
                <h6>Trip Types</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/trips?trip_type=trekking" class="tm-footer-link">Trekking</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/trips?trip_type=camping" class="tm-footer-link">Camping</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/trips?trip_type=road_trip" class="tm-footer-link">Road Trips</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/trips?trip_type=backpacking" class="tm-footer-link">Backpacking</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/trips?trip_type=beach" class="tm-footer-link">Beach &amp; Leisure</a></li>
                </ul>
            </div>

            <!-- Stats / Mini Highlights Column -->
            <div class="col-lg-4">
                <h6>Travel Together</h6>
                <p style="color: rgba(148,180,220,0.7); font-size: 0.88rem; line-height: 1.8;">
                    From mountain peaks to ocean shores — TravelMate helps you coordinate every detail, split every cost, and capture every memory.
                </p>
                <div class="d-flex gap-4 mt-3">
                    <div>
                        <div style="font-size:1.5rem; font-weight:800; color:#ffffff; text-shadow:0 0 10px rgba(14,165,233,0.4);">500+</div>
                        <div style="font-size:0.75rem; color:rgba(160,200,240,0.8); text-transform:uppercase; letter-spacing:0.08em;">Trips Created</div>
                    </div>
                    <div>
                        <div style="font-size:1.5rem; font-weight:800; color:#ffffff; text-shadow:0 0 10px rgba(14,165,233,0.4);">1k+</div>
                        <div style="font-size:0.75rem; color:rgba(160,200,240,0.8); text-transform:uppercase; letter-spacing:0.08em;">Travellers</div>
                    </div>
                    <div>
                        <div style="font-size:1.5rem; font-weight:800; color:#38bdf8; text-shadow:0 0 10px rgba(14,165,233,0.5);">50+</div>
                        <div style="font-size:0.75rem; color:rgba(160,200,240,0.8); text-transform:uppercase; letter-spacing:0.08em;">Destinations</div>
                    </div>
                </div>
            </div>

        </div>

        <hr class="tm-footer-divider">
    </div>

    <!-- Bottom Bar -->
    <div class="tm-footer-bottom">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>&copy; <?= date('Y') ?> TravelMate &mdash; All rights reserved.</div>
            <div class="tm-footer-tagline">Build simple. Build secure. Build maintainable.</div>
        </div>
    </div>
</footer>

<!-- ======================================================
     SCRIPTS
     ====================================================== -->
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom App JS -->
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>

<?php if (isset($extraScripts)): ?>
    <?= $extraScripts ?>
<?php endif; ?>

</body>
</html>
