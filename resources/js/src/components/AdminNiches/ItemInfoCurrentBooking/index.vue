<template>
<div class="item-current-booking">
    <div class="title-info">
        {{title}}
    </div>
    <div class="value-info">
        {{value}}
    </div>
</div>
</template>

<script>
export default {
    name: "ItemInfoCurrentBooking",
    props: {
        title: {
            type: String,
            default: "",
        },
        value: {
            type: String,
            default: "",
        },

    },
    data() {
        return {

        }
    },

}
</script>
