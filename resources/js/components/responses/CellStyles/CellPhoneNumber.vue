<template>
    <div>
        {{value}}
    </div>
</template>

<script>
    export default {
        name: "CellPhoneNumber",

        props: {
            value: {
                required: true,
                default: null
            }
        },

        data() {
            return {}
        },

        methods: {},

        computed: {}
    }
</script>

<style scoped>

</style>