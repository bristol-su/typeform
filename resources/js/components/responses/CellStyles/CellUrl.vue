<template>
    <div>
        <a :href="value">{{value}}</a>
    </div>
</template>

<script>
    export default {
        name: "CellUrl",

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