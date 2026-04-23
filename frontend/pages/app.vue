<template>
  <div v-if="!loading">
    <AppHeader />

    <!-- ═══ HERO ═══ -->
    <img src="../public/exemple3.png" alt="Ecolive Slogan" style="width: 100%; height: auto;" />

    <!-- ═══ CATEGORIES ═══ -->
    <section id="categories" class="section-light">
      <div class="container">
        <div class="section-label">Browse By</div>
        <div class="section-header">
          <h2 class="section-title">Categories</h2>
          <NuxtLink to="/category" class="link-more">View all →</NuxtLink>
        </div>
        <div class="cat-grid">
          <NuxtLink
            v-for="cat in data"
            :key="cat.id"
            :to="`/category/${cat.id}`"
            class="cat-card"
          >
            <div class="cat-icon">
              <img v-if="cat.img" :src="cat.img" :alt="cat.name" width="28" />
              <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            </div>
            <div class="cat-name">{{ cat.name }}</div>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- ═══ TOP RATED ═══ -->
    <section class="section-white">
      <div class="container">
        <div class="section-label">Best Sellers</div>
        <div class="section-header">
          <h2 class="section-title">Top Rated</h2>
          <NuxtLink to="/product" class="link-more">View all →</NuxtLink>
        </div>
        <div class="prod-grid">
          <product-card
            v-for="product in topRate"
            :key="product.id"
            :id="product.id"
            :name="product.nom"
            :img="product.img"
            :description="product.description"
            :prix="product.prix"
            :stock="product.stock"
            :unit="product.unit"
          />
        </div>
      </div>
    </section>

    <!-- ═══ ABOUT US ═══ -->
    <section class="section-dark" id="about">
      <div class="container about-grid">
        <div class="about-text">
          <div class="section-label-light">Our Story</div>
          <h2 class="section-title-light">Quality and Trust<br>First</h2>
          <p class="about-sub">We believe that great products shouldn't be a compromise. Every item in our store is carefully selected and tested.</p>
          <ul class="about-list">
            <li>Authentic products, sourced with care</li>
            <li>Fast delivery across the country</li>
            <li>Dedicated customer support, always available</li>
          </ul>
        </div>
        <div class="about-stats">
          <div class="astat"><div class="astat-n">500+</div><div class="astat-l">Products</div></div>
          <div class="astat"><div class="astat-n">12k</div><div class="astat-l">Customers</div></div>
          <div class="astat"><div class="astat-n">5 years</div><div class="astat-l">Experience</div></div>
          <div class="astat"><div class="astat-n">4.9★</div><div class="astat-l">Average Rating</div></div>
        </div>
      </div>
    </section>

    <!-- ═══ CONTACT US ═══ -->
    <section class="section-light" id="contact">
      <div class="container contact-grid">
        <div>
          <div class="section-label">Write to us</div>
          <h2 class="section-title">Contact Us</h2>
          <p class="section-sub">Do you have a question or need assistance? We're here to help you quickly.</p>
          <div class="contact-info">
            <div class="contact-item">
              <div class="contact-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </div>
              <div>
                <div class="ci-label">Address</div>
                <div class="ci-val">123 Street of Peace, Tunis 1000</div>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.68A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
              </div>
              <div>
                <div class="ci-label">Phone</div>
                <div class="ci-val">+216 71 000 000</div>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              </div>
              <div>
                <div class="ci-label">Email</div>
                <div class="ci-val">joa@boutique.tn</div>
              </div>
            </div>
          </div>
        </div>

        <form class="contact-form" @submit.prevent="sendMessage">
          <div class="form-row">
            <div class="form-field">
              <label>Prénom</label>
              <input v-model="form.firstName" type="text" placeholder="Amine" required />
            </div>
            <div class="form-field">
              <label>Nom</label>
              <input v-model="form.lastName" type="text" placeholder="Ben Ali" required />
            </div>
          </div>
          <div class="form-field">
            <label>Email</label>
            <input v-model="form.email" type="email" placeholder="vous@exemple.com" required />
          </div>
          <div class="form-field">
            <label>Message</label>
            <textarea v-model="form.message" placeholder="Your message..." rows="4" required></textarea>
          </div>
          <button type="submit" class="btn-primary">Send Message</button>
        </form>
      </div>
    </section>

    <AppFooter/>
  </div>

  <div v-else class="loader">
    <div class="spinner"></div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'

const data = ref([])
const loading = ref(false)
const topRate = ref([])
const form = ref({ firstName: '', lastName: '', email: '', message: '' })

const fetchCategory = async () => {
  try {
    const response = await axios.get('http://localhost:8000/category')
    data.value = response.data
  } catch (err) {
    console.error(err)
  }
}

const fetchTopRate = async () => {
  try {
    const response = await axios.get('http://localhost:8000/topRate')
    topRate.value = response.data
  } catch (err) {
    console.error(err)
  }
}

const sendMessage = async () => {
  // adapter selon votre API
  alert('Message envoyé !')
  form.value = { firstName: '', lastName: '', email: '', message: '' }
}

onMounted(async () => {
  loading.value = true
  await Promise.all([fetchCategory(), fetchTopRate()])
  loading.value = false
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@400;500&display=swap');

* { box-sizing: border-box; }

.container { max-width: 1100px; margin: 0 auto; }
.section-light { background: #F8F2E6; padding: 5rem 2rem; }
.section-white { background: #fff; padding: 5rem 2rem; }
.section-dark  { background: #2C2117; padding: 5rem 2rem; }

.section-label       { font-size: 11px; font-weight: 500; letter-spacing: 2px; text-transform: uppercase; color: #C8854A; margin-bottom: 8px; }
.section-label-light { font-size: 11px; font-weight: 500; letter-spacing: 2px; text-transform: uppercase; color: #C8854A; margin-bottom: 8px; }
.section-title       { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 500; color: #2C2117; }
.section-title-light { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 500; color: #F8F2E6; margin-bottom: 1rem; }
.section-sub         { font-size: 15px; color: #7a6555; line-height: 1.7; margin-top: 8px; }
.section-header      { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; }
.link-more           { font-size: 13px; color: #C8854A; text-decoration: none; font-weight: 500; }

/* ── Hero ── */
.hero {
  background: #2C2117;
  min-height: 520px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 5rem 4rem;
  position: relative;
  overflow: hidden;
  gap: 2rem;
}
.hero-pattern {
  position: absolute; inset: 0; opacity: 0.04;
  background-image: repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 0, transparent 50%);
  background-size: 20px 20px;
}
.hero-content { position: relative; z-index: 1; max-width: 520px; }
.hero-tag {
  display: inline-block; font-size: 11px; letter-spacing: 2px; text-transform: uppercase;
  color: #C8854A; background: rgba(200,133,74,0.12); padding: 5px 14px;
  border-radius: 20px; margin-bottom: 1.5rem; border: 1px solid rgba(200,133,74,0.25);
}
.hero h1 {
  font-family: 'Playfair Display', serif; font-size: 3rem; color: #F8F2E6;
  line-height: 1.15; margin-bottom: 1.2rem; font-weight: 600;
}
.hero p { font-size: 15px; color: rgba(248,242,230,0.6); line-height: 1.7; margin-bottom: 2rem; max-width: 380px; }
.hero-cta { display: flex; gap: 12px; flex-wrap: wrap; }
.hero-stats {
  position: relative; z-index: 1;
  background: rgba(248,242,230,0.06); border: 1px solid rgba(248,242,230,0.1);
  border-radius: 16px; padding: 2rem; display: flex; flex-direction: column; gap: 2rem;
  flex-shrink: 0;
}
.stat { text-align: center; }
.stat-n { font-family: 'Playfair Display', serif; font-size: 2rem; color: #F8F2E6; font-weight: 500; }
.stat-l { font-size: 11px; color: rgba(248,242,230,0.4); text-transform: uppercase; letter-spacing: 1.5px; margin-top: 4px; }

/* ── Buttons ── */
.btn-primary {
  display: inline-flex; align-items: center; gap: 8px;
  background: #C8854A; color: #fff; font-size: 13.5px; font-weight: 500;
  padding: 12px 24px; border-radius: 8px; text-decoration: none; border: none; cursor: pointer;
  font-family: 'DM Sans', sans-serif;
}
.btn-outline {
  display: inline-flex; align-items: center; gap: 8px;
  background: transparent; color: #F8F2E6; font-size: 13.5px; font-weight: 500;
  padding: 12px 24px; border-radius: 8px; text-decoration: none;
  border: 1px solid rgba(248,242,230,0.2);
}

/* ── Categories ── */
.cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 16px; }
.cat-card {
  background: #fff; border-radius: 12px; border: 0.5px solid rgba(44,33,23,0.1);
  padding: 1.5rem 1rem; text-align: center; text-decoration: none;
  transition: border-color 0.15s;
}
.cat-card:hover { border-color: #C8854A; }
.cat-icon {
  width: 48px; height: 48px; border-radius: 10px; background: #F8F2E6;
  margin: 0 auto 12px; display: flex; align-items: center; justify-content: center;
}
.cat-name { font-size: 13.5px; font-weight: 500; color: #2C2117; }

/* ── Products ── */
.prod-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }

/* ── About ── */
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }
.about-sub { font-size: 15px; color: rgba(248,242,230,0.6); line-height: 1.7; margin-bottom: 1.5rem; }
.about-list { list-style: none; display: flex; flex-direction: column; gap: 10px; padding: 0; }
.about-list li { font-size: 14px; color: rgba(248,242,230,0.6); display: flex; align-items: center; gap: 10px; }
.about-list li::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #C8854A; flex-shrink: 0; }
.about-stats {
  display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
  background: rgba(248,242,230,0.06); border-radius: 16px; border: 1px solid rgba(248,242,230,0.1); padding: 2rem;
}
.astat { background: rgba(248,242,230,0.04); border-radius: 10px; border: 1px solid rgba(248,242,230,0.08); padding: 1.2rem; text-align: center; }
.astat-n { font-family: 'Playfair Display', serif; font-size: 1.8rem; color: #F8F2E6; font-weight: 500; }
.astat-l { font-size: 11px; color: rgba(248,242,230,0.4); text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

/* ── Contact ── */
.contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: start; }
.contact-info { display: flex; flex-direction: column; gap: 1.2rem; margin-top: 2rem; }
.contact-item { display: flex; gap: 14px; align-items: flex-start; }
.contact-icon {
  width: 38px; height: 38px; border-radius: 8px; background: #fff;
  border: 0.5px solid rgba(44,33,23,0.1); flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.ci-label { font-size: 13px; font-weight: 500; color: #2C2117; }
.ci-val   { font-size: 13px; color: #7a6555; margin-top: 2px; }
.contact-form {
  background: #fff; border-radius: 16px; border: 0.5px solid rgba(44,33,23,0.1);
  padding: 2rem; display: flex; flex-direction: column; gap: 14px;
}
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.form-field { display: flex; flex-direction: column; gap: 6px; }
.form-field label { font-size: 11px; font-weight: 500; color: #7a6555; text-transform: uppercase; letter-spacing: 1px; }
.form-field input,
.form-field textarea {
  background: #F8F2E6; border: 0.5px solid rgba(44,33,23,0.15); border-radius: 8px;
  padding: 10px 14px; font-size: 14px; color: #2C2117; font-family: 'DM Sans', sans-serif;
  outline: none; resize: none; transition: border-color 0.15s;
}
.form-field input:focus,
.form-field textarea:focus { border-color: #C8854A; }

/* ── Loader ── */
.loader { display: flex; justify-content: center; align-items: center; height: 100vh; }
.spinner {
  width: 36px; height: 36px; border: 2px solid rgba(200,133,74,0.2);
  border-top-color: #C8854A; border-radius: 50%; animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Responsive ── */
@media (max-width: 768px) {
  .hero { flex-direction: column; padding: 3rem 1.5rem; text-align: center; }
  .hero-stats { flex-direction: row; justify-content: center; width: 100%; }
  .hero h1 { font-size: 2.2rem; }
  .about-grid, .contact-grid { grid-template-columns: 1fr; gap: 2.5rem; }
}
</style>