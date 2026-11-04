<?php
/** @var array $courses */
$courses = $courses ?? [];
$selectedCourse = $defaultCourse ?? ($_GET['course'] ?? old('course', ''));
?>
<section class="section" id="enquiry" style="background-color: var(--color-navy); color: #fff;">
    <div class="container-main">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
            <!-- Left: Info Column -->
            <div>
                <span class="section-label" style="color: var(--color-gold);">Get In Touch</span>
                <h2 class="section-title-white">Start Your Career Journey Today</h2>
                <div class="section-divider" style="background-color: var(--color-gold);"></div>
                <p style="color: #D1D5DB; font-size: 1rem; line-height: 1.8; margin-bottom: 2rem;">
                    Submit your details and an experienced admissions counsellor will get in touch with you within one business day to discuss syllabus details, fee structures, and batch availability.
                </p>

                <div class="flex flex-col gap-4">
                    <?php
                    $points = [
                        'Personalised course advisory based on your career goals',
                        'Detailed fee breakdown & flexible instalment options',
                        'Upcoming batch schedule & seat reservations',
                        'Free trial session & demo lecture scheduling'
                    ];
                    foreach ($points as $p):
                    ?>
                        <div class="flex items-center gap-3">
                            <span style="color: var(--color-gold); font-size: 1rem; font-weight: bold;">★</span>
                            <span style="font-size: 0.9375rem; color: #E5E7EB;"><?= e($p) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Interactive Form -->
            <div style="background-color: #fff; padding: 2.25rem; border-radius: 8px; box-shadow: var(--shadow-xl); color: var(--color-charcoal);">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-navy); margin-bottom: 0.5rem;">
                    Course Enquiry & Counselling
                </h3>
                <p style="font-size: 0.8125rem; color: #6B7280; margin-bottom: 1.5rem;">
                    Please fill out the form below. We respect your privacy.
                </p>

                <div id="enquiry-feedback"></div>

                <form id="public-enquiry-form" action="<?= base_url('contact') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <!-- Honeypot Field (Hidden from humans, traps automated spam bots) -->
                    <div style="display: none !important;">
                        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <!-- Full Name -->
                    <div style="margin-bottom: 1.25rem;">
                        <label for="enquiry-name" class="form-label">Full Name <span style="color: #dc2626;">*</span></label>
                        <input type="text" id="enquiry-name" name="name" class="form-input" placeholder="e.g. Rahul Sharma" value="<?= e(old('name')) ?>" required>
                        <span class="field-error error-name form-error"></span>
                    </div>

                    <!-- Phone Number -->
                    <div style="margin-bottom: 1.25rem;">
                        <label for="enquiry-phone" class="form-label">Phone Number <span style="color: #dc2626;">*</span></label>
                        <input type="tel" id="enquiry-phone" name="phone" class="form-input" placeholder="e.g. +91 98765 43210" value="<?= e(old('phone')) ?>" required>
                        <span class="field-error error-phone form-error"></span>
                    </div>

                    <!-- Email Address -->
                    <div style="margin-bottom: 1.25rem;">
                        <label for="enquiry-email" class="form-label">Email Address (Optional)</label>
                        <input type="email" id="enquiry-email" name="email" class="form-input" placeholder="e.g. rahul@example.com" value="<?= e(old('email')) ?>">
                        <span class="field-error error-email form-error"></span>
                    </div>

                    <!-- Interested Program -->
                    <div style="margin-bottom: 1.25rem;">
                        <label for="enquiry-course" class="form-label">Interested Program</label>
                        <select id="enquiry-course" name="course" class="form-input">
                            <option value="">Select a Course Program</option>
                            <?php foreach ($courses as $c):
                                $title = is_array($c) ? $c['title'] : (string)$c;
                                $isSelected = ($title === $selectedCourse);
                            ?>
                                <option value="<?= e($title) ?>" <?= $isSelected ? 'selected' : '' ?>>
                                    <?= e($title) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field-error error-course form-error"></span>
                    </div>

                    <!-- Message -->
                    <div style="margin-bottom: 1.5rem;">
                        <label for="enquiry-message" class="form-label">Message / Questions (Optional)</label>
                        <textarea id="enquiry-message" name="message" class="form-input" rows="3" placeholder="Tell us about your background, career goals, or any specific questions..."><?= e(old('message')) ?></textarea>
                        <span class="field-error error-message form-error"></span>
                    </div>

                    <button type="submit" class="btn-secondary w-full" style="width: 100%; display: flex; justify-content: center; font-size: 1rem; padding: 0.85rem;">
                        Submit Enquiry &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
