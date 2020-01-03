<template>
    <div>
        {{value}}
    </div>
</template>

<script>
    export default {
        name: "CellNumber",

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
                if(this.value.isNumber) {
                    return this.value;
                }
                return this.value.toInteger;
            }
        }
    }
</script>

<style scoped>

</style>