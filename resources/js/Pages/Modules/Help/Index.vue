<template>
    <nav-bar />
    <hero />
    <div class="relative rounded-lg m-2 bg-white">
        <div class="grid w-full content-center p-7" >
            <img :src="help" alt="..." class="h-24 mx-auto">
        </div>
    </div>
    <div class="grid">
        <div class="mx-auto flex items-center row-span-2">
            <div class="text-center">
                <div class="text-center grid grid-rows-2">

                    <a :href="'/refund/' + order_id + '/info'" class="text-gray-50 w-64 m-2 bg-blue-500 rounded-3xl py-3 text-lg font-bold hover:bg-blue-700">
                        <i class="fa-solid fa-landmark mx-1"></i>
                        Refund Ticket
                    </a>

                    <a :href="'/gra/' + slave_id" class="text-gray-50 w-64 m-2 bg-blue-500 rounded-3xl py-3 text-lg font-bold hover:bg-blue-700">
                        <i class="fa-solid fa-person-running mx-1"></i>
                        Unable to exit station
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NavBar from "../../../shared/NavBar";
import Hero from "../../../shared/Hero";
export default {
    props: {
        slave_id: String,
        order_id: String
    },
    data() {
        return {
            help: '/img/help.png'
        }
    },
    name: "Index",
    components: {Hero, NavBar},
}
</script>

<style scoped>

</style>
