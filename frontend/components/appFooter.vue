<template>
  <footer class="footer">
    <div class="footer-main">
      <div class="container footer-grid">
        <!-- Brand -->
        <div class="footer-brand">
          <NuxtLink to="/" class="footer-logo">
            <div class="logo-dot"></div>
            <img src="../public/logo.png" height="44" alt="Logo">
          </NuxtLink>
          <p class="brand-desc">
            Votre marketplace de confiance pour des produits premium, sourcés avec soin et livrés rapidement partout en Tunisie.
          </p>
          <div class="social-links">
            <a href="#" class="soc-btn" aria-label="Facebook">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
              </svg>
            </a>
            <a href="#" class="soc-btn" aria-label="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="2" y="2" width="20" height="20" rx="5"/>
                <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
              </svg>
            </a>
            <a href="#" class="soc-btn" aria-label="Twitter / X">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
              </svg>
            </a>
            <a href="#" class="soc-btn" aria-label="TikTok">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M9 12a4 4 0 104 4V4a5 5 0 005 5"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Shop Links -->
        <div class="footer-col">
          <h4 class="col-title">Boutique</h4>
          <ul class="col-links">
            <li><NuxtLink to="/product">All Products</NuxtLink></li>
            <li><NuxtLink to="/categories">Categories</NuxtLink></li>
            <li><NuxtLink to="/#top-rate">Top Rated</NuxtLink></li>
            <!-- <li><NuxtLink to="/new">New Arrivals</NuxtLink></li> -->
            <!-- <li><NuxtLink to="/promo">Promotions</NuxtLink></li> -->
          </ul>
        </div>

        <!-- Company Links -->
        <div class="footer-col">
          <h4 class="col-title">JOA Enterprise</h4>
          <ul class="col-links">
            <li><NuxtLink to="/#about">About Us</NuxtLink></li>
            <li><NuxtLink to="/#contact">Contact</NuxtLink></li>
          </ul>
        </div>

        <!-- Support Links -->
        <div class="footer-col">
          <h4 class="col-title">Support</h4>
          <ul class="col-links">
            <li><NuxtLink to="/faq">FAQ</NuxtLink></li>
            <li><NuxtLink to="/shipping">Livraison</NuxtLink></li>
            <li><NuxtLink to="/returns">Retours</NuxtLink></li>
            <li><NuxtLink to="/privacy">Confidentialité</NuxtLink></li>
            <li><NuxtLink to="/terms">CGU</NuxtLink></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div class="footer-col">
          <h4 class="col-title">Contact</h4>
          <ul class="contact-list">
            <li>
              <div class="contact-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
              </div>
              <span>123 Rue de la Paix,<br>Tunis 1000</span>
            </li>
            <li>
              <div class="contact-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.99 1.13 2 2 0 013 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.08 6.08l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                </svg>
              </div>
              <span>+216 71 000 000</span>
            </li>
            <li>
              <div class="contact-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
              </div>
              <span>joa@boutique.tn</span>
            </li>
            <li>
              <div class="contact-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
              </div>
              <span>Lun–Sam : 8h – 20h</span>
            </li>
          </ul>
        </div>

      </div>
    </div>

    <!-- ═══ TRUST BADGES ═══ -->
    <div class="trust-bar">
      <div class="container trust-inner">
        <div class="trust-item">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6">
            <rect x="1" y="3" width="15" height="13" rx="2"/>
            <path d="M16 8h4a2 2 0 012 2v6a2 2 0 01-2 2h-4M5 18v2M9 18v2"/>
            <circle cx="19" cy="17" r="1"/>
          </svg>
          <span>Livraison rapide</span>
        </div>
        <div class="trust-sep"></div>
        <div class="trust-item">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
          <span>Paiement sécurisé</span>
        </div>
        <div class="trust-sep"></div>
        <div class="trust-item">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6">
            <polyline points="23 4 23 10 17 10"/>
            <polyline points="1 20 1 14 7 14"/>
            <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
          </svg>
          <span>Retours gratuits</span>
        </div>
        <div class="trust-sep"></div>
        <div class="trust-item">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6">
            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
          </svg>
          <span>Support 7j/7</span>
        </div>
        <div class="trust-sep"></div>
        <div class="trust-item">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <span>Produits authentiques</span>
        </div>
      </div>
    </div>

    <!-- ═══ BOTTOM BAR ═══ -->
    <div class="footer-bottom">
      <div class="container bottom-inner">
        <p class="copyright">© {{ new Date().getFullYear() }} BrandName. Tous droits réservés.</p>
        <div class="payment-badges">
          <div class="pay-badge">
            <svg width="28" height="18" viewBox="0 0 32 20" fill="none">
              <rect width="32" height="20" rx="3" fill="rgba(248,242,230,0.08)"/>
              <circle cx="12" cy="10" r="6" fill="#EB001B" fill-opacity="0.8"/>
              <circle cx="20" cy="10" r="6" fill="#F79E1B" fill-opacity="0.8"/>
            </svg>
          </div>
          <div class="pay-badge">
            <svg width="28" height="18" viewBox="0 0 32 20" fill="none">
              <rect width="32" height="20" rx="3" fill="rgba(248,242,230,0.08)"/>
              <path d="M10 13h3l2-6h-3l-2 6zM17 7l-1.5 6h2.8l1.5-6H17z" fill="rgba(248,242,230,0.6)"/>
            </svg>
          </div>
          <div class="pay-badge">
            <svg width="28" height="18" viewBox="0 0 32 20" fill="none">
              <rect width="32" height="20" rx="3" fill="rgba(248,242,230,0.08)"/>
              <rect x="6" y="7" width="20" height="6" rx="1" fill="rgba(248,242,230,0.15)"/>
              <rect x="6" y="8" width="20" height="2" fill="rgba(248,242,230,0.3)"/>
            </svg>
          </div>
        </div>
        <div class="bottom-links">
          <NuxtLink to="/privacy">Confidentialité</NuxtLink>
          <span>·</span>
          <NuxtLink to="/terms">CGU</NuxtLink>
          <span>·</span>
          <NuxtLink to="/cookies">Cookies</NuxtLink>
        </div>
      </div>
    </div>

  </footer>
</template>

<script setup>


</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&family=DM+Sans:wght@400;500&display=swap');

* { box-sizing: border-box; }
.container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; }

/* ── Newsletter ── */
.newsletter-bar {
  background: #C8854A;
  padding: 2.5rem 2rem;
}
.newsletter-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  flex-wrap: wrap;
}
.nl-label {
  font-size: 11px;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.7);
  margin-bottom: 4px;
}
.nl-title {
  font-family: 'Playfair Display', serif;
  font-size: 1.4rem;
  color: #fff;
  font-weight: 500;
}
.nl-form {
  display: flex;
  gap: 0;
  flex-shrink: 0;
}
.nl-form input {
  padding: 11px 18px;
  border: none;
  border-radius: 8px 0 0 8px;
  font-size: 14px;
  font-family: 'DM Sans', sans-serif;
  outline: none;
  width: 260px;
  background: rgba(255,255,255,0.15);
  color: #fff;
}
.nl-form input::placeholder { color: rgba(255,255,255,0.55); }
.nl-form button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 11px 20px;
  background: #2C2117;
  color: #F8F2E6;
  border: none;
  border-radius: 0 8px 8px 0;
  font-size: 13.5px;
  font-weight: 500;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  transition: background 0.15s;
  white-space: nowrap;
}
.nl-form button:hover { background: #1a1208; }

/* ── Main Footer ── */
.footer-main {
  background: #1a1208;
  padding: 4rem 2rem 3rem;
}
.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr 1.4fr;
  gap: 3rem;
}

/* Brand */
.footer-brand {}
.footer-logo {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  margin-bottom: 1rem;
}
.logo-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #C8854A;
  flex-shrink: 0;
}
.brand-desc {
  font-size: 13px;
  color: rgba(248,242,230,0.4);
  line-height: 1.7;
  margin-bottom: 1.5rem;
  max-width: 240px;
}
.social-links { display: flex; gap: 8px; }
.soc-btn {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  border: 0.5px solid rgba(248,242,230,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(248,242,230,0.45);
  text-decoration: none;
  transition: border-color 0.15s, color 0.15s;
}
.soc-btn:hover { border-color: #C8854A; color: #C8854A; }

/* Columns */
.col-title {
  font-size: 11px;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: rgba(248,242,230,0.3);
  margin-bottom: 1.2rem;
  font-weight: 500;
}
.col-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.col-links a {
  font-size: 13.5px;
  color: rgba(248,242,230,0.5);
  text-decoration: none;
  transition: color 0.15s;
}
.col-links a:hover { color: #C8854A; }

/* Contact list */
.contact-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.contact-list li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}
.contact-icon {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  background: rgba(200,133,74,0.1);
  border: 0.5px solid rgba(200,133,74,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: #C8854A;
}
.contact-list span {
  font-size: 13px;
  color: rgba(248,242,230,0.45);
  line-height: 1.5;
}

/* ── Trust Bar ── */
.trust-bar {
  background: #221910;
  border-top: 0.5px solid rgba(248,242,230,0.05);
  border-bottom: 0.5px solid rgba(248,242,230,0.05);
  padding: 1.2rem 2rem;
}
.trust-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  flex-wrap: wrap;
}
.trust-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 2rem;
  font-size: 13px;
  color: rgba(248,242,230,0.5);
  font-weight: 500;
}
.trust-sep {
  width: 1px;
  height: 24px;
  background: rgba(248,242,230,0.08);
  flex-shrink: 0;
}

/* ── Bottom Bar ── */
.footer-bottom {
  background: #110e07;
  padding: 1.2rem 2rem;
}
.bottom-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}
.copyright {
  font-size: 12px;
  color: rgba(248,242,230,0.2);
}
.payment-badges { display: flex; align-items: center; gap: 8px; }
.pay-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.7;
}
.bottom-links {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 12px;
  color: rgba(248,242,230,0.2);
}
.bottom-links a {
  color: rgba(248,242,230,0.25);
  text-decoration: none;
  transition: color 0.15s;
}
.bottom-links a:hover { color: #C8854A; }

/* ── Responsive ── */
@media (max-width: 1100px) {
  .footer-grid { grid-template-columns: 1fr 1fr 1fr; }
  .footer-brand { grid-column: 1 / -1; }
}
@media (max-width: 700px) {
  .footer-grid { grid-template-columns: 1fr 1fr; }
  .newsletter-inner { flex-direction: column; align-items: flex-start; }
  .nl-form { width: 100%; }
  .nl-form input { flex: 1; width: auto; }
  .trust-inner { justify-content: flex-start; gap: 1rem; }
  .trust-sep { display: none; }
  .trust-item { padding: 0; }
  .bottom-inner { flex-direction: column; align-items: center; text-align: center; }
}
@media (max-width: 480px) {
  .footer-grid { grid-template-columns: 1fr; }
}
</style>