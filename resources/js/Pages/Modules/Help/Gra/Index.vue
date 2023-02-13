<template>

    <nav-bar />
    <hero />

    <div class="m-2 bg-white rounded border p-3">

        <div class="bg-gray-200 rounded border p-3 text-gray-900 font-bold text-center">
            Select on which station you are to get information about gate rejection.
        </div>

        <div class="my-6">
            <label class="c-label">Select Station</label>
            <select class="c-select" v-model="station_id" v-on:change="getPenalties">
                <option disabled value="">Select station</option>
                <option
                    v-for="{id, stn_id, stn_name} in stations"
                    :key="id"
                    :value="stn_id">
                    {{ stn_name }}
                </option>
            </select>
        </div>

        <div class="bg-gray-200 rounded border p-3" v-if="penalty">

            <div v-if="penaltyNames || penaltyAmount">
                <div class="grid grid-cols-2">
                    <div class="text-gray-900 font-bold">
                        Penalties
                    </div>
                    <div>
                        {{ penaltyNames }}
                    </div>
                </div>
                <div class="grid grid-cols-2">
                    <div class="text-gray-900 font-bold">
                        Penalty Amount
                    </div>
                    <div>
                        ₹ {{ penaltyAmount }}
                    </div>
                </div>
                <div class="grid grid-cols-2">
                    <div class="text-gray-900 font-bold">
                       Generate new QR code for exit
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

    <Button
        v-if="penaltyNames || penaltyAmount"
        :title="'Proceed to Pay ₹ ' + penaltyAmount"
        v-on:click="genPenalty"
    />

</template>

<script>

import NavBar from "../../../../shared/NavBar";
import Hero from "../../../../shared/Hero";
import axios from "axios";
import AnchorButton from "../../../../shared/Components/AnchorButton";
import Button from "../../../../shared/Components/Button";

export default {

    props:{
        stations: Array,
        slave_id: String,
    },

    data() {
        return {
            penalty: null,
            penaltyNames: null,
            penaltyAmount: null,
            station_id: ''
        }
    },

    name: "Index",

    components: {Button, AnchorButton, Hero, NavBar},

    methods:{
        getPenalties: async function () {

            const res = await axios.get('/gra/' + this.slave_id + "/" + this.station_id )
            const data = await res.data;

            if (data.status)
            {

                this.penalty = data.data
                const {penalties, overTravelCharges} = data.data

                if (penalties.length && overTravelCharges.length)
                {
                    this.penaltyNames = 'Penalty + Over Travel'

                    penalties.forEach((penalty) => {
                        this.penaltyAmount += penalty.amount
                    })
                    overTravelCharges.forEach((overTravelCharge) => {
                        this.penaltyAmount += overTravelCharge.amount
                    })
                }
                else if (penalties.length)
                {
                    this.penaltyNames = 'Penalty'

                    penalties.forEach((penalty) => {
                        this.penaltyAmount += penalty.amount
                    })
                }
                else if (overTravelCharges.length)
                {
                    this.penaltyNames = 'Over Travel'

                    overTravelCharges.forEach((overTravelCharge) => {
                        this.penaltyAmount += overTravelCharge.amount
                    })
                }
                else
                {
                    this.penaltyNames = null
                    this.penaltyAmount = null
                }
            }
        },
        genPenalty: function () {
            this.$inertia.post('/gra', {
                penaltyInfo: this.penalty,
                station_id: this.station_id
            })
        }
    }

}
</script>

<style scoped>

</style>
