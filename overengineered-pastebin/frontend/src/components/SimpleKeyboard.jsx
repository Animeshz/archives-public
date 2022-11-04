import React, { useRef, useState, useEffect } from "react";
import ReactDOM from "react-dom";
import Keyboard from "react-simple-keyboard";
import "react-simple-keyboard/build/css/index.css";
import "./SimpleKeyboard.css"

function App() {
  const [input, setInput] = useState("");
  const [layoutName, setLayoutName] = useState("english");
  const [layoutStyle, setLayout] = useState("default");
  const keyboard = useRef();

  const onChange = input => {
    setInput(input);
    console.log("Input changed", input);
  };

  const handleShift = () => {
    const newLayoutStyle = layoutStyle === "default" ? "shift" : "default";
    setLayout(newLayoutStyle);
  };

  const onKeyPress = button => {
    console.log("Button pressed", button);

    /**
     * If you want to handle the shift and caps lock buttons
     */
    if (button === "{shift}" || button === "{lock}") handleShift();
  };

  const onChangeInput = event => {
    const input = event.target.value;
    setInput(input);
    keyboard.current.setInput(input);
  };

  const ip = document.getElementById('textarea');
  ip.value = input;
  ip.addEventListener("change", onChangeInput);

  const layout = {
    'english-default': [
      '` 1 2 3 4 5 6 7 8 9 0 - = {bksp}',
      '{tab} q w e r t y u i o p [ ] \\',
      '{lock} a s d f g h j k l ; \' {enter}',
      '{shift} z x c v b n m , . / {shift}',
      '.com @ {space}'
    ],
    'english-shift': [
      '~ ! @ # $ % ^ &amp; * ( ) _ + {bksp}',
      '{tab} Q W E R T Y U I O P { } |',
      '{lock} A S D F G H J K L : " {enter}',
      '{shift} Z X C V B N M &lt; &gt; ? {shift}',
      '.com @ {space}'
    ],
    'superscript-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ ⁻ ⁼ {bksp}',
      '{tab} ᵠ ʷ ᵉ ʳ ᵗ ʸ ᵘ ⁱ ᵒ ᵖ [ ] \\',
      '{lock} ᵃ ˢ ᵈ ᶠ ᵍ ʰ ʲ ᵏ ˡ ; \' {enter}',
      '{shift} ᶻ ˣ ᶜ ᵛ ᵇ ⁿ ᵐ , . / {shift}',
      '.com @ {space}'
    ],
    'superscript-shift': [
      '~ ! @ # $ % ^ & * ⁽ ⁾ _ ⁺ {bksp}',
      '{tab} ᵠ ᵂ ᴱ ᴿ ᵀ ʸ ᵁ ᴵ ᴼ ᴾ { } |',
      '{lock} ᴬ ˢ ᴰ ᶠ ᴳ ᴴ ᴶ ᴷ ᴸ : " {enter}',
      '{shift} ᶻ ˣ ᶜ ⱽ ᴮ ᴺ ᴹ < > ? {shift}',
      '.com @ {space}'
    ],
    'circled-default': [
      '` " ① ② ③ ④ ⑤ ⑥ ⑦ ⑧ ⑨ ⓪ - = {bksp}',
      '{tab} ⓠ ⓦ ⓔ ⓡ ⓣ ⓨ ⓤ ⓘ ⓞ ⓟ [ ] \\',
      '{lock} ⓐ ⓢ ⓓ ⓕ ⓖ ⓗ ⓙ ⓚ ⓛ ; \' {enter}',
      '{shift} ⓩ ⓧ ⓒ ⓥ ⓑ ⓝ ⓜ , . / {shift}',
      '.com @ {space}'
    ],
    'circled-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} Ⓠ Ⓦ Ⓔ Ⓡ Ⓣ Ⓨ Ⓤ Ⓘ Ⓞ Ⓟ { } |',
      '{lock} Ⓐ Ⓢ Ⓓ Ⓕ Ⓖ Ⓗ Ⓙ Ⓚ Ⓛ : " {enter}',
      '{shift} Ⓩ Ⓧ Ⓒ Ⓥ Ⓑ Ⓝ Ⓜ < > ? {shift}',
      '.com @ {space}'
    ],
    'circled-neg-default': [
      '` " ❶ ❷ ❸ ❹ ❺ ❻ ❼ ❽ ❾ ⓿ - = {bksp}',
      '{tab} 🅠 🅦 🅔 🅡 🅣 🅨 🅤 🅘 🅞 🅟 [ ] \\',
      '{lock} 🅐 🅢 🅓 🅕 🅖 🅗 🅙 🅚 🅛 ; \' {enter}',
      '{shift} 🅩 🅧 🅒 🅥 🅑 🅝 🅜 , . / {shift}',
      '.com @ {space}'
    ],
    'circled-neg-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 🅠 🅦 🅔 🅡 🅣 🅨 🅤 🅘 🅞 🅟 { } |',
      '{lock} 🅐 🅢 🅓 🅕 🅖 🅗 🅙 🅚 🅛 : " {enter}',
      '{shift} 🅩 🅧 🅒 🅥 🅑 🅝 🅜 < > ? {shift}',
      '.com @ {space}'
    ],
    'fullwidth-default': [
      '｀ ＂ １ ２ ３ ４ ５ ６ ７ ８ ９ ０ － ＝ {bksp}',
      '{tab} ｑ ｗ ｅ ｒ ｔ ｙ ｕ ｉ ｏ ｐ ［ ］ ＼',
      '{lock} ａ ｓ ｄ ｆ ｇ ｈ ｊ ｋ ｌ ； \' {enter}',
      '{shift} ｚ ｘ ｃ ｖ ｂ ｎ ｍ ， ． ／ {shift}',
      '.com ＠ {space}'
    ],
    'fullwidth-shift': [
      '～ ！ ＠ ＃ ＄ ％ ＾ ＆ ＊ （ ） ＿ ＋ {bksp}',
      '{tab} Ｑ Ｗ Ｅ Ｒ Ｔ Ｙ Ｕ Ｉ Ｏ Ｐ ｛ ｝ |',
      '{lock} Ａ Ｓ Ｄ Ｆ Ｇ Ｈ Ｊ Ｋ Ｌ ： ＂ {enter}',
      '{shift} Ｚ Ｘ Ｃ Ｖ Ｂ Ｎ Ｍ ＜ ＞ ？ {shift}',
      '.com ＠ {space}'
    ],
    'math-bold-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝐪 𝐰 𝐞 𝐫 𝐭 𝐲 𝐮 𝐢 𝐨 𝐩 [ ] \\',
      '{lock} 𝐚 𝐬 𝐝 𝐟 𝐠 𝐡 𝐣 𝐤 𝐥 ; \' {enter}',
      '{shift} 𝐳 𝐱 𝐜 𝐯 𝐛 𝐧 𝐦 , . / {shift}',
      '.com @ {space}'
    ],
    'math-bold-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 𝐐 𝐖 𝐄 𝐑 𝐓 𝐘 𝐔 𝐈 𝐎 𝐏 { } |',
      '{lock} 𝐀 𝐒 𝐃 𝐅 𝐆 𝐇 𝐉 𝐊 𝐋 : " {enter}',
      '{shift} 𝐙 𝐗 𝐂 𝐕 𝐁 𝐍 𝐌 < > ? {shift}',
      '.com @ {space}'
    ],
    'math-bold-fraktur-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝖖 𝖜 𝖊 𝖗 𝖙 𝖞 𝖚 𝖎 𝖔 𝖕 [ ] \\',
      '{lock} 𝖆 𝖘 𝖉 𝖋 𝖌 𝖍 𝖏 𝖐 𝖑 ; \' {enter}',
      '{shift} 𝖟 𝖝 𝖈 𝖛 𝖇 𝖓 𝖒 , . / {shift}',
      '.com @ {space}'
    ],
    'math-bold-fraktur-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 𝕼 𝖂 𝕰 𝕽 𝕿 𝖄 𝖀 𝕴 𝕺 𝕻 { } |',
      '{lock} 𝕬 𝕾 𝕯 𝕱 𝕲 𝕳 𝕵 𝕶 𝕷 : " {enter}',
      '{shift} 𝖅 𝖃 𝕮 𝖁 𝕭 𝕹 𝕸 < > ? {shift}',
      '.com @ {space}'
    ],
    'math-bold-italic-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝒒 𝒘 𝒆 𝒓 𝒕 𝒚 𝒖 𝒊 𝒐 𝒑 [ ] \\',
      '{lock} 𝒂 𝒔 𝒅 𝒇 𝒈 𝒉 𝒋 𝒌 𝒍 ; \' {enter}',
      '{shift} 𝒛 𝒙 𝒄 𝒗 𝒃 𝒏 𝒎 , . / {shift}',
      '.com @ {space}'
    ],
    'math-bold-italic-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 𝑸 𝑾 𝑬 𝑹 𝑻 𝒀 𝑼 𝑰 𝑶 𝑷 { } |',
      '{lock} 𝑨 𝑺 𝑫 𝑭 𝑮 𝑯 𝑱 𝑲 𝑳 : " {enter}',
      '{shift} 𝒁 𝑿 𝑪 𝑽 𝑩 𝑵 𝑴 < > ? {shift}',
      '.com @ {space}'
    ],
    'math-bold-script-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝓺 𝔀 𝓮 𝓻 𝓽 𝔂 𝓾 𝓲 𝓸 𝓹 [ ] \\',
      '{lock} 𝓪 𝓼 𝓭 𝓯 𝓰 𝓱 𝓳 𝓴 𝓵 ; \' {enter}',
      '{shift} 𝔃 𝔁 𝓬 𝓿 𝓫 𝓷 𝓶 , . / {shift}',
      '.com @ {space}'
    ],
    'math-bold-script-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 𝓠 𝓦 𝓔 𝓡 𝓣 𝓨 𝓤 𝓘 𝓞 𝓟 { } |',
      '{lock} 𝓐 𝓢 𝓓 𝓕 𝓖 𝓗 𝓙 𝓚 𝓛 : " {enter}',
      '{shift} 𝓩 𝓧 𝓒 𝓥 𝓑 𝓝 𝓜 < > ? {shift}',
      '.com @ {space}'
    ],
    'math-double-struck-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝕢 𝕨 𝕖 𝕣 𝕥 𝕪 𝕦 𝕚 𝕠 𝕡 [ ] \\',
      '{lock} 𝕒 𝕤 𝕕 𝕗 𝕘 𝕙 𝕛 𝕜 𝕝 ; \' {enter}',
      '{shift} 𝕫 𝕩 𝕔 𝕧 𝕓 𝕟 𝕞 , . / {shift}',
      '.com @ {space}'
    ],
    'math-double-struck-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} ℚ 𝕎 𝔼 ℝ 𝕋 𝕐 𝕌 𝕀 𝕆 ℙ { } |',
      '{lock} 𝔸 𝕊 𝔻 𝔽 𝔾 ℍ 𝕁 𝕂 𝕃 : " {enter}',
      '{shift} ℤ 𝕏 ℂ 𝕍 𝔹 ℕ 𝕄 < > ? {shift}',
      '.com @ {space}'
    ],
    'math-monospace-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝚚 𝚠 𝚎 𝚛 𝚝 𝚢 𝚞 𝚒 𝚘 𝚙 [ ] \\',
      '{lock} 𝚊 𝚜 𝚍 𝚏 𝚐 𝚑 𝚓 𝚔 𝚕 ; \' {enter}',
      '{shift} 𝚣 𝚡 𝚌 𝚟 𝚋 𝚗 𝚖 , . / {shift}',
      '.com @ {space}'
    ],
    'math-monospace-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 𝚀 𝚆 𝙴 𝚁 𝚃 𝚈 𝚄 𝙸 𝙾 𝙿 { } |',
      '{lock} 𝙰 𝚂 𝙳 𝙵 𝙶 𝙷 𝙹 𝙺 𝙻 : " {enter}',
      '{shift} 𝚉 𝚇 𝙲 𝚅 𝙱 𝙽 𝙼 < > ? {shift}',
      '.com @ {space}'
    ],
    'math-sans-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝗊 𝗐 𝖾 𝗋 𝗍 𝗒 𝗎 𝗂 𝗈 𝗉 [ ] \\',
      '{lock} 𝖺 𝗌 𝖽 𝖿 𝗀 𝗁 𝗃 𝗄 𝗅 ; \' {enter}',
      '{shift} 𝗓 𝗑 𝖼 𝗏 𝖻 𝗇 𝗆 , . / {shift}',
      '.com @ {space}'
    ],
    'math-sans-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 𝖰 𝖶 𝖤 𝖱 𝖳 𝖸 𝖴 𝖨 𝖮 𝖯 { } |',
      '{lock} 𝖠 𝖲 𝖣 𝖥 𝖦 𝖧 𝖩 𝖪 𝖫 : " {enter}',
      '{shift} 𝖹 𝖷 𝖢 𝖵 𝖡 𝖭 𝖬 < > ? {shift}',
      '.com @ {space}'
    ],
    'math-sans-bold-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝗾 𝘄 𝗲 𝗿 𝘁 𝘆 𝘂 𝗶 𝗼 𝗽 [ ] \\',
      '{lock} 𝗮 𝘀 𝗱 𝗳 𝗴 𝗵 𝗷 𝗸 𝗹 ; \' {enter}',
      '{shift} 𝘇 𝘅 𝗰 𝘃 𝗯 𝗻 𝗺 , . / {shift}',
      '.com @ {space}'
    ],
    'math-sans-bold-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 𝗤 𝗪 𝗘 𝗥 𝗧 𝗬 𝗨 𝗜 𝗢 𝗣 { } |',
      '{lock} 𝗔 𝗦 𝗗 𝗙 𝗚 𝗛 𝗝 𝗞 𝗟 : " {enter}',
      '{shift} 𝗭 𝗫 𝗖 𝗩 𝗕 𝗡 𝗠 < > ? {shift}',
      '.com @ {space}'
    ],
    'math-sans-bold-italic-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝙦 𝙬 𝙚 𝙧 𝙩 𝙮 𝙪 𝙞 𝙤 𝙥 [ ] \\',
      '{lock} 𝙖 𝙨 𝙙 𝙛 𝙜 𝙝 𝙟 𝙠 𝙡 ; \' {enter}',
      '{shift} 𝙯 𝙭 𝙘 𝙫 𝙗 𝙣 𝙢 , . / {shift}',
      '.com @ {space}'
    ],
    'math-sans-bold-italic-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 𝙌 𝙒 𝙀 𝙍 𝙏 𝙔 𝙐 𝙄 𝙊 𝙋 { } |',
      '{lock} 𝘼 𝙎 𝘿 𝙁 𝙂 𝙃 𝙅 𝙆 𝙇 : " {enter}',
      '{shift} 𝙕 𝙓 𝘾 𝙑 𝘽 𝙉 𝙈 < > ? {shift}',
      '.com @ {space}'
    ],
    'math-sans-italic-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝘲 𝘸 𝘦 𝘳 𝘵 𝘺 𝘶 𝘪 𝘰 𝘱 [ ] \\',
      '{lock} 𝘢 𝘴 𝘥 𝘧 𝘨 𝘩 𝘫 𝘬 𝘭 ; \' {enter}',
      '{shift} 𝘻 𝘹 𝘤 𝘷 𝘣 𝘯 𝘮 , . / {shift}',
      '.com @ {space}'
    ],
    'math-sans-italic-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 𝘘 𝘞 𝘌 𝘙 𝘛 𝘠 𝘜 𝘐 𝘖 𝘗 { } |',
      '{lock} 𝘈 𝘚 𝘋 𝘍 𝘎 𝘏 𝘑 𝘒 𝘓 : " {enter}',
      '{shift} 𝘡 𝘟 𝘊 𝘝 𝘉 𝘕 𝘔 < > ? {shift}',
      '.com @ {space}'
    ],
    'parenthesized-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} ⒬ ⒲ ⒠ ⒭ ⒯ ⒴ ⒰ ⒤ ⒪ ⒫ [ ] \\',
      '{lock} ⒜ ⒮ ⒟ ⒡ ⒢ ⒣ ⒥ ⒦ ⒧ ; \' {enter}',
      '{shift} ⒵ ⒳ ⒞ ⒱ ⒝ ⒩ ⒨ , . / {shift}',
      '.com @ {space}'
    ],
    'parenthesized-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} ⒬ ⒲ ⒠ ⒭ ⒯ ⒴ ⒰ ⒤ ⒪ ⒫ { } |',
      '{lock} ⒜ ⒮ ⒟ ⒡ ⒢ ⒣ ⒥ ⒦ ⒧ : " {enter}',
      '{shift} ⒵ ⒳ ⒞ ⒱ ⒝ ⒩ ⒨ < > ? {shift}',
      '.com @ {space}'
    ],
    'squared-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 🅀 🅆 🄴 🅁 🅃 🅈 🅄 🄸 🄾 🄿 [ ] \\',
      '{lock} 🄰 🅂 🄳 🄵 🄶 🄷 🄹 🄺 🄻 ; \' {enter}',
      '{shift} 🅉 🅇 🄲 🅅 🄱 🄽 🄼 , . / {shift}',
      '.com @ {space}'
    ],
    'squared-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 🅀 🅆 🄴 🅁 🅃 🅈 🅄 🄸 🄾 🄿 { } |',
      '{lock} 🄰 🅂 🄳 🄵 🄶 🄷 🄹 🄺 🄻 : " {enter}',
      '{shift} 🅉 🅇 🄲 🅅 🄱 🄽 🄼 < > ? {shift}',
      '.com @ {space}'
    ],
    'squared-neg-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 🆀 🆆 🅴 🆁 🆃 🆈 🆄 🅸 🅾 🅿 [ ] \\',
      '{lock} 🅰 🆂 🅳 🅵 🅶 🅷 🅹 🅺 🅻 ; \' {enter}',
      '{shift} 🆉 🆇 🅲 🆅 🅱 🅽 🅼 , . / {shift}',
      '.com @ {space}'
    ],
    'squared-neg-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 🆀 🆆 🅴 🆁 🆃 🆈 🆄 🅸 🅾 🅿 { } |',
      '{lock} 🅰 🆂 🅳 🅵 🅶 🅷 🅹 🅺 🅻 : " {enter}',
      '{shift} 🆉 🆇 🅲 🆅 🅱 🅽 🅼 < > ? {shift}',
      '.com @ {space}'
    ],
    'a-cute-pseudoalphabet-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} q ẃ é ŕ t ӳ ú í ő ṕ [ ] \\',
      '{lock} á ś d f ǵ h j ḱ ĺ ; \' {enter}',
      '{shift} ź x ć v b ń ḿ , . / {shift}',
      '.com @ {space}'
    ],
    'a-cute-pseudoalphabet-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} Q Ẃ É Ŕ T Ӳ Ű í Ő Ṕ { } |',
      '{lock} Á ś D F Ǵ H J Ḱ Ĺ : " {enter}',
      '{shift} Ź X Ć V B Ń Ḿ < > ? {shift}',
      '.com @ {space}'
    ],
    'cjk+thai-pseudoalphabet-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} q w 乇 尺 ｲ ﾘ u ﾉ o ｱ [ ] \\',
      '{lock} ﾑ 丂 d ｷ g ん ﾌ ズ ﾚ ; \' {enter}',
      '{shift} 乙 ﾒ c √ 乃 刀 ﾶ , . / {shift}',
      '.com @ {space}'
    ],
    'cjk+thai-pseudoalphabet-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} q w 乇 尺 ｲ ﾘ u ﾉ o ｱ { } |',
      '{lock} ﾑ 丂 d ｷ g ん ﾌ ズ ﾚ : " {enter}',
      '{shift} 乙 ﾒ c √ 乃 刀 ﾶ < > ? {shift}',
      '.com @ {space}'
    ],
    'curvy-2-pseudoalphabet-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} ۹ ω є я т у υ ι σ ρ [ ] \\',
      '{lock} α ѕ ∂ ƒ ﻭ н נ к ℓ ; \' {enter}',
      '{shift} չ χ ¢ ν в η м , . / {shift}',
      '.com @ {space}'
    ],
    'curvy-2-pseudoalphabet-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} ۹ ω є я т у υ ι σ ρ { } |',
      '{lock} α ѕ ∂ ƒ ﻭ н נ к ℓ : " {enter}',
      '{shift} չ χ ¢ ν в η м < > ? {shift}',
      '.com @ {space}'
    ],
    'curvy-3-pseudoalphabet-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} ợ ฬ є г Շ ץ ย เ ๏ ק [ ] \\',
      '{lock} ค ร ๔ Ŧ ﻮ ђ ן к ɭ ; \' {enter}',
      '{shift} չ א ς ש ๒ ภ ๓ , . / {shift}',
      '.com @ {space}'
    ],
    'curvy-3-pseudoalphabet-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} ợ ฬ є г Շ ץ ย เ ๏ ק { } |',
      '{lock} ค ร ๔ Ŧ ﻮ ђ ן к ɭ : " {enter}',
      '{shift} չ א ς ש ๒ ภ ๓ < > ? {shift}',
      '.com @ {space}'
    ],
    'faux-cyrillic-pseudoalphabet-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} q ш э ѓ т Ў ц і о р [ ] \\',
      '{lock} а ѕ ↁ f Б Ђ ј к l ; \' {enter}',
      '{shift} z х с v ъ и м , . / {shift}',
      '.com @ {space}'
    ],
    'faux-cyrillic-pseudoalphabet-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} Q Щ Є Я Г Ч Ц І Ф Р { } |',
      '{lock} Д Ѕ ↁ F Б Н Ј Ќ L : " {enter}',
      '{shift} Z Ж Ҁ V Б И М < > ? {shift}',
      '.com @ {space}'
    ],
    'faux-ethiopic-pseudoalphabet-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} ዒ ሠ ቿ ዪ ፕ ሃ ሁ ጎ ዐ የ [ ] \\',
      '{lock} ል ነ ዕ ቻ ኗ ዘ ጋ ጕ ረ ; \' {enter}',
      '{shift} ጊ ሸ ር ሀ ጌ ክ ጠ , . / {shift}',
      '.com @ {space}'
    ],
    'faux-ethiopic-pseudoalphabet-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} ዒ ሠ ቿ ዪ ፕ ሃ ሁ ጎ ዐ የ { } |',
      '{lock} ል ነ ዕ ቻ ኗ ዘ ጋ ጕ ረ : " {enter}',
      '{shift} ጊ ሸ ር ሀ ጌ ክ ጠ < > ? {shift}',
      '.com @ {space}'
    ],
    'math-fraktur-pseudoalphabet-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} 𝔮 𝔴 𝔢 𝔯 𝔱 𝔶 𝔲 𝔦 𝔬 𝔭 [ ] \\',
      '{lock} 𝔞 𝔰 𝔡 𝔣 𝔤 𝔥 𝔧 𝔨 𝔩 ; \' {enter}',
      '{shift} 𝔷 𝔵 𝔠 𝔳 𝔟 𝔫 𝔪 , . / {shift}',
      '.com @ {space}'
    ],
    'math-fraktur-pseudoalphabet-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} 𝔔 𝔚 𝔈 ℜ 𝔗 𝔜 𝔘 ℑ 𝔒 𝔓 { } |',
      '{lock} 𝔄 𝔖 𝔇 𝔉 𝔊 ℌ 𝔍 𝔎 𝔏 : " {enter}',
      '{shift} ℨ 𝔛 ℭ 𝔙 𝔅 𝔑 𝔐 < > ? {shift}',
      '.com @ {space}'
    ],
    'rock-dots-pseudoalphabet-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} q ẅ ë ṛ ẗ ÿ ü ï ö ṗ [ ] \\',
      '{lock} ä ṡ ḋ ḟ ġ ḧ j ḳ ḷ ; \' {enter}',
      '{shift} ż ẍ ċ ṿ ḅ ṅ ṁ , . / {shift}',
      '.com @ {space}'
    ],
    'rock-dots-pseudoalphabet-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} Q Ẅ Ё Ṛ Ṫ Ÿ Ü Ї Ö Ṗ { } |',
      '{lock} Ä Ṡ Ḋ Ḟ Ġ Ḧ J Ḳ Ḷ : " {enter}',
      '{shift} Ż Ẍ Ċ Ṿ Ḅ Ṅ Ṁ < > ? {shift}',
      '.com @ {space}'
    ],
    'small-caps-pseudoalphabet-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} q ᴡ ᴇ ʀ ᴛ y ᴜ ɪ ᴏ ᴩ [ ] \\',
      '{lock} ᴀ ꜱ ᴅ ꜰ ɢ ʜ ᴊ ᴋ ʟ ; \' {enter}',
      '{shift} ᴢ x ᴄ ᴠ ʙ ɴ ᴍ , . / {shift}',
      '.com @ {space}'
    ],
    'small-caps-pseudoalphabet-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} Q ᴡ ᴇ ʀ ᴛ Y ᴜ ɪ ᴏ ᴩ { } |',
      '{lock} ᴀ ꜱ ᴅ ꜰ ɢ ʜ ᴊ ᴋ ʟ : " {enter}',
      '{shift} ᴢ x ᴄ ᴠ ʙ ɴ ᴍ < > ? {shift}',
      '.com @ {space}'
    ],
    'stroked_pseudoalphabet-default': [
      '` " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} ꝗ w ɇ ɍ ŧ ɏ ᵾ ɨ ø ᵽ [ ] \\',
      '{lock} Ⱥ s đ f ǥ ħ ɉ ꝁ ł ; \' {enter}',
      '{shift} ƶ x ȼ v ƀ n m , . / {shift}',
      '.com @ {space}'
    ],
    'stroked_pseudoalphabet-shift': [
      '~ ! @ # $ % ^ & * ( ) _ + {bksp}',
      '{tab} Ꝗ W Ɇ Ɍ Ŧ Ɏ ᵾ Ɨ Ø Ᵽ { } |',
      '{lock} Ⱥ S Đ F Ǥ Ħ Ɉ Ꝁ Ł : " {enter}',
      '{shift} Ƶ X Ȼ V Ƀ N M < > ? {shift}',
      '.com @ {space}'
    ],
    'inverted_pseudoalphabet-default': [
      'ɐ " ¹ ² ³ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁰ - = {bksp}',
      '{tab} ɹ x ɟ s n z ʌ ɾ d b \ ^ ]',
      '{lock} q ʇ ǝ ƃ ɥ ı ʞ ן ɯ ; \' {enter}',
      '{shift} { ʎ p ʍ ɔ o u , . / {shift}',
      '.com @ {space}'
    ],
    'inverted_pseudoalphabet-shift': [
      ' ! @ # $ % _ & * ( ) ` + {bksp}',
      '{tab} b ʍ ǝ ɹ ʇ ʎ n ı o d | ~ |',
      '{lock} ɐ s p ɟ ƃ ɥ ɾ ʞ ן : " {enter}',
      '{shift} [ x ɔ 𐌡 q u ɯ < > ? {shift}',
      '.com @ {space}'
    ],
  };
  window.setLayout = setLayoutName;

  useEffect(() => console.log(layoutName + '-' + layoutStyle), [layoutName, layoutStyle])
  return (
    <div className="App" style={{transform: "scale(0.98, 0.75) translateY(-33px)"}}>
      <Keyboard
        keyboardRef={r => (keyboard.current = r)}
        layoutName={layoutName + '-' + layoutStyle}
        layout={layout}
        onChange={onChange}
        onKeyPress={onKeyPress}
      />


    </div>
  );
}

export default App;
