<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Utils;

class CommandHelpers
{
	static $unicodeTypes = [
		'Circled',
		'Circled (neg)',
		'Fullwidth',
		'Math bold',
		'Math bold Fraktur',
		'Math bold italic',
		'Math bold script',
		'Math double-struck',
		'Math monospace',
		'Math sans',
		'Math sans bold',
		'Math sans bold italic',
		'Math sans italic',
		'Parenthesized',
		'Squared',
		'Squared (neg)',
		'A-cute pseudoalphabet',
		'CJK+Thai pseudoalphabet',
		'Curvy 1 pseudoalphabet',
		'Curvy 2 pseudoalphabet',
		'Curvy 3 pseudoalphabet',
		'Faux Cyrillic pseudoalphabet',
		'Faux Ethiopic pseudoalphabet',
		'Math Fraktur pseudoalphabet',
		'Rock Dots pseudoalphabet',
		'Small Caps pseudoalphabet',
		'Stroked pseudoalphabet',
		'Inverted pseudoalphabet'
	];

	static $unicodeSamplesUC = [
		[
			'Ⓐ', 'Ⓑ', 'Ⓒ', 'Ⓓ', 'Ⓔ', 'Ⓕ', 'Ⓖ', 'Ⓗ', 'Ⓘ', 'Ⓙ', 'Ⓚ', 'Ⓛ', 'Ⓜ', 'Ⓝ', 'Ⓞ', 'Ⓟ', 'Ⓠ', 'Ⓡ', 'Ⓢ', 'Ⓣ', 'Ⓤ', 'Ⓥ', 'Ⓦ', 'Ⓧ', 'Ⓨ', 'Ⓩ'
		],
		[
			'🅐', '🅑', '🅒', '🅓', '🅔', '🅕', '🅖', '🅗', '🅘', '🅙', '🅚', '🅛', '🅜', '🅝', '🅞', '🅟', '🅠', '🅡', '🅢', '🅣', '🅤', '🅥', '🅦', '🅧', '🅨', '🅩'
		],
		[
			'Ａ', 'Ｂ', 'Ｃ', 'Ｄ', 'Ｅ', 'Ｆ', 'Ｇ', 'Ｈ', 'Ｉ', 'Ｊ', 'Ｋ', 'Ｌ', 'Ｍ', 'Ｎ', 'Ｏ', 'Ｐ', 'Ｑ', 'Ｒ', 'Ｓ', 'Ｔ', 'Ｕ', 'Ｖ', 'Ｗ', 'Ｘ', 'Ｙ', 'Ｚ'
		],
		[
			'𝐀', '𝐁', '𝐂', '𝐃', '𝐄', '𝐅', '𝐆', '𝐇', '𝐈', '𝐉', '𝐊', '𝐋', '𝐌', '𝐍', '𝐎', '𝐏', '𝐐', '𝐑', '𝐒', '𝐓', '𝐔', '𝐕', '𝐖', '𝐗', '𝐘', '𝐙'
		],
		[
			'𝕬', '𝕭', '𝕮', '𝕯', '𝕰', '𝕱', '𝕲', '𝕳', '𝕴', '𝕵', '𝕶', '𝕷', '𝕸', '𝕹', '𝕺', '𝕻', '𝕼', '𝕽', '𝕾', '𝕿', '𝖀', '𝖁', '𝖂', '𝖃', '𝖄', '𝖅'
		],
		[
			'𝑨', '𝑩', '𝑪', '𝑫', '𝑬', '𝑭', '𝑮', '𝑯', '𝑰', '𝑱', '𝑲', '𝑳', '𝑴', '𝑵', '𝑶', '𝑷', '𝑸', '𝑹', '𝑺', '𝑻', '𝑼', '𝑽', '𝑾', '𝑿', '𝒀', '𝒁'
		],
		[
			'𝓐', '𝓑', '𝓒', '𝓓', '𝓔', '𝓕', '𝓖', '𝓗', '𝓘', '𝓙', '𝓚', '𝓛', '𝓜', '𝓝', '𝓞', '𝓟', '𝓠', '𝓡', '𝓢', '𝓣', '𝓤', '𝓥', '𝓦', '𝓧', '𝓨', '𝓩'
		],
		[
			'𝔸', '𝔹', 'ℂ', '𝔻', '𝔼', '𝔽', '𝔾', 'ℍ', '𝕀', '𝕁', '𝕂', '𝕃', '𝕄', 'ℕ', '𝕆', 'ℙ', 'ℚ', 'ℝ', '𝕊', '𝕋', '𝕌', '𝕍', '𝕎', '𝕏', '𝕐', 'ℤ'
		],
		[
			'𝙰', '𝙱', '𝙲', '𝙳', '𝙴', '𝙵', '𝙶', '𝙷', '𝙸', '𝙹', '𝙺', '𝙻', '𝙼', '𝙽', '𝙾', '𝙿', '𝚀', '𝚁', '𝚂', '𝚃', '𝚄', '𝚅', '𝚆', '𝚇', '𝚈', '𝚉'
		],
		[
			'𝖠', '𝖡', '𝖢', '𝖣', '𝖤', '𝖥', '𝖦', '𝖧', '𝖨', '𝖩', '𝖪', '𝖫', '𝖬', '𝖭', '𝖮', '𝖯', '𝖰', '𝖱', '𝖲', '𝖳', '𝖴', '𝖵', '𝖶', '𝖷', '𝖸', '𝖹'
		],
		[
			'𝗔', '𝗕', '𝗖', '𝗗', '𝗘', '𝗙', '𝗚', '𝗛', '𝗜', '𝗝', '𝗞', '𝗟', '𝗠', '𝗡', '𝗢', '𝗣', '𝗤', '𝗥', '𝗦', '𝗧', '𝗨', '𝗩', '𝗪', '𝗫', '𝗬', '𝗭'
		],
		[
			'𝘼', '𝘽', '𝘾', '𝘿', '𝙀', '𝙁', '𝙂', '𝙃', '𝙄', '𝙅', '𝙆', '𝙇', '𝙈', '𝙉', '𝙊', '𝙋', '𝙌', '𝙍', '𝙎', '𝙏', '𝙐', '𝙑', '𝙒', '𝙓', '𝙔', '𝙕'
		],
		[
			'𝘈', '𝘉', '𝘊', '𝘋', '𝘌', '𝘍', '𝘎', '𝘏', '𝘐', '𝘑', '𝘒', '𝘓', '𝘔', '𝘕', '𝘖', '𝘗', '𝘘', '𝘙', '𝘚', '𝘛', '𝘜', '𝘝', '𝘞', '𝘟', '𝘠', '𝘡'
		],
		[
			'⒜', '⒝', '⒞', '⒟', '⒠', '⒡', '⒢', '⒣', '⒤', '⒥', '⒦', '⒧', '⒨', '⒩', '⒪', '⒫', '⒬', '⒭', '⒮', ⒯', '⒰', '⒱', ⒲', '⒳', '⒴', '⒵'
		],
		[
			'🄰', '🄱', '🄲', '🄳', '🄴', '🄵', '🄶', '🄷', '🄸', '🄹', '🄺', '🄻', '🄼', '🄽', '🄾', '🄿', '🅀', '🅁', '🅂', '🅃', '🅄', '🅅', '🅆', '🅇', '🅈', '🅉'
		],
		[
			'🅰', '🅱', '🅲', '🅳', '🅴', '🅵', '🅶', '🅷', '🅸', '🅹', '🅺', '🅻', '🅼', '🅽', '🅾', '🅿', '🆀', '🆁', '🆂', '🆃', '🆄', '🆅', '🆆', '🆇', '🆈', '🆉'
		],
		[
			'Á', 'B', 'Ć', 'D', 'É', 'F', 'Ǵ', 'H', 'í', 'J', 'Ḱ', 'Ĺ', 'Ḿ', 'Ń', 'Ő', 'Ṕ', 'Q', 'Ŕ', 'ś', 'T', 'Ű', 'V', 'Ẃ', 'X', 'Ӳ', 'Ź'
		],
		[
			'ﾑ', '乃', 'c', 'd', '乇', 'ｷ', 'g', 'ん', 'ﾉ', 'ﾌ', 'ズ', 'ﾚ', 'ﾶ', '刀', 'o', 'ｱ', 'q', '尺', '丂', 'ｲ', 'u', '√', 'w', 'ﾒ', 'ﾘ', '乙'
		],
		[
			'ค', '๒', 'ƈ', 'ɗ', 'ﻉ', 'ि', 'ﻭ', 'ɦ', 'ٱ', 'ﻝ', 'ᛕ', 'ɭ', '๓', 'ก', 'ѻ', 'ρ', '۹', 'ɼ', 'ร', 'Շ', 'પ', '۷', 'ฝ', 'ซ', 'ץ', 'չ'
		],
		[
			'α', 'в', '¢', '∂', 'є', 'ƒ', 'ﻭ', 'н', 'ι', 'נ', 'к', 'ℓ', 'м', 'η', 'σ', 'ρ', '۹', 'я', 'ѕ', 'т', 'υ', 'ν', 'ω', 'χ', 'у', 'չ'
		],
		[
			'ค', '๒', 'ς', '๔', 'є', 'Ŧ', 'ﻮ', 'ђ', 'เ', 'ן', 'к', 'ɭ', '๓', 'ภ', '๏', 'ק', 'ợ', 'г', 'ร', 'Շ', 'ย', 'ש', 'ฬ', 'א', 'ץ', 'չ'
		],
		[
			'Д', 'Б', 'Ҁ', 'ↁ', 'Є', 'F', 'Б', 'Н', 'І', 'Ј', 'Ќ', 'L', 'М', 'И', 'Ф', 'Р', 'Q', 'Я', 'Ѕ', 'Г', 'Ц', 'V', 'Щ', 'Ж', 'Ч', 'Z'
		],
		[
			'ል', 'ጌ', 'ር', 'ዕ', 'ቿ', 'ቻ', 'ኗ', 'ዘ', 'ጎ', 'ጋ', 'ጕ', 'ረ', 'ጠ', 'ክ', 'ዐ', 'የ', 'ዒ', 'ዪ', 'ነ', 'ፕ', 'ሁ', 'ሀ', 'ሠ', 'ሸ', 'ሃ', 'ጊ'
		],
		[
			'𝔄', '𝔅', 'ℭ', '𝔇', '𝔈', '𝔉', '𝔊', 'ℌ', 'ℑ', '𝔍', '𝔎', '𝔏', '𝔐', '𝔑', '𝔒', '𝔓', '𝔔', 'ℜ', '𝔖', '𝔗', '𝔘', '𝔙', '𝔚', '𝔛', '𝔜', 'ℨ'
		],
		[
			'Ä', 'Ḅ', 'Ċ', 'Ḋ', 'Ё', 'Ḟ', 'Ġ', 'Ḧ', 'Ї', 'J', 'Ḳ', 'Ḷ', 'Ṁ', 'Ṅ', 'Ö', 'Ṗ', 'Q', 'Ṛ', 'Ṡ', 'Ṫ', 'Ü', 'Ṿ', 'Ẅ', 'Ẍ', 'Ÿ', 'Ż'
		],
		[
			'ᴀ', 'ʙ', 'ᴄ', 'ᴅ', 'ᴇ', 'ꜰ', 'ɢ', 'ʜ', 'ɪ', 'ᴊ', 'ᴋ', 'ʟ', 'ᴍ', 'ɴ', 'ᴏ', 'ᴩ', 'Q', 'ʀ', 'ꜱ', 'ᴛ, 'ᴜ, 'ᴠ', 'ᴡ', 'x', 'Y', 'ᴢ'
		],
		[
			'Ⱥ', 'Ƀ', 'Ȼ', 'Đ', 'Ɇ', 'F', 'Ǥ', 'Ħ', 'Ɨ', 'Ɉ', 'Ꝁ', 'Ł', 'M', 'N', 'Ø', 'Ᵽ', 'Ꝗ', 'Ɍ', 'S', 'Ŧ', 'ᵾ', 'V', 'W', 'X', 'Ɏ', 'Ƶ'
		],
		[
			'ɐ', 'q', 'ɔ', 'p', 'ǝ', 'ɟ', 'ƃ', 'ɥ', 'ı', 'ɾ', 'ʞ', 'ן', 'ɯ', 'u', 'o', 'd', 'b', 'ɹ', 's', 'ʇ', 'n', '𐌡', 'ʍ', 'x', 'ʎ', 'z'
		],
	];

	static $unicodeSamplesLC = [
		'ⓐⓑⓒⓓⓔⓕⓖⓗⓘⓙⓚⓛⓜⓝⓞⓟⓠⓡⓢⓣⓤⓥⓦⓧⓨⓩ',
		'🅐🅑🅒🅓🅔🅕🅖🅗🅘🅙🅚🅛🅜🅝🅞🅟🅠🅡🅢🅣🅤🅥🅦🅧🅨🅩',
		'ａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚ',
		'𝐚𝐛𝐜𝐝𝐞𝐟𝐠𝐡𝐢𝐣𝐤𝐥𝐦𝐧𝐨𝐩𝐪𝐫𝐬𝐭𝐮𝐯𝐰𝐱𝐲𝐳',
		'𝖆𝖇𝖈𝖉𝖊𝖋𝖌𝖍𝖎𝖏𝖐𝖑𝖒𝖓𝖔𝖕𝖖𝖗𝖘𝖙𝖚𝖛𝖜𝖝𝖞𝖟',
		'𝒂𝒃𝒄𝒅𝒆𝒇𝒈𝒉𝒊𝒋𝒌𝒍𝒎𝒏𝒐𝒑𝒒𝒓𝒔𝒕𝒖𝒗𝒘𝒙𝒚𝒛',
		'𝓪𝓫𝓬𝓭𝓮𝓯𝓰𝓱𝓲𝓳𝓴𝓵𝓶𝓷𝓸𝓹𝓺𝓻𝓼𝓽𝓾𝓿𝔀𝔁𝔂𝔃',
		'𝕒𝕓𝕔𝕕𝕖𝕗𝕘𝕙𝕚𝕛𝕜𝕝𝕞𝕟𝕠𝕡𝕢𝕣𝕤𝕥𝕦𝕧𝕨𝕩𝕪𝕫',
		'𝚊𝚋𝚌𝚍𝚎𝚏𝚐𝚑𝚒𝚓𝚔𝚕𝚖𝚗𝚘𝚙𝚚𝚛𝚜𝚝𝚞𝚟𝚠𝚡𝚢𝚣',
		'𝖺𝖻𝖼𝖽𝖾𝖿𝗀𝗁𝗂𝗃𝗄𝗅𝗆𝗇𝗈𝗉𝗊𝗋𝗌𝗍𝗎𝗏𝗐𝗑𝗒𝗓',
		'𝗮𝗯𝗰𝗱𝗲𝗳𝗴𝗵𝗶𝗷𝗸𝗹𝗺𝗻𝗼𝗽𝗾𝗿𝘀𝘁𝘂𝘃𝘄𝘅𝘆𝘇',
		'𝙖𝙗𝙘𝙙𝙚𝙛𝙜𝙝𝙞𝙟𝙠𝙡𝙢𝙣𝙤𝙥𝙦𝙧𝙨𝙩𝙪𝙫𝙬𝙭𝙮𝙯',
		'𝘢𝘣𝘤𝘥𝘦𝘧𝘨𝘩𝘪𝘫𝘬𝘭𝘮𝘯𝘰𝘱𝘲𝘳𝘴𝘵𝘶𝘷𝘸𝘹𝘺𝘻',
		'⒜⒝⒞⒟⒠⒡⒢⒣⒤⒥⒦⒧⒨⒩⒪⒫⒬⒭⒮⒯⒰⒱⒲⒳⒴⒵',
		'🄰🄱🄲🄳🄴🄵🄶🄷🄸🄹🄺🄻🄼🄽🄾🄿🅀🅁🅂🅃🅄🅅🅆🅇🅈🅉',
		'🅰🅱🅲🅳🅴🅵🅶🅷🅸🅹🅺🅻🅼🅽🅾🅿🆀🆁🆂🆃🆄🆅🆆🆇🆈🆉',
		'ábćdéfǵhíjḱĺḿńőṕqŕśtúvẃxӳź',
		'ﾑ乃cd乇ｷgんﾉﾌズﾚﾶ刀oｱq尺丂ｲu√wﾒﾘ乙',
		'ค๒ƈɗﻉिﻭɦٱﻝᛕɭ๓กѻρ۹ɼรՇપ۷ฝซץչ',
		'αв¢∂єƒﻭнιנкℓмησρ۹яѕтυνωχуչ',
		'ค๒ς๔єŦﻮђเןкɭ๓ภ๏קợгรՇยשฬאץչ',
		'аъсↁэfБЂіјкlмиорqѓѕтцvшхЎz',
		'ልጌርዕቿቻኗዘጎጋጕረጠክዐየዒዪነፕሁሀሠሸሃጊ',
		'𝔞𝔟𝔠𝔡𝔢𝔣𝔤𝔥𝔦𝔧𝔨𝔩𝔪𝔫𝔬𝔭𝔮𝔯𝔰𝔱𝔲𝔳𝔴𝔵𝔶𝔷',
		'äḅċḋëḟġḧïjḳḷṁṅöṗqṛṡẗüṿẅẍÿż',
		'ᴀʙᴄᴅᴇꜰɢʜɪᴊᴋʟᴍɴᴏᴩqʀꜱᴛᴜᴠᴡxyᴢ',
		'Ⱥƀȼđɇfǥħɨɉꝁłmnøᵽꝗɍsŧᵾvwxɏƶ',
		'ɐqɔpǝɟƃɥıɾʞןɯuodbɹsʇnʌʍxʎz',
	];

	static function unicodeConvert(string $text, int $type): string
	{
		$split = \str_split($text);
		$joined = '';

		foreach ($split as $letter) {
			if (\ctype_upper($letter)) {
				$position = \ord(\strtolower($letter)) - 97;
				$joined .= self::$unicodeSamplesUC[$type][$position];
			} elseif (\ctype_lower($letter)) {
				$position = \ord($letter) - 97;
				$joined .= self::$unicodeSamplesLC[$type][$position];
			// } elseif ($i = !\empty((int) $letter)){
			// 	$joined .= self::$unicodeSamplesNums[$type][$i];
			} else {
				$joined .= $letter;
			}
		}

		return $joined;
	}

	static function getUnicodeSamples()
	{
		$output = '';

		for($i = 0; $i<\count(self::$unicodeTypes); $i++) {
			$samples = '';
			foreach (self::$unicodeSamplesUC[$i] as $letter) {
				$samples .= $letter;
			}
			$output .= ($i+1).'. '.self::$unicodeTypes[$i].': '.$samples.\PHP_EOL;
		}
		return $output;
	}

	static function getUnicodeTypesCount()
	{
		return \count(self::$unicodeTypes);
	}
}