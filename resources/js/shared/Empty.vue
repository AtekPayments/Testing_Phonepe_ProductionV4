<template>
    <div class="m-2 h-52 border rounded-lg p-3 shadow-lg bg-white">
        <div class="h-full w-full flex">
            <div class="my-auto mx-auto">
                <img :src="image" class="h-16 w-auto ml-5 items-center animate-bounce"  alt=""/>
                <div class="text-sm font-bold text-gray-600">Nothing to show ...</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Empty",
    data() {
        return {
            image: '/img/nothing.png'
        }
    }
}
</script>

<style scoped>

</style>
