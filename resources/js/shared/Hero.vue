<template>
    <div class="relative m-2 rounded-lg bg-white">
        <div class="grid grid-cols-3 divide-x p-5">
            <div class="col-span-2">
                <img :src="logo" alt="..." />
            </div>

            <div class="flex items-end justify-center">
                <img :src="ateklogo" alt="..." />
            </div>
        </div>
    </div>

</template>

<script>
export default {
    name: "Hero",
    data() {
        return {

            logo: '/img/logo_short.png',
            ateklogo : '/img/atek_logo.png',

        }

    }
}
</script>

<style scoped>

    hr {
        transform: rotate(90deg);
    }
</style>

<!--
<div class="relative rounded-lg m-2 bg-white">
    <div class="grid w-full content-center p-7" >
        <img :src="logo" alt="..." class="h-24 mx-auto">
    </div>
</div>-->

<!--<div class="grid w-full content-center p-7" >
              <img :src="logo" alt="..." class="h-24 mx-auto">
          </div>
          <div class="grid w-full content-center p-7">
              <img :src="ateklogo" alt="..." class="h-24 mx-auto" >
          </div>-->
