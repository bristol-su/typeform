<template>
    <div>
        {{castedValue}}
    </div>
</template>

<script>
    export default {
        name: "CellChoice",

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
            castedValue() {
                return this.value.label;
            }
        }
    }
</script>

<style scoped>

</style>