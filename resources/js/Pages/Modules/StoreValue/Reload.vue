<template>

    <nav-bar/>
    <hero/>

    <div class="bg-white m-2 p-3 shadow border text-center">
        <p class="font-bold text-lg text-gray-600">Reload StoreValue Pass</p>
    </div>

    <div class="bg-white m-2 p-5 shadow border rounded">
        <div class="grid grid-cols-5 text-center content-center w-full items-center">
            <div  v-on:click="addAmount(-100)"><i class="fas fa-minus-circle fa-xl mt-1"></i></div>
            <div class="mb-3 col-span-3">
                <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Enter Amount</label>
                <input
                    disabled
                    id="price"
                    class="form_number_input"
                    v-model="reload.reloadAmount"
                    v-on:keyup="validate"
                />
                <div class="c-error" v-if="error">
                    {{ error }}
                </div>
            </div>
            <div

                v-on:click="addAmount(100)">
                <i class="fas fa-plus-circle fa-xl mt-1"></i>
            </div>
        </div>


        <div class="mt-3 grid grid-cols-3 gap-5">
            <chip :title="'₹ 100'" v-on:click="setAmount(100)"/>
            <chip :title="'₹ 200'" v-on:click="setAmount(200)"/>
            <chip :title="'₹ 500'" v-on:click="setAmount(500)"/>
        </div>

    </div>

    <Button
        v-on:click="genOrder"
        :is-loading="isLoading"
        :is-disabled="isDisabled"
        :type="'button'"
        :title="'PROCEED TO PAY ₹ ' + reload.reloadAmount"
    />


</template>

<script>


import Chip from "../../../Shared/Component/Chip";
import Hero from "../../../Shared/Hero";
import NavBar from "../../../Shared/NavBar";
import Button from "../../../Shared/Component/Button";
import axios from "axios";
import Footer from "../../../Shared/Footer";

export default {

    props: {
        order_id: String
    },

    name: "Reload.vue",

    components: {Footer, Button, NavBar, Hero, Chip},


    data() {
        return {
            reload: {
                reloadAmount: 100,
                order_id: this.order_id
            },

            isLoading: false,
            isDisabled: false,
            error: null
        }
    },


    methods: {

        addAmount: function (amount) {
            this.reload.reloadAmount += parseInt(amount)
            this.validate()
        },

        setAmount: function (amount) {
            this.reload.reloadAmount = parseInt(amount)
            this.validate()
        },

        validate: function () {

            if (this.reload.reloadAmount === '') {
                this.reload.reloadAmount = 0
            } else if (this.reload.reloadAmount < 100) {
                this.error = 'Amount must be minimum 100'
                this.reload.reloadAmount = 100
                this.isDisabled = false
            } else if (this.reload.reloadAmount % 100 !== 0) {
                this.error = 'Amount must be multiple of 100'
                this.isDisabled = true
            } else if (this.reload.reloadAmount > 3000) {
                this.error = 'Amount must not be greater then 3000'
                this.isDisabled = true
            } else {
                this.error = null
                this.isDisabled = false
                return true
            }
            return false
        },

        genOrder: async function () {
            this.isLoading = true
            const response = await axios.post('/sv/reload', this.reload)
            let data = await response.data
            if (data.status) this.onSuccess(data)
            else this.onFailure(data)
        },

        onSuccess: function (data) {
            this.isLoading = false
            const {redirectUrl} = data
            window.location.replace(redirectUrl)
        },

        onFailure: function (data) {
            this.isLoading = false
            const {errors} = data
            this.error = errors
        },


    }
}
</script>

<style scoped>

</style>
