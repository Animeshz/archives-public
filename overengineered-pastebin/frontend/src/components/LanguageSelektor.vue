<script setup>
const layouts = [
"curvy-1-pseudoalphabet",
"inverted_pseudoalphabet",
"stroked_pseudoalphabet",
"small-caps-pseudoalphabet",
"rock-dots-pseudoalphabet",
"math-fraktur-pseudoalphabet",
"faux-ethiopic-pseudoalphabet",
"faux-cyrillic-pseudoalphabet",
"curvy-3-pseudoalphabet",
"curvy-2-pseudoalphabet",
"cjk+thai-pseudoalphabet",
"a-cute-pseudoalphabet",
"squared-neg",
"squared",
"parenthesized",
"math-sans-italic",
"math-sans-bold-italic",
"math-sans-bold",
"math-sans",
"math-monospace",
"math-double-struck",
"math-bold-script",
"math-bold-italic",
"math-bold-fraktur",
"math-bold",
"fullwidth",
"circled-neg",
"circled",
"superscript",
];
</script>

<template>
    <div style="margin-top: 50px; display:flex;flex-wrap:wrap;">
        <div style="flex: 1 1 0; padding:15px; background:linear-gradient(60deg, rgba(84,58,183,1) 0%, rgba(0,172,193,1) 100%); margin: 10px; border-radius: 10px;" v-for="layout in layouts" onclick="window.setLayout(event.target.innerText)">{{layout}}</div>
    </div>
</template>
