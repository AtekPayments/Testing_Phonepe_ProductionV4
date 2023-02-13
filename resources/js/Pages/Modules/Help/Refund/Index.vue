<template>

    <nav-bar/>
    <hero/>

    <div class="bg-white rounded-md m-2 p-4" v-if="status">
        <div class="pb-3 text-xl text-center font-bold text-gray-600">
            {{ order_id }}
        </div>
        <hr class="py-2"/>
        <div class="grid grid-cols-2">
            <div class="text-left font-bold  text-gray-600">
                Ticket Fare
            </div>
            <div class="text-right font-bold text-gray-600">
                {{ pass_price }}
            </div>
        </div>
        <div class="grid grid-cols-2">
            <div class="text-left font-bold text-gray-600">
                Processing Charges
            </div>
            <div class="text-right font-bold text-gray-600">
                {{ processing_fee_amount }}
            </div>
        </div>
        <div class="grid grid-cols-2 mt-2">
            <div class="text-left font-bold text-gray-600">
                Refundable Amount
            </div>
            <div class="text-right font-bold text-gray-600">
                {{ refund_amount }}
            </div>
        </div>
    </div>

    <div class="rounded-md m-2 p-4 text-gray-700 text-center" :class="status ? 'bg-green-200' : 'bg-red-200'">
        <div v-if="status">
            Pass can be refunded <i class="fa-solid fa-circle-check mx-1"></i>
        </div>
        <div v-if="!status">
            <i class="fa-solid fa-xmark mx-1"></i> Pass can't be refunded {{ error }}
        </div>
    </div>

    <Button :title="status ? 'Refund Ticket' : 'Okay'" v-on:click="refundTicket"/>

</template>

<script>
import NavBar from "../../../../shared/NavBar";
import Hero from "../../../../shared/Hero";
import Button from "../../../../shared/Components/Button";
import axios from "axios";

export default {

    components: {Button, Hero, NavBar},

    props: {
        status: Boolean,
        order_id: String,
        processing_fee: Number,
        processing_fee_amount: Number,
        refund_amount: Number,
        pass_price: Number,
        error: String
    },

    name: "Index",

    methods: {
        refundTicket: function () {
            this.$swal.fire({
                icon: 'error',
                title: 'Refund Ticket',
                text: 'Are you sure, you want to refund ticket !',
                confirmButtonText: 'Yes, Refund Ticket',
                showDenyButton: true,
                denyButtonText: 'No'
            }).then((res) => {
                if (res.isConfirmed) {
                    this.refund()
                }
            })
        },

        refund: async function () {

            let loader = this.$loading.show({
                loader: "dots",
                color: "#3992e6"
            })

            const res = await axios.get('/refund/ticket/' + this.order_id)
            const data = await res.data

            if (data.status)
            {
                loader.hide()

                this.$swal.fire(
                    'Refunded Successfully !',
                    'success'
                )
                this.$inertia.get("/products" )
            }
            else
            {
                loader.hide()

                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                })
            }

        }

    },
}
</script>

<style scoped>

</style>
