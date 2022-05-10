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
            }
        },

        data() {
            return {}
        },

        methods: {},

        computed: {
            url() {
                return this.$tools.routes.query.addQueryStringToWebUrl(this.$tools.routes.module.moduleUrl() + '/file/' + this.value + '/download');
            }
        }
    }
</script>

<style scoped>

</style>
