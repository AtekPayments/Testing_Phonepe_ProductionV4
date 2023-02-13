<template>
    <div class="p-2">
        <button
            type="submit"
            class="w-full py-3 bg-blue-500 border rounded-2xl text-gray-100 font-bold hover:bg-blue-700">
            {{ title }}
        </button>
    </div>
</template>

<script>
export default {

    props: {
      title: String,
    },

    name: "Button",

}
</script>

<style scoped>

</style>
