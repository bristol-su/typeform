<template>
    <div>
        {{(castedValue?'Yes':'No')}}
    </div>
</template>

<script>
    export default {
        name: "CellBoolean",

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
                if(this.value === false || this.value === 0 || this.value === '0') {
                    return false;
                }
                return true;
            }
        }
    }
</script>

<style scoped>

</style>