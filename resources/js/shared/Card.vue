<template>

    <div class="m-1">
        <div  class= " bg-blue-500 rounded-xl shadow-md overflow-hidden " >
            <div class="md:flex">
                <div class="p-8">
                    <!--USER NAME & COMPANY LOGO-->
                    <div class="flex justify-between">
                        <img class="w-28 h-auto" :src="short_logo" alt=""/>
                        <img class="w-28 h-auto md:ml-" :src="ateklogo" alt=""/>
                    </div>
                    <!--CARD NUMBER-->
                    <div class="mt-2 flex justify-between">
                        <div>
                            <p class="font-light text-white">Name</p>
                            <p class="font-bold tracking-widest text-white">{{ user.pax_name }}</p>
                        </div>
                        <div>
                            <p class="font-light text-white">Master Number</p>
                            <p class="tracking-more-wider font-bold text-white">{{ passDetails.ms_qr_no }}</p>
                        </div>
                    </div>

                    <template class="mt-5" v-if="!isSv">
                        <div class="flex justify-between">
                            <div>
                                <p class="font-light text-white">Source</p>
                                <p class="tracking-widest font-bold text-white">
                                    {{ passDetails.source }}
                                </p>
                            </div>
                            <div>
                                <p class="font-light text-white">Destination</p>
                                <p class="tracking-widest font-bold text-white">
                                    {{ passDetails.destination }}
                                </p>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-between mt-4">
                        <!--BALANCE-->
                        <div>
                            <p class="font-light text-xs text-white">{{ isSv ? 'Balance' : 'Balance Trips' }}</p>
                            <p class="tracking-wider text-sm font-bold text-white">
                                {{
                                isSv ?
                                (balance === 0 ? 'fetching ..' : '₹ ' + balance)
                                :
                                (balance === 0 ? 'fetching ..' : balance + ' Trips')
                                }}
                            </p>
                        </div>
                        <!--EXP DATE-->
                        <div>
                            <p class="font-light text-xs text-white">Expiry</p>
                            <p class="tracking-wider text-sm font-bold text-white">
                                {{ new Date(passDetails.ms_qr_exp).toLocaleDateString() + ' 01:10 AM' }}
                            </p>
                        </div>
                    </div>

                    <!--RELOAD-->
                    <div class="flex flex-row-reverse" v-if="isSv">
                        <ReloadButton
                            :type="'button'"
                            :is-disabled="isLoading"
                            :is-loading="isLoading"
                            :title="'Reload Pass'"
                            v-on:click="svReloadStatus"
                        />
                    </div>

                    <div class="flex flex-row-reverse" v-if="!isSv">
                        <ReloadButton
                            :type="'button'"
                            :is-disabled="isLoading"
                            :is-loading="isLoading"
                            :title="'Reload Pass'"
                            v-on:click="tpReloadStatus"
                        />
                    </div>


                </div>
            </div>
        </div>
    </div>

</template>
<script>

import {Link} from '@inertiajs/inertia-vue3';
import axios from "axios";
import ReloadButton from "./Component/ReloadButton";

export default {
    name: "Card",
    props: {
        passDetails: Object,
        user: Object,
        isSv: Boolean,
        balance: Number,
    },
    components: {
        ReloadButton,
        Link
    },
    data() {
        return {
            short_logo: '/img/logo_short.png',
            ateklogo : '/img/atek_logo.png',
            card_img: '/img/card-bg.png',
            isLoading: false
        }
    },
    methods: {
        svReloadStatus: async function () {

            this.isLoading = true
            const res = await axios.get('/sv/reload/status/' + this.passDetails.sale_or_no)
            const data = res.data
            if (data.status) this.onSuccess(data)
            else this.onFailure(data)
        },
        tpReloadStatus: async function () {

            this.isLoading = true
            const res = await axios.get('/tp/reload/status/' + this.passDetails.sale_or_no)
            const data = res.data
            if (data.status) this.onSuccess(data)
            else this.onFailure(data)
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
                title: 'Failed to reload pass !',
                text: 'Please complete active trip to reload !',
                confirmButtonText: 'Okay',
            })
            this.isLoading = false;
        }
    }
}
</script>

<style scoped>

</style>
