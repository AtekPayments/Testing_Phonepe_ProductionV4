<template>
    <div class="hidden overflow-y-auto overflow-x-hidden fixed right-0 left-0 top-20 z-50 justify-center items-center md:inset-0 h-modal sm:h-full" id="need-help">
        <div class="relative px-4 w-full max-w-md h-full md:h-auto">

            <!--NEED HELP-->
            <div v-if="showHelp" class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex justify-end p-2">
                    <button v-on:click="close" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="need-help">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 pt-0 text-center">
                    <svg class="mx-auto mb-4 w-14 h-14 text-gray-400 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Please select the type of
                        help, you need ?
                    </h3>

                    <Button
                        :is-loading="isRefundButtonLoading"
                        :is-disabled="isRefundButtonLoading"
                        :title="'Refund Order'"
                        :type="'button'"
                        v-on:click="getRefundInfo"
                    />

                    <Button
                        :is-loading="isGraButtonLoading"
                        :is-disabled="isGraButtonLoading"
                        :title="'Unable to exit ?'"
                        :type="'button'"
                        v-on:click="getGra"
                    />

                </div>
            </div>

            <!--REFUND INFO-->
            <div v-if="showRefund" class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex justify-between items-start p-5 rounded-t border-b dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 lg:text-2xl dark:text-white">
                        Refund Ticket
                    </h3>
                    <button type="button" v-on:click="close" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="need-help">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        Refunding ticket will refund all of the tickets, Please verify below details !
                    </p>
                    <table class="min-w-full border rounded-lg bg-gray-50">
                        <tbody>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Ticket Fare
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                ₹ {{ refund.pass_price }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Processing Charges
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                ₹ {{ refund.processing_fee_amount }}
                            </td>
                        </tr>
                        <tr class="bg-white dark:bg-gray-800">
                            <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Refundable Amount
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                ₹ {{ refund.refund_amount }}
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600">

                    <Button
                        :is-loading="isRefundButtonLoading"
                        :is-disabled="isRefundButtonLoading"
                        :title="'Yes, Refund Order'"
                        :type="'button'"
                        v-on:click="refundTicket"
                    />

                    <Button
                        :is-loading="false"
                        :is-disabled="false"
                        :title="'Decline'"
                        :type="'button'"
                        v-on:click="close"
                    />

                </div>
            </div>

            <!--GRA INFO-->
            <div v-if="showGra" class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex justify-between items-start p-5 rounded-t border-b dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 lg:text-2xl dark:text-white">
                        Unable to exit ?
                    </h3>
                    <button type="button" v-on:click="close" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="need-help">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        Please select station to continue ...
                    </p>

                    <div class="my-6">
                        <label class="c-label">Select Station</label>
                        <select class="c-select" v-model="station_id" v-on:change="getGraInfo">
                            <option disabled value="">Select station</option>
                            <option
                                v-for="{id, stn_id, stn_name} in stations"
                                :key="id"
                                :value="stn_id">
                                {{ stn_name }}
                            </option>
                        </select>
                    </div>

                    <div class="bg-gray-200 rounded border p-3" v-if="gra.penalty">

                        <div v-if="gra.penaltyNames || gra.penaltyAmount">
                            <div v-if="showPenaltyNames" class="grid grid-cols-1">
                                <div>
                                    {{ gra.penaltyNames }}
                                </div>
                            </div>
                            <div v-if = showTravelPenalty class="grid grid-cols-1">
                                <div>
                                    {{ gra.TravelpenaltyName }}
                                </div>
                            </div>
                            <div class="grid grid-cols-1">
                                <div class="text-gray-900 font-bold">
                                  Total Amount Charged =  ₹{{ gra.totalAmount }}
                                </div>

                            </div>
                            <div class="grid grid-cols-1">
                                <div class="text-gray-900 font-bold">
                                   Generate new QR Code for Exit
                                </div>

                            </div>
                        </div>


                        <div v-else>
                            <div class="bg-gray-200 rounded border p-3 text-gray-900 font-bold text-center">
                                No Penalties found !
                            </div>
                        </div>

                    </div>

                </div>

                <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600">

                    <Button
                        v-if="gra.totalAmount > 0"
                        :is-loading="isGraButtonLoading"
                        :is-disabled="isGraButtonLoading"
                        :title="'Pay  '+'  ₹' + gra.totalAmount"
                        :type="'button'"
                        v-on:click="applyGra"
                    />

                    <Button
                        :is-loading="false"
                        :is-disabled="false"
                        :title="'Decline'"
                        :type="'button'"
                        v-on:click="close"
                    />

                </div>
            </div>

        </div>
    </div>
</template>

<script>
import axios from "axios";
import Button from "../Component/Button";
import {Inertia} from "@inertiajs/inertia";

export default {
    components: {Button},
    props: {
        order_id: String,
        slave_id: String,
        stations: Array
    },
    name: "NeedHelpModel",
    data() {
        return {
            status: Boolean,
            error: String,
            showHelp: true,
            showRefund: false,
            showGra: false,
            isRefundButtonLoading: false,
            isGraButtonLoading: false,
            station_id: '',
            showTravelPenalty : false,
            showPenaltyNames : false,
            refund: {
                order_id: null,
                processing_fee: null,
                processing_fee_amount: null,
                refund_amount: null,
                pass_price: null,
            },
            gra: {
                penalty: null,
                penaltyNames: null,
                TravelpenaltyName : null,
                TravelPenalty : null,
                penaltyAmount: null,
                totalAmount : null
            }
        }
    },
    methods: {

        close: function () {
            this.showRefund = false
            this.showGra = false
            this.showHelp = true
            toggleModal('need-help', false)
        },

        getRefundInfo: async function () {
            this.isRefundButtonLoading = true
            const res = await axios.get('/refund/' + this.order_id)
            const data = await res.data
            if (data.status) {
                this.showHelp = false
                this.showRefund = true
                this.refund = data.refund
                this.isRefundButtonLoading = false
            } else {
                this.isRefundButtonLoading = false
                this.close()
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.error,
                })
            }
        },

        refundTicket: async function () {
            this.isRefundButtonLoading = true
            const res = await axios.get('/refund/ticket/' + this.order_id)
            const data = await res.data
            if (data.status) {
                this.isRefundButtonLoading = false
                this.close()
                this.$swal.fire(
                    'Refunded Successfully !',
                )
                this.$inertia.visit('/products')
            } else {
                this.isRefundButtonLoading = false
                this.close()
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.error,
                })
            }
        },

        getGra: function () {
            this.station_id = '';
            this.showGra = true
            this.gra.penalty = false
            this.gra.totalAmount = 0
            this.showRefund = false
            this.showHelp = false
        },

        getGraInfo: async function () {

            this.gra.penaltyAmount = 0;
            this.gra.TravelPenalty = 0;
            this.gra.totalAmount = 0;
            this.showTravelPenalty = false;
            this.showPenaltyNames = false;
            const res = await axios.get('/gra/' + this.slave_id + "/" + this.station_id )
            const data = await res.data;
            console.log(data);
            if (data.status) {

                this.gra.penalty = data.data
                const {penalties, overTravelCharges} = data.data

                if (penalties.length && overTravelCharges.length)
                {


                    penalties.forEach((penalty) => {
                        //this.gra.penaltyAmount += penalty.amount
                        this.gra.TravelPenalty += penalty.amount
                        if (this.gra.TravelPenalty !== 0){
                            this.showTravelPenalty = true
                            this.gra.TravelpenaltyName = '• Over Time Penalty = ₹' + this.gra.TravelPenalty

                        }

                    })
                    overTravelCharges.forEach((overTravelCharge) => {

                        this.gra.penaltyAmount += overTravelCharge.amount
                        if(this.gra.penaltyAmount !== 0 ){
                            this.showPenaltyNames = true;
                            this.gra.penaltyNames = '• Fare Difference = ₹' + this.gra.penaltyAmount
                        }




                    })
                    this.gra.totalAmount = this.gra.penaltyAmount + this.gra.TravelPenalty ;



                }
                else if (penalties.length)
                {

                    penalties.forEach((penalty) => {
                        this.gra.penaltyAmount += penalty.amount
                    })
                    this.showPenaltyNames = true;
                    this.gra.penaltyNames = '• Over Time Penalty = ₹' + this.gra.penaltyAmount
                    this.gra.totalAmount = this.gra.penaltyAmount


                }
                else if (overTravelCharges.length)
                {

                    overTravelCharges.forEach((overTravelCharge) => {
                        this.gra.penaltyAmount += overTravelCharge.amount
                    })
                    this.showPenaltyNames = true;
                    this.gra.penaltyNames = '• Fare Difference = ₹'+this.gra.penaltyAmount
                    this.gra.totalAmount = this.gra.penaltyAmount


                }
                else
                {
                    this.gra.penaltyNames = null
                    this.gra.penaltyAmount = null
                }
            }
            else {
                this.close()
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.error,
                })
            }
        },

        applyGra: async function () {
            this.isGraButtonLoading = true
            const res = await axios.post('/gra', {
                penaltyInfo: this.gra.penalty,
                station_id: this.station_id
            })
            const {error, status, redirectUrl} = await res.data
            if (status) {
                this.isGraButtonLoading = false
                this.close()

                window.location.href = redirectUrl
            } else {
                this.isGraButtonLoading = false
                this.close()
                this.$swal.fire({
                    icon: 'error',
                    title: error,
                    text: error,
                })
            }
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
    },


    mounted() {

        this.ticketStatus();

    }

}
</script>

<style scoped>

</style>

































