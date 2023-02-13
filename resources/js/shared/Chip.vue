<template>
    <span class="px-2 py-1 border rounded-full bg-gray-200 text-gray-700 font-medium text-center hover:bg-gray-300 cursor-pointer">
        <span class="flex-initial text-xsl">{{ title }}</span>
    </span>
</template>

<script>
export default {
    props: {
        title: String
    },
    name: "Chip"
}
</script>

<style scoped>

</style>
