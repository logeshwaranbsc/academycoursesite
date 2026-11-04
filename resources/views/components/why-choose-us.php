<section class="section" id="why-us" style="background-color: var(--color-white);">
    <div class="container-main">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <!-- Left: Headline & Stats -->
            <div>
                <span class="section-label">Why Choose Digital Teenz Academy</span>
                <h2 class="section-title">An Education Built For The Real World</h2>
                <div class="section-divider"></div>
                <p style="font-size: 1rem; color: #4B5563; line-height: 1.8; margin-bottom: 2rem;">
                    We don’t simply teach theoretical slides. We engineer immersive learning environments where students build production-grade projects, practice code quality standards, and receive 1-on-1 mentorship until placed.
                </p>

                <!-- Metrics Counter -->
                <div class="grid grid-cols-3 gap-6" style="padding-top: 1.5rem; border-top: 1px solid var(--color-gray-200);">
                    <div>
                        <div style="font-family: var(--font-heading); font-size: 2rem; font-weight: 700; color: var(--color-navy); line-height: 1;">
                            500+
                        </div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; color: #6B7280; font-weight: 600; letter-spacing: 0.05em; display: block; margin-top: 0.35rem;">
                            Students Placed
                        </span>
                    </div>
                    <div>
                        <div style="font-family: var(--font-heading); font-size: 2rem; font-weight: 700; color: var(--color-navy); line-height: 1;">
                            6+
                        </div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; color: #6B7280; font-weight: 600; letter-spacing: 0.05em; display: block; margin-top: 0.35rem;">
                            Years of Excellence
                        </span>
                    </div>
                    <div>
                        <div style="font-family: var(--font-heading); font-size: 2rem; font-weight: 700; color: var(--color-gold); line-height: 1;">
                            95%
                        </div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; color: #6B7280; font-weight: 600; letter-spacing: 0.05em; display: block; margin-top: 0.35rem;">
                            Placement Rate
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: 6 Reasons Checklist -->
            <div class="flex flex-col gap-5">
                <?php
                $reasons = [
                    ['title' => 'Industry Expert Trainers', 'desc' => 'Instructors are active senior developers and technology architects with years of production expertise.'],
                    ['title' => 'Hands-On Practical Lab Training', 'desc' => 'Every concept is paired with real code implementation, architectural design, and live problem-solving.'],
                    ['title' => 'Live Commercial Client Projects', 'desc' => 'Graduate with a tangible GitHub portfolio showcasing real deliverables rather than generic toy tutorials.'],
                    ['title' => 'Dedicated Career & Soft Skills Coaching', 'desc' => 'Mock behavioral interviews, technical screening preparation, resume optimization, and salary negotiation guidance.'],
                    ['title' => 'Direct Corporate Placement Referrals', 'desc' => 'Our corporate partnership network guarantees interviews with fast-growing tech startups and MNCs.'],
                    ['title' => 'Lifelong Learning Alumni Network', 'desc' => 'Continuous access to community updates, refresher workshops, and peer developer circles.'],
                ];
                foreach ($reasons as $r):
                ?>
                    <div class="flex items-start gap-4" style="background: var(--color-cream); padding: 1.25rem 1.5rem; border-radius: 6px;">
                        <span style="color: var(--color-gold); font-size: 1.25rem; font-weight: bold; line-height: 1;">✔</span>
                        <div>
                            <h4 style="font-size: 1rem; font-weight: 700; color: var(--color-navy); margin-bottom: 0.25rem;">
                                <?= e($r['title']) ?>
                            </h4>
                            <p style="font-size: 0.875rem; color: #4B5563; line-height: 1.6; margin: 0;">
                                <?= e($r['desc']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
