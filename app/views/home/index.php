<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<!-- ======================================================
     HERO SECTION — 3D Immersive
     ====================================================== -->
<section class="tm-hero tm-hero-v2">
    <!-- Animated particle canvas -->
    <canvas id="tm-particles" class="tm-particles-canvas"></canvas>
    <div class="tm-hero-glow"></div>
    <div class="tm-hero-glow-2"></div>

    <div class="container" style="position:relative;z-index:2;">
        <div class="row align-items-center g-5">

            <!-- LEFT: Copy -->
            <div class="col-lg-6 tm-hero-content"> 
                <div class="tm-hero-pill tm-animate-fade-in">
                    <span class="tm-hero-pill-dot"></span>
                    <i class="bi bi-stars me-1"></i> Collaborative Travel Planning
                </div>
                <h1 class="tm-hero-title tm-animate-fade-in delay-1" style="color:#fff;">
                    Travel Together.<br>
                    <span style="color:#38bdf8;">Share the Journey.</span>
                </h1>
                <p class="tm-hero-subtitle tm-animate-fade-in delay-2">
                    TravelMate brings travelers together. Create trips, find companions,
                    assign responsibilities, track expenses, and preserve memories — all in one place.
                </p>
                <div class="d-flex flex-wrap gap-3 tm-animate-fade-in delay-3">
                    <?php if (Security::isLoggedIn()): ?>
                        <a href="<?= BASE_URL ?>/trips/create" class="tm-btn-hero-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create a Trip
                        </a>
                        <a href="<?= BASE_URL ?>/trips" class="tm-btn-hero-outline">
                            <i class="bi bi-compass me-2"></i>Explore Trips
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/auth/register" class="tm-btn-hero-primary">
                            <i class="bi bi-rocket-takeoff me-2"></i>Start Free
                        </a>
                        <a href="<?= BASE_URL ?>/trips" class="tm-btn-hero-outline">
                            <i class="bi bi-compass me-2"></i>Explore Trips
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Stats row -->
                <div class="tm-hero-stats tm-animate-fade-in delay-4">
                    <div class="tm-hero-stat">
                        <div class="tm-hero-stat-value" data-count="500">0</div>
                        <div class="tm-hero-stat-label">Trips Created</div>
                    </div>
                    <div class="tm-hero-stat-sep"></div>
                    <div class="tm-hero-stat">
                        <div class="tm-hero-stat-value" data-count="2000">0</div>
                        <div class="tm-hero-stat-label">Travelers</div>
                    </div>
                    <div class="tm-hero-stat-sep"></div>
                    <div class="tm-hero-stat">
                        <div class="tm-hero-stat-value" data-count="50">0</div>
                        <div class="tm-hero-stat-label">Destinations</div>
                    </div>
                </div>

                <!-- Trust badges -->
                <div class="d-flex flex-wrap gap-2 mt-4 tm-animate-fade-in delay-4">
                    <span class="tm-trust-badge"><i class="bi bi-shield-check-fill"></i> Secure</span>
                    <span class="tm-trust-badge"><i class="bi bi-people-fill"></i> Community</span>
                    <span class="tm-trust-badge"><i class="bi bi-star-fill"></i> Trusted</span>
                </div>
            </div>

            <!-- RIGHT: 3D Floating Image Stack -->
            <div class="col-lg-6 d-none d-lg-flex justify-content-center tm-animate-slide-up delay-2">
                <div class="tm-hero-3d-stack">
                    <!-- Floating cards around main image -->
                    <div class="tm-hero-img-main">
                        <img src="<?= BASE_URL ?>/assets/images/hero_collage.png" alt="Travel adventures collage">
                        <div class="tm-hero-img-overlay"></div>
                    </div>

                    <!-- Floating info chip — top left -->
                    <div class="tm-float-chip tm-float-chip-1">
                        <div class="tm-float-chip-icon" style="background:#10B981;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:0.85rem;color:#fff;">240 Travelers</div>
                            <div style="font-size:0.72rem;color:rgba(255,255,255,0.65);">Online now</div>
                        </div>
                    </div>

                    <!-- Floating info chip — bottom right -->
                    <div class="tm-float-chip tm-float-chip-2">
                        <div class="tm-float-chip-icon" style="background:#F97316;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:0.85rem;color:#fff;">Manali Trek</div>
                            <div style="font-size:0.72rem;color:rgba(255,255,255,0.65);">5 spots left</div>
                        </div>
                    </div>

                    <!-- Floating mini stars chip -->
                    <div class="tm-float-chip tm-float-chip-3">
                        <div style="display:flex;gap:2px;color:#F59E0B;font-size:0.8rem;">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <div style="font-size:0.72rem;color:rgba(255,255,255,0.7);margin-top:2px;">Rated 4.9/5 by travelers</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Wave divider -->
    <div class="tm-hero-wave">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="#F8FAFC"/>
        </svg>
    </div>
</section>

<!-- ======================================================
     MARQUEE TRIP TYPES
     ====================================================== -->
<div class="tm-marquee-wrap">
    <div class="tm-marquee">
        <?php
        $types = ['🏔️ Trekking','🏕️ Camping','🚗 Road Trip','🎒 Backpacking','🏖️ Beach','🌿 Nature','🏙️ City Tours','🤿 Diving','🚵 Cycling','❄️ Winter Sports'];
        for ($i = 0; $i < 3; $i++) foreach ($types as $t) echo "<span class=\"tm-marquee-item\">$t</span>";
        ?>
    </div>
</div>

<!-- ======================================================
     DESTINATION SHOWCASE
     ====================================================== -->
<section class="tm-section-destinations">
    <div class="container">
        <div class="text-center mb-5 tm-reveal">
            <span class="tm-section-pill">✦ Explore</span>
            <h2 class="tm-section-headline mt-3">Adventure Awaits</h2>
            <p class="tm-section-sub">Real destinations, real people, unforgettable stories</p>
        </div>
        <div class="row g-4 align-items-stretch">
            <!-- Card 1 — large left -->
            <div class="col-lg-5 tm-reveal">
                <div class="tm-dest-card tm-dest-card-large">
                    <img src="<?= BASE_URL ?>/assets/images/dest_mountain.png" alt="Mountain Trekking">
                    <div class="tm-dest-card-overlay">
                        <span class="tm-dest-type-badge">🏔️ Trekking</span>
                        <div class="tm-dest-card-info">
                            <h3>Himalayan High Routes</h3>
                            <p>Snow peaks, glacial lakes, and trails that test your spirit</p>
                            <a href="<?= BASE_URL ?>/trips?trip_type=trekking" class="tm-dest-cta">Explore Trips <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cards 2 & 3 + CTA stacked right -->
            <div class="col-lg-7">
                <div class="row g-4">
                    <div class="col-6 tm-reveal" style="--delay:0.1s">
                        <div class="tm-dest-card" style="height:230px;">
                            <img src="<?= BASE_URL ?>/assets/images/dest_beach.png" alt="Beach Camping">
                            <div class="tm-dest-card-overlay">
                                <span class="tm-dest-type-badge">🏖️ Beach</span>
                                <div class="tm-dest-card-info">
                                    <h3>Island Escapes</h3>
                                    <p>Turquoise water, bonfire nights, starlit skies</p>
                                    <a href="<?= BASE_URL ?>/trips?trip_type=beach" class="tm-dest-cta">Explore <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 tm-reveal" style="--delay:0.2s">
                        <div class="tm-dest-card" style="height:230px;">
                            <img src="<?= BASE_URL ?>/assets/images/dest_roadtrip.png" alt="Road Trip">
                            <div class="tm-dest-card-overlay">
                                <span class="tm-dest-type-badge">🚗 Road Trip</span>
                                <div class="tm-dest-card-info">
                                    <h3>Open Road Adventures</h3>
                                    <p>Freedom, forests, and unforgettable sunsets</p>
                                    <a href="<?= BASE_URL ?>/trips?trip_type=road_trip" class="tm-dest-cta">Explore <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Mini CTA card -->
                    <div class="col-12 tm-reveal" style="--delay:0.3s">
                        <div class="tm-dest-mini-cta">
                            <div>
                                <div style="font-weight:700;font-size:1.1rem;color:#fff;">Can't find your adventure?</div>
                                <div style="color:rgba(255,255,255,0.7);font-size:0.9rem;">Create your own trip and invite friends</div>
                            </div>
                            <a href="<?= BASE_URL ?>/trips/create" class="tm-btn-glow">
                                <i class="bi bi-plus-circle me-1"></i> Create Trip
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================
     HOW IT WORKS — Animated Steps
     ====================================================== -->
<section class="tm-section-steps">
    <div class="container">
        <div class="text-center mb-5 tm-reveal">
            <span class="tm-section-pill">✦ Simple Process</span>
            <h2 class="tm-section-headline mt-3">How TravelMate Works</h2>
            <p class="tm-section-sub">From idea to adventure in four easy steps</p>
        </div>
        <div class="tm-steps-track">
            <?php
            $steps = [
                ['num'=>'01','icon'=>'bi-person-plus','color'=>'#2563EB','bg'=>'rgba(37,99,235,0.12)','title'=>'Create Account','desc'=>'Sign up free and build your traveler profile with your adventure preferences and reliability score.'],
                ['num'=>'02','icon'=>'bi-map','color'=>'#10B981','bg'=>'rgba(16,185,129,0.12)','title'=>'Create or Join Trip','desc'=>'Start a new trip or browse exciting trips planned by other travelers around the world.'],
                ['num'=>'03','icon'=>'bi-people','color'=>'#F97316','bg'=>'rgba(249,115,22,0.12)','title'=>'Collaborate','desc'=>'Assign responsibilities, share resources, chat with your group, and track shared expenses.'],
                ['num'=>'04','icon'=>'bi-images','color'=>'#8B5CF6','bg'=>'rgba(139,92,246,0.12)','title'=>'Make Memories','desc'=>'Upload photos & videos to shared albums and leave reviews for your travel companions.'],
            ];
            foreach ($steps as $i => $s): ?>
            <div class="tm-step-item tm-reveal" style="--delay:<?= $i*0.12 ?>s">
                <div class="tm-step-num"><?= $s['num'] ?></div>
                <div class="tm-step-icon-wrap" style="background:<?= $s['bg'] ?>;color:<?= $s['color'] ?>;">
                    <i class="bi <?= $s['icon'] ?>"></i>
                </div>
                <h5 class="tm-step-title"><?= $s['title'] ?></h5>
                <p class="tm-step-desc"><?= $s['desc'] ?></p>
                <?php if ($i < count($steps)-1): ?>
                <div class="tm-step-connector"></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ======================================================
     FEATURED TRIPS
     ====================================================== -->
<?php if (!empty($featuredTrips)): ?>
<section class="tm-section-trips">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 tm-reveal">
            <div>
                <span class="tm-section-pill">✦ Live Trips</span>
                <h2 class="tm-section-headline mt-2 mb-1">Upcoming Adventures</h2>
                <p class="tm-section-sub mb-0">Join a trip planned by fellow travelers</p>
            </div>
            <a href="<?= BASE_URL ?>/trips" class="tm-btn-outline-brand">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredTrips as $i => $trip): ?>
            <div class="col-md-6 col-lg-4 tm-reveal" style="--delay:<?= $i*0.08 ?>s">
                <div class="tm-trip-card tm-trip-card-3d">
                    <?php if ($trip['cover_image']): ?>
                        <img src="<?= BASE_URL ?>/uploads/trips/<?= Security::e($trip['cover_image']) ?>"
                             alt="<?= Security::e($trip['title']) ?>" class="tm-trip-card-img">
                    <?php else: ?>
                        <div class="tm-trip-card-img-placeholder">
                            <i class="bi bi-mountain-snow"></i>
                        </div>
                    <?php endif; ?>
                    <div class="tm-trip-card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="tm-badge tm-status-upcoming"><i class="bi bi-calendar-event me-1"></i>Upcoming</span>
                            <?php if ($trip['trip_type']): ?>
                                <span class="tm-badge tm-badge-primary"><?= Security::e(ucwords(str_replace('_',' ',$trip['trip_type']))) ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="tm-trip-card-title"><?= Security::e($trip['title']) ?></h3>
                        <div class="tm-trip-card-destination mb-2">
                            <i class="bi bi-geo-alt me-1"></i><?= Security::e($trip['destination']) ?>
                        </div>
                        <div class="tm-trip-card-meta">
                            <span><i class="bi bi-calendar3 me-1"></i><?= date('M d', strtotime($trip['start_date'])) ?> – <?= date('M d', strtotime($trip['end_date'])) ?></span>
                            <span><i class="bi bi-people me-1"></i><?= (int)$trip['member_count'] ?>/<?= (int)$trip['max_participants'] ?></span>
                        </div>
                    </div>
                    <div class="tm-trip-card-footer">
                        <div class="d-flex align-items-center gap-2">
                            <div class="tm-avatar-placeholder-sm" style="width:28px;height:28px;font-size:0.75rem;">
                                <?= strtoupper(substr($trip['creator_name'],0,1)) ?>
                            </div>
                            <span class="small" style="color:#64748B;"><?= Security::e($trip['creator_name']) ?></span>
                        </div>
                        <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>" class="btn btn-primary btn-sm">View Trip</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ======================================================
     FEATURES GRID — 3D Cards
     ====================================================== -->
<section class="tm-section-features">
    <div class="container">
        <div class="text-center mb-5 tm-reveal">
            <span class="tm-section-pill">✦ Features</span>
            <h2 class="tm-section-headline mt-3">Everything You Need</h2>
            <p class="tm-section-sub">All tools for seamless group travel planning</p>
        </div>
        <div class="row g-4">
            <?php
            $features = [
                ['icon'=>'bi-shield-check','color'=>'#2563EB','bg'=>'rgba(37,99,235,0.1)','title'=>'Secure Platform','desc'=>'CSRF protection, password hashing, and input validation keep your data safe.','grad'=>'135deg,#2563EB,#0EA5E9'],
                ['icon'=>'bi-chat-dots-fill','color'=>'#10B981','bg'=>'rgba(16,185,129,0.1)','title'=>'Group Chat','desc'=>'Real-time messaging with all trip participants in one dedicated chat room.','grad'=>'135deg,#10B981,#34D399'],
                ['icon'=>'bi-wallet2','color'=>'#F97316','bg'=>'rgba(249,115,22,0.1)','title'=>'Expense Tracking','desc'=>'Track shared costs and calculate individual contributions automatically.','grad'=>'135deg,#F97316,#FBBF24'],
                ['icon'=>'bi-clipboard-check-fill','color'=>'#8B5CF6','bg'=>'rgba(139,92,246,0.1)','title'=>'Task Management','desc'=>'Assign and track responsibilities so nothing falls through the cracks.','grad'=>'135deg,#8B5CF6,#C084FC'],
                ['icon'=>'bi-box-seam-fill','color'=>'#0EA5E9','bg'=>'rgba(14,165,233,0.1)','title'=>'Resource Board','desc'=>'Know exactly who is bringing what — tents, stoves, first aid kits and more.','grad'=>'135deg,#0EA5E9,#38BDF8'],
                ['icon'=>'bi-star-fill','color'=>'#F59E0B','bg'=>'rgba(245,158,11,0.1)','title'=>'Reviews & Ratings','desc'=>'Build trust through peer reviews and a transparent reliability score system.','grad'=>'135deg,#F59E0B,#FDE68A'],
            ];
            foreach ($features as $i => $f): ?>
            <div class="col-md-6 col-lg-4 tm-reveal" style="--delay:<?= $i*0.08 ?>s">
                <div class="tm-feature-card-3d">
                    <div class="tm-feature-card-glow" style="background:linear-gradient(<?= $f['grad'] ?>);"></div>
                    <div class="tm-feature-icon-3d" style="background:<?= $f['bg'] ?>;color:<?= $f['color'] ?>;">
                        <i class="bi <?= $f['icon'] ?>"></i>
                    </div>
                    <h5 class="tm-feature-title"><?= $f['title'] ?></h5>
                    <p class="tm-feature-desc"><?= $f['desc'] ?></p>
                    <div class="tm-feature-card-line" style="background:linear-gradient(<?= $f['grad'] ?>);"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ======================================================
     TESTIMONIAL / SOCIAL PROOF STRIP
     ====================================================== -->
<section class="tm-section-proof tm-reveal">
    <div class="container">
        <div class="tm-proof-strip">
            <div class="tm-proof-avatars">
                <?php
                $colors = ['#2563EB','#10B981','#F97316','#8B5CF6'];
                $letters = ['A','K','P','R'];
                foreach ($letters as $j => $l):
                ?>
                <div class="tm-proof-avatar" style="background:<?= $colors[$j] ?>;z-index:<?= 4-$j ?>;"><?= $l ?></div>
                <?php endforeach; ?>
            </div>
            <div class="tm-proof-text">
                <strong style="color:#0F172A;">Loved by 2,000+ travelers</strong>
                <div style="color:#64748B;font-size:0.875rem;">Join the community and plan your next adventure</div>
            </div>
            <div class="d-flex gap-2 align-items-center ms-auto">
                <div style="color:#F59E0B;font-size:1rem;">★★★★★</div>
                <span style="font-weight:700;color:#0F172A;">4.9</span>
                <span style="color:#64748B;font-size:0.85rem;">/5 rating</span>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================
     CTA SECTION — Immersive
     ====================================================== -->
<?php if (!Security::isLoggedIn()): ?>
<section class="tm-section-cta">
    <div class="container">
        <div class="tm-cta-block tm-reveal">
            <div class="tm-cta-bg-img">
                <img src="<?= BASE_URL ?>/assets/images/hero_collage.png" alt="Adventure">
            </div>
            <div class="tm-cta-bg-overlay"></div>
            <div class="tm-cta-content">
                <div class="tm-cta-badge">🚀 Free Forever</div>
                <h2 class="tm-cta-title">Ready for Your Next Adventure?</h2>
                <p class="tm-cta-sub">Join thousands of travelers who plan, collaborate, and create memories together on TravelMate.</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="<?= BASE_URL ?>/auth/register" class="tm-btn-hero-primary">
                        <i class="bi bi-rocket-takeoff me-2"></i>Start Planning Free
                    </a>
                    <a href="<?= BASE_URL ?>/trips" class="tm-btn-hero-outline">
                        <i class="bi bi-binoculars me-2"></i>Browse Trips
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>