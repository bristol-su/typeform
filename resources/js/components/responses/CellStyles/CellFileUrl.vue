<template>
    <div>
        <a :href="url">Download</a>
    </div>
</template>

<script>
    export default {
        name: "CellFileUrl",

        props: {
            value: {
                required: true,
                default: null
            },
            queryString: {
                required: true,
                type: String
            }
        },

        data() {
            return {}
        },

        methods: {},

        computed: {
            url() {
                return 'typeform/file/' + this.value + '/download?' + this.queryString;
            }
        }
    }
</script>

<style scoped>

</style>