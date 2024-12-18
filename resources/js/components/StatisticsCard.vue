<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="">
                <div class="card pointer" @click="goToPage()">
                    <div class="card-header bg-danger text-uppercase fs-2 text-white text-center">
                        {{ value }}
                    </div>

                    <div class="card-body bg-white text-uppercase fs-6 text-center">
                        {{ title }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script> 
    export default {
        props: [
            "title",
            "value",
            "user_role",
            "url"
        ],

        methods: {
            goToPage() {
                if(!this.url){return}
                window.location = `${this.url.toLowerCase()}`
            }
        },

        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style>
.pointer{
    cursor: pointer;
}
</style>
