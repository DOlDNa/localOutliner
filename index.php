<?php
$title = ['タイトル', '第"%s"話　', 'counter(i,cjk-ideographic)', '新規タイトル', 'リカバリー'];
$confirm = ['削除しますか？', '「%s」を保存しますか？', '「%s」のデータに戻しますか？'];
$memo = ['350px', '450px', '2em'];
$backup_files = 20;
$icon = ['➕', '📥', '❌', '🔻', '🔺'];
$separator = '.';
$disallow_symbols = ['"','#','$','%','&','(',')','*','+',',','/',':',';','>','<','=','?','[','\\',']','^','_','`','{','|','}','~'];
$replace_symbols = ['”','＃','＄','％','＆','（','）','＊','＋','，','／','：','；','＞','＜','＝','？','［','￥','］','＾','＿','｀','｛','｜','｝','～'];
$disallow_character = ['\\', '"', '/', PHP_EOL, "\r"];
$replace_character = ['\u005c', '\u0022', '\u002f', '\n'];
$folder = !filter_has_var(INPUT_GET, 'folder') ? '' : str_replace($disallow_symbols, $replace_symbols, filter_input(INPUT_GET, 'folder'));
$del = !filter_has_var(INPUT_GET, 'del') ? '' : basename(filter_input(INPUT_GET, 'del'));
$del_chapter = !filter_has_var(INPUT_POST, 'del') ? '' : json_decode(filter_input(INPUT_POST, 'del'));
if ($glob_folder = glob('*', GLOB_ONLYDIR|GLOB_NOSORT)) usort($glob_folder, function ($a, $b) {return filemtime($b) - filemtime($a);});
$title[0] = basename($folder ?? $glob_folder[0] ?: $title[0]);

if (filter_has_var(INPUT_GET, 'backup'))
{
	if (!is_dir($backup_folder = $title[0]. '/0/')) mkdir($backup_folder);
	$filename = $backup_folder. time(). '.tar.gz';
	`tar -czf "$filename" --exclude='0' "$title[0]"`;
	if ($glob_backup_folder = glob($backup_folder. '*'))
	{
		if ($backup_files < count($glob_backup_folder)) unlink($glob_backup_folder[0]);
	}
}
if ($recover = filter_input(INPUT_GET, 'recover', FILTER_SANITIZE_NUMBER_INT))
{
	if (is_file($recover_gz = $title[0]. '/0/'. basename($recover). '.tar.gz'))
	{
		foreach (glob($title[0]. '/*', GLOB_NOSORT) as $g) if (is_file($g)) unlink($g);
		`tar -C ./ -xf "$recover_gz"`;
	}
}
if ($rename = filter_input(INPUT_POST, 'rename'))
{
	$replace_name = str_replace($disallow_symbols, $replace_symbols, $rename);
	rename($title[0], $replace_name);
	touch($replace_name);
}
elseif ($input = json_decode(file_get_contents('php://input') ?? null, true))
{
	if (isset($input['1'], $input['2']) && is_file($an = $title[0]. '/'. $input['1'][0]))
	{
		file_put_contents($an, $input['2'], LOCK_EX);
	}
	elseif (isset($input['a'], $input['b']))
	{
		$e = $title[0]. '/'. str_replace($disallow_symbols, $replace_symbols, $input['a']);
		if ('b' === $input['b'] && !is_file($e))
		{
			touch($e. $separator);
		}
		elseif ('k' === $input['b'])
		{
			if (isset($input['d']) && is_file($e))
			{
				file_put_contents($e, $input['d'], LOCK_EX);
			}
			elseif (isset($input['c']))
			{
				$f = $title[0]. '/'. basename($input['c']);
				$g = glob($f. '*')[0];
				rename((isset($g) ? $g : $f), $e);
			}
		}
	}
}
elseif (!is_dir($glob_folder[0]) && !is_dir($title[0]))
{
	mkdir($title[0]);
	header('location: ./'. urlencode($title[0]). '/');
}
elseif (is_dir($glob_folder[0]) && !$folder && !$del) header('location: ./'. urlencode($glob_folder[0]). '/');
elseif (!is_dir($title[3]) && $folder === $title[3]) mkdir($title[3]);
if (is_dir($title[0]))
{
	if (!is_file($af = $title[0]. '/a')) touch($af);
	if (!is_file($bf = $title[0]. '/b')) touch($bf);
	if (!is_file($cf = $title[0]. '/c')) touch($cf);
	if (!is_file($df = $title[0]. '/d')) touch($df);
}
$fglob = glob($title[0]. '/[1-9]*', GLOB_NOSORT);
$c = count($fglob);
natsort($fglob);
if ($c !== (int)explode($separator, basename($last = end($fglob)))[0]) unlink($last);
if ($del && is_dir($del)) `rm -r ./"$del"`;
if (is_array($del_chapter))
{
	foreach ($fglob as $old_chapter) unlink($old_chapter);
	foreach ($del_chapter as $dc) file_put_contents($folder. '/'. basename($dc[0]), $dc[1], LOCK_EX);
}
?><!doctype html><html lang=ja><head><meta charset=utf-8><title>localOutliner</title><meta name=viewport content="width=device-width,initial-scale=1"><style>.hide~label[for^="e-"]:before{font-size:x-large;content:"<?=$icon[3]?>"}.memo{height:0;line-height:1.8em;opacity:0;overflow-y:auto;padding:0;transition:opacity.3s,width.5s,height.5s;white-space:pre-wrap;width:0;z-index:2}aside button,select{padding:.5em}button,label{cursor:pointer;opacity:.35;transition:opacity.5s}button,[contenteditable]:focus,textarea,select{border:0;outline:0}button:hover,label:hover{opacity:1}div label{background:black;padding:.5em;z-index:1}div[id]{display:flex;position:relative}fieldset button{position:absolute;top:0;right:0}fieldset label[for^="e-"]:before{font-size:x-large;content:"<?=$icon[4]?>";padding:0;position:absolute;top:1.4em;left:.5em;z-index:1}fieldset.dragging{opacity:.7}fieldset[draggable=true]{cursor:row-resize}fieldset{border:0;margin-bottom:1em;padding:1em 2.5em;position:relative;transition:background.5s}footer{bottom:0;font-size:small;left:45%;margin:.5em;position:fixed}h1,h2,textarea{font-weight:unset}h2:before{content:"<?=sprintf($title[1], $title[2])?>";counter-increment:i}h1,h2{font-family:serif;padding-left:.5em;padding-right:.5em}header button,fieldset button{background-color:inherit;padding:1em}header{display:flex;align-items:start;justify-content:space-between;transition:background.5s}input,.hide{display:none!important}label{padding:.7em;z-index:3}main{counter-reset:i;padding:0}option{color:dimgray}samp b{font-weight:unset;text-emphasis-style:dot}samp i{font-style:normal;text-combine-upright:all}samp{background-color:rebeccapurple;column-fill:auto;column-gap:4em;columns:20em;font-family:serif;font-size:x-large;height:100%;letter-spacing:.05em;line-height:2;outline:0;overflow-y:scroll;overscroll-behavior-y:none;padding:1em;position:absolute;white-space:pre-wrap;width:100%;word-break:break-word;writing-mode:vertical-rl}textarea{background:rgba(0,0,0,.1);color:inherit;font-family:unset;font-size:large;height:500px;letter-spacing:.05em;line-height:2em;padding:1em;width:100%}#select{background-color:rgba(0,0,0,.1);color:inherit;position:fixed;top:0;left:45%;width:10em;overflow:hidden}*{box-sizing:border-box}::-webkit-scrollbar,::-webkit-scrollbar-corner,::-webkit-resizer{background:inherit}::-webkit-scrollbar-thumb{background:#ccc;border-radius:5px}[contenteditable]:focus{cursor:auto}</style><link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text x='50%' y='50%' style='dominant-baseline:central;font-size:5em;text-anchor:middle'>📝</text></svg>"></head><body style="background:#222;color:#ccc;margin-left:10%;margin-right:10%;margin-top:3em;margin-bottom:3em"><script>const d=document,b=d.body,copyright=d.title;b.appendChild(header=d.createElement("header")),header.appendChild(h1=d.createElement("h1")),h1.textContent=d.title="<?=$title[0]?>",h1.contentEditable=true,header.appendChild(h1btn=d.createElement("button")),h1btn.textContent="<?=$icon[2]?>",h1.accessKey="h",h1btn.accessKey="z",h1.oninput=e=>border(e),h1.onkeydown=e=>{nl=n(e.target.textContent),e.target.style.color=nl[1];if("Enter"===e.key||"NumpadEnter"===e.key){t=e.target.textContent,e.preventDefault();if(nl[0]){fd.append("rename",t),fetch("",{method:"post",cache:"no-cache",body:fd}).then(r=>{if(r.ok)location.href="../"}).catch(()=>location.href="../")}}},h1btn.onmouseover=e=>header.style.backgroundColor="rgba(255,0,0,.1)",h1btn.onmouseout=e=>header.style.backgroundColor="inherit",h1btn.onclick=e=>{if(confirm("<?=$confirm[0]?>"))fetch("./del").then(r=>{if(r.ok)location.href="../"}).catch(()=>location.href="../")},b.appendChild(main=d.createElement("main")),b.appendChild(aside=d.createElement("aside")),aside.appendChild(btn=d.createElement("button")),btn.textContent="<?=$icon[0]?>",btn.accessKey="n",btn.style.margin="1em",aside.appendChild(bkbtn=d.createElement("button")),bkbtn.textContent="<?=$icon[1]?>",bkbtn.accessKey="y",i=<?=1+$c?>,fd=new FormData(),btn.onclick=()=>{fetch("",{method:"post",cache:"no-cache",body:JSON.stringify({a:i,b:"b"})}).then(r=>{if(r.ok)location.reload()}).catch(()=>location.href="../"),l(i),d.getElementById("d-"+i).classList.remove("hide"),location.hash="c-"+i,d.getElementById("a-"+i).focus(),i++},bkbtn.onclick=()=>{if(confirm("<?=sprintf($confirm[1], $title[0])?>"))fetch("./backup").then(r=>{if(r.ok)location.href="../"})},ondragstart=({target})=>{dragged=target,id=target.id,dlist=main.children,localStorage.clear();for(i1=0,l1=dlist.length;i1<l1;i1++){if(dragged===dlist[i1])idx=i1}},ondragover=({target,drop})=>{if("FIELDSET"===target.tagName&&id!==target.id){for(i2=0,l2=dlist.length;i2<l2;i2++){if(false===dlist[i2].querySelector("div").classList.contains("hide"))dlist[i2].querySelector("div").classList.add("hide");if(target===dlist[i2])drop=i2;if(idx>=drop)target.before(dragged);else target.after(dragged)}}},ondragend=({target})=>{if("TEXTAREA"!==target.tagName){list=main.children,dlist=[];for(i3=0,l3=list.length;i3<l3;i3++)dlist.push([1+i3+"<?=$separator?>"+list[i3].querySelector("h2").textContent,list[i3].querySelector("textarea").value]);fd.append("del",JSON.stringify(dlist)),fetch("",{method:"post",cache:"no-cache",body:fd}).then(r=>{if(r.ok)location.href="../"})}},aside.appendChild(abtn=d.createElement("input")),aside.appendChild(bbtn=d.createElement("input")),aside.appendChild(cbtn=d.createElement("input")),aside.appendChild(dbtn=d.createElement("input")),aside.appendChild(abel=d.createElement("label")),aside.appendChild(bbel=d.createElement("label")),aside.appendChild(cbel=d.createElement("label")),aside.appendChild(dbel=d.createElement("label")),aside.appendChild(aa=d.createElement("textarea")),aside.appendChild(ba=d.createElement("textarea")),aside.appendChild(ca=d.createElement("textarea")),aside.appendChild(da=d.createElement("textarea")),abel.style.position=bbel.style.position=cbel.style.position=dbel.style.position=aa.style.position=ba.style.position=ca.style.position=da.style.position="fixed",abel.style.top=abel.style.left=aa.style.top=aa.style.left=bbel.style.top=bbel.style.right=ba.style.top=ba.style.right=cbel.style.left=cbel.style.bottom=ca.style.left=ca.style.bottom=dbel.style.right=dbel.style.bottom=da.style.right=da.style.bottom=0,abtn.type=bbtn.type=cbtn.type=dbtn.type="checkbox",abel.style.backgroundColor="royalblue",bbel.style.backgroundColor="palevioletred",cbel.style.backgroundColor="forestgreen",dbel.style.backgroundColor="goldenrod",abtn.id="a",bbtn.id="b",cbtn.id="c",dbtn.id="d",aa.id="aa",ba.id="ba",ca.id="ca",da.id="da",aa.style.color="royalblue",ba.style.color="palevioletred",ca.style.color="forestgreen",da.style.color="goldenrod",aa.style.backgroundColor="lightcyan",ba.style.backgroundColor="lavenderblush",ca.style.backgroundColor="honeydew",da.style.backgroundColor="lemonchiffon",aa.className=ba.className=ca.className=da.className="memo",abel.htmlFor=abtn.accessKey="a",bbel.htmlFor=bbtn.accessKey="b",cbel.htmlFor=cbtn.accessKey="c",dbel.htmlFor=dbtn.accessKey="d",[abtn,bbtn,cbtn,dbtn].forEach(c=>c.onchange=e=>{memo=d.getElementById(e.target.id+"a"),style=memo.style,style.transition="opacity .3s,width .5s,height .5s";if(e.target.checked){style.width="<?=$memo[0]?>",style.height="<?=$memo[1]?>",style.padding="<?=$memo[2]?>",style.opacity=1,memo.focus(),memo.onkeydown=f=>{tab(f),fet(f)}}else{style.width=style.height=style.opacity=0}localStorage.setItem(e.target.id,e.target.checked)}),b.appendChild(footer=d.createElement("footer")),footer.textContent="\u00a9 "+new Date().toISOString().substring(0,4)+" "+copyright,d.title+=" - "+copyright,l=(i,j="",k="")=>{main.appendChild(fieldset=d.createElement("fieldset")),fieldset.appendChild(h2=d.createElement("h2")),fieldset.appendChild(inp=d.createElement("input")),fieldset.appendChild(div=d.createElement("div")),div.id="d-"+i,div.appendChild(plb=d.createElement("label")),div.appendChild(pinp=d.createElement("input")),div.appendChild(textarea=d.createElement("textarea")),div.appendChild(samp=d.createElement("samp")),fieldset.appendChild(lb=d.createElement("label")),inp.setAttribute("accesskey",i),fieldset.appendChild(h2btn=d.createElement("button")),h2btn.textContent="<?=$icon[2]?>",inp.type=pinp.type="checkbox",inp.id=lb.htmlFor="e-"+i,pinp.id=plb.htmlFor="p-"+i,fieldset.draggable=true,fieldset.id="c-"+i,h2.contentEditable=true,h2.textContent=j,textarea.id="a-"+i,samp.id="pp-"+i,div.classList.add("hide"),samp.classList.add("hide"),textarea.innerHTML=k,h2.oninput=e=>border(e),h2.onkeydown=e=>{nl=n(e.target.textContent),e.target.style.color=nl[1];if("Enter"===e.key||"NumpadEnter"===e.key){e.preventDefault();if(nl[0])fetch("",{method:"post",cache:"no-cache",body:JSON.stringify({a:i+"<?=$separator?>"+e.target.textContent,b:"k",c:i+"<?=$separator?>",d:e.target.closest("fieldset").querySelector("textarea").value})}).then(r=>{if(r.ok)e.target.style.borderColor="transparent"}).then(()=>location.href="../#c-"+i).catch(()=>location.href="../")}},textarea.onfocus=e=>e.target.previousSibling.accessKey="p",textarea.onblur=e=>e.target.previousSibling.removeAttribute("accessKey"),textarea.oninput=e=>border(e),textarea.onkeydown=e=>{tab(e);if("Enter"===e.key||"NumpadEnter"===e.key){fetch("",{method:"post",cache:"no-cache",body:JSON.stringify({a:i+"<?=$separator?>"+e.target.closest("fieldset").querySelector("h2").textContent,b:"k",c:i+"<?=$separator?>",d:e.target.value})}).then(r=>{if(r.ok)e.target.style.borderColor="transparent"}).catch(()=>location.href="../")}},h2btn.onclick=e=>{if(confirm("<?=$confirm[0]?>")){fetch("./del/"+i+"<?=$separator?>"+e.target.closest("fieldset").querySelector("h2").textContent.replace(/ /g,"+")).then(r=>{if(r.ok){e.target.parentNode.remove();list=main.children,plist=[];for(i4=0,l4=list.length;i4<l4;i4++)plist.push([1+i4+"<?=$separator?>"+list[i4].querySelector("h2").textContent,list[i4].querySelector("textarea").value]);fd.append("del",JSON.stringify(plist)),fetch("",{method:"post",cache:"no-cache",body:fd}).then(r=>{if(r.ok)location.href="../"})}})}},h2btn.onmouseover=e=>e.target.parentNode.style.backgroundColor="rgba(255,0,0,.1)",h2btn.onmouseout=e=>e.target.parentNode.style.backgroundColor="inherit",inp.onchange=e=>{if(false===e.target.nextSibling.classList.contains("hide")){location.hash="",e.target.nextSibling.closest("fieldset").draggable=true,localStorage.setItem(e.target.nextSibling.id,false)}else{location.hash="c-"+i,e.target.nextSibling.closest("fieldset").draggable=false,localStorage.setItem(e.target.nextSibling.id,true),setTimeout(()=>d.getElementById("a-"+i).focus(),5)}e.target.nextSibling.classList.toggle("hide")},pinp.onchange=e=>{d.getElementById("p"+e.target.id).classList.toggle("hide"),d.getElementById("p"+e.target.id).innerHTML=e.target.nextSibling.value.replace(/([｜\|])?(《《([\p{sc=Hira}\p{sc=Kana}\p{sc=Han}ー]+)》》)/gu,(b,b1,b2,b3)=>{if(b1)return b2;else return"<b>"+b3+"<\/b>"}).replace(/[｜\|]([\p{scx=Hira}\p{scx=Kana}\p{scx=Han}]+)《([\p{scx=Hira}\p{scx=Kana}\p{sc=Han}]+)》/gu,"<ruby>$1<rt>$2<\/rt><\/ruby>").replace(/([\p{sc=Han}]+)《([\p{scx=Hira}\p{scx=Kana}]+)》/gu,"<ruby>$1<rt>$2<\/rt><\/ruby>").replace(/[｜\|]([\p{scx=Hira}\p{scx=Kana}\p{scx=Han}]+)/gu,"$1").replace(/(\d{5,})/g,num=>{const numeral=["○","一","二","三","四","五","六","七","八","九"],unit=["零","十","百","千","万","億","兆","京","垓"];if(num.length/4>unit.length-3)return num.replace(/¥d/g,s=>numeral[parseInt(s)]);let kn,str="",arr=num.match(/\d{1,4}?(?=(\d{4})*$)/gm);for(i5=0,l5=arr.length-1;i5<=l5;i5++){if(!parseInt(arr[i5]))continue;for(i6=0,l6=arr[i5].length-1;i6<=l6;i6++){kn=parseInt(arr[i5][i6]);if(!kn)continue;if(i6===l6)str+=numeral[kn];else str+=(1<kn?numeral[kn]:"")+unit[l6-i6]}if(i5!==l5)str+=unit[l5-i5+3]}return str}).replace(/(\d{3}\,\d{3}|\d{2,4}|\d\.\d{1,4}|\.\d{1,4}|\d| [A-Za-z] )/g,"<i>$1<\/i>").replace(/(?!<\/?b>|<\/?i>|<\/?rt>|<\/?ruby>)<([^>]+)>/gi,"&lt$1&gt").split("\n").map(line=>{return (line.match(/^\s/)?"":"　")+line}).join("\n"),d.getElementById("p"+e.target.id).tabIndex=0,d.getElementById("p"+e.target.id).focus(),d.getElementById("p-"+i).accessKey="p";if(d.getElementById("p"+e.target.id).classList.contains("hide"))e.target.nextSibling.focus()}},n=(s,l=0)=>{for(i in s)if(s[i].match(/[^\x01-\x7e\uff65-\uff9f]/))l+=3;else l+=1;return 240>=l?[true,"inherit"]:[false,"red"]},tab=e=>{if(e.shiftKey&&" "===e.key){e.preventDefault();start=e.target.selectionStart,end=e.target.selectionEnd,e.target.value=e.target.value.substring(0,start)+"\t"+e.target.value.substring(end),e.target.selectionStart=e.target.selectionEnd=1+start}},fet=e=>{if("Enter"===e.key||"NumpadEnter"===e.key)fetch("",{method:"post",cache:"no-cache",body:JSON.stringify({1:e.target.id[0],2:e.target.value})}).then(r=>{if(r.ok)e.target.style.borderColor="transparent"}).catch(()=>location.href="../")},border=e=>{e.target.style.transition="border-color.3s",e.target.style.border="thin solid darkorange"},<?php
if ($fglob)
{
	$i = 1;
	foreach ($fglob as $file)
	{
		if (is_file($file))
		{
			$bfile = basename($file);
			$j = !preg_match('/^\d+./', $bfile) ? ',""' : ',"'. preg_replace('/^(\d+.).*?/', '', $bfile). '"';
			$k = !filesize($file) ? '' : ',"'. str_replace($disallow_character, $replace_character, file_get_contents($file)). '"';
			echo 'l(', $i, $j, $k, '),';
			++$i;
		}
	}
}
if ($ad = glob($title[0]. '/[a-d]'))
	foreach ($ad as $afile)
	{
		$baf = basename($afile);
		echo $baf, 'a.textContent="', str_replace($disallow_character, $replace_character, file_get_contents($afile)), '",', $baf, 'a.oninput=e=>{e.target.style.transition="border-color.3s",e.target.style.border="thin solid red"},';
	}
if ($glob_folder)
{
	echo 'aside.appendChild(select=d.createElement("select")),select.id="select",select.accessKey="s",';
	foreach ($glob_folder as $key => $val)
	{
		$dirname = basename($val);
		echo 'select.add(opt', $key, '=new Option("', $dirname, '")),';
		if ($title[0] === $dirname) echo 'opt', $key, '.setAttribute("selected","selected"),';
	}
	if (!is_dir($title[3])) echo 'select.appendChild(hr=d.createElement("hr")),select.add(opt', (1+$key), '=new Option("', $title[3], '")),opt', (1+$key), '.style.color="forestgreen",';
	echo 'select.onchange=e=>location.href="../"+encodeURI(e.target.value).replace(/%20/g,"+")+"/";';
}
if ($gzs = glob($title[0]. '/0/[1-9][7-9][1-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]\.tar.gz', GLOB_NOSORT))
{
	rsort($gzs);
	echo 'aside.appendChild(rlabel=d.createElement("label")),rlabel.appendChild(rselect=d.createElement("select")),rselect.id="recover",rselect.add(new Option("', $title[4], '")),rselect.firstChild.disabled=true,';
	foreach ($gzs as $gz)
	{
		$gzname = basename($gz, '.tar.gz');
		echo 'rselect.add(new Option("', date('Y-m-d H:i:s', $gzname), '","', $gzname, '")),';
	}
	echo 'rselect.onchange=e=>{if(confirm("', sprintf($confirm[2], date('Y-m-d H:i:s', $gzname)), '"))fetch("./recover/"+e.target.value).then(r=>{if(r.ok)location.href="../"})};';
}
?>for(i7=0,l7=localStorage.length;i7<l7;i7++){key=localStorage.key(i7);if(key[0].match(/[a-d]/)){item=JSON.parse(localStorage.getItem(key)),el=d.getElementById(key+"a"),em=d.getElementById(key);if(1===key.length){if(el){em.checked=el.style.opacity=item?1:0,el.style.width=item?"<?=$memo[0]?>":0,el.style.height=item?"<?=$memo[1]?>":0,el.style.padding=item?"<?=$memo[2]?>":0,el.onkeydown=e=>{tab(e),fet(e)}}}else{if(em){if(item){em.classList.remove("hide"),em.previousSibling.checked=true,em.closest("fieldset").draggable=false}else{em.classList.add("hide"),em.previousSibling.checked=false,em.closest("fieldset").draggable=true}}}}}if(hash=location.hash){if(null!==(qh=d.querySelector(hash))){if((qdiv=qh.querySelector("div")).classList.contains("hide"))qdiv.classList.remove("hide");setTimeout(()=>qh.querySelector("textarea").focus(),5)}}</script></body></html>
