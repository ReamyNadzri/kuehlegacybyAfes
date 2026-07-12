<footer class="footer-panel">
    <div class="footer-content">
        <div class="footer-brand-grid">
            <div class="footer-about">
                <h3>Tentang Kami</h3>
                <p>
                    Matlamat kami adalah untuk <strong>menjadikan penyediaan dan perkongsian resipi kueh sebagai satu pengalaman yang menyeronokkan.</strong> 
                    Kami percaya bahawa kuih tradisional memainkan peranan penting dalam mengeratkan hubungan, melestarikan budaya, dan mencipta gaya hidup yang lebih bermakna. 
                    Dengan platform kami, kami memperkasakan individu untuk berkongsi resipi dan ilmu berkaitan kueh, demi memelihara warisan serta menghubungkan komuniti.
                </p>
            </div>
            <div class="footer-links-grid d-flex flex-column gap-2 text-start text-md-end">
                <h5 class="font-mono text-uppercase tracking-wider" style="font-size: 0.8rem; color: var(--color-accent);">KuehLegacy</h5>
                <a href="<?php echo $root_path; ?>index.php">Utama</a>
                <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=">Carian Resipi</a>
                <a href="<?php echo $root_path; ?>auth/userProfile.php">Resipi Anda</a>
            </div>
        </div>
        
        <div class="footer-copyright">
            <span>Hak Cipta &copy; <?php echo date('Y'); ?> AbeFiwan Expert Studio. Hak Cipta Terpelihara.</span>
            <span>Preserving the Sweet Heritage &bull; Seni Warisan Kuih-Muih Tradisional Malaysia.</span>
        </div>
    </div>
    
    <?php if (file_exists($root_path . 'sources/footer/footer.png')): ?>
        <img src="<?php echo $root_path; ?>sources/footer/footer.png" alt="Decorative Footer Graphic" style="width: 100%; height: auto; display: block; margin-top: 2rem; border-radius: 12px; filter: grayscale(1) opacity(0.15);">
    <?php endif; ?>
</footer>


<!-- Cinematic GSAP Animations Engine -->
<script>
    (function initAnimations() {
        function runAnimations() {
            if (typeof gsap === 'undefined') return;
            gsap.registerPlugin(ScrollTrigger);

            // Defer page-load entrance animations until the preloader is fully dismissed to prevent frame drops
            function playEntrance() {
                // 2. Double-Bezel Cards Entrance Page-Load Stagger (Filter-safe & robust)
                const cards = gsap.utils.toArray('.double-bezel-outer');
                if (cards.length > 0) {
                    gsap.set(cards, { opacity: 0, y: 35 });
                    gsap.to(cards, {
                        y: 0,
                        opacity: 1,
                        duration: 0.6,
                        stagger: 0.06,
                        ease: 'power2.out',
                        delay: 0.1,
                        force3D: true
                    });
                }

                // 4. Split Page Columns Cinematic Slide (Details Page)
                const coverCard = document.querySelector('.recipe-cover-card');
                if (coverCard) {
                    gsap.from(coverCard, {
                        x: -40,
                        opacity: 0,
                        duration: 0.8,
                        ease: 'power3.out',
                        force3D: true
                    });
                }

                const contentPanel = document.querySelector('.recipe-content-panel');
                if (contentPanel) {
                    gsap.from(contentPanel, {
                        x: 40,
                        opacity: 0,
                        duration: 0.8,
                        ease: 'power3.out',
                        delay: 0.05,
                        force3D: true
                    });
                }
            }

            const preloader = document.getElementById('preloader');
            if (preloader && window.getComputedStyle(preloader).display !== 'none') {
                window.addEventListener('preloaderDismissed', playEntrance, { once: true });
            } else {
                playEntrance();
            }

            // 5. Magnetic Buttons Micro-Interactions (Only on CTA buttons)
            const magneticButtons = gsap.utils.toArray('.btn-primary-custom, .btn-secondary-custom');
            magneticButtons.forEach(btn => {
                btn.addEventListener('mousemove', (e) => {
                    const bounding = btn.getBoundingClientRect();
                    const x = e.clientX - bounding.left - bounding.width / 2;
                    const y = e.clientY - bounding.top - bounding.height / 2;
                    
                    gsap.to(btn, {
                        x: x * 0.25,
                        y: y * 0.25,
                        duration: 0.3,
                        ease: 'power2.out',
                        force3D: true
                    });
                });
                
                btn.addEventListener('mouseleave', () => {
                    gsap.to(btn, {
                        x: 0,
                        y: 0,
                        duration: 0.5,
                        ease: 'elastic.out(1, 0.3)',
                        force3D: true
                    });
                });
            });


        }

        // Safe execution: run immediately if DOM is ready, otherwise wait
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runAnimations);
        } else {
            runAnimations();
        }
    })();
</script>