<template>
    <div class="h-screen grid grid-rows-1">
        <div class="mx-auto flex items-center">
            <div class="text-center p-10">
                <p class="text-gray-600 font-bold">Redirecting to payment page please wait ...</p>
                <Spinner />
            </div>
        </div>
    </div>
</template>

<script>
import Spinner from "../../../shared/Components/Spinner";
import axios from "axios";
export default {

    name: "Payment",

    components: {Spinner},

    props: {
        order: String
    },

    async mounted() {

        const res = await axios.get("/payment/init/" + this.order)
        const data = res.data
        if (data.status) this.onSuccess(data)
        else this.onFailure(data)

    },

    methods: {

        onSuccess: function (response) {
            const {redirectUrl: url} = response
            window.location.replace = url
        },
        onFailure: function (response) {
            this.$swal.fire({
                icon: 'error',
                title: 'Payment Failed !',
                text: response.error,
                confirmButtonText: 'Try Again',
                showDenyButton: true,
                denyButtonText: 'Go Home !'
            }).then((res) => {
                if (res.isConfirmed) {
                    this.$inertia.get('/payment/init/' + this.order)
                } else if (res.isDenied) {
                    this.$inertia.get('/payment/failed/' + this.order)
                }
            })
        }
    }

}
</script>

<style scoped>

</style>
