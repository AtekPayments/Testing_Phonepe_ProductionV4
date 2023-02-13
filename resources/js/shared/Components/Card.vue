<template>

    <div class="h-56 m-2 bg-blue-500 rounded-xl relative text-white shadow">
        <img class="relative object-cover w-full h-full rounded-xl" :src="card_img" alt="">
        <div class="w-full px-8 absolute top-8">

            <!--USER NAME & COMPANY LOGO-->
            <div class="flex justify-between">

                <div>
                    <p class="font-light">Name</p>
                    <p class="tracking-widest font-bold">
                        {{ user.name }}
                    </p>
                </div>

                <img class="w-28 h-auto" :src="short_logo" alt=""/>

            </div>

            <!--CARD NUMBER-->
            <div class="pt-1">
                <p class="font-light">Master Number</p>
                <p class="tracking-more-wider font-bold">
                    {{ passDetails.ms_qr_no }}
                </p>
            </div>

            <div class="pt-6 pr-6">
                <div class="flex justify-between">

                    <!--BALANCE-->
                    <div>
                        <p class="font-light text-xs">{{ isSv ? 'Balance' : 'Balance Trips' }}</p>
                        <p class="tracking-wider text-sm font-bold">
                            {{
                                isSv ?
                                    (balance === 0 ? 'fetching ..' : '₹ ' + balance)
                                    :
                                    (balance === 0 ? '0' : balance + ' Trips')
                            }}
                        </p>
                    </div>

                    <!--EXP DATE-->
                    <div>
                        <p class="font-light text-xs">Expiry</p>
                        <p class="tracking-wider text-sm font-bold">
                            {{ new Date(passDetails.ms_qr_exp).toLocaleDateString() }}
                        </p>
                    </div>

                    <!--RELOAD-->
                    <div class="flex flex-row-reverse" v-if="isSv">
                        <button v-on:click="svReloadStatus" class="bg-blue-700 rounded shadow font-bold text-sm p-2 text-white">
                            <svg v-if="isLoading" class="inline w-4 mb-1 mr-2 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                            </svg>
                            <template v-if="isLoading">Loading ...</template>
                            <template v-else><i class="fa-solid fa-retweet mx-2"></i> Reload Pass</template>
                        </button>
                    </div>

                    <div class="flex flex-row-reverse" v-if="!isSv">
                        <button v-on:click="tpReloadStatus" class="bg-blue-700 rounded shadow font-bold text-sm pb-2 pt-1.5 px-2 text-white">
                            <svg v-if="isLoading" class="inline w-4 mb-1 mx-2 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                            </svg>
                            <template v-if="isLoading">Loading ...</template>
                            <template v-else><i class="fa-solid fa-retweet mx-2"></i> Reload Pass</template>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

</template>

<script>

import {Link} from '@inertiajs/inertia-vue3';
import axios from "axios";
import Spinner from "./Spinner";

export default {
    props: {
        passDetails: Object,
        user: Object,
        isSv: Boolean,
        balance: Number,
    },
    name: "Card",
    components: {
        Spinner,
        Link
    },
    data() {
        return {
            short_logo: '/img/logo_short.png',
            card_img: '/img/card-bg.png',
            isLoading: false
        }
    },
    methods: {
        svReloadStatus: async function () {
            console.log("hello")
            if (this.passDetails.product_id === 3) {
                this.isLoading = true
                const res = await axios.get('/sv/reload/status/' + this.passDetails.sale_or_no)
                const data = res.data
                if (data.status) this.onSuccess(data)
                else this.onFailure(data)
            }
        },
        tpReloadStatus: async function () {
            console.log("hello")
            if (this.passDetails.product_id === 4) {
                this.isLoading = true
                const res = await axios.get('/tp/reload/status/' + this.passDetails.sale_or_no)
                const data = res.data
                if (data.status) this.onSuccess(data)
                else this.onFailure(data)
            }
        },
        onSuccess: function (data) {
            if (this.isSv)
            {
                this.$inertia.get('/sv/reload/' + this.passDetails.sale_or_no)
                this.isLoading = false;
            }
            else
            {
                this.$inertia.get('/tp/reload/' + this.passDetails.sale_or_no)
                this.isLoading = false;
            }
        },
        onFailure: function (data) {
            console.log(data)
            this.$swal.fire({
                icon: 'error',
                title: 'Generation Failed !',
                text: data.error,
                confirmButtonText: 'Okay',
            })
            this.isLoading = false;
        }
    }

}
</script>

<style scoped>

</style>
