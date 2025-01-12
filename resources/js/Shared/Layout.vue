<!-- resources/js/Shared/Layout.vue -->
<template>
    <div>
        <header>
            <h1>Band Name Picker</h1>
        </header>
        <main>
            <slot></slot>
        </main>
        <footer>
            <p>&copy; 2025 Band Name Picker</p>
        </footer>
    </div>
</template>

<script>
export default {
    name: 'Layout',
};
</script>

<style scoped>
header, footer {
    background-color: #f8f9fa;
    padding: 10px;
    text-align: center;
}
main {
    padding: 20px;
}
</style>
