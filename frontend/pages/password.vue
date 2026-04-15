<template>
  <div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card d-flex justify-content-center p-4 gap-3">
      <h2 class="text-center fw-bold mb-1">Update Password</h2>
      <p class="text-center text-muted mb-4">lease fill the form to update password</p>
      <form
          @submit.prevent="changePassword"
          class="d-flex flex-column gap-2"

      >
        <div class="d-flex flex-column gap-2">
          <label>Current Password :</label>
          <input v-model="form.oldpassword" type="password"
                 class="rounded border"
                 :class="v$.oldpassword.$error ? 'text-danger' : 'text-success'"
                 placeholder="********">
        </div>
        <div class="d-flex flex-column gap-2">
          <label>New Password :</label>
          <input v-model="form.newpassword"
                 type="password"
                 class="rounded border"
                 :class="v$.newpassword.$error ? 'text-danger' : 'text-success'"
                 placeholder="********">
        </div>

        <div class="d-flex flex-column gap-2">
          <label>Confirm Password :</label>
          <input v-model="form.confirmpassword" type="password"
                 class="rounded border"
                 :class="v$.confirmpassword.$error ? 'text-danger' : 'text-success'"
                 placeholder="********">
        </div>

        <div>
          <h4 class="fw-bold">Your password must contains:</h4>
          <ul>
            <li>A minimum of 8 charecters</li>
            <li>At least one special character and one number</li>
          </ul>

        </div>



        <button v-if="!loading" class="btn btn-success mt-2">Update Password</button>
        <button v-else class="btn btn-success mt-2">Update Password...</button>
        <NuxtLink to="#">Forgot Password?</NuxtLink>

      </form>

    </div>

  </div>


</template>
<script setup>
import {ref,computed} from "vue";
import axios from "axios";
import {useRouter} from "vue-router";
import useVuelidate from '@vuelidate/core'
import {required,  minLength, sameAs} from '@vuelidate/validators'


const token=localStorage.getItem('token')
const router=useRouter()
const loading=ref(false)


const form=ref({
  oldpassword :'',
  newpassword:'',
  confirmpassword:'',

})

const rules =computed(()=>({
  oldpassword:{ required},
  newpassword :{required,minLength:minLength(8)},
  confirmpassword:{required,sameAsPassword: sameAs(form.value.newpassword) }
}))

const v$ = useVuelidate(rules, form)
const changePassword=async ()=>{

    v$.value.$touch()
    if (v$.value.$invalid) return

    else{
      try{
      loading.value=true
      await axios.put('http://localhost:8000/api/change-password',
          {
            'oldPassword':form.value.oldpassword,
            'newPassword':form.value.newpassword
          },{
        headers: { Authorization: `Bearer ${token}` },
    })
      router.push('/account')
    }catch (e) {
      alert(e)
    }
    finally {
      loading.value=false
    }
  }
}

</script>