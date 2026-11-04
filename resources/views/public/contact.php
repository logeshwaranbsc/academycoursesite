<?php
/** @var array $settings */
/** @var array $courses */
use App\Core\View;

$phone = $settings['phone'] ?? '+91 98765 43210';
$email = $settings['email'] ?? 'info@Digital Teenz Academyacademy.com';
$address = $settings['address'] ?? '123, Education Hub, Koramangala, Bangalore – 560 095';
$whatsapp = $settings['whatsapp'] ?? '+91 98765 43210';
?>
<!-- Page Hero Banner -->
<div style="background-color: var(--color-navy); color: #fff; padding: 4.5rem 0 3.5rem;">
    <div class="container-main">
        <span class="section-label" style="color: var(--color-gold);">Admissions & Counselling</span>
        <h1 style="color: #fff; margin-bottom: 1rem;">Get In Touch With Our Career Advisors</h1>
        <div class="section-divider" style="background-color: var(--color-gold);"></div>
        <p style="color: #D1D5DB; font-size: 1.125rem; max-width: 750px; line-height: 1.8;">
            Have questions about our curriculums, batch schedules, fees, or job placement assistance? Reach out to our expert academic advisors today.
        </p>
    </div>
</div>

<!-- Contact Information Cards -->
<section class="section" style="background-color: var(--color-cream); padding: 4rem 0 2rem;">
    <div class="container-main">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Campus Address -->
            <div class="card" style="padding: 1.75rem;">
                <div style="font-size: 2rem; margin-bottom: 0.75rem;">📍</div>
                <h4 style="font-size: 1rem; color: var(--color-navy); margin-bottom: 0.35rem;">Visit Campus</h4>
                <p style="font-size: 0.875rem; color: #4B5563; line-height: 1.6; margin: 0;">
                    <?= e($address) ?>
                </p>
            </div>

            <!-- Phone Counselling -->
            <div class="card" style="padding: 1.75rem;">
                <div style="font-size: 2rem; margin-bottom: 0.75rem;">📞</div>
                <h4 style="font-size: 1rem; color: var(--color-navy); margin-bottom: 0.35rem;">Call Admissions</h4>
                <p style="font-size: 0.875rem; color: #4B5563; line-height: 1.6; margin: 0;">
                    <a href="tel:<?= e($phone) ?>" style="color: var(--color-navy); font-weight: 600;"><?= e($phone) ?></a><br>
                    Mon - Sat: 9:00 AM - 7:00 PM
                </p>
            </div>

            <!-- Email Inquiries -->
            <div class="card" style="padding: 1.75rem;">
                <div style="font-size: 2rem; margin-bottom: 0.75rem;">✉</div>
                <h4 style="font-size: 1rem; color: var(--color-navy); margin-bottom: 0.35rem;">Email Support</h4>
                <p style="font-size: 0.875rem; color: #4B5563; line-height: 1.6; margin: 0;">
                    <a href="mailto:<?= e($email) ?>" style="color: var(--color-navy); font-weight: 600;"><?= e($email) ?></a><br>
                    Quick response within 24 hours
                </p>
            </div>

            <!-- WhatsApp Chat -->
            <div class="card" style="padding: 1.75rem;">
                <div style="font-size: 2rem; margin-bottom: 0.75rem;">💬</div>
                <h4 style="font-size: 1rem; color: var(--color-navy); margin-bottom: 0.35rem;">WhatsApp Chat</h4>
                <p style="font-size: 0.875rem; color: #4B5563; line-height: 1.6; margin: 0;">
                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $whatsapp) ?>" target="_blank" rel="noopener" style="color: var(--color-gold-dark); font-weight: 600;">Instant WhatsApp Chat</a><br>
                    Direct advisor assistance
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Public Enquiry Form Component -->
<?= View::component('enquiry-form', ['courses' => $courses ?? []]) ?>
