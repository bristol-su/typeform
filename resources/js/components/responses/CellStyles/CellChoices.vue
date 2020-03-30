<template>
    <div>
        {{implodedValue}}
    </div>
</template>

<script>
    export default {
        name: "CellChoices",

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
            implodedValue() {
                return this.value.labels.join(', ');
            }
        }
    }
</script>

<style scoped>

</style>