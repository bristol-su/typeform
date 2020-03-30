<template>
    <div>
        <a :href="'mailto:' + value">{{value}}</a>
    </div>
</template>

<script>
    export default {
        name: "CellEmail",

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