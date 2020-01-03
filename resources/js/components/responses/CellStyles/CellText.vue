<template>
    <div>
        {{value}}
    </div>
</template>

<script>
    export default {
        name: "CellText",

        props: {
            value: {
                required: true,
                default: null
            }
        },

    }
</script>

<style scoped>

</style>