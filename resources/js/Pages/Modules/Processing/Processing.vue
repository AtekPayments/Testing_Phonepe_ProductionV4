<template>
    <div class="h-screen grid grid-rows-3">
        <div class="mx-auto flex items-center row-span-2">
            <div class="text-center">
                <p class="text-gray-600 font-bold">Generating your ticket</p>
                <Spinner />
                <p class="text-gray-600 font-bold pt-3">Please wait!</p>
                <p>Please do not press back, close or minimize the App !</p>

            </div>
        </div>
        <div class="mx-auto flex items-center">
            <img :src="coBrand" class="h-24" alt="logo">
        </div>
    </div>
</template>

<script>

import Spinner from "../../../Shared/Component/Spinner";

import axios from "axios";

export default {

    name: "Processing",

    components: {Spinner},

    props: {
        order: String
    },

    data() {
        return {
            coBrand: '/img/atek_logo.png',
            old_order : ""
        }
    },

    async mounted() {
        await this.initCreation()
    },

    methods: {

        sleep(milliseconds) {
            return new Promise((resolve) => setTimeout(resolve, milliseconds));
        },

        initCreation: async function () {
            let retry = 0;
            if(retry === 0){
                await this.sleep(3000)
            }

            while (true) {
                const res = await axios.get("/processing/init/" + this.order)
                const data = res.data
                if (data.status) {
                    if (data.response.sale_or_status === 105 || data.response.sale_or_status === 108 || data.response.sale_or_status === 109) {
                        this.old_order = data.order_id
                        this.onSuccess(data.response, this.old_order)
                        break
                    } else if (data.response.sale_or_status === 103) {
                        this.onFailure("Payment Failed")
                        break
                    }
                }
                retry++;
                console.log("Count =" + retry)
                await this.sleep(3000)
                if (retry > 20) {
                    this.onFailure("Payment Failed")
                    break
                }
            }
        },

        onSuccess: function (data, order_id) {

            const {product_id, op_type_id, sale_or_no} = data

            if (op_type_id === 1)
            {
                this.ticketStatus();
                if (product_id === 1 || product_id === 2) this.$inertia.replace('/ticket/view/' + this.order)
                else if (product_id === 3) this.$inertia.replace('/sv/dashboard')
                else this.$inertia.replace('/tp/dashboard')
            }
            else if (op_type_id === 3)
            {
                if (product_id === 3) this.$inertia.replace('/sv/dashboard')
                else this.$inertia.replace('/tp/dashboard')
            }
            else
            {
                this.ticketStatus();
                if (product_id === 1 || product_id === 2) this.$inertia.replace('/ticket/view/' + order_id)
                else if (product_id === 3) this.$inertia.replace('/sv/dashboard')
                else this.$inertia.replace('/tp/dashboard')
            }

        },

        onFailure: function (data) {
            this.$swal.fire({
                icon: 'error',
                title: data,
                confirmButtonText: 'Ok',
            }).then((res) => {
                if (res.isConfirmed) {
                    this.$inertia.replace("/products")
                }
            })
        },

        ticketStatus: async function () {
            const res = await axios.get('/ticket/status');
            const data = res.data
            if (data.status) {
                console.log("Status Hit Success");
            }else{
                console.log("Status Hit Failed");
            }
        },
    }
}
</script>

<style scoped>

</style>
