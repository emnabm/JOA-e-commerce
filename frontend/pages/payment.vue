
<template>
  <div class="page-wrap">
    <AppHeader />

    <main class="payment-main">
      <div class="payment-grid">

        <!-- Gauche : formulaire paiement -->
        <div class="payment-left">

          <!-- Steps -->
          <div class="steps">
            <div class="step">
              <div class="step-dot done">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <span>Panier</span>
            </div>
            <div class="step-line done"></div>
            <div class="step">
              <div class="step-dot done">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <span>Livraison</span>
            </div>
            <div class="step-line active"></div>
            <div class="step active">
              <div class="step-dot filled">3</div>
              <span>Paiement</span>
            </div>
          </div>

          <!-- Méthode de paiement -->
          <div class="section-card">
            <h2 class="section-title">Mode de paiement</h2>

            <div class="pay-methods">
              <label v-for="m in methods" :key="m.value" class="pay-method" :class="{ selected: payMethod === m.value }">
                <input type="radio" v-model="payMethod" :value="m.value" hidden />
                <div class="method-icon" v-html="m.icon"></div>
                <div class="method-info">
                  <div class="method-name">{{ m.label }}</div>
                  <div class="method-desc">{{ m.desc }}</div>
                </div>
                <div class="method-check" v-if="payMethod === m.value">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
              </label>
            </div>
          </div>

          <!-- Formulaire carte -->
          <div v-if="payMethod === 'card'" class="section-card">
            <h2 class="section-title">Détails de la carte</h2>

            <div class="card-preview" :class="{ flipped: showBack }">
              <div class="card-front">
                <div class="card-top">
                  <div class="card-chip">
                    <div class="chip-line"></div><div class="chip-line"></div><div class="chip-line"></div>
                  </div>
                  <div class="card-brand">
                    <svg width="36" height="24" viewBox="0 0 36 24">
                      <rect width="36" height="24" rx="4" fill="rgba(255,255,255,0.12)"/>
                      <circle cx="14" cy="12" r="8" fill="#EB001B" opacity="0.9"/>
                      <circle cx="22" cy="12" r="8" fill="#F79E1B" opacity="0.9"/>
                    </svg>
                  </div>
                </div>
                <div class="card-number-display">{{ displayNumber }}</div>
                <div class="card-bottom">
                  <div>
                    <div class="card-label">Titulaire</div>
                    <div class="card-value">{{ cardName || 'VOTRE NOM' }}</div>
                  </div>
                  <div>
                    <div class="card-label">Expire</div>
                    <div class="card-value">{{ cardExpiry || 'MM/AA' }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-fields">
              <div class="field">
                <label>Numéro de carte</label>
                <div class="input-wrap">
                  <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                  <input v-model="cardNumber" @input="formatCard" maxlength="19" placeholder="1234 5678 9012 3456" />
                </div>
              </div>

              <div class="field">
                <label>Nom sur la carte</label>
                <div class="input-wrap">
                  <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  <input v-model="cardName" type="text" placeholder="AMINE BEN ALI" style="text-transform:uppercase" />
                </div>
              </div>

              <div class="fields-row">
                <div class="field">
                  <label>Date d'expiration</label>
                  <div class="input-wrap">
                    <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <input v-model="cardExpiry" @input="formatExpiry" maxlength="5" placeholder="MM/AA" />
                  </div>
                </div>
                <div class="field">
                  <label>CVV</label>
                  <div class="input-wrap">
                    <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <input v-model="cardCvv" @focus="showBack=true" @blur="showBack=false" maxlength="3" placeholder="123" type="password" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Paiement à la livraison -->
          <div v-if="payMethod === 'cash'" class="section-card info-card">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            <div>
              <div class="info-title">Paiement à la livraison</div>
              <div class="info-desc">Préparez le montant exact en espèces. Le livreur ne peut pas toujours rendre la monnaie.</div>
            </div>
          </div>

          <!-- Virement -->
          <div v-if="payMethod === 'transfer'" class="section-card info-card">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 014-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>
            <div>
              <div class="info-title">Virement bancaire</div>
              <div class="info-desc">RIB : <strong>12 345 6789012 34</strong> — Référence : votre numéro de commande. Traitement sous 24-48h.</div>
            </div>
          </div>

          <!-- Erreur -->
          <div v-if="errorMsg" class="alert-error">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ errorMsg }}
          </div>

        </div>

        <!-- Droite : récapitulatif commande -->
        <div class="payment-right">
          <div class="section-card summary-card">
            <h2 class="section-title">Récapitulatif</h2>

            <!-- Infos livraison -->
            <div class="summary-block">
              <div class="summary-label">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Livraison
              </div>
              <div class="summary-val">{{ deliveryInfo.fullName }}</div>
              <div class="summary-sub">{{ deliveryInfo.address }}, {{ deliveryInfo.city }} {{ deliveryInfo.postalCode }}</div>
              <div class="summary-sub">{{ deliveryInfo.phone }}</div>
            </div>

            <div class="summary-block">
              <div class="summary-label">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                Mode de livraison
              </div>
              <div class="summary-val">{{ deliveryLabel }}</div>
            </div>

            <!-- Totaux -->
            <div class="totals">
              <div class="total-row"><span>Sous-total</span><span>{{ formatPrice(deliveryInfo.subtotal) }}</span></div>
              <div class="total-row"><span>Livraison</span><span :class="deliveryInfo.deliveryFee === 0 ? 'free-tag' : ''">{{ deliveryInfo.deliveryFee === 0 ? 'Gratuit' : formatPrice(deliveryInfo.deliveryFee) }}</span></div>
              <div class="total-row total-final"><span>Total à payer</span><span>{{ formatPrice(deliveryInfo.total) }}</span></div>
            </div>

            <!-- Badges sécurité -->
            <div class="security-badges">
              <div class="badge-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3B6D11" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>Paiement sécurisé SSL</span>
              </div>
              <div class="badge-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3B6D11" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                <span>Données chiffrées</span>
              </div>
              <div class="badge-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3B6D11" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                <span>Support 7j/7</span>
              </div>
            </div>

            <!-- Bouton confirmer -->
            <button class="btn-submit" @click="confirmPayment" :disabled="!payMethod || submitting">
              <div v-if="submitting" class="btn-spinner"></div>
              <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
              {{ submitting ? 'Traitement...' : `Payer ${formatPrice(deliveryInfo.total)}` }}
            </button>

            <p class="cancel-link">
              <NuxtLink to="/category/product/checkout">← Modifier la livraison</NuxtLink>
            </p>
          </div>
        </div>

      </div>
    </main>

    <!-- Modal succès -->
    <div v-if="showSuccess" class="modal-overlay">
      <div class="modal-card">
        <div class="modal-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#3B6D11" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2 class="modal-title">Commande confirmée !</h2>
        <p class="modal-sub">Merci pour votre achat. Vous recevrez un email de confirmation sous peu.</p>
        <div class="modal-order">N° commande : <strong>#{{ orderNumber }}</strong></div>
        <NuxtLink to="/" class="btn-submit" style="text-decoration:none;justify-content:center">
          Retour à la boutique
        </NuxtLink>
      </div>
    </div>

    <AppFooter />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const token = localStorage.getItem('token')

const payMethod = ref('')
const cardNumber = ref('')
const cardName = ref('')
const cardExpiry = ref('')
const cardCvv = ref('')
const showBack = ref(false)
const submitting = ref(false)
const errorMsg = ref('')
const showSuccess = ref(false)
const orderNumber = ref('')
const deliveryInfo = ref({
  fullName: '',
  phone: '',
  address: '',
  city: '',
  postalCode: '',
  delivery: '',
  note: '',
  subtotal: 0,
  deliveryFee: 0,
  total: 0
})

const deliveryOptions = [
  { value: 'Standard', label: 'Livraison standard', cost: 7 },
  { value: 'Express', label: 'Livraison express', cost: 15 },
  { value: 'clickAndCollect', label: 'Click & Collect', cost: 0 },
  { value: 'Relay', label: 'Point relais', cost: 4 },
]

const methods = [
  {
    value: 'card', label: 'Carte bancaire', desc: 'Visa, Mastercard, CB',
    icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>`,
  },
  {
    value: 'cash', label: 'Paiement à la livraison', desc: 'Espèces à la réception',
    icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>`,
  },
  {
    value: 'transfer', label: 'Virement bancaire', desc: 'Traitement sous 24-48h',
    icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 014-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>`,
  },
]

const deliveryLabel = computed(() => {
  const option = deliveryOptions.find(o => o.value === deliveryInfo.value.delivery)
  return option?.label ?? ''
})

const displayNumber = computed(() => {
  const raw = cardNumber.value.replace(/\s/g, '')
  return raw ? raw.padEnd(16, '•').replace(/(.{4})/g, '$1 ').trim() : '•••• •••• •••• ••••'
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('tn-TN', {
    style: 'currency',
    currency: 'TND',
    minimumFractionDigits: 3,
    maximumFractionDigits: 3
  }).format(price)
}

const formatCard = () => {
  let v = cardNumber.value.replace(/\D/g, '').substring(0, 16)
  cardNumber.value = v.replace(/(.{4})/g, '$1 ').trim()
}

const formatExpiry = () => {
  let v = cardExpiry.value.replace(/\D/g, '').substring(0, 4)
  if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2)
  cardExpiry.value = v
}

const confirmPayment = async () => {
  errorMsg.value = ''
  if (!payMethod.value) { 
    errorMsg.value = 'Sélectionnez un mode de paiement'
    return 
  }
  if (payMethod.value === 'card' && (!cardNumber.value || !cardName.value || !cardExpiry.value || !cardCvv.value)) {
    errorMsg.value = 'Veuillez remplir tous les champs de la carte'
    return
  }
  
  submitting.value = true
  try {
    const res = await axios.post(
      'http://localhost:8000/api/createOrder',
      {
        delivery: deliveryInfo.value.delivery,
        address: `${deliveryInfo.value.address}, ${deliveryInfo.value.city} ${deliveryInfo.value.postalCode}`,
        payMethod: payMethod.value,
        fullName: deliveryInfo.value.fullName,
        phone: deliveryInfo.value.phone,
        note: deliveryInfo.value.note,
      },
      { headers: { Authorization: `Bearer ${token}` } }
    )
    orderNumber.value = res.data.order_id || Math.random().toString(36).substring(2,8).toUpperCase()
    showSuccess.value = true
    
    // Clear session storage
    sessionStorage.removeItem('deliveryInfo')
  } catch (err) {
    errorMsg.value = err.response?.data?.error || 'Erreur lors du paiement. Veuillez réessayer.'
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  const savedInfo = sessionStorage.getItem('deliveryInfo')
  if (savedInfo) {
    deliveryInfo.value = JSON.parse(savedInfo)
  } else {
    // Redirect back to checkout if no delivery info
    router.push('/checkout')
  }
})
</script>

<style scoped>
/* Tous les styles de votre page payment originale sont conservés */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&family=DM+Sans:wght@400;500&display=swap');

.page-wrap { font-family: 'DM Sans', sans-serif; background: #F8F2E6; min-height: 100vh; display: flex; flex-direction: column; }
.payment-main { flex: 1; max-width: 1100px; margin: 0 auto; padding: 3rem 2rem; width: 100%; }
.payment-grid { display: grid; grid-template-columns: 1fr 360px; gap: 2rem; align-items: start; }

/* Steps */
.steps { display: flex; align-items: center; margin-bottom: 1.5rem; }
.step { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #b0a090; }
.step.active { color: #2C2117; font-weight: 500; }
.step-dot { width: 28px; height: 28px; border-radius: 50%; border: 1.5px solid #d4c8bc; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 500; color: #b0a090; flex-shrink: 0; }
.step-dot.filled { background: #2C2117; border-color: #2C2117; color: #F8F2E6; }
.step-dot.done { background: #3B6D11; border-color: #3B6D11; color: #fff; }
.step-line { flex: 1; height: 1px; background: #d4c8bc; margin: 0 10px; }
.step-line.done { background: #3B6D11; }
.step-line.active { background: #2C2117; }

/* Cards */
.section-card { background: #fff; border-radius: 16px; border: 0.5px solid rgba(44,33,23,0.1); padding: 1.8rem; margin-bottom: 1.5rem; }
.section-title { font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 500; color: #2C2117; margin-bottom: 1.2rem; }

/* Payment methods */
.pay-methods { display: flex; flex-direction: column; gap: 10px; }
.pay-method {
  display: flex; align-items: center; gap: 14px;
  background: #F8F2E6; border: 1.5px solid transparent; border-radius: 10px;
  padding: 14px; cursor: pointer; transition: all 0.15s; position: relative;
}
.pay-method:hover { border-color: rgba(200,133,74,0.3); }
.pay-method.selected { border-color: #C8854A; background: #fff; }
.method-icon { width: 40px; height: 40px; background: #fff; border-radius: 9px; display: flex; align-items: center; justify-content: center; border: 0.5px solid rgba(44,33,23,0.08); flex-shrink: 0; }
.method-info { flex: 1; }
.method-name { font-size: 14px; font-weight: 500; color: #2C2117; }
.method-desc { font-size: 12px; color: #7a6555; margin-top: 2px; }
.method-check { position: absolute; right: 14px; width: 20px; height: 20px; border-radius: 50%; background: #C8854A; display: flex; align-items: center; justify-content: center; color: #fff; }

/* Card preview */
.card-preview {
  background: linear-gradient(135deg, #2C2117 0%, #4a3520 100%);
  border-radius: 14px; padding: 1.5rem; margin-bottom: 1.5rem;
  position: relative; overflow: hidden; min-height: 160px;
}
.card-preview::before {
  content: ''; position: absolute; top: -40px; right: -40px;
  width: 160px; height: 160px; border-radius: 50%;
  background: rgba(200,133,74,0.15);
}
.card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; }
.card-chip { width: 36px; height: 28px; background: rgba(255,255,255,0.15); border-radius: 5px; padding: 5px; display: flex; flex-direction: column; gap: 3px; }
.chip-line { height: 2px; background: rgba(255,255,255,0.4); border-radius: 1px; }
.card-number-display { font-size: 1.2rem; letter-spacing: 3px; color: rgba(248,242,230,0.9); font-weight: 500; margin-bottom: 1.2rem; font-family: monospace; }
.card-bottom { display: flex; justify-content: space-between; }
.card-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(248,242,230,0.4); margin-bottom: 4px; }
.card-value { font-size: 13px; color: rgba(248,242,230,0.9); font-weight: 500; letter-spacing: 1px; text-transform: uppercase; }

/* Form fields */
.form-fields { display: flex; flex-direction: column; gap: 14px; }
.fields-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 11px; font-weight: 500; color: #7a6555; text-transform: uppercase; letter-spacing: 1px; }
.input-wrap { position: relative; display: flex; align-items: center; }
.input-wrap input {
  width: 100%; padding: 10px 14px 10px 38px;
  background: #F8F2E6; border: 0.5px solid rgba(44,33,23,0.15); border-radius: 9px;
  font-size: 13.5px; color: #2C2117; font-family: 'DM Sans', sans-serif;
  outline: none;
}
.input-wrap input:focus { border-color: #C8854A; background: #fff; }
.input-icon { position: absolute; left: 12px; color: #b0a090; pointer-events: none; }

/* Info card */
.info-card { display: flex; align-items: flex-start; gap: 16px; background: #FFF5EC; border-color: rgba(200,133,74,0.2); }
.info-title { font-size: 14px; font-weight: 500; color: #2C2117; margin-bottom: 4px; }
.info-desc { font-size: 13px; color: #7a6555; line-height: 1.6; }

/* Summary */
.summary-card { position: sticky; top: 100px; }
.summary-block { margin-bottom: 1.2rem; padding-bottom: 1.2rem; border-bottom: 0.5px solid rgba(44,33,23,0.08); }
.summary-label { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 500; color: #7a6555; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
.summary-val { font-size: 14px; font-weight: 500; color: #2C2117; }
.summary-sub { font-size: 12.5px; color: #7a6555; margin-top: 2px; }

.totals { display: flex; flex-direction: column; gap: 10px; margin-bottom: 1.2rem; }
.total-row { display: flex; justify-content: space-between; font-size: 14px; color: #5a4a3a; }
.total-final { font-size: 16px; font-weight: 500; color: #2C2117; border-top: 0.5px solid rgba(44,33,23,0.08); padding-top: 10px; margin-top: 4px; }
.free-tag { color: #3B6D11; font-weight: 500; }

.security-badges { display: flex; flex-direction: column; gap: 8px; margin-bottom: 1.5rem; }
.badge-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #3B6D11; }

/* Buttons */
.btn-submit {
  display: flex; align-items: center; justify-content: center; gap: 10px;
  background: #2C2117; color: #F8F2E6; font-size: 14px; font-weight: 500;
  font-family: 'DM Sans', sans-serif; padding: 14px; border-radius: 10px;
  border: none; cursor: pointer; width: 100%;
}
.btn-submit:hover:not(:disabled) { background: #C8854A; }
.btn-submit:disabled { opacity: 0.45; cursor: default; }
.btn-spinner { width: 16px; height: 16px; border: 2px solid rgba(248,242,230,0.3); border-top-color: #F8F2E6; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.cancel-link { text-align: center; margin-top: 12px; }
.cancel-link a { font-size: 13px; color: #7a6555; text-decoration: none; }
.cancel-link a:hover { color: #C8854A; }

/* Alert */
.alert-error { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 10px; font-size: 13px; background: #FCEBEB; color: #A32D2D; border: 0.5px solid #F7C1C1; margin-bottom: 1rem; }

/* Modal */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(44,33,23,0.5);
  display: flex; align-items: center; justify-content: center; z-index: 100;
  backdrop-filter: blur(4px);
}
.modal-card { background: #fff; border-radius: 20px; padding: 2.5rem; max-width: 420px; width: 90%; text-align: center; }
.modal-icon { width: 64px; height: 64px; border-radius: 50%; background: #EAF3DE; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
.modal-title { font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #2C2117; margin-bottom: 0.5rem; }
.modal-sub { font-size: 14px; color: #7a6555; margin-bottom: 1rem; }
.modal-order { font-size: 13.5px; color: #5a4a3a; background: #F8F2E6; padding: 10px 20px; border-radius: 8px; margin-bottom: 1rem; }

@media (max-width: 900px) {
  .payment-grid { grid-template-columns: 1fr; }
  .summary-card { position: static; }
  .payment-main { padding: 2rem 1rem; }
}
</style>